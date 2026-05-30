@php
    $isEdit = isset($item) && $item;
    $action = $isEdit ? route('admin.home-galleries.update', $item->id) : route('admin.home-galleries.store');
    $imageUrl = ($isEdit && !empty($item->image_path)) ? asset('storage/' . ltrim((string) $item->image_path, '/')) : '';
    $batchItems = $batchItems ?? collect();
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    @if($errors->has('category_key'))
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
            {{ $errors->first('category_key') }}
        </div>
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
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">{{ $isEdit ? 'Layout' : 'Layout (first image)' }} @include('admin.partials.required-mark')</label>
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
                <p class="text-sm font-semibold text-slate-800 mb-1">Gallery {{ $isEdit ? 'Image' : 'Images' }}</p>
                @if(!$isEdit)
                    <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">Select a category, then choose one or more images. All images in one save are grouped as <strong>one upload</strong> in the admin list. Each image is stored separately so it can appear on the gallery page — the <strong>first image</strong> is the main one for that category on the homepage.</p>
                @else
                    <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">All images from this upload are shown below. Replace the image you are editing, or add more images to the same upload.</p>
                @endif

                @if($isEdit)
                    @include('admin.home-galleries._batch-images', ['batchItems' => $batchItems, 'item' => $item])

                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <span class="text-xs font-medium text-slate-700">Replace image for this item (optional)</span>
                        <input id="home_gallery_replace_input" type="file" name="image" class="hidden" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                    </label>
                    @error('image')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror

                    <div class="mt-4 pt-4 border-t border-slate-200">
                        <p class="text-xs font-semibold text-slate-800 mb-2">Add more images to this upload</p>
                        <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-indigo-200 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                            <span class="text-xs font-medium text-slate-700">Choose images (multiple allowed)</span>
                            <span id="home_gallery_upload_count" class="hidden text-[11px] font-semibold text-indigo-600"></span>
                            <input id="home_gallery_images_input" type="file" name="images[]" class="hidden" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple>
                        </label>
                    </div>
                @endif

                @if(!$isEdit)
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <div class="text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                                <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-slate-700">
                            Upload images (click again to add more) @include('admin.partials.required-mark')
                        </span>
                        <span id="home_gallery_upload_count" class="hidden text-[11px] font-semibold text-indigo-600"></span>
                        <input id="home_gallery_images_input" type="file" name="images[]" class="hidden" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple>
                    </label>
                @endif

                @error('images')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                @error('images.*')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                <div id="home_gallery_upload_preview" class="hidden mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2"></div>
            </div>

            @if($isEdit)
                <label class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-900">
                    <input type="checkbox" name="is_category_primary" value="1" {{ old('is_category_primary', $item->is_category_primary ?? false) ? 'checked' : '' }}>
                    Show as main image for this category on homepage
                </label>
            @endif

            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
                Display on website
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
            {{ $isEdit ? 'Update Gallery Item' : 'Upload Gallery Images' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('home_gallery_images_input');
        const preview = document.getElementById('home_gallery_upload_preview');
        const countLabel = document.getElementById('home_gallery_upload_count');
        const form = input ? input.closest('form') : null;
        const isEdit = @json((bool) $isEdit);
        if (!input || !preview || !form) return;

        let accumulated = [];
        let objectUrls = [];

        function fileKey(file) {
            return [file.name, file.size, file.lastModified].join(':');
        }

        function syncInputFiles() {
            const dt = new DataTransfer();
            accumulated.forEach((file) => dt.items.add(file));
            input.files = dt.files;
        }

        function removeFileAt(index) {
            accumulated.splice(index, 1);
            syncInputFiles();
            renderPreview();
        }

        function renderPreview() {
            objectUrls.forEach((url) => URL.revokeObjectURL(url));
            objectUrls = [];
            preview.innerHTML = '';

            if (accumulated.length === 0) {
                preview.classList.add('hidden');
                if (countLabel) {
                    countLabel.textContent = '';
                    countLabel.classList.add('hidden');
                }
                return;
            }

            if (countLabel) {
                countLabel.textContent = accumulated.length + ' image' + (accumulated.length === 1 ? '' : 's') + ' ready to upload';
                countLabel.classList.remove('hidden');
            }

            preview.classList.remove('hidden');
            accumulated.forEach((file, index) => {
                const url = URL.createObjectURL(file);
                objectUrls.push(url);
                const wrap = document.createElement('div');
                wrap.className = 'rounded-lg border border-slate-200 overflow-hidden bg-white relative group';
                const badge = !isEdit && index === 0
                    ? '<span class="absolute left-1 top-1 z-10 rounded bg-indigo-600 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white">Category main</span>'
                    : '';
                wrap.innerHTML = badge
                    + '<img src="' + url + '" alt="" class="w-full h-20 object-cover">'
                    + '<p class="px-2 py-1 text-[10px] font-semibold text-slate-600 truncate">' + file.name + '</p>'
                    + '<button type="button" class="absolute right-1 top-1 rounded bg-white/90 px-1.5 py-0.5 text-[10px] font-bold text-rose-600 opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Remove">×</button>';
                wrap.querySelector('button').addEventListener('click', () => removeFileAt(index));
                preview.appendChild(wrap);
            });
        }

        input.addEventListener('change', function () {
            const picked = Array.from(input.files || []);
            picked.forEach((file) => {
                const key = fileKey(file);
                if (!accumulated.some((existing) => fileKey(existing) === key)) {
                    accumulated.push(file);
                }
            });
            syncInputFiles();
            renderPreview();
            input.value = '';
        });

        form.addEventListener('submit', function (e) {
            if (accumulated.length > 0) {
                syncInputFiles();
            } else if (isEdit) {
                input.removeAttribute('name');
            }

            if (!isEdit && accumulated.length === 0) {
                e.preventDefault();
                alert('Please select at least one image to upload.');
            }
        });
    })();
</script>
@endpush
