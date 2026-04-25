<?php $__env->startSection('title', 'Change password — GNAT Association'); ?>

<?php $__env->startSection('content'); ?>
    <div class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-6 shadow-md sm:p-8">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Account</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Change password</h1>
                <p class="mt-2 text-sm text-[#351c42]/65">Update your login password from this page.</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('member.password.update')); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#351c42]/80">Current password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                        class="w-full rounded-2xl border border-[#351c42]/10 bg-white px-4 py-3 text-sm text-[#351c42] outline-none transition focus:border-[#965995]/60 focus:ring-4 focus:ring-[#965995]/15" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#351c42]/80">New password <span class="text-red-500">*</span></label>
                    <input type="password" name="new_password" required minlength="6" autocomplete="new-password"
                        class="w-full rounded-2xl border border-[#351c42]/10 bg-white px-4 py-3 text-sm text-[#351c42] outline-none transition focus:border-[#965995]/60 focus:ring-4 focus:ring-[#965995]/15" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-[#351c42]/80">Confirm new password <span class="text-red-500">*</span></label>
                    <input type="password" name="new_password_confirmation" required minlength="6" autocomplete="new-password"
                        class="w-full rounded-2xl border border-[#351c42]/10 bg-white px-4 py-3 text-sm text-[#351c42] outline-none transition focus:border-[#965995]/60 focus:ring-4 focus:ring-[#965995]/15" />
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#351c42]/10 pt-6 sm:flex-row sm:justify-end sm:gap-4">
                <a href="<?php echo e(route('member.profile.edit')); ?>"
                    class="inline-flex w-full items-center justify-center rounded-full border border-[#351c42]/14 bg-white px-6 py-3 text-sm font-bold text-[#351c42] sm:w-auto">
                    Back to profile
                </a>
                <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-br from-[#351c42] to-[#4d2a5c] px-6 py-3 text-sm font-bold text-[#fddc6a] shadow-lg shadow-[#351c42]/25 sm:w-auto">
                    Update password
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('member.layouts.gnat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\profile\password.blade.php ENDPATH**/ ?>