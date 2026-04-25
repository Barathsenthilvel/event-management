<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-5xl mx-auto space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Create Event</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Create a new event and configure dates, seats, files, and status.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <?php echo $__env->make('admin.events._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\events\create.blade.php ENDPATH**/ ?>