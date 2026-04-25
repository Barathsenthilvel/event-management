<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Edit Admin</h1>
        <p class="text-sm text-slate-500">Update admin user details</p>
    </div>

    <form method="POST" action="<?php echo e(route('admin.admins.update', $admin)); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Full Name <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <input type="text" name="name" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="John Doe" value="<?php echo e(old('name', $admin->name)); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <input type="email" name="email" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="john@example.com" value="<?php echo e(old('email', $admin->email)); ?>">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Phone (Optional)</label>
                <input type="tel" name="phone"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="+1234567890" value="<?php echo e(old('phone', $admin->phone)); ?>">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">New Password (Leave blank to keep current)</label>
                <input type="password" name="password"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="••••••••">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="••••••••">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-4">Assign Roles</label>
                <div class="space-y-2">
                    <?php
                        $adminRoleIds = $admin->roles->pluck('id')->toArray();
                    ?>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="roles[]" value="<?php echo e($role->id); ?>"
                            <?php echo e(in_array($role->id, old('roles', $adminRoleIds)) ? 'checked' : ''); ?>

                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                        <div>
                            <span class="text-sm font-bold text-slate-700"><?php echo e($role->name); ?></span>
                            <?php if($role->description): ?>
                            <p class="text-xs text-slate-500"><?php echo e($role->description); ?></p>
                            <?php endif; ?>
                        </div>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex gap-3 pt-4">
                <a href="<?php echo e(route('admin.admins.index')); ?>"
                    class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center">
                    Cancel
                </a>
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                    Update Admin
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\admins\edit.blade.php ENDPATH**/ ?>