<section class="relative bg-gradient-to-b from-[#eae7f3] to-[#f5f3f9] py-10 lg:py-14" aria-label="Featured banners">
    <div class="mx-auto max-w-8xl px-4 sm:px-6">
        <div class="relative w-full min-w-0">
            <div
                id="bannerCarouselViewport"
                class="min-w-0 w-full overflow-hidden rounded-[1.25rem] border border-[#351c42]/10 bg-transparent shadow-none sm:rounded-[1.5rem]"
            >
                <div
                    id="bannerCarouselTrack"
                    class="flex items-stretch gap-3 sm:gap-4 transition-transform duration-500 ease-out will-change-transform"
                >
                    @foreach ($banners as $banner)
                        <div
                            class="banner-slide group relative aspect-[16/7] shrink-0 overflow-hidden rounded-[1.75rem] border border-[#351c42]/15 shadow-[0_20px_50px_-12px_rgba(53,28,66,0.45)] ring-1 ring-black/5 focus-within:ring-2 focus-within:ring-[#965995] sm:aspect-[21/6] sm:rounded-[2rem] lg:aspect-[24/7] lg:rounded-[2.25rem]"
                            data-banner-href="{{ url('/') }}{{ $banner['href'] }}"
                        >
                            {{-- Subtle photo underlay + source for lightbox (reference: low-opacity image on plum) --}}
                            <img
                                src="{{ asset($banner['src']) }}"
                                alt="{{ $banner['alt'] }}"
                                data-banner-photo
                                class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-[0.28] saturate-[0.85] sm:opacity-[0.32]"
                                width="1200"
                                height="400"
                                loading="lazy"
                                decoding="async"
                            />
                            <div class="absolute inset-0 bg-gradient-to-br from-[#351c42]/94 via-[#3d2249]/92 to-[#4a2f58]/93" aria-hidden="true"></div>

                            <a
                                href="{{ url('/') }}{{ $banner['href'] }}"
                                class="relative z-[1] flex min-h-0 min-w-0 flex-1 flex-col justify-center px-5 py-6 text-left sm:px-8 sm:py-7 md:px-12 md:py-8 lg:px-14 lg:py-9"
                            >
                                <span class="text-[10px] font-bold uppercase tracking-[0.32em] text-[#fddc6a] sm:text-[11px]">{{ $banner['eyebrow'] ?? 'EVENTS' }}</span>
                                <h3 class="mt-2 max-w-3xl text-xl font-extrabold leading-tight tracking-tight text-white sm:text-2xl md:text-3xl lg:text-[1.75rem] xl:text-4xl">
                                    {{ $banner['title'] ?? '' }}
                                </h3>
                                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/90 sm:text-[0.9375rem] md:text-base">
                                    {{ $banner['text'] ?? '' }}
                                </p>
                            </a>

                            <button
                                type="button"
                                class="absolute bottom-4 right-4 z-10 flex h-11 w-11 items-center justify-center rounded-full bg-white/95 text-[#351c42] shadow-lg ring-1 ring-[#351c42]/15 transition hover:bg-[#fddc6a] hover:ring-[#351c42]/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995] focus-visible:ring-offset-2"
                                data-banner-expand
                                aria-label="Enlarge image"
                            >
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <nav
                class="banner-vertical-nav absolute right-0 top-1/2 z-10 -translate-y-1/2"
                aria-label="Banner slider controls"
            >
                <button type="button" id="bannerCarouselPrev" class="banner-vertical-nav__btn" aria-label="Previous banner" title="Previous">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 19V5M5 12l7-7 7 7"/>
                    </svg>
                </button>
                <span id="bannerVertCurrent" class="banner-vertical-nav__num text-white">01</span>
                <div class="banner-vertical-nav__line" aria-hidden="true"></div>
                <span id="bannerVertNext" class="banner-vertical-nav__num text-white/75">02</span>
                <button type="button" id="bannerCarouselNext" class="banner-vertical-nav__btn" aria-label="Next banner" title="Next">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 5v14M19 12l-7 7-7-7"/>
                    </svg>
                </button>
            </nav>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
            <div id="bannerCarouselDots" class="flex justify-center gap-2"></div>
            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#351c42]/45 hidden sm:inline" aria-hidden="true">More slides →</span>
        </div>
    </div>

    {{-- Lightbox: + on slide opens; − closes and resumes autoplay (see scripts) --}}
    <div
        id="bannerImageLightbox"
        class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-8"
        role="dialog"
        aria-modal="true"
        aria-hidden="true"
        aria-label="Expanded banner"
    >
        <div class="absolute inset-0 bg-black/82 backdrop-blur-[2px]" data-banner-lightbox-backdrop tabindex="-1"></div>
        <div class="relative z-10 flex w-full max-w-6xl flex-col items-center gap-5">
            <div class="relative flex w-full justify-end">
                <button
                    type="button"
                    data-banner-lightbox-close
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-3xl font-light leading-none text-[#351c42] shadow-xl ring-1 ring-black/10 transition hover:bg-[#fddc6a]"
                    aria-label="Close"
                >
                    −
                </button>
            </div>
            <img id="bannerLightboxImg" src="" alt="" class="max-h-[min(85vh,900px)] w-auto max-w-full rounded-2xl object-contain shadow-2xl" width="1200" height="800" />
            <a
                id="bannerLightboxLink"
                href="#"
                class="inline-flex items-center gap-2 rounded-full bg-[#351c42] px-5 py-2.5 text-sm font-bold text-[#fddc6a] shadow-lg ring-1 ring-white/10 transition hover:bg-[#2a1533] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a]"
            >
                Go to section
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </div>
</section>
