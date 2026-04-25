<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Dashboard</h1>
        <p class="text-sm text-slate-500">Welcome back, <?php echo e(Auth::guard('admin')->user()->name); ?>!</p>
    </div>

    <?php if(Auth::guard('admin')->user()->is_super_admin): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-600 flex-1 pr-2">Total Admins</h3>
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900"><?php echo e(\App\Models\Admin::count()); ?></p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-600 flex-1 pr-2">Total Roles</h3>
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900"><?php echo e(\App\Models\Role::count()); ?></p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-600 flex-1 pr-2">Total Permissions</h3>
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900"><?php echo e(\App\Models\Permission::count()); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>