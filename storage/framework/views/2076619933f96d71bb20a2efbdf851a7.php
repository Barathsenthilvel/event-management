<?php $__env->startSection('title', 'E-Books — Members'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-7">
    <section class="rounded-3xl border border-[#351c42]/10 bg-white/85 backdrop-blur p-5 md:p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">E-Books</p>
                <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">Member library</h1>
                <p class="mt-1 text-sm text-[#351c42]/65">Browse materials available to approved members.</p>
            </div>
        </div>

        <form method="get" action="<?php echo e(route('member.ebooks.index')); ?>" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search title, code, or description…"
                   class="min-w-0 flex-1 rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25">
            <button type="submit" class="rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                Search
            </button>
        </form>
    </section>

    <?php if($ebooks->isEmpty()): ?>
        <section class="rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
            <p class="text-sm font-bold text-[#351c42]/80">No e-books available right now.</p>
        </section>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <?php $__currentLoopData = $ebooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $coverSrc = $book->cover_image_path
                        ? asset('storage/' . $book->cover_image_path)
                        : 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300"><rect fill="#e8e3dc" width="100%" height="100%"/></svg>');
                    $excerpt = $book->short_description
                        ?: \Illuminate\Support\Str::limit(strip_tags((string) $book->description), 220);
                    $readMoreText = trim(strip_tags((string) ($book->description ?: $book->short_description)));
                    $showReadMore = $readMoreText !== '';
                    $isPaid = ($book->pricing_type ?? 'free') === 'paid';
                    $materialUrl = $book->material_path ? asset('storage/' . $book->material_path) : null;
                ?>
                <article class="min-w-0 h-full w-full rounded-3xl overflow-hidden border border-[#351c42]/10 bg-white shadow-md flex flex-col sm:flex-row min-h-[280px] sm:min-h-[240px]">
                    <div class="relative sm:w-[42%] min-h-[200px] sm:min-h-full overflow-hidden">
                        <img src="<?php echo e($coverSrc); ?>" alt="<?php echo e($book->title); ?>" class="absolute inset-0 h-full w-full object-cover" width="400" height="300">
                    </div>
                    <div class="flex flex-1 flex-col justify-center p-5 sm:p-6 bg-[linear-gradient(180deg,#faf8f5_0%,#f3f0ea_100%)]">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full border border-[#351c42]/20 bg-white px-3 py-1 text-xs font-semibold text-[#351c42]">E-Book</span>
                            <?php if($isPaid): ?>
                                <span class="rounded-full border border-[#351c42]/20 bg-white px-3 py-1 text-xs font-semibold text-[#351c42]">
                                    Paid <?php if($book->price): ?> · ₹<?php echo e(number_format((float) $book->price, 2)); ?> <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Free</span>
                            <?php endif; ?>
                        </div>
                        <h2 class="mt-4 text-lg sm:text-xl font-extrabold text-[#351c42] leading-snug"><?php echo e($book->title); ?></h2>
                        <?php if($book->code): ?>
                            <p class="mt-1 text-[11px] font-bold uppercase tracking-wide text-[#965995]/90"><?php echo e($book->code); ?></p>
                        <?php endif; ?>
                        <?php if($excerpt !== ''): ?>
                            <p class="mt-2 text-sm text-[#351c42]/65 line-clamp-2"><?php echo e($excerpt); ?></p>
                        <?php endif; ?>
                        <?php if($showReadMore): ?>
                            <?php
                                $readMoreMeta = array_values(array_filter([
                                    ['label' => 'Type', 'value' => 'E-Book'],
                                    ['label' => 'Code', 'value' => $book->code],
                                    ['label' => 'Pricing', 'value' => $isPaid ? ('Paid' . ($book->price ? ' (₹' . number_format((float) $book->price, 2) . ')' : '')) : null],
                                ], fn ($item) => !empty($item['value'])));
                            ?>
                            <button
                                type="button"
                                data-read-more
                                data-read-more-title="<?php echo e(e($book->title)); ?>"
                                data-read-more-content="<?php echo e(e($readMoreText)); ?>"
                                data-read-more-meta='<?php echo json_encode($readMoreMeta, 15, 512) ?>'
                                class="mt-2 inline-flex items-center gap-1 text-xs font-extrabold text-[#965995] hover:text-[#351c42]"
                            >
                                Read more
                                <span aria-hidden="true">→</span>
                            </button>
                        <?php endif; ?>
                        <div class="mt-4 self-start">
                            <?php if($materialUrl): ?>
                                <a href="<?php echo e($materialUrl); ?>" download
                                   class="inline-flex items-center gap-2 rounded-2xl bg-[#351c42] px-5 py-2.5 text-sm font-extrabold text-[#fddc6a] shadow-md shadow-[#351c42]/20 transition hover:bg-[#4d2a5c]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                                    </svg>
                                    Download
                                </a>
                            <?php else: ?>
                                <span class="text-xs font-semibold text-[#351c42]/45">File coming soon</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <section class="rounded-2xl border border-[#351c42]/10 bg-white p-4">
            <?php echo e($ebooks->links()); ?>

        </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('member.layouts.gnat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/member/ebooks/index.blade.php ENDPATH**/ ?>