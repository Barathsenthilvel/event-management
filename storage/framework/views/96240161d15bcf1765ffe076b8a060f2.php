<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manage Polling</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Home / Polling</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full md:w-auto md:flex-1 md:max-w-3xl">
                <form method="GET" class="flex items-center gap-2 w-full sm:max-w-md min-w-0">
                    <div class="relative flex-1 min-w-0">
                        <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search"
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
                </form>
                <div class="flex shrink-0 justify-end">
                    <a href="<?php echo e(route('admin.pollings.create')); ?>" class="inline-flex px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">+ Add</a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-rose-50">
                    <tr>
                        <th class="px-4 py-3">Polling Title</th>
                        <th class="px-4 py-3">Voting Counts</th>
                        <th class="px-4 py-3">Promote Front</th>
                        <th class="px-4 py-3">Open & Closes On</th>
                        <th class="px-4 py-3">Last Updated</th>
                        <th class="px-4 py-3">Publish Status</th>
                        <th class="px-4 py-3">Polling Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $pollings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $polling): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3"><?php echo e($polling->title); ?></td>
                            <td class="px-4 py-3"><?php echo e($polling->votes_count); ?> Votes</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="<?php echo e(route('admin.pollings.toggle-promote', $polling->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black <?php echo e($polling->promote_front ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>">
                                        <?php echo e($polling->promote_front ? 'ON' : 'OFF'); ?>

                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <p>
                                    <?php if($polling->polling_date_to && $polling->polling_date_to->toDateString() !== $polling->polling_date->toDateString()): ?>
                                        <?php echo e(optional($polling->polling_date)->format('d M Y')); ?> – <?php echo e($polling->polling_date_to->format('d M Y')); ?>

                                    <?php else: ?>
                                        <?php echo e(optional($polling->polling_date)->format('d M Y')); ?>

                                    <?php endif; ?>
                                </p>
                                <p class="text-[10px] text-slate-500"><?php echo e($polling->polling_from); ?> - <?php echo e($polling->polling_to); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <p><?php echo e($polling->updated_at->format('d M Y')); ?></p>
                                <p class="text-[10px] text-slate-500"><?php echo e($polling->creator->name ?? 'Admin'); ?></p>
                            </td>
                            <td class="px-4 py-3"><?php echo e(strtoupper($polling->publish_status === 'na' ? 'N/A' : $polling->publish_status)); ?></td>
                            <td class="px-4 py-3">
                                <form method="POST" action="<?php echo e(route('admin.pollings.toggle-status', $polling->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black <?php echo e($polling->polling_status === 'live' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                        <?php echo e($polling->polling_status === 'live' ? 'In Live' : 'Ends'); ?>

                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="<?php echo e(route('admin.pollings.edit', $polling->id)); ?>" title="Modify"
                                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" /></svg>
                                    </a>
                                    <a href="<?php echo e(route('admin.pollings.stats', $polling->id)); ?>" title="View Stats"
                                       class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14" /></svg>
                                    </a>
                                    <form id="admin-delete-polling-<?php echo e($polling->id); ?>" method="POST" action="<?php echo e(route('admin.pollings.destroy', $polling->id)); ?>" class="inline-flex">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" title="Delete"
                                            data-delete-form="admin-delete-polling-<?php echo e($polling->id); ?>"
                                            data-delete-title="Delete this polling?"
                                            data-delete-message="This will remove the polling and its voting data from the admin and public site."
                                            onclick="adminOpenDeleteModalFromEl(this)"
                                            class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500 font-bold">No pollings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4"><?php echo e($pollings->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/admin/pollings/index.blade.php ENDPATH**/ ?>