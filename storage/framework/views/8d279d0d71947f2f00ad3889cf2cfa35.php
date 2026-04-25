<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[24px] border border-white bg-linear-to-br from-white via-white to-indigo-50/40 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Edit designation</h1>
                <p class="mt-1 text-xs font-bold text-slate-500"><?php echo e($designation->name); ?></p>
            </div>
            <a href="<?php echo e(route('admin.designations.index')); ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to list
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-6 max-w-xl">
        <form method="POST" action="<?php echo e(route('admin.designations.update', $designation)); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Name</label>
                <input id="name" name="name" value="<?php echo e(old('name', $designation->name)); ?>" required maxlength="255"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-[11px] font-bold text-rose-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="sort_order" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Sort order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="<?php echo e(old('sort_order', $designation->sort_order)); ?>"
                    class="w-full max-w-xs px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all">
                    Save changes
                </button>
                <a href="<?php echo e(route('admin.designations.index')); ?>"
                   class="px-6 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-extrabold hover:bg-slate-50 transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\designations\edit.blade.php ENDPATH**/ ?>