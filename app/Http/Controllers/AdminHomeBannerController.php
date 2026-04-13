<?php

namespace App\Http\Controllers;

use App\Models\HomeBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminHomeBannerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $banners = HomeBanner::query()
            ->with('creator:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%' . $q . '%')
                        ->orWhere('caption_title', 'like', '%' . $q . '%')
                        ->orWhere('link_url', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.home-banners.index', compact('banners', 'q'));
    }

    public function create()
    {
        return view('admin.home-banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(true));

        $payload = $this->buildPayload($request, $validated, true);
        HomeBanner::create($payload);

        return redirect()->route('admin.home-banners.index')->with('success', 'Homepage banner created successfully.');
    }

    public function edit(HomeBanner $homeBanner)
    {
        return view('admin.home-banners.edit', ['banner' => $homeBanner]);
    }

    public function update(Request $request, HomeBanner $homeBanner)
    {
        $validated = $request->validate($this->rules(false));
        $payload = $this->buildPayload($request, $validated, false, $homeBanner);
        $homeBanner->update($payload);

        return redirect()->route('admin.home-banners.index')->with('success', 'Homepage banner updated successfully.');
    }

    public function destroy(HomeBanner $homeBanner)
    {
        if ($homeBanner->image_path && Storage::disk('public')->exists($homeBanner->image_path)) {
            Storage::disk('public')->delete($homeBanner->image_path);
        }

        $homeBanner->delete();

        return redirect()->route('admin.home-banners.index')->with('success', 'Homepage banner deleted successfully.');
    }

    public function toggleStatus(HomeBanner $homeBanner)
    {
        $homeBanner->update(['is_active' => ! $homeBanner->is_active]);

        return redirect()->route('admin.home-banners.index')->with('success', 'Banner display status updated.');
    }

    private function rules(bool $creating): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'caption_title' => ['nullable', 'string', 'max:255'],
            'caption_text' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => $creating
                ? ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120']
                : ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating, ?HomeBanner $banner = null): array
    {
        $payload = [
            'title' => $validated['title'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'eyebrow' => $validated['eyebrow'] ?? null,
            'caption_title' => $validated['caption_title'] ?? null,
            'caption_text' => $validated['caption_text'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('image')) {
            if ($banner && $banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $payload['image_path'] = $request->file('image')->store('home/banners', 'public');
        } elseif ($banner) {
            $payload['image_path'] = $banner->image_path;
        }

        return $payload;
    }
}
