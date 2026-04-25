<?php
    $isEdit = isset($meeting);
    $action = $isEdit ? route('admin.meetings.update', $meeting->id) : route('admin.meetings.store');
    $defaults = $duplicateSource ?? [];
    $titleValue = old('title', $meeting->title ?? ($defaults['title'] ?? ''));
    $linkValue = old('meeting_link', $meeting->meeting_link ?? ($defaults['meeting_link'] ?? ''));
    $descriptionValue = old('description', $meeting->description ?? ($defaults['description'] ?? ''));
    $modeValue = old('meeting_mode', $meeting->meeting_mode ?? ($defaults['meeting_mode'] ?? 'direct'));
    $statusValue = old('status', $meeting->status ?? ($defaults['status'] ?? 'upcoming'));
    $isActiveValue = old('is_active', $meeting->is_active ?? ($defaults['is_active'] ?? true));
    $repeatEnabledValue = old('repeat_enabled', 0);
    $repeatFrequencyValue = old('repeat_frequency', 'weekly');
    $repeatCountValue = old('repeat_count', 1);
    $schedule = old('schedule_date')
        ? [
            'meeting_date' => old('schedule_date'),
            'from_time' => old('schedule_from'),
            'to_time' => old('schedule_to'),
        ]
        : ($isEdit && $meeting->schedules->first()
            ? [
                'meeting_date' => optional($meeting->schedules->first()->meeting_date)->format('Y-m-d'),
                'from_time' => $meeting->schedules->first()->from_time,
                'to_time' => $meeting->schedules->first()->to_time,
            ]
            : [
                'meeting_date' => $defaults['schedule_date'] ?? '',
                'from_time' => $defaults['schedule_from'] ?? '',
                'to_time' => $defaults['schedule_to'] ?? '',
            ]);
?>

<form action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting Title <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <input type="text" name="title" value="<?php echo e($titleValue); ?>" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting link <span class="text-slate-400 font-normal">(optional)</span></label>
                <p class="text-[11px] text-slate-500 mb-1">Add a Zoom / Meet / WhatsApp link when ready. Members still see the meeting on their dashboard from the schedule.</p>
                <input type="url" name="meeting_link" value="<?php echo e($linkValue); ?>" placeholder="https://…"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                <?php $__errorArgs = ['meeting_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200"><?php echo e($descriptionValue); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting Mode <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <select name="meeting_mode"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="whatsapp" <?php echo e($modeValue === 'whatsapp' ? 'selected' : ''); ?>>WhatsApp</option>
                    <option value="teams" <?php echo e($modeValue === 'teams' ? 'selected' : ''); ?>>Meet / Teams / Others</option>
                    <option value="others" <?php echo e($modeValue === 'others' ? 'selected' : ''); ?>>Others</option>
                    <option value="direct" <?php echo e($modeValue === 'direct' ? 'selected' : ''); ?>>Direct</option>
                    <option value="phone_call" <?php echo e($modeValue === 'phone_call' ? 'selected' : ''); ?>>Phone Call</option>
                </select>
                <?php $__errorArgs = ['meeting_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-3">Meeting Date & Time <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <input type="date" name="schedule_date" value="<?php echo e($schedule['meeting_date']); ?>"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div>
                        <input type="time" name="schedule_from" value="<?php echo e($schedule['from_time']); ?>"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div>
                        <input type="time" name="schedule_to" value="<?php echo e($schedule['to_time']); ?>"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                </div>
                <?php $__errorArgs = ['schedule_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['schedule_from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['schedule_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <?php if(!$isEdit): ?>
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-black text-slate-700 mb-3">Repeat Meeting (optional)</p>
                    <div class="space-y-3">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="repeat_enabled" value="0">
                            <input type="checkbox" name="repeat_enabled" value="1" <?php echo e((int) $repeatEnabledValue === 1 ? 'checked' : ''); ?>>
                            Create additional repeated meetings automatically
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Frequency</label>
                                <select name="repeat_frequency" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                                    <option value="weekly" <?php echo e($repeatFrequencyValue === 'weekly' ? 'selected' : ''); ?>>Weekly</option>
                                    <option value="monthly" <?php echo e($repeatFrequencyValue === 'monthly' ? 'selected' : ''); ?>>Monthly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">How many extra meetings</label>
                                <input type="number" min="1" max="24" name="repeat_count" value="<?php echo e($repeatCountValue); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                            </div>
                        </div>
                        <p class="text-[11px] font-semibold text-slate-500">Example: count 4 + weekly creates 4 extra meetings (next 4 weeks) in addition to this one.</p>
                    </div>
                    <?php $__errorArgs = ['repeat_frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['repeat_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            <?php endif; ?>

            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-3">Images (optional)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-5 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <span class="text-xs font-semibold text-slate-700">Cover Image</span>
                        <input type="file" name="cover_image" class="hidden" accept="image/*">
                    </label>
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-5 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <span class="text-xs font-semibold text-slate-700">Banner Image</span>
                        <input type="file" name="banner_image" class="hidden" accept="image/*">
                    </label>
                </div>
                <?php $__errorArgs = ['cover_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['banner_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-2">Status <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        <option value="upcoming" <?php echo e($statusValue === 'upcoming' ? 'selected' : ''); ?>>Upcoming</option>
                        <option value="live" <?php echo e($statusValue === 'live' ? 'selected' : ''); ?>>Live</option>
                        <option value="completed" <?php echo e($statusValue === 'completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="cancelled" <?php echo e($statusValue === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" <?php echo e($isActiveValue ? 'checked' : ''); ?>>
                        Display Active
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="<?php echo e(route('admin.meetings.index')); ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold">
            <?php echo e($isEdit ? 'Update Meeting' : 'Create Meeting'); ?>

        </button>
    </div>
</form>

<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\meetings\_form.blade.php ENDPATH**/ ?>