<?php $__env->startSection('content'); ?>
<?php
    $n = $nomination;
    $coverUrl = $n->cover_image_path ? asset('storage/' . ltrim($n->cover_image_path, '/')) : null;
    $bannerUrl = $n->banner_image_path ? asset('storage/' . ltrim($n->banner_image_path, '/')) : null;
?>
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="mx-auto max-w-6xl space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Nomination · View only</p>
                <h1 class="text-xl font-extrabold text-slate-900"><?php echo e($n->title); ?></h1>
                <p class="mt-1 text-xs font-bold text-slate-500">Read-only snapshot of stored data.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.nominations.submissions', $n)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-extrabold text-indigo-800 hover:bg-indigo-100">
                    Interested members
                </a>
                <a href="<?php echo e(route('admin.nominations.edit', $n)); ?>" class="inline-flex rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">Edit</a>
                <a href="<?php echo e(route('admin.nominations.index')); ?>" class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-xs font-extrabold text-white hover:bg-indigo-600">Back to list</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Title</p>
                        <p class="mt-1 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($n->title); ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Positions</p>
                        <ul class="mt-2 space-y-2">
                            <?php $__currentLoopData = $n->positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-800"><?php echo e($pos->position); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Terms</p>
                        <div class="mt-1 min-h-[6rem] whitespace-pre-wrap rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-700"><?php echo e($n->terms ?: '—'); ?></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="rounded-xl border border-slate-100 p-4">
                        <p class="text-xs font-black text-slate-600">Interest window</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">
                            <?php if($n->polling_date_to && $n->polling_date_to->toDateString() !== $n->polling_date->toDateString()): ?>
                                <?php echo e(optional($n->polling_date)->format('d M Y')); ?> – <?php echo e($n->polling_date_to->format('d M Y')); ?>

                            <?php else: ?>
                                <?php echo e(optional($n->polling_date)->format('d M Y')); ?>

                            <?php endif; ?>
                        </p>
                        <p class="mt-1 text-sm text-slate-600"><?php echo e($n->polling_from); ?> – <?php echo e($n->polling_to); ?></p>
                    </div>
                    <div class="rounded-xl border border-slate-100 p-4">
                        <p class="text-xs font-black text-slate-600 mb-3">Images</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-slate-400 mb-1">Cover</p>
                                <?php if($coverUrl): ?>
                                    <img src="<?php echo e($coverUrl); ?>" alt="Cover" class="h-40 w-full rounded-xl object-cover ring-1 ring-slate-100" />
                                <?php else: ?>
                                    <p class="rounded-xl border border-dashed border-slate-200 py-8 text-center text-xs text-slate-400">No cover image</p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-slate-400 mb-1">Banner</p>
                                <?php if($bannerUrl): ?>
                                    <img src="<?php echo e($bannerUrl); ?>" alt="Banner" class="h-40 w-full rounded-xl object-cover ring-1 ring-slate-100" />
                                <?php else: ?>
                                    <p class="rounded-xl border border-dashed border-slate-200 py-8 text-center text-xs text-slate-400">No banner image</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 rounded-xl border border-slate-100 p-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status</p>
                            <p class="mt-1 text-sm font-bold capitalize text-slate-900"><?php echo e($n->status); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Display</p>
                            <p class="mt-1 text-sm font-bold text-slate-900"><?php echo e($n->is_active ? 'Active' : 'Inactive'); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Created</p>
                            <p class="mt-1 text-sm text-slate-700"><?php echo e($n->created_at?->format('d M Y H:i')); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($n->creator->name ?? 'Admin'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\nominations\show.blade.php ENDPATH**/ ?>