<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 uppercase"><?php echo e($job->title); ?></h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Posted On Date & Time</p>
            <p class="text-xs font-bold text-slate-700 mt-1">No. of Seats - <?php echo e($job->no_of_openings); ?> | Applied - <?php echo e($applications->total()); ?></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.jobs.report', $job->id)); ?>" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">Download Report</a>
            <a href="<?php echo e(route('admin.jobs.index')); ?>" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold">Back</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
        <div class="flex items-center justify-end mb-3">
            <form method="GET">
                <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Search"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-56 outline-none focus:ring-2 focus:ring-indigo-200">
            </form>
        </div>
        <div class="space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-3 border border-slate-200 rounded-xl p-3">
                    <div class="w-8 h-8 rounded border border-slate-400 flex items-center justify-center text-sm"><?php echo e(strtoupper(substr($application->user->name ?? 'M', 0, 1))); ?></div>
                    <div class="w-48">
                        <p class="text-sm font-extrabold"><?php echo e($application->user->name ?? 'Member'); ?></p>
                        <p class="text-[11px] text-slate-500"><?php echo e($application->job->code); ?></p>
                    </div>
                    <div class="w-44 text-[11px] text-slate-600"><?php echo e($application->user->email ?? '-'); ?><br><?php echo e($application->user->mobile ?? '-'); ?></div>
                    <div class="w-28 text-[11px] text-slate-700">Member</div>
                    <div class="w-40 text-[11px] text-slate-700"><?php echo e(optional($application->submitted_at)->format('d M Y h:i A') ?: '-'); ?></div>
                    <div class="flex-1">
                        <form method="POST" action="<?php echo e(route('admin.jobs.applications.status', [$job->id, $application->id])); ?>" class="inline-flex items-center gap-2">
                            <?php echo csrf_field(); ?>
                            <select name="application_status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold">
                                <?php $__currentLoopData = ['pending' => 'Pending','selected' => 'Selected','not_selected' => 'Not Selected','joined' => 'Joined','not_joined' => 'Not Joined']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k); ?>" <?php echo e($application->application_status === $k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="text-[10px] text-slate-400"><?php echo e($application->status_emailed_at ? 'mail-triggered' : ''); ?></span>
                        </form>
                    </div>
                    <div>
                        <?php $resumePath = $application->resume_path ?: ($application->user->educational_certificate_path ?? null); ?>
                        <?php if($resumePath): ?>
                            <a href="<?php echo e(asset('storage/' . $resumePath)); ?>" target="_blank" class="text-[11px] font-extrabold text-indigo-700">Resume</a>
                        <?php else: ?>
                            <span class="text-[11px] text-slate-400">Resume</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8 text-slate-500 font-bold">No applications found.</div>
            <?php endif; ?>
        </div>
        <div class="mt-4"><?php echo e($applications->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\jobs\applications.blade.php ENDPATH**/ ?>