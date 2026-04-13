@php
    $isEdit = isset($item) && $item;
    $action = $isEdit ? route('admin.home-galleries.update', $item->id) : route('admin.home-galleries.store');
    $imageUrl = ($isEdit && !empty($item->image_path)) ? asset('storage/' . ltrim((string) $item->image_path, '/')) : '';
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
                    Title @include('admin.partials.required-mark')
                </label>
                <input type="text" name="title" value="{{ old('title', $item->title ?? '') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"
                       required>
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Category @include('admin.partials.required-mark')</label>
                    <select name="category_key" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        @php($cat = old('category_key', $item->category_key ?? 'programs'))
                        <option value="programs" {{ $cat === 'programs' ? 'selected' : '' }}>Programs</option>
                        <option value="events" {{ $cat === 'events' ? 'selected' : '' }}>Events</option>
                        <option value="community" {{ $cat === 'community' ? 'selected' : '' }}>Community</option>
                    </select>
                    @error('category_key')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Layout @include('admin.partials.required-mark')</label>
                    <select name="layout_type" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        @php($layout = old('layout_type', $item->layout_type ?? 'cell'))
                        <option value="hero" {{ $layout === 'hero' ? 'selected' : '' }}>Hero</option>
                        <option value="wide" {{ $layout === 'wide' ? 'selected' : '' }}>Wide</option>
                        <option value="banner" {{ $layout === 'banner' ? 'selected' : '' }}>Banner</option>
                        <option value="cell" {{ $layout === 'cell' ? 'selected' : '' }}>Cell</option>
                    </select>
                    @error('layout_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Eyebrow</label>
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $item->eyebrow ?? '') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                @error('eyebrow')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Alt text</label>
                <input type="text" name="alt_text" value="{{ old('alt_text', $item->alt_text ?? '') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                @error('alt_text')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Sort order</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    @error('sort_order')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Description text</label>
                <textarea name="description_text" rows="4"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('description_text', $item->description_text ?? '') }}</textarea>
                @error('description_text')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-5">
            <div class="border border-slate-200 rounded-2xl px-5 py-4">
                <p class="text-sm font-semibold text-slate-800 mb-3">Gallery Image</p>

                @if($imageUrl)
                    <div class="mb-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <img src="{{ $imageUrl }}" alt="" class="h-28 w-full rounded-lg object-cover border border-slate-100">
                    </div>
                @endif

                <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                    <div class="text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                            <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-slate-700">
                        Upload Image
                        @if(!$isEdit)@include('admin.partials.required-mark')@endif
                    </span>
                    <input type="file" name="image" class="hidden" accept="image/*" @if(!$isEdit) required @endif>
                </label>
                @error('image')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
                Display this gallery item
            </label>
        </div>
    </div>

    <div class="mt-8 flex gap-3">
        <a href="{{ route('admin.home-galleries.index') }}"
           class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center border border-slate-200">
            Cancel
        </a>
        <button type="submit"
                class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
            {{ $isEdit ? 'Update Gallery Item' : 'Create Gallery Item' }}
        </button>
    </div>
</form>
