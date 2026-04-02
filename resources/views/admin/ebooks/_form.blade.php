@php
    $isEdit = isset($ebook) && $ebook;
    $action = $isEdit ? route('admin.ebooks.update', $ebook->id) : route('admin.ebooks.store');
    $pricingDefault = old('pricing_type', $isEdit ? $ebook->pricing_type : 'free');
    $coverUrl = ($isEdit && !empty($ebook->cover_image_path)) ? asset('storage/' . $ebook->cover_image_path) : '';
    $bannerUrl = ($isEdit && !empty($ebook->banner_image_path)) ? asset('storage/' . $ebook->banner_image_path) : '';
    $materialUrl = ($isEdit && !empty($ebook->material_path)) ? asset('storage/' . $ebook->material_path) : '';
    $materialName = ($isEdit && !empty($ebook->material_path)) ? basename($ebook->material_path) : '';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
        <!-- Left: text fields -->
        <div class="space-y-5 min-w-0">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">E-Book Title @include('admin.partials.required-mark')</label>
                <input type="text" name="title" value="{{ old('title', $ebook->title ?? '') }}" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Short Description</label>
                <textarea name="short_description" rows="3"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('short_description', $ebook->short_description ?? '') }}</textarea>
                @error('short_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('description', $ebook->description ?? '') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="pt-4">
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Pricing Type @include('admin.partials.required-mark')</label>
                <div x-data="{ type: '{{ $pricingDefault }}' }" class="space-y-4">
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="radio" name="pricing_type" value="free" x-model="type"
                                   class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500/30">
                            <span>Free</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="radio" name="pricing_type" value="paid" x-model="type"
                                   class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500/30">
                            <span>Paid</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-2">
                            Price
                            <span class="text-red-500 font-bold" x-show="type === 'paid'" x-cloak aria-hidden="true">*</span>
                        </label>
                        <input type="number" step="0.01" name="price" min="0"
                               value="{{ old('price', $isEdit ? $ebook->price : '') }}"
                               :disabled="type === 'free'"
                               :required="type === 'paid'"
                               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 disabled:bg-slate-50 disabled:text-slate-400">
                        @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $isEdit ? $ebook->is_active : true) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500/30">
                    Display Active
                </label>
            </div>
        </div>

        <!-- Right: uploads + previews -->
        <div class="flex flex-col gap-6 min-w-0">
            <div class="border border-slate-200 rounded-2xl px-6 py-5">
                <p class="text-sm font-semibold text-slate-800 mb-4">Images</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Cover -->
                    <div class="space-y-3">
                        <div id="ebook_cover_preview"
                             class="{{ $coverUrl ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <img id="ebook_cover_preview_img" src="{{ $coverUrl }}"
                                     class="w-24 h-16 object-cover rounded-lg border border-slate-100" alt="Cover preview">
                                <div class="flex items-center gap-2 shrink-0">
                                    <a id="ebook_cover_view_link" href="{{ $coverUrl ?: '#' }}" target="_blank" title="View cover"
                                       class="{{ $coverUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                                        </svg>
                                    </a>
                                    <button type="button" id="ebook_cover_zoom_btn" data-zoom-src="{{ $coverUrl }}" title="Zoom cover"
                                            class="{{ $coverUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center"
                                            onclick="ebookZoomFrom(this.dataset.zoomSrc)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 7v6m3-3h-6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                            <div class="text-slate-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                                    <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-700">Cover Image</span>
                            <input id="ebook_cover_image_input" type="file" name="cover_image" class="hidden" accept="image/*">
                        </label>
                        @error('cover_image')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Banner -->
                    <div class="space-y-3">
                        <div id="ebook_banner_preview"
                             class="{{ $bannerUrl ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <img id="ebook_banner_preview_img" src="{{ $bannerUrl }}"
                                     class="w-24 h-16 object-cover rounded-lg border border-slate-100" alt="Banner preview">
                                <div class="flex items-center gap-2 shrink-0">
                                    <a id="ebook_banner_view_link" href="{{ $bannerUrl ?: '#' }}" target="_blank" title="View banner"
                                       class="{{ $bannerUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                                        </svg>
                                    </a>
                                    <button type="button" id="ebook_banner_zoom_btn" data-zoom-src="{{ $bannerUrl }}" title="Zoom banner"
                                            class="{{ $bannerUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center"
                                            onclick="ebookZoomFrom(this.dataset.zoomSrc)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 7v6m3-3h-6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                            <div class="text-slate-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                                    <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-700">Banner Image</span>
                            <input id="ebook_banner_image_input" type="file" name="banner_image" class="hidden" accept="image/*">
                        </label>
                        @error('banner_image')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="border border-slate-200 rounded-2xl px-6 py-5">
                <p class="text-sm font-semibold text-slate-800 mb-4">Material Upload</p>
                <div class="space-y-3">
                    <div id="ebook_material_preview"
                         class="{{ $materialUrl ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">Current file</p>
                        <p id="ebook_material_name" class="text-sm font-semibold text-slate-800 truncate" title="{{ $materialName }}">{{ $materialName }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a id="ebook_material_download" href="{{ $materialUrl ?: '#' }}" target="_blank" download
                               class="{{ $materialUrl ? '' : 'hidden' }} inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[#0f172a] text-white text-xs font-bold hover:bg-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                                Download
                            </a>
                        </div>
                    </div>
                    <p id="ebook_material_selected" class="hidden text-xs font-semibold text-indigo-600"></p>
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <div class="text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                                <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-slate-700">Replace with Word, PDF, or Zip</span>
                        <input id="ebook_material_input" type="file" name="material" class="hidden" accept=".pdf,.doc,.docx,.zip">
                    </label>
                </div>
                @error('material')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <!-- Actions: right-aligned (donations-style) -->
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6 mt-8 border-t border-slate-100">
        <a href="{{ route('admin.ebooks.index') }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center border border-slate-200 sm:min-w-[140px]">
            Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all sm:min-w-[180px]">
            {{ $isEdit ? 'Update E-Book' : 'Create E-Book' }}
        </button>
    </div>
</form>

<div id="ebook-zoom-modal" class="fixed inset-0 z-[210] hidden">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="ebookCloseZoom()"></div>
    <div class="relative w-full h-full flex items-center justify-center p-4">
        <div class="rounded-[28px] bg-white border border-slate-100 shadow-2xl max-w-3xl w-full p-4 overflow-hidden">
            <img id="ebook-zoom-img" class="w-full h-auto max-h-[75vh] object-contain rounded-[22px]" src="" alt="Zoomed image">
            <div class="mt-3 flex justify-end">
                <button type="button" onclick="ebookCloseZoom()"
                        class="px-5 py-2 rounded-xl bg-[#0f172a] hover:bg-indigo-600 text-white text-xs font-extrabold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        window.ebookZoomFrom = function (src) {
            const modal = document.getElementById('ebook-zoom-modal');
            const img = document.getElementById('ebook-zoom-img');
            if (!modal || !img || !src) return;
            img.src = src;
            modal.classList.remove('hidden');
        };

        window.ebookCloseZoom = function () {
            const modal = document.getElementById('ebook-zoom-modal');
            const img = document.getElementById('ebook-zoom-img');
            if (modal) modal.classList.add('hidden');
            if (img) img.src = '';
        };

        function setupImagePreview(inputId, previewWrapId, previewImgId, viewLinkId, zoomBtnId) {
            const input = document.getElementById(inputId);
            const wrap = document.getElementById(previewWrapId);
            const imgEl = document.getElementById(previewImgId);
            const view = document.getElementById(viewLinkId);
            const zoomBtn = document.getElementById(zoomBtnId);
            let objUrl = null;

            if (!input) return;

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) return;

                if (objUrl) URL.revokeObjectURL(objUrl);
                objUrl = URL.createObjectURL(file);

                if (wrap) wrap.classList.remove('hidden');
                if (imgEl) imgEl.src = objUrl;

                if (view) {
                    view.href = objUrl;
                    view.classList.remove('hidden');
                }
                if (zoomBtn) {
                    zoomBtn.dataset.zoomSrc = objUrl;
                    zoomBtn.classList.remove('hidden');
                }
            });
        }

        setupImagePreview(
            'ebook_cover_image_input',
            'ebook_cover_preview',
            'ebook_cover_preview_img',
            'ebook_cover_view_link',
            'ebook_cover_zoom_btn'
        );

        setupImagePreview(
            'ebook_banner_image_input',
            'ebook_banner_preview',
            'ebook_banner_preview_img',
            'ebook_banner_view_link',
            'ebook_banner_zoom_btn'
        );

        const matInput = document.getElementById('ebook_material_input');
        const matSelected = document.getElementById('ebook_material_selected');
        if (matInput && matSelected) {
            matInput.addEventListener('change', function () {
                const file = matInput.files && matInput.files[0];
                if (!file) {
                    matSelected.classList.add('hidden');
                    matSelected.textContent = '';
                    return;
                }
                matSelected.textContent = 'Selected: ' + file.name;
                matSelected.classList.remove('hidden');
            });
        }
    })();
</script>
