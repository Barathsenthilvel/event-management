<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs — GNAT Association</title>
    <?php echo $__env->make('home.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
    <?php echo $__env->make('home.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="mx-auto max-w-7xl px-4 py-8 space-y-7">
        <section class="rounded-3xl border border-[#351c42]/10 bg-white/85 backdrop-blur p-5 md:p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">Blogs</p>
                    <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">Explore All Posts</h1>
                    <p class="mt-1 text-sm text-[#351c42]/65">Posts added in admin will be listed here.</p>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('blogs.index')); ?>" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search blog title, tag, or excerpt…"
                       class="min-w-0 flex-1 rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25">
                <button type="submit" class="rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                    Search
                </button>
            </form>
        </section>

        <?php if($posts->isEmpty()): ?>
            <section class="rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
                <p class="text-sm font-bold text-[#351c42]/80">No blog posts found.</p>
            </section>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="rounded-2xl bg-white border border-[#351c42]/10 overflow-hidden shadow-sm">
                        <div class="relative h-56">
                            <img src="<?php echo e(asset('storage/' . ltrim((string) $post->image_path, '/'))); ?>" alt="Blog post image" class="h-full w-full object-cover"/>
                            <span class="absolute left-4 top-4 rounded-full bg-white/75 backdrop-blur px-3 py-1 text-xs font-semibold text-[#351c42]"><?php echo e($post->tag ?: 'Blog'); ?></span>
                            <?php if($post->published_at): ?>
                                <div class="absolute right-0 bottom-0 rounded-tl-2xl bg-[#351c42] text-[#fddc6a] text-center px-4 py-2">
                                    <div class="text-4xl font-extrabold leading-none"><?php echo e($post->published_at->format('d')); ?></div>
                                    <div class="text-lg font-semibold leading-none mt-1 text-white"><?php echo e($post->published_at->format('M')); ?></div>
                                    <div class="mt-1 text-[10px] tracking-[0.35em] font-bold bg-[#fddc6a] text-[#351c42] rounded px-2 py-0.5"><?php echo e($post->published_at->format('Y')); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-5">
                            <h2 class="text-2xl font-extrabold text-[#351c42]"><?php echo e($post->title); ?></h2>
                            <p class="mt-2 text-[#351c42]/65 leading-6"><?php echo e($post->excerpt); ?></p>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="<?php echo e($post->read_more_url ?: '#'); ?>" class="click-btn click-btn--sm btn-style506">
                                    <span class="click-btn__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                                            <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="click-btn__label">Read More</span>
                                </a>
                                <span class="text-sm font-semibold text-[#351c42]/70 inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 8L20 10L18 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 16L4 14L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 14H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo e($post->comments_count); ?>

                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <section class="mt-6 rounded-2xl border border-[#351c42]/10 bg-white p-4">
                <?php echo e($posts->links()); ?>

            </section>
        <?php endif; ?>
    </main>

    <?php echo $__env->make('home.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.floating', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.donate-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.donate-payment-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\home\blogs.blade.php ENDPATH**/ ?>