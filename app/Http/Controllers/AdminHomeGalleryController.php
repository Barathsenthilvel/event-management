<?php

namespace App\Http\Controllers;

use App\Models\HomeGalleryItem;
use App\Models\HomeGallerySection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminHomeGalleryController extends Controller
{
    private const CATEGORY_OPTIONS = ['programs', 'events', 'community'];

    private const LAYOUT_OPTIONS = ['hero', 'wide', 'banner', 'cell'];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $items = HomeGalleryItem::query()
            ->with('creator:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('eyebrow', 'like', '%'.$q.'%')
                        ->orWhere('category_key', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $section = HomeGallerySection::query()->first();

        return view('admin.home-galleries.index', compact('items', 'q', 'section'));
    }

    public function create()
    {
        return view('admin.home-galleries.create');
    }

    public function store(Request $request)
    {
        $files = $this->normalizeUploadedImages($request);

        if ($files !== []) {
            $request->files->set('images', $files);

            return $this->storeBulk($request, $files);
        }

        if ($request->hasFile('image')) {
            $validated = $request->validate($this->singleImageRules(true));
            $payload = $this->buildPayload($request, $validated, true);

            if ($request->boolean('is_category_primary')) {
                $this->clearCategoryPrimary($payload['category_key']);
                $payload['is_category_primary'] = true;
            } else {
                $payload['is_category_primary'] = ! HomeGalleryItem::query()
                    ->where('category_key', $payload['category_key'])
                    ->where('is_category_primary', true)
                    ->exists();
            }

            HomeGalleryItem::create($payload);

            return redirect()->route('admin.home-galleries.index')->with('success', 'Home gallery item created successfully.');
        }

        return back()
            ->withErrors(['images' => 'Please select at least one image to upload.'])
            ->withInput();
    }

    public function edit(HomeGalleryItem $homeGallery)
    {
        return view('admin.home-galleries.edit', ['item' => $homeGallery]);
    }

    public function update(Request $request, HomeGalleryItem $homeGallery)
    {
        $validated = $request->validate($this->singleImageRules(false));
        $payload = $this->buildPayload($request, $validated, false, $homeGallery);

        if ($request->boolean('is_category_primary')) {
            $this->clearCategoryPrimary($payload['category_key'], $homeGallery->id);
            $payload['is_category_primary'] = true;
        } else {
            $payload['is_category_primary'] = false;
        }

        $homeGallery->update($payload);

        return redirect()->route('admin.home-galleries.index')->with('success', 'Home gallery item updated successfully.');
    }

    public function destroy(HomeGalleryItem $homeGallery)
    {
        if ($homeGallery->image_path && Storage::disk('public')->exists($homeGallery->image_path)) {
            Storage::disk('public')->delete($homeGallery->image_path);
        }

        $wasPrimary = $homeGallery->is_category_primary;
        $categoryKey = $homeGallery->category_key;

        $homeGallery->delete();

        if ($wasPrimary) {
            $this->promoteFirstInCategory($categoryKey);
        }

        return redirect()->route('admin.home-galleries.index')->with('success', 'Home gallery item deleted successfully.');
    }

    public function toggleStatus(HomeGalleryItem $homeGallery)
    {
        $homeGallery->update(['is_active' => ! $homeGallery->is_active]);

        return redirect()->route('admin.home-galleries.index')->with('success', 'Gallery item display status updated.');
    }

    public function updateSection(Request $request)
    {
        $validated = $request->validate([
            'section_badge' => ['nullable', 'string', 'max:120'],
            'section_title' => ['nullable', 'string', 'max:255'],
            'section_description' => ['nullable', 'string', 'max:1200'],
        ]);

        $section = HomeGallerySection::query()->first();

        if (! $section) {
            HomeGallerySection::create(array_merge(
                ['created_by_admin_id' => Auth::guard('admin')->id()],
                $validated
            ));
        } else {
            $section->update($validated);
        }

        return redirect()->route('admin.home-galleries.index')->with('success', 'Gallery section content updated successfully.');
    }

    private function storeBulk(Request $request, array $files)
    {
        $validated = $request->validate($this->bulkImageRules());

        $categoryKey = $validated['category_key'];
        $baseSort = (int) ($validated['sort_order'] ?? 0);
        $layoutFirst = $validated['layout_type'];
        $title = $validated['title'];
        $eyebrow = $validated['eyebrow'] ?? ucfirst($categoryKey);
        $description = $validated['description_text'] ?? null;
        $isActive = $request->boolean('is_active', true);

        $this->clearCategoryPrimary($categoryKey);

        $created = 0;
        foreach ($files as $index => $file) {
            $path = $file->store('home/gallery', 'public');

            HomeGalleryItem::create([
                'created_by_admin_id' => Auth::guard('admin')->id(),
                'category_key' => $categoryKey,
                'is_category_primary' => $index === 0,
                'layout_type' => $index === 0 ? $layoutFirst : 'cell',
                'image_path' => $path,
                'alt_text' => $validated['alt_text'] ?? $title,
                'eyebrow' => $eyebrow,
                'title' => $title,
                'description_text' => $index === 0 ? $description : null,
                'sort_order' => $baseSort + $index,
                'is_active' => $isActive,
            ]);
            $created++;
        }

        $categoryLabel = ucfirst($categoryKey);

        return redirect()
            ->route('admin.home-galleries.index')
            ->with('success', "{$created} gallery image(s) added for {$categoryLabel}. The first uploaded image is shown when visitors filter by {$categoryLabel} on the homepage.");
    }

    /**
     * @return list<UploadedFile>
     */
    private function normalizeUploadedImages(Request $request): array
    {
        $raw = $request->file('images');

        if ($raw === null) {
            $raw = $request->file('images[]');
        }

        if ($raw instanceof UploadedFile) {
            return $raw->isValid() ? [$raw] : [];
        }

        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_filter(
            $raw,
            fn ($file) => $file instanceof UploadedFile && $file->isValid()
        ));
    }

    private function bulkImageRules(): array
    {
        return [
            'category_key' => ['required', Rule::in(self::CATEGORY_OPTIONS)],
            'layout_type' => ['required', Rule::in(self::LAYOUT_OPTIONS)],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description_text' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['required', 'array', 'min:1', 'max:20'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }

    private function singleImageRules(bool $creating): array
    {
        return [
            'category_key' => ['required', Rule::in(self::CATEGORY_OPTIONS)],
            'layout_type' => ['required', Rule::in(self::LAYOUT_OPTIONS)],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description_text' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_category_primary' => ['nullable', 'boolean'],
            'image' => $creating
                ? ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120']
                : ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating, ?HomeGalleryItem $item = null): array
    {
        $payload = [
            'category_key' => $validated['category_key'],
            'layout_type' => $validated['layout_type'],
            'alt_text' => $validated['alt_text'] ?? null,
            'eyebrow' => $validated['eyebrow'] ?? null,
            'title' => $validated['title'],
            'description_text' => $validated['description_text'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('image')) {
            if ($item && $item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }
            $payload['image_path'] = $request->file('image')->store('home/gallery', 'public');
        } elseif ($item) {
            $payload['image_path'] = $item->image_path;
        }

        return $payload;
    }

    private function clearCategoryPrimary(string $categoryKey, ?int $exceptId = null): void
    {
        HomeGalleryItem::query()
            ->where('category_key', $categoryKey)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['is_category_primary' => false]);
    }

    private function promoteFirstInCategory(string $categoryKey): void
    {
        $next = HomeGalleryItem::query()
            ->where('category_key', $categoryKey)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($next) {
            $this->clearCategoryPrimary($categoryKey);
            $next->update(['is_category_primary' => true]);
        }
    }
}
