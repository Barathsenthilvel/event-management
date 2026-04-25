<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 uppercase"><?php echo e($nomination->title); ?></h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Interest submissions for this nomination</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.nominations.show', $nomination)); ?>" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-extrabold text-slate-800 hover:bg-slate-50">View nomination (read-only)</a>
            <a href="<?php echo e(route('admin.nominations.report', $nomination->id)); ?>" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Download Report</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 p-4 space-y-3">
            <p class="text-[11px] font-extrabold uppercase tracking-wide text-slate-600">Position-wise summary</p>
            <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-extrabold text-slate-800"><?php echo e($pos->position); ?></p>
                    <div class="flex items-center gap-1">
                        <span class="px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-black"><?php echo e($pos->interested_entries_count); ?></span>
                        <span class="px-2 py-1 rounded-md bg-slate-200 text-slate-700 text-[10px] font-black"><?php echo e($pos->not_interested_entries_count); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-100 p-4">
            <div class="mb-3 flex flex-wrap items-center justify-end gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <select name="response" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">
                        <option value="all" <?php echo e(($response ?? 'all') === 'all' ? 'selected' : ''); ?>>All responses</option>
                        <option value="interested" <?php echo e(($response ?? 'all') === 'interested' ? 'selected' : ''); ?>>Interested only</option>
                        <option value="not_interested" <?php echo e(($response ?? 'all') === 'not_interested' ? 'selected' : ''); ?>>Not interested only</option>
                    </select>
                    <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Search"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-56 outline-none focus:ring-2 focus:ring-indigo-200">
                    <button type="submit" class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-black">Apply</button>
                </form>
            </div>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center gap-3 border border-slate-200 rounded-xl p-3">
                        <div class="w-8 h-8 rounded border border-slate-400 flex items-center justify-center text-sm"><?php echo e(strtoupper(substr($entry->user->name ?? 'M', 0, 1))); ?></div>
                        <div class="w-48">
                            <p class="text-sm font-extrabold"><?php echo e($entry->user->name ?? 'Member'); ?></p>
                            <p class="text-[11px] text-slate-500"><?php echo e($entry->position->position ?? '-'); ?></p>
                        </div>
                        <div class="w-44 text-[11px] text-slate-600"><?php echo e($entry->user->email ?? '-'); ?><br><?php echo e($entry->user->mobile ?? '-'); ?></div>
                        <div class="w-28 text-[11px]">
                            <?php if(($entry->response_status ?? 'interested') === 'not_interested'): ?>
                                <span class="inline-flex rounded-full bg-slate-200 px-2 py-1 font-black text-slate-700">Not interested</span>
                            <?php else: ?>
                                <span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 font-black text-emerald-700">Interested</span>
                            <?php endif; ?>
                        </div>
                        <div class="w-40 text-[11px] text-slate-700"><?php echo e(optional($entry->submitted_at)->format('d M Y h:i A') ?: '-'); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-slate-500 font-bold">No submissions found.</div>
                <?php endif; ?>
            </div>
            <div class="mt-4"><?php echo e($entries->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\nominations\submissions.blade.php ENDPATH**/ ?>