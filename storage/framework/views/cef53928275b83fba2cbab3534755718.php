<?php
    $isEdit = isset($event);
    $action = $isEdit ? route('admin.events.update', $event->id) : route('admin.events.store');
    $dates = old('event_dates', $isEdit ? $event->dates->map(fn($d) => [
        'date' => optional($d->event_date)->format('Y-m-d'),
        'start_time' => $d->start_time ? \Illuminate\Support\Carbon::parse($d->start_time)->format('H:i') : '',
        'end_time' => $d->end_time ? \Illuminate\Support\Carbon::parse($d->end_time)->format('H:i') : '',
    ])->values()->all() : [['date' => '', 'start_time' => '', 'end_time' => '']]);
?>

<form action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Event Title <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
            <input type="text" name="title" value="<?php echo e(old('title', $event->title ?? '')); ?>"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none" required>
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
            <label class="block text-xs font-bold text-slate-700 mb-2">Venue (optional)</label>
            <input type="text" name="venue" value="<?php echo e(old('venue', $event->venue ?? '')); ?>"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
            <?php $__errorArgs = ['venue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-2">Event Description</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none"><?php echo e(old('description', $event->description ?? '')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Seat Mode <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
            <?php $seatMode = old('seat_mode', $event->seat_mode ?? 'unlimited'); ?>
            <select name="seat_mode" id="seat_mode"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
                <option value="unlimited" <?php echo e($seatMode === 'unlimited' ? 'selected' : ''); ?>>Unlimited</option>
                <option value="limited" <?php echo e($seatMode === 'limited' ? 'selected' : ''); ?>>Limited</option>
            </select>
            <?php $__errorArgs = ['seat_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Seat Limit</label>
            <input type="number" min="1" name="seat_limit" id="seat_limit" value="<?php echo e(old('seat_limit', $event->seat_limit ?? '')); ?>"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
            <?php $__errorArgs = ['seat_limit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Event Status <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
            <?php $status = old('status', $event->status ?? 'upcoming'); ?>
            <select name="status"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
                <option value="upcoming" <?php echo e($status === 'upcoming' ? 'selected' : ''); ?>>Upcoming</option>
                <option value="live" <?php echo e($status === 'live' ? 'selected' : ''); ?>>Live</option>
                <option value="completed" <?php echo e($status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                <option value="cancelled" <?php echo e($status === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
            </select>
            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-6">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="promote_front" value="0">
            <input type="checkbox" name="promote_front" value="1" <?php echo e(old('promote_front', $event->promote_front ?? false) ? 'checked' : ''); ?> class="rounded border-slate-300">
            Promote on front
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $event->is_active ?? true) ? 'checked' : ''); ?> class="rounded border-slate-300">
            Display active
        </label>
    </div>

    <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-800">Event Dates <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></h3>
            <button type="button" id="add-date-row" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold">+ Add Date</button>
        </div>
        <div id="event-dates-wrapper" class="space-y-3">
            <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 date-row">
                    <input type="date" name="event_dates[<?php echo e($i); ?>][date]" value="<?php echo e($d['date'] ?? ''); ?>" class="px-3 py-2 rounded-lg border border-slate-200" required>
                    <input type="time" name="event_dates[<?php echo e($i); ?>][start_time]" value="<?php echo e($d['start_time'] ?? ''); ?>" class="px-3 py-2 rounded-lg border border-slate-200">
                    <input type="time" name="event_dates[<?php echo e($i); ?>][end_time]" value="<?php echo e($d['end_time'] ?? ''); ?>" class="px-3 py-2 rounded-lg border border-slate-200">
                    <button type="button" class="remove-date-row px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold">Remove</button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['event_dates'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php $__errorArgs = ['event_dates.*.date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Cover Image</label>
            <input type="file" name="cover_image" accept="image/*" class="w-full text-sm">
            <?php if($isEdit && !empty($event->cover_image_path)): ?>
                <div class="mt-2 rounded-xl border border-slate-200 bg-white p-2">
                    <p class="text-[11px] font-bold text-slate-500 mb-2">Current cover image</p>
                    <a href="<?php echo e(asset('storage/' . $event->cover_image_path)); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo e(asset('storage/' . $event->cover_image_path)); ?>" alt="Current cover image"
                             class="h-28 w-full rounded-lg object-cover border border-slate-100">
                    </a>
                </div>
            <?php endif; ?>
            <?php $__errorArgs = ['cover_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Banner Image</label>
            <input type="file" name="banner_image" accept="image/*" class="w-full text-sm">
            <?php if($isEdit && !empty($event->banner_image_path)): ?>
                <div class="mt-2 rounded-xl border border-slate-200 bg-white p-2">
                    <p class="text-[11px] font-bold text-slate-500 mb-2">Current banner image</p>
                    <a href="<?php echo e(asset('storage/' . $event->banner_image_path)); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo e(asset('storage/' . $event->banner_image_path)); ?>" alt="Current banner image"
                             class="h-28 w-full rounded-lg object-cover border border-slate-100">
                    </a>
                </div>
            <?php endif; ?>
            <?php $__errorArgs = ['banner_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Certificate template (PDF)</label>
            <p class="text-[11px] text-slate-500 mb-2 leading-relaxed">Upload the certificate file members will download after the event is <strong class="text-slate-700">Completed</strong> and you mark them as <strong class="text-slate-700">Attended</strong> on the event detail page.</p>
            <input type="file" name="template_pdf" accept="application/pdf" class="w-full text-sm">
            <?php if($isEdit && !empty($event->template_pdf_path)): ?>
                <div class="mt-2 rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-[11px] font-bold text-slate-500">Current template PDF</p>
                    <a href="<?php echo e(asset('storage/' . $event->template_pdf_path)); ?>"
                       target="_blank"
                       rel="noopener"
                       class="mt-1 inline-flex items-center gap-2 text-xs font-extrabold text-indigo-700 hover:text-indigo-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <?php echo e(basename($event->template_pdf_path)); ?>

                    </a>
                </div>
            <?php endif; ?>
            <?php $__errorArgs = ['template_pdf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="<?php echo e(route('admin.events.index')); ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold">
            <?php echo e($isEdit ? 'Update Event' : 'Create Event'); ?>

        </button>
    </div>
</form>

<script>
    (function () {
        const seatMode = document.getElementById('seat_mode');
        const seatLimit = document.getElementById('seat_limit');
        const wrapper = document.getElementById('event-dates-wrapper');
        const addBtn = document.getElementById('add-date-row');

        function toggleSeatLimit() {
            if (!seatMode || !seatLimit) return;
            seatLimit.disabled = seatMode.value !== 'limited';
            if (seatMode.value !== 'limited') seatLimit.value = '';
        }

        function reindexRows() {
            const rows = wrapper.querySelectorAll('.date-row');
            rows.forEach((row, i) => {
                const date = row.querySelector('input[type="date"]');
                const start = row.querySelector('input[type="time"][name*="[start_time]"]');
                const end = row.querySelector('input[type="time"][name*="[end_time]"]');
                date.name = `event_dates[${i}][date]`;
                start.name = `event_dates[${i}][start_time]`;
                end.name = `event_dates[${i}][end_time]`;
            });
        }

        addBtn?.addEventListener('click', function () {
            const idx = wrapper.querySelectorAll('.date-row').length;
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 md:grid-cols-4 gap-3 date-row';
            row.innerHTML = `
                <input type="date" name="event_dates[${idx}][date]" class="px-3 py-2 rounded-lg border border-slate-200" required>
                <input type="time" name="event_dates[${idx}][start_time]" class="px-3 py-2 rounded-lg border border-slate-200">
                <input type="time" name="event_dates[${idx}][end_time]" class="px-3 py-2 rounded-lg border border-slate-200">
                <button type="button" class="remove-date-row px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold">Remove</button>
            `;
            wrapper.appendChild(row);
        });

        wrapper?.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-date-row')) return;
            const rows = wrapper.querySelectorAll('.date-row');
            if (rows.length <= 1) return;
            e.target.closest('.date-row').remove();
            reindexRows();
        });

        seatMode?.addEventListener('change', toggleSeatLimit);
        toggleSeatLimit();
    })();
</script>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\events\_form.blade.php ENDPATH**/ ?>