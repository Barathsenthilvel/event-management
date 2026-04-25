<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5" x-data="{ viewType: 'list' }">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Member Subscriptions</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">View member subscriptions with transaction details.</p>
            </div>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between w-full lg:flex-1 lg:min-w-0">
                <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:max-w-2xl min-w-0">
                    <div class="relative flex-1 min-w-0">
                        <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search member / transaction / order"
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <select name="status" class="w-full sm:w-auto shrink-0 px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo e($status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="successful" <?php echo e($status === 'successful' ? 'selected' : ''); ?>>Successful</option>
                        <option value="failed" <?php echo e($status === 'failed' ? 'selected' : ''); ?>>Failed</option>
                    </select>
                    <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
                </form>
                <div class="flex items-center gap-2 justify-end shrink-0">
                    <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-100">
                        <button type="button" @click="viewType = 'list'"
                            :class="viewType === 'list' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                            class="p-2 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" @click="viewType = 'grid'"
                            :class="viewType === 'grid' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                            class="p-2 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <?php if($transactions->count() === 0): ?>
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No subscriptions found</p>
                <p class="text-xs font-bold text-slate-500 mt-1">No payment transactions available for the selected filter.</p>
            </div>
        <?php else: ?>
            <div x-show="viewType === 'list'" class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="px-5 py-4">Member</th>
                            <th class="px-5 py-4">Transaction ID</th>
                            <th class="px-5 py-4">Order / Payment Ref</th>
                            <th class="px-5 py-4">Subscription Type</th>
                            <th class="px-5 py-4">Amount</th>
                            <th class="px-5 py-4">Payment Status</th>
                            <th class="px-5 py-4">Subscription Status</th>
                            <th class="px-5 py-4">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $key = $tx->user_id . '|' . $tx->razorpay_order_id;
                                $sub = $subscriptions->get($key);
                            ?>
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-extrabold text-slate-900"><?php echo e($tx->user->name ?? 'Member'); ?></p>
                                    <p class="text-[11px] font-bold text-slate-500"><?php echo e($tx->user->email ?? '-'); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-extrabold text-slate-800">#<?php echo e($tx->id); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700 break-all"><?php echo e($tx->razorpay_order_id ?: '-'); ?></p>
                                    <p class="text-[11px] text-slate-500 break-all"><?php echo e($tx->razorpay_payment_id ?: '-'); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700 uppercase"><?php echo e($tx->type ?: '-'); ?></p>
                                    <p class="text-[11px] text-slate-500"><?php echo e($tx->subscriptionPlan->payment_type ?? '-'); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-extrabold text-slate-800">INR <?php echo e(number_format((float) $tx->amount, 2)); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase
                                        <?php echo e($tx->status === 'successful' ? 'bg-emerald-100 text-emerald-700' : ''); ?>

                                        <?php echo e($tx->status === 'pending' ? 'bg-amber-100 text-amber-700' : ''); ?>

                                        <?php echo e($tx->status === 'failed' ? 'bg-rose-100 text-rose-700' : ''); ?>">
                                        <?php echo e($tx->status); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <?php if($sub): ?>
                                        <p class="text-[11px] font-bold text-slate-700 capitalize"><?php echo e($sub->status); ?></p>
                                        <p class="text-[11px] text-slate-500">
                                            <?php echo e($sub->start_date?->format('d M Y')); ?> - <?php echo e($sub->end_date?->format('d M Y')); ?>

                                        </p>
                                    <?php else: ?>
                                        <p class="text-[11px] font-bold text-slate-400">N/A</p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700"><?php echo e($tx->paid_at?->format('d M Y h:i A') ?? '-'); ?></p>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div x-show="viewType === 'grid'" class="px-4 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $key = $tx->user_id . '|' . $tx->razorpay_order_id;
                            $sub = $subscriptions->get($key);
                        ?>
                        <div class="rounded-[20px] border border-slate-100 bg-white shadow-sm p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-extrabold text-slate-900 truncate"><?php echo e($tx->user->name ?? 'Member'); ?></p>
                                    <p class="text-[11px] font-bold text-slate-500 truncate"><?php echo e($tx->user->email ?? '-'); ?></p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase
                                    <?php echo e($tx->status === 'successful' ? 'bg-emerald-100 text-emerald-700' : ''); ?>

                                    <?php echo e($tx->status === 'pending' ? 'bg-amber-100 text-amber-700' : ''); ?>

                                    <?php echo e($tx->status === 'failed' ? 'bg-rose-100 text-rose-700' : ''); ?>">
                                    <?php echo e($tx->status); ?>

                                </span>
                            </div>

                            <div class="mt-3 space-y-2">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Transaction ID</p>
                                    <p class="text-[11px] font-extrabold text-slate-800">#<?php echo e($tx->id); ?></p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Order / Payment Ref</p>
                                    <p class="text-[11px] font-bold text-slate-700 break-all"><?php echo e($tx->razorpay_order_id ?: '-'); ?></p>
                                    <p class="text-[11px] text-slate-500 break-all"><?php echo e($tx->razorpay_payment_id ?: '-'); ?></p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Subscription Type</p>
                                    <p class="text-[11px] font-extrabold text-slate-700 uppercase"><?php echo e($tx->type ?: '-'); ?></p>
                                    <p class="text-[11px] text-slate-500"><?php echo e($tx->subscriptionPlan->payment_type ?? '-'); ?></p>
                                </div>

                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Amount</p>
                                        <p class="text-[11px] font-extrabold text-slate-800 tabular-nums">INR <?php echo e(number_format((float) $tx->amount, 2)); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Paid At</p>
                                        <p class="text-[11px] font-bold text-slate-700"><?php echo e($tx->paid_at?->format('d M Y h:i A') ?? '-'); ?></p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Subscription Status</p>
                                    <?php if($sub): ?>
                                        <p class="text-[11px] font-bold text-slate-700 capitalize"><?php echo e($sub->status); ?></p>
                                        <p class="text-[11px] text-slate-500">
                                            <?php echo e($sub->start_date?->format('d M Y')); ?> - <?php echo e($sub->end_date?->format('d M Y')); ?>

                                        </p>
                                    <?php else: ?>
                                        <p class="text-[11px] font-bold text-slate-400">N/A</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100">
                <?php echo e($transactions->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\subscriptions\index.blade.php ENDPATH**/ ?>