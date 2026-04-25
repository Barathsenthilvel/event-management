<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Event Album</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Event: <?php echo e($event->title); ?> • Status: <?php echo e(ucfirst($event->status)); ?></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.events.show', $event->id)); ?>" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Event Details</a>
                <a href="<?php echo e(route('admin.events.index')); ?>" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <?php if($event->status !== 'completed'): ?>
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
                    Album upload is available only when event status is Completed.
                </div>
            <?php else: ?>
                <form method="POST" action="<?php echo e(route('admin.events.album.store', $event->id)); ?>" enctype="multipart/form-data" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-500">Upload Event Photos</label>
                    <input type="file" name="photos[]" multiple accept="image/*"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">
                    <?php $__errorArgs = ['photos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['photos.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Upload Photos</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-900 mb-4">Album Photos (<?php echo e($event->photos->count()); ?>)</h2>
            <?php if($event->photos->isEmpty()): ?>
                <p class="text-sm text-slate-500">No album photos yet.</p>
            <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php $__currentLoopData = $event->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50">
                            <img src="<?php echo e(asset('storage/' . $photo->photo_path)); ?>" alt="Event photo" class="w-full h-40 object-cover">
                            <div class="p-2.5">
                                <form id="admin-delete-event-photo-<?php echo e($photo->id); ?>" method="POST" action="<?php echo e(route('admin.events.album.destroy', [$event->id, $photo->id])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button" class="w-full rounded-lg bg-rose-600 text-white text-xs font-extrabold py-2 hover:bg-rose-700"
                                        data-delete-form="admin-delete-event-photo-<?php echo e($photo->id); ?>"
                                        data-delete-title="Remove this photo?"
                                        data-delete-message="This image will be deleted from the event album permanently."
                                        onclick="adminOpenDeleteModalFromEl(this)">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\events\album.blade.php ENDPATH**/ ?>