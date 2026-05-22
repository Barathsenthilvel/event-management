@php
    $isEdit = isset($post) && $post;
    $action = $isEdit ? route('admin.home-blogs.update', $post->id) : route('admin.home-blogs.store');
    $imageUrl = ($isEdit && !empty($post->image_path)) ? asset('storage/' . ltrim((string) $post->image_path, '/')) : '';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <div class="space-y-5">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">
                    Blog title @include('admin.partials.required-mark')
                </label>
                <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"
                       required>
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Tag</label>
                    <input type="text" name="tag" value="{{ old('tag', $post->tag ?? '') }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    @error('tag')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Publish date</label>
                    <input type="date" name="published_at"
                           value="{{ old('published_at', isset($post->published_at) ? $post->published_at->format('Y-m-d') : '') }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    @error('published_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Excerpt</label>
                <textarea name="excerpt" rows="4"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                @error('excerpt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Comments count</label>
                    <input type="number" name="comments_count" min="0" value="{{ old('comments_count', $post->comments_count ?? 0) }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    @error('comments_count')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Sort order</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $post->sort_order ?? 0) }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    @error('sort_order')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Read more URL</label>
                <input type="text" name="read_more_url" value="{{ old('read_more_url', $post->read_more_url ?? '') }}"
                       placeholder="/blogs/example or https://..."
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                @error('read_more_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-5">
            <div class="border border-slate-200 rounded-2xl px-5 py-4">
                <p class="text-sm font-semibold text-slate-800 mb-3">Blog Image</p>

                <div id="home_blog_image_preview"
                     class="{{ $imageUrl ? '' : 'hidden' }} mb-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p id="home_blog_image_preview_label" class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-2">
                        {{ $isEdit && $imageUrl ? 'Current image' : 'Selected image' }}
                    </p>
                    <div class="flex items-start gap-3">
                        <img id="home_blog_image_preview_img"
                             src="{{ $imageUrl }}"
                             alt="Blog image preview"
                             class="h-36 w-full max-w-sm rounded-lg object-cover border border-slate-100">
                        <a id="home_blog_image_view_link"
                           href="{{ $imageUrl ?: '#' }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="View full image"
                           class="{{ $imageUrl ? '' : 'hidden' }} shrink-0 w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-white inline-flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                    <div class="text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                            <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-slate-700">
                        {{ $isEdit && $imageUrl ? 'Replace image' : 'Upload Image' }}
                        @if(!$isEdit)@include('admin.partials.required-mark')@endif
                    </span>
                    <input id="home_blog_image_input" type="file" name="image" class="hidden" accept="image/*" @if(!$isEdit) required @endif>
                </label>
                <p id="home_blog_image_filename" class="hidden mt-2 text-xs font-semibold text-indigo-600 truncate"></p>
                @error('image')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $post->is_active ?? true) ? 'checked' : '' }}>
                Display this blog post on home page
            </label>
        </div>
    </div>

    <div class="mt-8 flex gap-3">
        <a href="{{ route('admin.home-blogs.index') }}"
           class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center border border-slate-200">
            Cancel
        </a>
        <button type="submit"
                class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
            {{ $isEdit ? 'Update Blog Post' : 'Create Blog Post' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('home_blog_image_input');
        const wrap = document.getElementById('home_blog_image_preview');
        const imgEl = document.getElementById('home_blog_image_preview_img');
        const viewLink = document.getElementById('home_blog_image_view_link');
        const labelEl = document.getElementById('home_blog_image_preview_label');
        const filenameEl = document.getElementById('home_blog_image_filename');
        const isEdit = @json($isEdit);
        let objectUrl = null;

        if (!input) return;

        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file) return;

            if (objectUrl) URL.revokeObjectURL(objectUrl);
            objectUrl = URL.createObjectURL(file);

            if (wrap) wrap.classList.remove('hidden');
            if (imgEl) imgEl.src = objectUrl;
            if (labelEl) labelEl.textContent = isEdit ? 'New image (not saved yet)' : 'Selected image';
            if (filenameEl) {
                filenameEl.textContent = file.name;
                filenameEl.classList.remove('hidden');
            }
            if (viewLink) {
                viewLink.href = objectUrl;
                viewLink.classList.remove('hidden');
            }
        });
    })();
</script>
@endpush
