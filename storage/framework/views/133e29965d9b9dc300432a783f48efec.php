<?php $__env->startSection('content'); ?>
<div class="flex h-full min-h-0 flex-col">
    <div class="shrink-0 px-6 pt-6 pb-4">
        <h1 class="text-xl font-bold text-slate-900">Homepage Banners</h1>
        <p class="text-xs text-slate-500">Homepage Banners / <span class="font-semibold text-indigo-600">Edit</span></p>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto custom-scroll px-6 pb-6">
        <?php echo $__env->make('admin.home-banners._form', ['banner' => $banner], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/admin/home-banners/edit.blade.php ENDPATH**/ ?>