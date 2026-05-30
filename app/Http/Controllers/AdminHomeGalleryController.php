<?php

namespace App\Http\Controllers;

use App\Models\HomeGalleryItem;
use App\Models\HomeGallerySection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminHomeGalleryController extends Controller
{
    private const CATEGORY_OPTIONS = ['programs', 'events', 'community'];

    private const LAYOUT_OPTIONS = ['hero', 'wide', 'banner', 'cell'];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $this->backfillUploadBatchIds();

        $allItems = HomeGalleryItem::query()
            ->with('creator:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('eyebrow', 'like', '%'.$q.'%')
                        ->orWhere('category_key', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('created_at')
            ->orderBy('sort_order')
            ->get();

        $groups = $allItems
            ->groupBy(fn (HomeGalleryItem $item) => $this->galleryGroupKey($item))
            ->map(function ($group) {
                $sorted = $group->sortBy('sort_order')->values();
                $primary = $sorted->firstWhere('is_category_primary', true) ?? $sorted->first();

                return (object) [
                    'items' => $sorted,
                    'primary' => $primary,
                    'count' => $sorted->count(),
                ];
            })
            ->sortByDesc(fn ($group) => $group->primary->created_at)
            ->values();

        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10;
        $items = new LengthAwarePaginator(
            $groups->forPage($page, $perPage)->values(),
            $groups->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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

        if ($files === []) {
            return back()
                ->withErrors(['images' => 'Please select at least one image to upload.'])
                ->withInput();
        }

        if (count($files) > 20) {
            return back()
                ->withErrors(['images' => 'You can upload up to 20 images at a time.'])
                ->withInput();
        }

        $this->validateImageFiles($files);

        return $this->storeBulk($request, $files);
    }

    public function edit(HomeGalleryItem $homeGallery)
    {
        $batchItems = $this->batchItemsFor($homeGallery);

        return view('admin.home-galleries.edit', [
            'item' => $homeGallery,
            'batchItems' => $batchItems,
        ]);
    }

    public function update(Request $request, HomeGalleryItem $homeGallery)
    {
        $additionalFiles = $this->normalizeUploadedImages($request);

        if (count($additionalFiles) > 20) {
            return back()
                ->withErrors(['images' => 'You can upload up to 20 additional images at a time.'])
                ->withInput();
        }

        if ($additionalFiles !== []) {
            $this->validateImageFiles($additionalFiles);
        }

        $validated = $request->validate($this->singleImageRules(false));
        $payload = $this->buildPayload($request, $validated, false, $homeGallery);

        if ($validated['category_key'] !== $homeGallery->category_key) {
            $slotError = $this->homepageSlotValidationError($validated['category_key'], $homeGallery->upload_batch_id);
            if ($slotError !== null) {
                return back()->withErrors(['category_key' => $slotError])->withInput();
            }
        }

        if ($request->boolean('is_category_primary')) {
            $this->clearCategoryPrimary($payload['category_key'], $homeGallery->id);
            $payload['is_category_primary'] = true;
        } else {
            $payload['is_category_primary'] = false;
        }

        $homeGallery->update($payload);

        $added = 0;
        if ($additionalFiles !== []) {
            $added = $this->appendGalleryImages($request, $validated, $additionalFiles, $homeGallery);
        }

        $message = 'Home gallery item updated successfully.';
        if ($added > 0) {
            $message .= " {$added} additional image(s) were added for ".ucfirst($validated['category_key']).'.';
        }

        return redirect()->route('admin.home-galleries.index')->with('success', $message);
    }

    public function destroy(HomeGalleryItem $homeGallery)
    {
        $batchId = $homeGallery->upload_batch_id;
        $toDelete = filled($batchId)
            ? HomeGalleryItem::query()->where('upload_batch_id', $batchId)->get()
            : collect([$homeGallery]);

        $wasPrimary = $homeGallery->is_category_primary;
        $categoryKey = $homeGallery->category_key;
        $deletedCount = $toDelete->count();

        foreach ($toDelete as $item) {
            if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }
            $item->delete();
        }

        if ($wasPrimary) {
            $this->promoteFirstInCategory($categoryKey);
        }

        $message = $deletedCount > 1
            ? "{$deletedCount} images from this upload were deleted."
            : 'Home gallery item deleted successfully.';

        return redirect()->route('admin.home-galleries.index')->with('success', $message);
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
        $validated = $request->validate($this->bulkMetaRules());

        $slotError = $this->homepageSlotValidationError($validated['category_key']);
        if ($slotError !== null) {
            return back()->withErrors(['category_key' => $slotError])->withInput();
        }

        $categoryKey = $validated['category_key'];
        $baseSort = (int) ($validated['sort_order'] ?? 0);
        $layoutFirst = $validated['layout_type'];
        $title = $validated['title'];
        $eyebrow = $validated['eyebrow'] ?? ucfirst($categoryKey);
        $description = $validated['description_text'] ?? null;
        $isActive = $request->boolean('is_active', true);

        $this->clearCategoryPrimary($categoryKey);

        $batchId = (string) Str::uuid();
        $created = 0;
        foreach ($files as $index => $file) {
            $path = $file->store('home/gallery', 'public');

            HomeGalleryItem::create([
                'created_by_admin_id' => Auth::guard('admin')->id(),
                'upload_batch_id' => $batchId,
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
            ->with('success', "One gallery entry saved with {$created} image(s) for {$categoryLabel}. The first image is the main one on the homepage.");
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    private function appendGalleryImages(Request $request, array $validated, array $files, HomeGalleryItem $homeGallery): int
    {
        $categoryKey = $validated['category_key'];
        $title = $validated['title'];
        $eyebrow = $validated['eyebrow'] ?? ucfirst($categoryKey);
        $altText = $validated['alt_text'] ?? $title;
        $isActive = $request->boolean('is_active', true);
        $baseSort = (int) (HomeGalleryItem::query()
            ->where('category_key', $categoryKey)
            ->max('sort_order') ?? 0);

        $batchId = $homeGallery->upload_batch_id ?: (string) Str::uuid();
        if (! filled($homeGallery->upload_batch_id)) {
            $homeGallery->update(['upload_batch_id' => $batchId]);
        }

        $created = 0;
        foreach ($files as $index => $file) {
            $path = $file->store('home/gallery', 'public');

            HomeGalleryItem::create([
                'created_by_admin_id' => Auth::guard('admin')->id(),
                'upload_batch_id' => $batchId,
                'category_key' => $categoryKey,
                'is_category_primary' => false,
                'layout_type' => 'cell',
                'image_path' => $path,
                'alt_text' => $altText,
                'eyebrow' => $eyebrow,
                'title' => $title,
                'description_text' => null,
                'sort_order' => $baseSort + $index + 1,
                'is_active' => $isActive,
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    private function validateImageFiles(array $files): void
    {
        foreach ($files as $index => $file) {
            validator(
                ['image' => $file],
                ['image' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120']],
                [],
                ['image' => 'image '.($index + 1)]
            )->validate();
        }
    }

    /**
     * @return list<UploadedFile>
     */
    private function normalizeUploadedImages(Request $request): array
    {
        $raw = $request->allFiles()['images'] ?? null;

        return $this->flattenUploadedFiles($raw);
    }

    /**
     * @return list<UploadedFile>
     */
    private function flattenUploadedFiles(mixed $raw): array
    {
        if ($raw instanceof UploadedFile) {
            return $raw->isValid() ? [$raw] : [];
        }

        if (! is_array($raw)) {
            return [];
        }

        $files = [];
        foreach ($raw as $entry) {
            foreach ($this->flattenUploadedFiles($entry) as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    private function bulkMetaRules(): array
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

    /**
     * @return array<string, int>
     */
    private function homepageSlotLimits(): array
    {
        return [
            'programs' => 2,
            'events' => 1,
            'community' => 1,
        ];
    }

    private function countActiveHomepageAlbums(string $categoryKey, ?string $excludeBatchId = null): int
    {
        return HomeGalleryItem::query()
            ->where('category_key', $categoryKey)
            ->where('is_active', true)
            ->when(filled($excludeBatchId), fn ($query) => $query->where('upload_batch_id', '!=', $excludeBatchId))
            ->get()
            ->groupBy(fn (HomeGalleryItem $item) => filled($item->upload_batch_id)
                ? 'batch:'.$item->upload_batch_id
                : 'single:'.$item->id)
            ->count();
    }

    private function homepageSlotValidationError(string $categoryKey, ?string $excludeBatchId = null): ?string
    {
        $limit = $this->homepageSlotLimits()[$categoryKey] ?? null;
        if ($limit === null) {
            return null;
        }

        if ($this->countActiveHomepageAlbums($categoryKey, $excludeBatchId) >= $limit) {
            $label = ucfirst($categoryKey);
            $slotLabel = $limit === 1 ? 'slot is' : 'slots are';

            return "The {$label} homepage {$slotLabel} already filled (max {$limit}). Please delete or edit the existing gallery item before adding a new one.";
        }

        return null;
    }

    private function galleryGroupKey(HomeGalleryItem $item): string
    {
        if (filled($item->upload_batch_id)) {
            return 'batch:'.$item->upload_batch_id;
        }

        return 'single:'.$item->id;
    }

    private function backfillUploadBatchIds(): void
    {
        if (! Schema::hasTable('home_gallery_items') || ! Schema::hasColumn('home_gallery_items', 'upload_batch_id')) {
            return;
        }

        $withoutBatch = HomeGalleryItem::query()
            ->whereNull('upload_batch_id')
            ->orderBy('created_at')
            ->get();

        $withoutBatch
            ->groupBy(fn (HomeGalleryItem $item) => $this->legacyUploadGroupSignature($item))
            ->filter(fn ($group) => $group->count() > 1)
            ->each(function ($group) {
                $batchId = (string) Str::uuid();
                HomeGalleryItem::query()
                    ->whereIn('id', $group->pluck('id'))
                    ->update(['upload_batch_id' => $batchId]);
            });
    }

    private function legacyUploadGroupSignature(HomeGalleryItem $item): string
    {
        $minute = $item->created_at?->format('Y-m-d H:i') ?? '';
        $title = mb_strtolower(trim((string) $item->title));

        return implode('|', [
            $item->category_key,
            $title,
            $minute,
            (string) ($item->created_by_admin_id ?? ''),
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, HomeGalleryItem>
     */
    private function batchItemsFor(HomeGalleryItem $item)
    {
        if (filled($item->upload_batch_id)) {
            return HomeGalleryItem::query()
                ->where('upload_batch_id', $item->upload_batch_id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        return collect([$item]);
    }
}
