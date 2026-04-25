<section id="about2" class="relative bg-[#351c42] overflow-hidden py-16 lg:py-24">
    <div class="pointer-events-none absolute -left-6 -top-8 h-28 w-28 rounded-b-[38px] border border-[#fddc6a]/35"></div>
    <div class="pointer-events-none absolute -right-10 -bottom-12 h-40 w-40 rounded-tl-[56px] border border-[#fddc6a]/35"></div>

    <div class="mx-auto max-w-7xl px-4">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-14 items-center">
            <div class="relative">
                <div class="relative max-w-[430px]">
                    <div
                        class="about2-img-main overflow-hidden border-4 border-white shadow-xl bg-white"
                        style="clip-path: ellipse(47% 50% at 50% 50%);"
                    >
                        <img
                            src="<?php echo e(asset($about['main_image'])); ?>"
                            alt="GNAT Association community support"
                            class="h-[320px] w-full object-cover"
                        />
                    </div>

                    <div class="about2-img-accent absolute -right-8 -top-2 w-44 overflow-hidden rounded-full border-4 border-white bg-white shadow-lg">
                        <img
                            src="<?php echo e(asset($about['accent_image'])); ?>"
                            alt="GNAT Association team"
                            class="h-32 w-full object-cover"
                        />
                    </div>
                </div>
            </div>

            <div>
                <div class="inline-flex items-center gap-2 text-xs font-semibold tracking-wide text-white/90">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#fddc6a]"></span>
                    <?php echo e($about['eyebrow']); ?>

                </div>

                <h2 class="mt-4 text-3xl md:text-4xl font-extrabold leading-tight text-white">
                    <?php echo e($about['title_lines'][0]); ?><br />
                    <?php echo e($about['title_lines'][1]); ?> <span class="relative inline-block"><?php echo e($about['title_highlight']); ?>

                        <span class="absolute left-0 right-0 -bottom-2 h-2 bg-[#fddc6a] rounded-full"></span>
                    </span><br />
                    <?php echo e($about['title_lines'][2]); ?>

                </h2>

                <p class="mt-6 max-w-xl text-sm md:text-base leading-7 text-white/80">
                    <?php echo e($about['text']); ?>

                </p>

                <div class="mt-8 flex flex-wrap items-center gap-5">
                    <a href="<?php echo e(url('/')); ?>#about2" class="click-btn btn-style506">
                        <span class="click-btn__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" aria-hidden="true">
                                <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="click-btn__label">More About Us</span>
                    </a>

                    <div class="inline-flex items-center gap-3 text-white">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#fddc6a]/70 text-[#fddc6a]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 16.92V20a2 2 0 0 1-2.18 2a19.8 19.8 0 0 1-8.63-3.07a19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18A2 2 0 0 1 4.11 2h3.09a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L8 9.83a16 16 0 0 0 6.17 6.17l1.38-1.37a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="text-sm">
                            <span class="block text-white/70">Call Any Time</span>
                            <span class="block font-bold text-white text-base leading-snug">
                                <?php $__currentLoopData = $contact['phones']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($idx > 0): ?>
                                        <span class="text-white/50 font-normal"> / </span>
                                    <?php endif; ?>
                                    <a href="tel:<?php echo e($phone['tel']); ?>" class="hover:text-[#fddc6a] transition-colors"><?php echo e($phone['label']); ?></a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/home/sections/about.blade.php ENDPATH**/ ?>