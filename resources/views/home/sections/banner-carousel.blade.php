{{-- Single-slide hero carousel: one banner visible; modern overlay controls + dock --}}
<section class="relative bg-gradient-to-b from-[#eae7f3] via-[#f0edf7] to-[#f8f6fc] py-10 lg:py-14" aria-label="Featured banners">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="relative">
            <div
                id="bannerCarouselViewport"
                class="banner-carousel-viewport relative w-full min-w-0 overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#1a0f24] shadow-[0_24px_60px_-12px_rgba(53,28,66,0.35)] ring-1 ring-black/5"
            >
                {{-- Track: one slide width = 100% of viewport (JS); gap-0 --}}
                <div
                    id="bannerCarouselTrack"
                    class="flex gap-0 will-change-transform"
                    style="transition: transform 520ms cubic-bezier(0.22, 1, 0.36, 1);"
                >
                    @foreach ($banners as $banner)
                        @php
                            $rawHref = (string) ($banner['href'] ?? '#');
                            $slideHref = \Illuminate\Support\Str::startsWith($rawHref, ['#', 'http://', 'https://', 'mailto:', 'tel:'])
                                ? $rawHref
                                : url('/').'/'.ltrim($rawHref, '/');
                        @endphp
                        <a
                            href="{{ $slideHref }}"
                            class="banner-slide group relative block h-[160px] shrink-0 overflow-hidden sm:h-[200px] md:h-[240px] lg:h-[280px] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#1a0f24]"
                        >
                            <img
                                src="{{ asset($banner['src']) }}"
                                alt="{{ $banner['alt'] }}"
                                class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.02]"
                                width="1200"
                                height="600"
                                sizes="100vw"
                                loading="lazy"
                                decoding="async"
                                data-banner-photo
                            />
                            <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#1a0f24]/80 via-transparent to-[#351c42]/15 opacity-90" aria-hidden="true"></span>

                            @if(!empty($banner['eyebrow']) || !empty($banner['title']) || !empty($banner['text']))
                                <div class="pointer-events-none absolute inset-x-0 bottom-0 z-[1] px-4 pb-16 pt-8 sm:px-6 sm:pb-20">
                                    <div class="max-w-2xl">
                                        @if(!empty($banner['eyebrow']))
                                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#fddc6a] sm:text-xs">{{ $banner['eyebrow'] }}</p>
                                        @endif
                                        @if(!empty($banner['title']))
                                            <h3 class="mt-1 text-base font-extrabold tracking-tight text-white drop-shadow-md sm:text-xl md:text-2xl">
                                                {{ $banner['title'] }}
                                            </h3>
                                        @endif
                                        @if(!empty($banner['text']))
                                            <p class="mt-1 max-w-xl text-xs font-medium leading-relaxed text-white/90 drop-shadow sm:text-sm">
                                                {{ $banner['text'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Side arrows (modern glass pills) --}}
                <div
                    class="banner-carousel-chrome pointer-events-none absolute inset-0 z-[2] flex items-center justify-between px-2 sm:px-4"
                    aria-hidden="false"
                >
                    <button
                        type="button"
                        id="bannerCarouselPrev"
                        class="banner-carousel-arrow pointer-events-auto -ml-0.5 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white shadow-lg backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] sm:h-12 sm:w-12"
                        aria-label="Previous banner"
                        title="Previous"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        id="bannerCarouselNext"
                        class="banner-carousel-arrow pointer-events-auto -mr-0.5 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white shadow-lg backdrop-blur-md transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] sm:h-12 sm:w-12"
                        aria-label="Next banner"
                        title="Next"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                </div>

                {{-- Bottom dock: counter + dots --}}
                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 z-[2] flex flex-col items-center gap-3 bg-gradient-to-t from-black/55 via-black/20 to-transparent px-4 pb-5 pt-20 sm:flex-row sm:justify-between sm:px-6"
                >
                    <p
                        id="bannerSlideLabel"
                        class="pointer-events-none text-[11px] font-bold uppercase tracking-[0.2em] text-white/90 tabular-nums drop-shadow-sm"
                    >
                        <span class="text-[#fddc6a]">01</span>
                        <span class="text-white/50"> / </span>
                        <span class="text-white/75">{{ str_pad((string) count($banners), 2, '0', STR_PAD_LEFT) }}</span>
                    </p>
                    <div id="bannerCarouselDots" class="pointer-events-auto flex flex-wrap justify-center gap-2"></div>
                </div>
            </div>

            <p class="mt-4 text-center text-[11px] font-medium text-[#351c42]/40 sm:hidden" aria-hidden="true">Swipe or use dots to explore</p>
        </div>
    </div>
</section>
