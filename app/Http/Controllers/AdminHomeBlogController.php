<?php

namespace App\Http\Controllers;

use App\Models\HomeBlogPost;
use App\Models\HomeBlogSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminHomeBlogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $posts = HomeBlogPost::query()
            ->with('creator:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%' . $q . '%')
                        ->orWhere('tag', 'like', '%' . $q . '%')
                        ->orWhere('excerpt', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $section = HomeBlogSection::query()->first();

        return view('admin.home-blogs.index', compact('posts', 'q', 'section'));
    }

    public function create()
    {
        return view('admin.home-blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(true));
        $payload = $this->buildPayload($request, $validated, true);

        HomeBlogPost::create($payload);

        return redirect()->route('admin.home-blogs.index')->with('success', 'Home blog post created successfully.');
    }

    public function edit(HomeBlogPost $homeBlog)
    {
        return view('admin.home-blogs.edit', ['post' => $homeBlog]);
    }

    public function update(Request $request, HomeBlogPost $homeBlog)
    {
        $validated = $request->validate($this->rules(false));
        $payload = $this->buildPayload($request, $validated, false, $homeBlog);

        $homeBlog->update($payload);

        return redirect()->route('admin.home-blogs.index')->with('success', 'Home blog post updated successfully.');
    }

    public function destroy(HomeBlogPost $homeBlog)
    {
        if ($homeBlog->image_path && Storage::disk('public')->exists($homeBlog->image_path)) {
            Storage::disk('public')->delete($homeBlog->image_path);
        }

        $homeBlog->delete();

        return redirect()->route('admin.home-blogs.index')->with('success', 'Home blog post deleted successfully.');
    }

    public function toggleStatus(HomeBlogPost $homeBlog)
    {
        $homeBlog->update(['is_active' => ! $homeBlog->is_active]);

        return redirect()->route('admin.home-blogs.index')->with('success', 'Blog post display status updated.');
    }

    public function updateSection(Request $request)
    {
        $validated = $request->validate([
            'section_badge' => ['nullable', 'string', 'max:120'],
            'section_title' => ['nullable', 'string', 'max:255'],
            'section_description' => ['nullable', 'string', 'max:1200'],
            'section_button_text' => ['nullable', 'string', 'max:120'],
        ]);

        $section = HomeBlogSection::query()->first();

        if (! $section) {
            $section = HomeBlogSection::create(array_merge(
                ['created_by_admin_id' => Auth::guard('admin')->id()],
                $validated
            ));
        } else {
            $section->update($validated);
        }

        return redirect()->route('admin.home-blogs.index')->with('success', 'Blog section content updated successfully.');
    }

    private function rules(bool $creating): array
    {
        return [
            'tag' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'comments_count' => ['nullable', 'integer', 'min:0'],
            'read_more_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => $creating
                ? ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120']
                : ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating, ?HomeBlogPost $post = null): array
    {
        $payload = [
            'tag' => $validated['tag'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? null,
            'comments_count' => (int) ($validated['comments_count'] ?? 0),
            'read_more_url' => $validated['read_more_url'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('image')) {
            if ($post && $post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }
            $payload['image_path'] = $request->file('image')->store('home/blog', 'public');
        } elseif ($post) {
            $payload['image_path'] = $post->image_path;
        }

        return $payload;
    }
}
