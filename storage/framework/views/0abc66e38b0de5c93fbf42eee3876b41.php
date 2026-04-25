<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery — GNAT Association</title>
    <?php echo $__env->make('home.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
    <?php echo $__env->make('home.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section id="gallery" class="relative overflow-hidden bg-gradient-to-b from-[#faf8f5] via-white to-[#f3efe8] py-12 lg:py-16">
        <div class="pointer-events-none absolute -right-40 top-24 h-[28rem] w-[28rem] rounded-full bg-[#965995]/12 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-32 bottom-16 h-80 w-80 rounded-full bg-[#fddc6a]/25 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4">
            <div class="rounded-3xl border border-[#351c42]/10 bg-white/85 backdrop-blur p-5 md:p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]"><?php echo e($section->section_badge ?? 'Impact in pictures'); ?></p>
                        <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]"><?php echo e($section->section_title ?? 'Our gallery'); ?></h1>
                        <p class="mt-1 text-sm text-[#351c42]/65"><?php echo e($section->section_description ?? 'Field moments from Aminjikarai and across our programs—outreach, learning spaces, and celebrations with the communities we serve.'); ?></p>
                    </div>
                </div>

                <form method="GET" action="<?php echo e(route('gallery.index')); ?>" class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto] lg:items-center">
                    <input type="hidden" name="category" value="<?php echo e($category); ?>">
                    <div class="relative">
                        <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search gallery title or description..."
                               class="w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25">
                    </div>
                    <button type="submit" class="rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                        Search
                    </button>

                    <div class="lg:col-span-2 flex flex-wrap items-center gap-2 pt-1">
                        <?php
                            $tabs = [
                                'all' => 'All',
                                'programs' => 'Programs',
                                'events' => 'Events',
                                'community' => 'Community',
                            ];
                        ?>
                        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isOn = $category === $key;
                                $href = route('gallery.index', array_filter(['q' => $q, 'category' => $key]));
                            ?>
                            <a href="<?php echo e($href); ?>"
                               data-gallery-filter="<?php echo e($key); ?>"
                               aria-pressed="<?php echo e($isOn ? 'true' : 'false'); ?>">
                                <?php echo e($label); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </form>
            </div>

            <?php if($items->isEmpty()): ?>
                <section class="mt-8 rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
                    <p class="text-sm font-bold text-[#351c42]/80">No gallery items found.</p>
                </section>
            <?php else: ?>
                <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 lg:gap-5 lg:auto-rows-[minmax(11rem,1fr)]">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $layout = $item->layout_type;
                        ?>
                        <?php if($layout === 'hero'): ?>
                            <article class="group relative col-span-2 row-span-2 min-h-[260px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#351c42]/5 shadow-lg ring-1 ring-black/5 sm:min-h-[320px] lg:min-h-0">
                                <img src="<?php echo e(asset('storage/' . ltrim((string) $item->image_path, '/'))); ?>" alt="<?php echo e($item->alt_text ?: $item->title); ?>" class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-out group-hover:scale-105" width="800" height="600" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#351c42] via-[#351c42]/35 to-transparent opacity-95 transition duration-500 group-hover:via-[#351c42]/45"></div>
                                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a] sm:text-xs"><?php echo e($item->eyebrow ?: ucfirst($item->category_key)); ?></p>
                                    <h3 class="mt-1 text-xl font-extrabold text-white sm:text-2xl"><?php echo e($item->title); ?></h3>
                                    <p class="mt-2 max-w-md text-sm text-white/80"><?php echo e($item->description_text ?: ''); ?></p>
                                </div>
                            </article>
                        <?php elseif($layout === 'wide'): ?>
                            <article class="group relative col-span-2 min-h-[140px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-md ring-1 ring-black/5 sm:min-h-[156px] lg:col-span-2 lg:min-h-0">
                                <img src="<?php echo e(asset('storage/' . ltrim((string) $item->image_path, '/'))); ?>" alt="<?php echo e($item->alt_text ?: $item->title); ?>" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" width="800" height="500" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-r from-[#351c42]/85 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 top-0 flex w-[70%] flex-col justify-end p-4 sm:p-5">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]"><?php echo e($item->eyebrow ?: ucfirst($item->category_key)); ?></p>
                                    <h3 class="mt-0.5 text-lg font-extrabold text-white"><?php echo e($item->title); ?></h3>
                                </div>
                            </article>
                        <?php elseif($layout === 'banner'): ?>
                            <article class="group relative col-span-2 min-h-[160px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#351c42] shadow-md ring-1 ring-black/5 sm:min-h-[180px] lg:col-span-2 lg:min-h-0">
                                <img src="<?php echo e(asset('storage/' . ltrim((string) $item->image_path, '/'))); ?>" alt="<?php echo e($item->alt_text ?: $item->title); ?>" class="absolute inset-0 h-full w-full object-cover opacity-60 mix-blend-overlay transition duration-700 group-hover:scale-105 group-hover:opacity-70" width="900" height="500" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-br from-[#965995]/40 to-[#351c42]"></div>
                                <div class="relative flex h-full flex-col justify-center p-5 sm:p-6">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]"><?php echo e($item->eyebrow ?: ucfirst($item->category_key)); ?></p>
                                    <h3 class="mt-1 text-xl font-extrabold text-white sm:text-2xl"><?php echo e($item->title); ?></h3>
                                    <p class="mt-2 max-w-lg text-sm text-white/85"><?php echo e($item->description_text ?: ''); ?></p>
                                </div>
                            </article>
                        <?php else: ?>
                            <article class="group relative min-h-[140px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-md ring-1 ring-black/5 sm:min-h-[156px] lg:min-h-0">
                                <img src="<?php echo e(asset('storage/' . ltrim((string) $item->image_path, '/'))); ?>" alt="<?php echo e($item->alt_text ?: $item->title); ?>" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" width="600" height="600" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#351c42]/90 to-transparent opacity-90"></div>
                                <div class="absolute inset-x-0 bottom-0 p-4">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]"><?php echo e($item->eyebrow ?: ucfirst($item->category_key)); ?></p>
                                    <h3 class="text-base font-extrabold text-white"><?php echo e($item->title); ?></h3>
                                </div>
                            </article>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <section class="mt-6 rounded-2xl border border-[#351c42]/10 bg-white p-4">
                    <?php echo e($items->links()); ?>

                </section>
            <?php endif; ?>
        </div>
    </section>

    <?php echo $__env->make('home.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.floating', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.donate-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.donate-payment-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\home\gallery.blade.php ENDPATH**/ ?>