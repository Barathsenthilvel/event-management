
<section class="relative bg-gradient-to-b from-[#eae7f3] via-[#f0edf7] to-[#f8f6fc] py-10 lg:py-14" aria-label="Featured banners">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="relative">
            <div
                id="bannerCarouselViewport"
                class="banner-carousel-viewport relative w-full min-w-0 overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#1a0f24] shadow-[0_24px_60px_-12px_rgba(53,28,66,0.35)] ring-1 ring-black/5"
            >
                
                <div
                    id="bannerCarouselTrack"
                    class="flex gap-0 will-change-transform"
                    style="transition: transform 520ms cubic-bezier(0.22, 1, 0.36, 1);"
                >
                    <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $rawHref = (string) ($banner['href'] ?? '#');
                            $slideHref = \Illuminate\Support\Str::startsWith($rawHref, ['#', 'http://', 'https://', 'mailto:', 'tel:'])
                                ? $rawHref
                                : url('/').'/'.ltrim($rawHref, '/');
                        ?>
                        <a
                            href="<?php echo e($slideHref); ?>"
                            class="banner-slide group relative block h-[160px] shrink-0 overflow-hidden sm:h-[200px] md:h-[240px] lg:h-[280px] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#1a0f24]"
                        >
                            <img
                                src="<?php echo e(asset($banner['src'])); ?>"
                                alt="<?php echo e($banner['alt']); ?>"
                                class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.02]"
                                width="1200"
                                height="600"
                                sizes="100vw"
                                loading="lazy"
                                decoding="async"
                                data-banner-photo
                            />
                            <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#1a0f24]/80 via-transparent to-[#351c42]/15 opacity-90" aria-hidden="true"></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
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

                
                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 z-[2] flex flex-col items-center gap-3 bg-gradient-to-t from-black/55 via-black/20 to-transparent px-4 pb-5 pt-20 sm:flex-row sm:justify-between sm:px-6"
                >
                    <p
                        id="bannerSlideLabel"
                        class="pointer-events-none text-[11px] font-bold uppercase tracking-[0.2em] text-white/90 tabular-nums drop-shadow-sm"
                    >
                        <span class="text-[#fddc6a]">01</span>
                        <span class="text-white/50"> / </span>
                        <span class="text-white/75"><?php echo e(str_pad((string) count($banners), 2, '0', STR_PAD_LEFT)); ?></span>
                    </p>
                    <div id="bannerCarouselDots" class="pointer-events-auto flex flex-wrap justify-center gap-2"></div>
                </div>
            </div>

            <p class="mt-4 text-center text-[11px] font-medium text-[#351c42]/40 sm:hidden" aria-hidden="true">Swipe or use dots to explore</p>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\home\sections\banner-carousel.blade.php ENDPATH**/ ?>