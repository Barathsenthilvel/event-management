<section class="relative bg-gradient-to-b from-[#eae7f3] to-[#f5f3f9] py-10 lg:py-14" aria-label="Featured banners">
    <div class="mx-auto max-w-8xl px-4 sm:px-6">
        <div class="relative w-full min-w-0">
            <div
                id="bannerCarouselViewport"
                class="min-w-0 w-full overflow-hidden rounded-2xl border border-[#351c42]/10 bg-white shadow-lg ring-1 ring-black/5"
            >
                <div
                    id="bannerCarouselTrack"
                    class="flex items-stretch gap-3 sm:gap-3 transition-transform duration-500 ease-out will-change-transform"
                >
                    @foreach ($banners as $banner)
                        <a href="{{ url('/') }}{{ $banner['href'] }}" class="banner-slide block shrink-0 overflow-hidden rounded-2xl bg-[#eae7f3] shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995] focus-visible:ring-offset-2">
                            <img src="{{ asset($banner['src']) }}" alt="{{ $banner['alt'] }}" class="h-64 w-full object-cover sm:h-72 md:h-80 lg:h-96" width="800" height="600" loading="lazy" decoding="async" />
                        </a>
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
</section>
