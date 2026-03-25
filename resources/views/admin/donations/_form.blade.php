@php
    $isEdit = isset($donation) && $donation;
    $action = $isEdit ? route('admin.donations.update', $donation->id) : route('admin.donations.store');
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      class="bg-white rounded-[24px] border border-slate-100 shadow-sm flex-1 p-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 h-full">
        <!-- Left: text fields -->
        <div class="space-y-5">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Purpose *</label>
                <input type="text" name="purpose" value="{{ old('purpose', $donation->purpose ?? '') }}"
                       required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                @error('purpose')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Short Description</label>
                <textarea name="short_description" rows="3"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('short_description', $donation->short_description ?? '') }}</textarea>
                @error('short_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Description</label>
                <textarea name="description" rows="5"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('description', $donation->description ?? '') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                @php
                    $promoteChecked = (bool) old('promote_front', $donation->promote_front ?? false);
                @endphp
                <label class="flex items-center justify-between gap-4 text-sm text-slate-700 cursor-pointer select-none">
                    <span class="font-bold text-slate-700">Promote Front</span>
                    <span class="relative inline-flex items-center">
                        <input type="hidden" name="promote_front" value="0">
                        <input
                            type="checkbox"
                            name="promote_front"
                            value="1"
                            {{ $promoteChecked ? 'checked' : '' }}
                            class="peer sr-only"
                        >
                        <span class="w-10 h-5 rounded-full bg-slate-300 peer-checked:bg-emerald-500 transition-colors shadow-inner"></span>
                        <span class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></span>
                    </span>
                </label>
            </div>

            <div>
                @php
                    $activeChecked = (bool) old('is_active', $donation->is_active ?? true);
                @endphp
                <label class="flex items-center justify-between gap-4 text-sm text-slate-700 cursor-pointer select-none">
                    <span class="font-bold text-slate-700">Display Active</span>
                    <span class="relative inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ $activeChecked ? 'checked' : '' }}
                            class="peer sr-only"
                        >
                        <span class="w-10 h-5 rounded-full bg-slate-300 peer-checked:bg-emerald-500 transition-colors shadow-inner"></span>
                        <span class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></span>
                    </span>
                </label>
            </div>

            <div class="flex gap-3 pt-4">
                <a href="{{ route('admin.donations.index') }}"
                   class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center border border-slate-200">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                    {{ $isEdit ? 'Update Donation' : 'Create Donation' }}
                </button>
            </div>
        </div>

        <!-- Right: image uploads -->
        <div class="flex flex-col gap-6">
            <div class="border border-slate-200 rounded-2xl px-6 py-5">
                <p class="text-sm font-semibold text-slate-800 mb-4">Images</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $coverUrl = ($isEdit && !empty($donation->cover_image_path))
                            ? asset('storage/' . $donation->cover_image_path)
                            : '';
                        $bannerUrl = ($isEdit && !empty($donation->banner_image_path))
                            ? asset('storage/' . $donation->banner_image_path)
                            : '';
                    @endphp

                    <!-- Cover -->
                    <div class="space-y-3">
                        <div id="donation_cover_preview"
                             class="{{ $coverUrl ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <img id="donation_cover_preview_img"
                                     src="{{ $coverUrl }}"
                                     class="w-24 h-16 object-cover rounded-lg border border-slate-100"
                                     alt="Cover preview">
                                <div class="flex items-center gap-2">
                                    <a id="donation_cover_view_link"
                                       href="{{ $coverUrl ?: '#' }}"
                                       target="_blank"
                                       title="View cover"
                                       class="{{ $coverUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                                        </svg>
                                    </a>
                                    <button id="donation_cover_zoom_btn"
                                            type="button"
                                            data-zoom-src="{{ $coverUrl }}"
                                            title="Zoom cover"
                                            class="{{ $coverUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center"
                                            onclick="donationZoomFrom(this.dataset.zoomSrc)">
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
                            <input id="donation_cover_image_input" type="file" name="cover_image" class="hidden" accept="image/*">
                        </label>
                        @error('cover_image')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Banner -->
                    <div class="space-y-3">
                        <div id="donation_banner_preview"
                             class="{{ $bannerUrl ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <img id="donation_banner_preview_img"
                                     src="{{ $bannerUrl }}"
                                     class="w-24 h-16 object-cover rounded-lg border border-slate-100"
                                     alt="Banner preview">
                                <div class="flex items-center gap-2">
                                    <a id="donation_banner_view_link"
                                       href="{{ $bannerUrl ?: '#' }}"
                                       target="_blank"
                                       title="View banner"
                                       class="{{ $bannerUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z" />
                                        </svg>
                                    </a>
                                    <button id="donation_banner_zoom_btn"
                                            type="button"
                                            data-zoom-src="{{ $bannerUrl }}"
                                            title="Zoom banner"
                                            class="{{ $bannerUrl ? '' : 'hidden' }} w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center"
                                            onclick="donationZoomFrom(this.dataset.zoomSrc)">
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
                            <input id="donation_banner_image_input" type="file" name="banner_image" class="hidden" accept="image/*">
                        </label>
                        @error('banner_image')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Donation zoom modal -->
<div id="donation-zoom-modal" class="fixed inset-0 z-[210] hidden">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="donationCloseZoom()"></div>
    <div class="relative w-full h-full flex items-center justify-center p-4">
        <div class="rounded-[28px] bg-white border border-slate-100 shadow-2xl max-w-3xl w-full p-4 overflow-hidden">
            <img id="donation-zoom-img" class="w-full h-auto max-h-[75vh] object-contain rounded-[22px]" src="" alt="Zoomed image">
            <div class="mt-3 flex justify-end">
                <button type="button"
                        onclick="donationCloseZoom()"
                        class="px-5 py-2 rounded-xl bg-[#0f172a] hover:bg-indigo-600 text-white text-xs font-extrabold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        window.donationZoomFrom = function (src) {
            const modal = document.getElementById('donation-zoom-modal');
            const img = document.getElementById('donation-zoom-img');
            if (!modal || !img || !src) return;
            img.src = src;
            modal.classList.remove('hidden');
        };

        window.donationCloseZoom = function () {
            const modal = document.getElementById('donation-zoom-modal');
            const img = document.getElementById('donation-zoom-img');
            if (modal) modal.classList.add('hidden');
            if (img) img.src = '';
        };

        function setupPreview(inputId, previewWrapId, previewImgId, viewLinkId, zoomBtnId) {
            const input = document.getElementById(inputId);
            const wrap = document.getElementById(previewWrapId);
            const img = document.getElementById(previewImgId);
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
                if (img) img.src = objUrl;

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

        setupPreview(
            'donation_cover_image_input',
            'donation_cover_preview',
            'donation_cover_preview_img',
            'donation_cover_view_link',
            'donation_cover_zoom_btn'
        );

        setupPreview(
            'donation_banner_image_input',
            'donation_banner_preview',
            'donation_banner_preview_img',
            'donation_banner_view_link',
            'donation_banner_zoom_btn'
        );
    })();
</script>
