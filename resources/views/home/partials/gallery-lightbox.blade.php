<div id="gallery-lightbox"
     class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/90 backdrop-blur-sm"
     role="dialog"
     aria-modal="true"
     aria-label="Image viewer">
    <button type="button" data-gallery-lightbox-close
            class="absolute right-4 top-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition"
            aria-label="Close">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>
    <button type="button" id="gallery-lb-prev" data-gallery-lightbox-prev
            class="absolute left-3 top-1/2 -translate-y-1/2 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition"
            aria-label="Previous image">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
    </button>
    <button type="button" id="gallery-lb-next" data-gallery-lightbox-next
            class="absolute right-3 top-1/2 -translate-y-1/2 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition"
            aria-label="Next image">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </button>
    <div class="relative flex flex-col items-center justify-center w-full h-full px-16 py-14 pointer-events-none">
        <img id="gallery-lb-img" src="" alt=""
             class="pointer-events-auto max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl transition-opacity duration-200" />
        <div class="mt-4 text-center pointer-events-auto">
            <p id="gallery-lb-cat" class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#fddc6a]"></p>
            <p id="gallery-lb-title" class="mt-1 text-base font-extrabold text-white"></p>
            <p id="gallery-lb-counter" class="mt-1 text-xs text-white/50"></p>
        </div>
    </div>
</div>
