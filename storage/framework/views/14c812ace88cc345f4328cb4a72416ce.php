<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Job Alert</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Meeting / Create</p>
        </div>

        <form method="POST" action="<?php echo e(route('admin.jobs.alert.store', $job->id)); ?>" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-black text-slate-700 mb-2">To Participant *</p>
                        <div class="flex items-center gap-4">
                            <?php $target = old('target', 'all'); ?>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="all" <?php echo e($target === 'all' ? 'checked' : ''); ?>> All Members
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="specific" <?php echo e($target === 'specific' ? 'checked' : ''); ?>> Specific
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="leaders_only" <?php echo e($target === 'leaders_only' ? 'checked' : ''); ?>> Leaders Only
                            </label>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-700 mb-2">Notify Members Via</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2"><input type="checkbox" name="notify_all" value="1" <?php echo e(old('notify_all') ? 'checked' : ''); ?>> All</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200"><input type="checkbox" name="notify_whatsapp" value="1" <?php echo e(old('notify_whatsapp') ? 'checked' : ''); ?>> WhatsApp</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200"><input type="checkbox" name="notify_sms" value="1" <?php echo e(old('notify_sms') ? 'checked' : ''); ?>> SMS</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2"><input type="checkbox" name="notify_email" value="1" <?php echo e(old('notify_email') ? 'checked' : ''); ?>> Email</label>
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
                    <div class="flex items-center justify-between mb-3 gap-2">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Members</p>
                        <div class="flex items-center gap-2">
                            <select name="designation_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold">
                                <option value="0">Select Role</option>
                                <?php $__currentLoopData = $designations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($designation->id); ?>" <?php echo e((int) $designationId === (int) $designation->id ? 'selected' : ''); ?>>
                                        <?php echo e($designation->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <label class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600">
                                <input type="checkbox" name="leaders_only" value="1" <?php echo e($leadersOnly ? 'checked' : ''); ?>>
                                Leaders Only
                            </label>
                            <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Search" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold">
                            <button type="submit" formmethod="GET" formaction="<?php echo e(route('admin.jobs.alert', $job->id)); ?>" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-extrabold">Search</button>
                        </div>
                    </div>
                    <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100">
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $checked = in_array($m->id, old('member_ids', $alertedIds)); ?>
                            <label class="flex items-start justify-between gap-3 p-3 hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900"><?php echo e($m->name); ?></p>
                                    <p class="text-[11px] font-bold text-slate-500"><?php echo e($m->email); ?> <?php if($m->mobile): ?> • <?php echo e($m->mobile); ?> <?php endif; ?></p>
                                    <p class="text-[11px] text-slate-400"><?php echo e($m->designation->name ?? 'No role'); ?></p>
                                </div>
                                <input type="checkbox" name="member_ids[]" value="<?php echo e($m->id); ?>" <?php echo e($checked ? 'checked' : ''); ?>>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="<?php echo e(route('admin.jobs.index')); ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold">Send</button>
            </div>
        </form>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <?php echo e($members->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\jobs\alert.blade.php ENDPATH**/ ?>