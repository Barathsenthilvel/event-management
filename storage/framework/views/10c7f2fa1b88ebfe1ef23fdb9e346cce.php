<?php $__env->startSection('title', 'Polling — GNAT Association'); ?>

<?php $__env->startSection('portal_main_id', 'member-pollings-main'); ?>

<?php $__env->startSection('content'); ?>
    <header class="scroll-mt-28 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Governance</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Polling</h1>
            <p class="mt-1 max-w-2xl text-sm text-[#351c42]/65">Vote during the scheduled window when candidates are set for each position.</p>
        </div>
        <a href="<?php echo e(route('member.dashboard')); ?>" class="shrink-0 text-sm font-semibold text-[#965995] hover:text-[#351c42]">← Back to dashboard</a>
    </header>

    <?php echo $__env->make('member.partials.member-pollings-panel', [
        'memberPollings' => $memberPollings,
        'pollingVotedPositionIds' => $pollingVotedPositionIds,
        'memberPollingVotes' => $memberPollingVotes ?? collect(),
        'pollingResultStats' => $pollingResultStats ?? [],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('member.layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\pollings.blade.php ENDPATH**/ ?>