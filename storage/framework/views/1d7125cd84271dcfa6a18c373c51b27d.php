<section id="testimonials" class="relative scroll-mt-32 bg-[#f6f3e9] overflow-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex flex-col lg:flex-row gap-10 items-start lg:items-center">
            <div class="w-full lg:w-[30%] lg:max-w-md lg:shrink-0">
                <div class="inline-flex items-center gap-2 text-sm font-semibold tracking-wide text-[#965995]">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#965995]"></span>
                    <?php echo e($testimonials_intro['eyebrow']); ?>

                </div>

                <h2 class="mt-4 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]">
                    <?php echo e($testimonials_intro['title']); ?>

                </h2>

                <p class="mt-4 text-[#351c42]/80 text-sm md:text-base leading-6">
                    <?php echo e($testimonials_intro['text']); ?>

                </p>

                <div class="mt-6 flex gap-3">
                    <button id="prevBtn" type="button" class="carousel-nav-btn" aria-label="Previous testimonial" title="Previous">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>
                    <button id="nextBtn" type="button" class="carousel-nav-btn" aria-label="Next testimonial" title="Next">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="relative w-full min-w-0 lg:flex-1 lg:w-auto overflow-hidden">
                <div class="absolute inset-0 -z-10">
                    <div class="absolute -right-10 top-10 h-60 w-72 rounded-3xl bg-[#351c42]/10 blur-2xl"></div>
                    <div class="absolute right-0 top-20 h-44 w-64 rounded-3xl bg-[#965995]/20 blur-2xl"></div>
                </div>

                <div id="testimonialStackScroll" class="hidden relative min-h-[520px] overflow-hidden">
                    <div class="sticky top-6">
                        <div id="testimonialStack" class="relative w-full">
                            <?php $__currentLoopData = $testimonial_stack_cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="stack-card rounded-3xl bg-[#351c42] shadow-sm overflow-hidden border border-white/10" style="--i:<?php echo e($i); ?>">
                                    <div class="absolute inset-0 opacity-90 bg-gradient-to-br from-[#351c42] via-[#351c42] to-[#2a1237]"></div>
                                    <div class="relative p-8">
                                        <div class="relative mx-auto w-full max-w-sm">
                                            <div class="absolute -left-12 -top-10 rounded-2xl bg-white/20 backdrop-blur-sm border border-white/10 p-2">
                                                <div class="relative h-44 w-36 overflow-hidden rounded-xl">
                                                    <img src="<?php echo e(asset($t['image'])); ?>" alt="Client" class="h-full w-full object-cover" />
                                                    <?php if(!empty($t['play'])): ?>
                                                        <button type="button" aria-label="Play testimonial" class="absolute inset-0 flex items-center justify-center">
                                                            <span class="h-12 w-12 rounded-full bg-black/35 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <path d="M9 7l10 5-10 5V7z" fill="white"/>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center gap-1">
                                                        <?php for($s = 0; $s < 5; $s++): ?>
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="#fbbf24" aria-hidden="true">
                                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                                            </svg>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="text-white text-sm font-medium">Rating <?php echo e($t['rating']); ?></span>
                                                </div>

                                                <p class="mt-5 text-white/90 text-sm leading-6"><?php echo e($t['quote']); ?></p>

                                                <div class="mt-5">
                                                    <div class="text-white font-semibold text-sm"><?php echo e($t['name']); ?></div>
                                                    <div class="text-white/70 text-xs mt-1"><?php echo e($t['role']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div id="carousel-viewport" class="overflow-hidden rounded-2xl bg-transparent px-0 sm:px-1" aria-label="GNAT Association testimonials">
                        <div id="carousel-track" class="flex items-stretch gap-5 sm:gap-6 transition-transform duration-500 ease-in-out will-change-transform"></div>
                    </div>
                    <div class="mt-4 flex justify-center gap-2" id="dots"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/home/sections/testimonials.blade.php ENDPATH**/ ?>