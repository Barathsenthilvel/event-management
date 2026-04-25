<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manage Meetings</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Manage schedules and member invites.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full md:w-auto md:flex-1 md:max-w-3xl">
                <form method="GET" class="flex items-center gap-2 w-full sm:max-w-md min-w-0">
                    <div class="relative flex-1 min-w-0">
                        <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search…"
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
                </form>
                <div class="flex shrink-0 justify-end">
                    <a href="<?php echo e(route('admin.meetings.create')); ?>" class="inline-flex px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">+ Add</a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <?php if($meetings->count() === 0): ?>
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No meetings found</p>
                <p class="text-xs font-bold text-slate-500 mt-1">Create a meeting to get started.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="px-5 py-4">Meeting</th>
                            <th class="px-5 py-4">Mode</th>
                            <th class="px-5 py-4">Created On / By</th>
                            <th class="px-5 py-4">Schedule</th>
                            <th class="px-5 py-4">Display</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $s = $meeting->schedules->first(); ?>
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-extrabold text-slate-900"><?php echo e($meeting->title); ?></p>
                                    <p class="text-[11px] font-bold text-slate-500 truncate max-w-64"><?php echo e($meeting->meeting_link); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700 uppercase"><?php echo e(str_replace('_', ' ', $meeting->meeting_mode)); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700"><?php echo e($meeting->created_at->format('d M Y')); ?></p>
                                    <p class="text-[10px] font-bold text-slate-500"><?php echo e($meeting->creator->name ?? 'Admin'); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700"><?php echo e(optional($s?->meeting_date)->format('d M Y') ?: '-'); ?></p>
                                    <p class="text-[10px] font-bold text-slate-500"><?php echo e($s?->from_time ?: '-'); ?> - <?php echo e($s?->to_time ?: '-'); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <form method="POST" action="<?php echo e(route('admin.meetings.toggle-display', $meeting->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button class="px-3 py-1 rounded-full text-[10px] font-black <?php echo e($meeting->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>">
                                            <?php echo e($meeting->is_active ? 'Active' : 'Inactive'); ?>

                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase
                                        <?php echo e($meeting->status === 'live' ? 'bg-emerald-100 text-emerald-700' : ''); ?>

                                        <?php echo e($meeting->status === 'upcoming' ? 'bg-blue-100 text-blue-700' : ''); ?>

                                        <?php echo e($meeting->status === 'completed' ? 'bg-amber-100 text-amber-700' : ''); ?>

                                        <?php echo e($meeting->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : ''); ?>">
                                        <?php echo e($meeting->status); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <a href="<?php echo e(route('admin.meetings.edit', $meeting->id)); ?>" title="Modify Meeting"
                                           class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" />
                                            </svg>
                                        </a>
                                        <a href="<?php echo e(route('admin.meetings.duplicate', $meeting->id)); ?>" title="Duplicate Meeting"
                                           class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7a2 2 0 012-2h8a2 2 0 012 2v8m-6 2H6a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                        <a href="<?php echo e(route('admin.meetings.invite', $meeting->id)); ?>" title="Invite or Remove Members"
                                           class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-6 0v5m6 0H9" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.meetings.send-reminder', $meeting->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button title="Send Reminder" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" />
                                                </svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('admin.meetings.cancel', $meeting->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button title="Cancel Meeting" class="w-8 h-8 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                        <form id="admin-delete-meeting-<?php echo e($meeting->id); ?>" method="POST" action="<?php echo e(route('admin.meetings.destroy', $meeting->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="button" title="Delete Meeting"
                                                data-delete-form="admin-delete-meeting-<?php echo e($meeting->id); ?>"
                                                data-delete-title="Delete this meeting?"
                                                data-delete-message="Invites and reminders linked to this meeting will be removed."
                                                onclick="adminOpenDeleteModalFromEl(this)"
                                                class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-100">
                <?php echo e($meetings->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\meetings\index.blade.php ENDPATH**/ ?>