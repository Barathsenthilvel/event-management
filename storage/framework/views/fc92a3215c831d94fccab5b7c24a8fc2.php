
<?php
    $votedSet = collect($pollingDashboardVotedIds ?? []);
    $votesByPosition = collect($pollingDashboardVotes ?? []);
    $pollStartDate = $poll->polling_date?->format('Y-m-d');
    $pollEndDate = ($poll->polling_date_to ?? $poll->polling_date)?->format('Y-m-d');
    $start = $pollStartDate ? \Carbon\Carbon::parse($pollStartDate.' '.$poll->polling_from) : null;
    $end = $pollEndDate ? \Carbon\Carbon::parse($pollEndDate.' '.$poll->polling_to) : null;
    $open = $start && $end && now()->between($start, $end);
    $hasCandidates = $ppos->candidates->isNotEmpty();
    $votedThis = $votedSet->contains((int) $ppos->id);
    $myVote = $votesByPosition->get($ppos->id);
?>
<div class="rounded-xl border border-[#351c42]/10 bg-white p-4">
    <p class="text-sm font-extrabold text-[#351c42]"><?php echo e($ppos->position); ?></p>

    <?php if(!$hasCandidates): ?>
        <p class="mt-2 text-sm text-amber-800">Candidates are not listed for this position yet.</p>
    <?php else: ?>
        <ul class="mt-3 space-y-2">
            <?php $__currentLoopData = $ppos->candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isChosen = $votedThis && $myVote && (int) $myVote->candidate_user_id === (int) $cand->id;
                    $fillPct = $votedThis ? ($isChosen ? 100 : 0) : 0;
                ?>
                <li>
                    <?php if($votedThis): ?>
                        <div class="relative overflow-hidden rounded-2xl border border-[#c4b5d5]/70 bg-[#faf7fd]">
                            <div
                                class="absolute inset-y-0 left-0 rounded-2xl bg-gradient-to-r from-[#c9b6e0] via-[#d4c4e8] to-[#dfc9ee] transition-all duration-500"
                                style="width: <?php echo e($fillPct); ?>%"
                            ></div>
                            <div class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="truncate text-sm font-bold text-[#351c42]"><?php echo e($cand->name); ?></span>
                                    <?php if($isChosen): ?>
                                        <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-[#351c42]/30 bg-white text-[#351c42]" title="Your vote">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if($isChosen): ?>
                                    <span class="shrink-0 text-[10px] font-black uppercase tracking-wide text-[#351c42]/60">Your vote</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif(!$open): ?>
                        <div class="relative overflow-hidden rounded-2xl border border-[#351c42]/15 bg-[#faf9fc] opacity-70">
                            <div class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                <span class="truncate text-sm font-semibold text-[#351c42]/70"><?php echo e($cand->name); ?></span>
                                <span class="text-[10px] font-bold text-[#351c42]/45">Closed</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('member.pollings.vote', $poll)); ?>" class="block">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="position_id" value="<?php echo e($ppos->id); ?>">
                            <input type="hidden" name="candidate_user_id" value="<?php echo e($cand->id); ?>">
                            <button type="submit" class="group relative w-full overflow-hidden rounded-2xl border border-[#c4b5d5]/80 bg-white text-left transition hover:border-[#965995]/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995]/40">
                                <div class="absolute inset-y-0 left-0 w-0 rounded-2xl bg-gradient-to-r from-[#e8dff5] to-[#ddd0ec] transition-all duration-300 group-hover:w-[12%]"></div>
                                <span class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                    <span class="truncate text-sm font-bold text-[#351c42]"><?php echo e($cand->name); ?></span>
                                    <span class="shrink-0 rounded-full bg-[#351c42] px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide text-[#fddc6a]">Vote</span>
                                </span>
                            </button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\partials\dashboard-polling-position-block.blade.php ENDPATH**/ ?>