<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div>
        <a href="<?php echo e(route('admin.pollings.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to pollings
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.pollings.results', $polling)); ?>" class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-5 shadow-sm space-y-4">
        <?php echo csrf_field(); ?>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Member-facing results</h2>
                <p class="text-xs text-slate-600 mt-1">When voting has ended, turn this on so members see totals and the official winner you select below.</p>
            </div>
            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-800">
                <input type="hidden" name="results_visible_to_members" value="0">
                <input type="checkbox" name="results_visible_to_members" value="1" class="rounded border-slate-300" <?php echo e(old('results_visible_to_members', $polling->results_visible_to_members) ? 'checked' : ''); ?>>
                Show results to members
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $positionStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $pos = $block['position']; ?>
                <div class="rounded-xl border border-white bg-white p-3 shadow-sm">
                    <p class="text-[10px] font-black uppercase text-slate-500">Official winner</p>
                    <p class="text-xs font-bold text-slate-800 mb-2"><?php echo e($pos->position); ?></p>
                    <select name="winners[<?php echo e($pos->id); ?>]" class="w-full rounded-lg border border-slate-200 px-2 py-2 text-xs font-semibold text-slate-800">
                        <option value="">— Not set —</option>
                        <?php $__currentLoopData = $pos->candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cand->id); ?>" <?php echo e((int) old('winners.'.$pos->id, $pos->winner_user_id ?? 0) === (int) $cand->id ? 'selected' : ''); ?>><?php echo e($cand->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white text-sm font-extrabold">Save results &amp; visibility</button>
        </div>
    </form>

    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-start justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 uppercase"><?php echo e($polling->title); ?></h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Posted On Date & Time</p>
        </div>
        <a href="<?php echo e(route('admin.pollings.report', $polling->id)); ?>" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Download Report</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 p-4">
            <p class="text-sm font-extrabold text-slate-800 mb-2"><?php echo e($polling->title); ?></p>
            <p class="text-xs text-slate-500">
                <?php if($polling->polling_date_to && $polling->polling_date_to->toDateString() !== $polling->polling_date->toDateString()): ?>
                    <?php echo e(optional($polling->polling_date)->format('d M Y')); ?> – <?php echo e($polling->polling_date_to->format('d M Y')); ?>

                <?php else: ?>
                    <?php echo e(optional($polling->polling_date)->format('d M Y')); ?>

                <?php endif; ?>
            </p>
            <p class="text-xs text-slate-500"><?php echo e($polling->polling_from); ?> - <?php echo e($polling->polling_to); ?></p>
            <div class="mt-3">
                <span class="px-3 py-1 rounded-full text-[10px] font-black <?php echo e($polling->polling_status === 'live' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                    <?php echo e($polling->polling_status === 'live' ? 'In Live' : 'Ends'); ?>

                </span>
            </div>
            <div class="mt-4 text-xs font-bold text-slate-700">Show Stats
                <span class="ml-2 px-2 py-0.5 rounded-full <?php echo e($polling->show_stats ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>">
                    <?php echo e($polling->show_stats ? 'ON' : 'OFF'); ?>

                </span>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-100 p-6 space-y-10">
            <?php $__empty_1 = true; $__currentLoopData = $positionStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <h3 class="text-lg font-extrabold text-[#351c42]"><?php echo e($block['position']->position); ?></h3>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700"><?php echo e($block['total_votes']); ?> <?php echo e($block['total_votes'] === 1 ? 'vote' : 'votes'); ?></span>
                    </div>
                    <?php if(!empty($block['winner_name'])): ?>
                        <p class="mb-3 text-xs font-semibold text-emerald-800">
                            Official winner:
                            <span class="font-extrabold"><?php echo e($block['winner_name']); ?></span>
                        </p>
                    <?php else: ?>
                        <p class="mb-3 text-xs font-semibold text-slate-500">Official winner: Not set</p>
                    <?php endif; ?>
                    <?php $__empty_2 = true; $__currentLoopData = $block['candidates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                        <div class="relative mb-3 overflow-hidden rounded-2xl border border-[#c4b5d5]/60 bg-[#f3eef9]/50">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 rounded-2xl bg-gradient-to-r from-[#d4c4e8] to-[#c9b6e0]"
                                style="width: <?php echo e($candidate['bar_percent']); ?>%"
                            ></div>
                            <div class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="truncate text-sm font-bold text-[#351c42]"><?php echo e($candidate['name']); ?></span>
                                    <?php if(!empty($candidate['is_winner'])): ?>
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-emerald-800">Winner</span>
                                    <?php endif; ?>
                                </div>
                                <span class="shrink-0 text-sm font-black tabular-nums text-[#351c42]"><?php echo e($candidate['votes']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                        <p class="text-sm text-slate-500">No candidates listed for this position.</p>
                    <?php endif; ?>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-[11px] font-extrabold uppercase tracking-wide text-slate-700">Voted Members</p>
                        <?php if(!empty($block['voters'])): ?>
                            <div class="mt-2 overflow-x-auto">
                                <table class="min-w-full text-left text-xs">
                                    <thead>
                                        <tr class="text-[10px] font-black uppercase tracking-wide text-slate-500">
                                            <th class="px-2 py-1.5">Member</th>
                                            <th class="px-2 py-1.5">Email</th>
                                            <th class="px-2 py-1.5">Mobile</th>
                                            <th class="px-2 py-1.5">Voted At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $block['voters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="border-t border-slate-200 text-slate-700">
                                                <td class="px-2 py-1.5 font-semibold"><?php echo e($voter['name']); ?></td>
                                                <td class="px-2 py-1.5"><?php echo e($voter['email']); ?></td>
                                                <td class="px-2 py-1.5"><?php echo e($voter['mobile']); ?></td>
                                                <td class="px-2 py-1.5"><?php echo e($voter['voted_at']); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="mt-2 text-xs text-slate-500">No members have voted for this position yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-slate-500">No positions found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\pollings\stats.blade.php ENDPATH**/ ?>