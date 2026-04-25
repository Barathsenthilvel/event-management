<section id="donate" class="relative z-30">
    <div class="bg-[#f6f6f4] py-10 lg:py-14 border-b border-[#351c42]/10">
        <div class="mx-auto max-w-7xl px-4">
            <div class="flex flex-col gap-3 min-[520px]:flex-row min-[520px]:items-start min-[520px]:justify-between min-[520px]:gap-6 mb-6 lg:mb-8">
                <div class="min-w-0 max-w-xl">
                    <p class="text-sm font-semibold tracking-wide text-[#351c42]/65 uppercase"><?php echo e($donate['intro_kicker']); ?></p>
                    <h2 class="mt-1 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]"><?php echo e($donate['intro_title']); ?></h2>
                    <p class="mt-2 text-sm text-[#351c42]/60"><?php echo e($donate['intro_text']); ?></p>
                </div>
                <div class="flex shrink-0 flex-col gap-3 min-[520px]:items-end">
                    <a href="<?php echo e(route('donations.index')); ?>" class="self-start text-sm font-semibold text-[#965995] underline-offset-4 hover:text-[#351c42] hover:underline transition-colors min-[520px]:self-end min-[520px]:pt-8 sm:pt-10">
                        View more
                    </a>
                    <div class="flex gap-2">
                        <button type="button" data-donate-prev class="carousel-nav-btn" aria-label="Previous campaigns">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button type="button" data-donate-next class="carousel-nav-btn" aria-label="Next campaigns">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden -mx-0" data-donate-viewport>
                <div class="flex gap-4 will-change-transform transition-transform duration-500 ease-out" data-donate-track>
                    <?php if(isset($homeDonations) && $homeDonations->isNotEmpty()): ?>
                        <?php $__currentLoopData = $homeDonations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $coverSrc = $donation->cover_image_path
                                    ? asset('storage/' . $donation->cover_image_path)
                                    : asset('images/events/event-1-2.jpg');
                                $excerpt = $donation->short_description
                                    ?: \Illuminate\Support\Str::limit(strip_tags((string) $donation->description), 220);
                                $readMoreText = trim(strip_tags((string) ($donation->description ?: $donation->short_description)));
                                $showReadMore = $readMoreText !== '';
                                [$pillA, $pillB] = $donation->pillTagLabels();
                                $readMoreMeta = array_values(array_filter([
                                    ['label' => 'Type', 'value' => 'Donation'],
                                    ['label' => 'Category 1', 'value' => $pillA],
                                    ['label' => 'Category 2', 'value' => $pillB],
                                ], fn ($item) => !empty($item['value'])));
                            ?>
                            <article class="donation-slide shrink-0 rounded-3xl overflow-hidden border border-[#351c42]/10 bg-white shadow-md flex flex-col sm:flex-row min-h-[280px] sm:min-h-[240px]">
                                <div class="relative sm:w-[42%] min-h-[200px] sm:min-h-full overflow-hidden">
                                    <img src="<?php echo e($coverSrc); ?>" alt="<?php echo e($donation->purpose); ?>" class="absolute inset-0 h-full w-full object-cover" width="400" height="300" />
                                </div>
                                <div class="flex flex-1 flex-col justify-center p-5 sm:p-6 bg-[linear-gradient(180deg,#faf8f5_0%,#f3f0ea_100%)]">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full border border-[#351c42]/20 bg-white px-3 py-1 text-xs font-semibold text-[#351c42]"><?php echo e($pillA); ?></span>
                                        <span class="rounded-full border border-[#351c42]/20 bg-white px-3 py-1 text-xs font-semibold text-[#351c42]"><?php echo e($pillB); ?></span>
                                    </div>
                                    <h4 class="mt-4 text-lg sm:text-xl font-extrabold text-[#351c42] leading-snug"><?php echo e($donation->purpose); ?></h4>
                                    <p class="mt-2 text-sm text-[#351c42]/65 line-clamp-2"><?php echo e($excerpt); ?></p>
                                    <?php if($showReadMore): ?>
                                        <button
                                            type="button"
                                            data-read-more
                                            data-read-more-title="<?php echo e(e($donation->purpose)); ?>"
                                            data-read-more-content="<?php echo e(e($readMoreText)); ?>"
                                            data-read-more-meta='<?php echo json_encode($readMoreMeta, 15, 512) ?>'
                                            class="mt-2 inline-flex items-center gap-1 text-xs font-extrabold text-[#965995] hover:text-[#351c42]"
                                        >
                                            Read more
                                            <span aria-hidden="true">→</span>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" data-open-donate-modal data-donation-id="<?php echo e($donation->id); ?>" class="click-btn click-btn--sm btn-style506 mt-4 self-start text-left" aria-label="Donate now">
                                        <span class="click-btn__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                                                <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <span class="click-btn__label">Donate Now</span>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php $__currentLoopData = $donate['campaigns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $fallbackPills = $c['pill_tags'] ?? ['Donation', 'Charity'];
                                $fallbackReadMoreText = trim((string) ($c['description'] ?? $c['excerpt'] ?? ''));
                                $fallbackShowReadMore = $fallbackReadMoreText !== '';
                                $fallbackReadMoreMeta = array_values(array_filter([
                                    ['label' => 'Type', 'value' => 'Donation'],
                                    ['label' => 'Category 1', 'value' => $fallbackPills[0] ?? null],
                                    ['label' => 'Category 2', 'value' => $fallbackPills[1] ?? null],
                                ], fn ($item) => !empty($item['value'])));
                            ?>
                            <article class="donation-slide shrink-0 rounded-3xl overflow-hidden border border-[#351c42]/10 bg-white shadow-md flex flex-col sm:flex-row min-h-[280px] sm:min-h-[240px]">
                                <div class="relative sm:w-[42%] min-h-[200px] sm:min-h-full overflow-hidden">
                                    <img src="<?php echo e(asset($c['image'])); ?>" alt="<?php echo e($c['alt']); ?>" class="absolute inset-0 h-full w-full object-cover" width="400" height="300" />
                                </div>
                                <div class="flex flex-1 flex-col justify-center p-5 sm:p-6 bg-[linear-gradient(180deg,#faf8f5_0%,#f3f0ea_100%)]">
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__currentLoopData = $fallbackPills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="rounded-full border border-[#351c42]/20 bg-white px-3 py-1 text-xs font-semibold text-[#351c42]"><?php echo e($pill); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <h4 class="mt-4 text-lg sm:text-xl font-extrabold text-[#351c42] leading-snug"><?php echo e($c['title']); ?></h4>
                                    <p class="mt-2 text-sm text-[#351c42]/65 line-clamp-2"><?php echo e($c['excerpt']); ?></p>
                                    <?php if($fallbackShowReadMore): ?>
                                        <button
                                            type="button"
                                            data-read-more
                                            data-read-more-title="<?php echo e(e($c['title'])); ?>"
                                            data-read-more-content="<?php echo e(e($fallbackReadMoreText)); ?>"
                                            data-read-more-meta='<?php echo json_encode($fallbackReadMoreMeta, 15, 512) ?>'
                                            class="mt-2 inline-flex items-center gap-1 text-xs font-extrabold text-[#965995] hover:text-[#351c42]"
                                        >
                                            Read more
                                            <span aria-hidden="true">→</span>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" data-open-donate-modal class="click-btn click-btn--sm btn-style506 mt-4 self-start text-left" aria-label="Donate now">
                                        <span class="click-btn__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                                                <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <span class="click-btn__label">Donate Now</span>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#351c42] text-white border-b border-white/10">
        <div class="mx-auto max-w-7xl px-4 py-6 lg:py-8">
            <div class="flex flex-col lg:flex-row lg:items-stretch gap-8 lg:gap-0">
                <div class="lg:w-[38%] lg:pr-8 lg:border-r lg:border-white/15 relative">
                    <svg class="pointer-events-none absolute right-2 bottom-0 h-28 w-28 text-white/[0.06] lg:right-4" viewBox="0 0 120 120" fill="currentColor" aria-hidden="true">
                        <path d="M60 20c-8 0-14 6-14 14 0 5 2 9 6 12-18 4-30 18-30 36v28h76V82c0-18-12-32-30-36 4-3 6-7 6-12 0-8-6-14-14-14zm-6 52h12v8H54v-8z"/>
                    </svg>
                    <h2 class="relative text-2xl md:text-3xl lg:text-[1.65rem] xl:text-3xl font-extrabold leading-tight text-[#fddc6a]">
                        Give Through GNAT Association<br class="hidden sm:inline" /> &amp; Change a Life!
                    </h2>
                    <p class="relative mt-3 flex items-center gap-2 text-sm text-white/85">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-500/20 text-red-300" aria-hidden="true">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </span>
                        Every contribution makes a difference.
                    </p>
                </div>
                <div id="home-donate-amounts" class="lg:flex-1 lg:pl-10 flex flex-col justify-center gap-5 min-w-0">
                    <div class="flex flex-col gap-3">
                        <span class="text-sm font-semibold text-white/90">Choose amount:</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <?php $__currentLoopData = $donate['amounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button
                                    type="button"
                                    data-donate-amt="<?php echo e($amt); ?>"
                                    class="donate-amt-btn rounded-full bg-white/10 hover:bg-white/20 px-4 py-2 text-sm font-semibold border border-white/15 transition-colors <?php echo e((int) $amt === (int) $donate['default_amount'] ? 'is-selected' : ''); ?>"
                                >₹<?php echo e($amt); ?></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" data-donate-custom class="rounded-full border-2 border-[#fddc6a] text-[#fddc6a] px-4 py-2 text-sm font-semibold inline-flex items-center gap-2 hover:bg-[#fddc6a]/10 transition-colors">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M9 10h6M16 14h-5"/></svg>
                                Custom Amount
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="h-2.5 rounded-full bg-black/25 overflow-hidden">
                            <div class="donate-progress-bar h-full rounded-full bg-[#fddc6a] transition-all duration-500" style="width: <?php echo e($donate['bar_percent_demo']); ?>%;" data-donate-bar></div>
                        </div>
                        <p class="mt-1.5 text-xs text-white/60">GNAT Association community goal (demo)</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 sm:items-stretch">
                        <label class="relative flex-1 flex items-center rounded-2xl bg-white pl-12 pr-4 py-3.5 shadow-inner">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#351c42]/10 text-[#351c42] font-bold text-base leading-none" aria-hidden="true">₹</span>
                            <input type="number" min="1" step="1" value="<?php echo e($donate['default_amount']); ?>" data-donate-input class="w-full min-w-0 border-0 bg-transparent text-[#351c42] text-lg font-bold outline-none focus:ring-0" />
                        </label>
                        <button type="button" data-donate-submit class="click-btn btn-style506 shrink-0">
                            <span class="click-btn__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                                    <path d="M8 8l3 4-3 4M13 8l3 4-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="click-btn__label">Donate Now</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/home/sections/donate.blade.php ENDPATH**/ ?>