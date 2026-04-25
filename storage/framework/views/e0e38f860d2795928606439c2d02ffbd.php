<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Invite Members</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Event: <?php echo e($event->title); ?></p>
            </div>
            <a href="<?php echo e(route('admin.events.index')); ?>" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
        </div>

        <form method="POST" action="<?php echo e(route('admin.events.invite.store', $event->id)); ?>" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Meeting Participant *</p>
                        <div class="flex items-center gap-4">
                            <?php $target = old('target', 'all'); ?>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="all" <?php echo e($target === 'all' ? 'checked' : ''); ?>>
                                All Members
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="specific" <?php echo e($target === 'specific' ? 'checked' : ''); ?>>
                                Specific
                            </label>
                        </div>
                        <?php $__errorArgs = ['target'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Notify Members Via</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200">
                                <input type="checkbox" name="notify_whatsapp" value="1" <?php echo e(old('notify_whatsapp') ? 'checked' : ''); ?>>
                                WhatsApp
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200">
                                <input type="checkbox" name="notify_sms" value="1" <?php echo e(old('notify_sms') ? 'checked' : ''); ?>>
                                SMS
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2">
                                <input type="checkbox" name="notify_email" value="1" <?php echo e(old('notify_email') ? 'checked' : ''); ?>>
                                Email
                            </label>
                        </div>
                        <?php $__errorArgs = ['notify_channel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Approved Members</p>
                        <form method="GET">
                            <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Search"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                        </form>
                    </div>
                    <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100">
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $checked = in_array($m->id, old('member_ids', $invitedUserIds)); ?>
                            <label class="flex items-start justify-between gap-3 p-3 hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900"><?php echo e($m->name); ?></p>
                                    <p class="text-[11px] font-bold text-slate-500"><?php echo e($m->email); ?> <?php if($m->mobile): ?> • <?php echo e($m->mobile); ?> <?php endif; ?></p>
                                </div>
                                <input type="checkbox" name="member_ids[]" value="<?php echo e($m->id); ?>" <?php echo e($checked ? 'checked' : ''); ?>>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php $__errorArgs = ['member_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['member_ids.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500">Total members: <?php echo e($members->total()); ?></p>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Send Invites</button>
            </div>
        </form>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <?php echo e($members->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\events\invite.blade.php ENDPATH**/ ?>