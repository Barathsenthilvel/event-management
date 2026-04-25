
<div class="space-y-10 lg:space-y-12">
    <section aria-labelledby="member-my-events-heading" class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-6 shadow-md sm:p-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="member-my-events-heading" class="text-xl font-bold text-[#351c42] sm:text-2xl">Events you’re tracking</h2>
                <p class="mt-1 text-sm text-[#351c42]/60">Events where you registered <span class="font-semibold text-[#351c42]">Interested</span> — status updates from the office.</p>
            </div>
            <a href="<?php echo e(route('events.index')); ?>" class="text-sm font-semibold text-[#965995] hover:text-[#351c42]">Browse public events</a>
        </div>

        <?php if(session('event_interest_error')): ?>
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                <?php echo e(session('event_interest_error')); ?>

            </div>
        <?php endif; ?>

        <?php if($myEventInvites->isEmpty()): ?>
            <p class="rounded-2xl border border-dashed border-[#351c42]/20 bg-[#faf9fc] px-6 py-8 text-center text-sm font-semibold text-[#351c42]/70">
                You haven’t registered interest in any event yet. Browse the <a href="<?php echo e(route('events.index')); ?>" class="font-bold text-[#965995] underline-offset-2 hover:text-[#351c42] hover:underline">public events</a> page to find one.
            </p>
        <?php else: ?>
        <div class="space-y-6">
            <?php $__currentLoopData = $myEventInvites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $ev = $inv->event; ?>
                <?php if(!$ev) continue; ?>
                <?php echo $__env->make('member.partials.member-event-card', ['event' => $ev, 'mode' => 'tracking', 'invite' => $inv], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </section>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\partials\member-events-panel.blade.php ENDPATH**/ ?>