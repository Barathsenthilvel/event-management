
<?php
    $expandAll = filter_var($expandAll ?? false, FILTER_VALIDATE_BOOLEAN);
?>
<div
    class="grid gap-4"
    <?php if($expandAll): ?>
        data-events-expand-all="true"
    <?php else: ?>
        id="home-events-accordion"
    <?php endif; ?>
>
    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $cover = $event->cover_image_path ? asset('storage/' . $event->cover_image_path) : asset('images/event1.jpg');
            $sortedDates = $event->dates->sortBy('event_date')->values();
            $firstDate = $sortedDates->first();
            $lastDate = $sortedDates->last();
            $summaryDate = $firstDate?->event_date?->format('d M Y') ?? 'TBA';
            if ($firstDate && $lastDate && $firstDate->event_date?->toDateString() !== $lastDate->event_date?->toDateString()) {
                $summaryDate = $firstDate->event_date?->format('d M Y') . ' - ' . $lastDate->event_date?->format('d M Y');
            }
            $day = $firstDate?->event_date?->format('d') ?? '—';
            $month = strtoupper((string) ($firstDate?->event_date?->format('M') ?? 'TBA'));
            $timeSlots = $sortedDates
                ->map(function ($d) {
                    $start = $d->start_time ? \Illuminate\Support\Carbon::parse($d->start_time)->format('h:i A') : null;
                    $end = $d->end_time ? \Illuminate\Support\Carbon::parse($d->end_time)->format('h:i A') : null;
                    return $start && $end ? ($start . ' - ' . $end) : ($start ?: ($end ?: 'Time TBA'));
                })
                ->filter()
                ->unique()
                ->values();
            $timeRange = $timeSlots->count() > 1 ? 'Multiple time slots' : ($timeSlots->first() ?? 'Time TBA');
            $organizer = $event->creator?->name ?? 'GNAT Team';
            $desc = $event->description ?: 'Join us for this GNAT event. More details will be shared with registered members.';
            $seatLimited = ($event->seat_mode ?? '') === 'limited';
            $seatFilled = (int) ($event->invites_count ?? 0);
            $seatCap = max(0, (int) ($event->seat_limit ?? 0));
            $seatsFull = $seatLimited && $seatCap > 0 && $seatFilled >= $seatCap;
            $isAdminEvent = filled($event->created_by_admin_id);
            $memberInterestReturn = request()->fullUrl();
        ?>
        <div class="rounded-2xl bg-white border border-[#351c42]/10 overflow-hidden" data-events-accordion-item <?php if($expandAll): ?> data-events-open="true" <?php endif; ?>>
            <button
                type="button"
                class="w-full text-left px-6 py-4 flex items-start justify-between gap-6 <?php echo e($expandAll ? 'cursor-default' : ''); ?>"
                data-events-accordion-trigger
                aria-expanded="<?php echo e($expandAll ? 'true' : 'false'); ?>"
            >
                <div class="min-w-0" data-events-header-summary>
                    <div class="flex items-center gap-3 text-xs font-semibold text-[#351c42]/70" data-events-header-time>
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#351c42]"></span>
                            <?php echo e($summaryDate); ?>

                        </span>
                        <span class="h-1 w-1 rounded-full bg-[#351c42]/30"></span>
                        <span><?php echo e($timeRange); ?></span>
                    </div>
                    <div class="mt-2 text-sm md:text-base font-bold text-[#351c42] truncate" data-events-header-title>
                        <?php echo e($event->title); ?>

                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-full bg-[#351c42] text-white flex items-center justify-center flex-shrink-0" data-events-trigger-icon aria-hidden="true">
                        <svg data-events-plus class="w-4 h-4 <?php echo e($expandAll ? 'hidden' : ''); ?>" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 1.5V14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <svg data-events-minus class="w-4 h-4 <?php echo e($expandAll ? '' : 'hidden'); ?>" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </button>

            <div class="px-6 pb-6 pt-0 <?php echo e($expandAll ? '' : 'hidden'); ?>" data-events-accordion-panel>
                <div class="mt-6 grid gap-8 md:grid-cols-[340px_1fr] items-stretch">
                    <div class="relative rounded-2xl overflow-hidden border border-[#351c42]/10 bg-[#f6f3e9]">
                        <img src="<?php echo e($cover); ?>" alt="<?php echo e($event->title); ?>" class="h-56 md:h-72 w-full object-cover" />

                        <div class="absolute left-4 top-4 rounded-full bg-[#fddc6a] px-3 py-2 text-center shadow-sm">
                            <div class="text-lg font-extrabold leading-none text-[#351c42]"><?php echo e($day); ?></div>
                            <div class="mt-0.5 text-[10px] font-extrabold tracking-widest leading-none text-[#965995] bg-white/70 rounded px-2 py-0.5 inline-block"><?php echo e($month); ?></div>
                        </div>
                    </div>

                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between gap-6 w-full">
                            <div class="inline-flex items-center gap-3 rounded-full border border-[#351c42]/10 bg-white/70 px-4 py-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="text-xs font-semibold text-[#351c42]/70"><?php echo e($timeRange); ?></span>
                            </div>
                            <div class="ml-auto shrink-0 rounded-full border border-[#351c42]/15 bg-white px-3 py-1.5 text-right shadow-sm min-w-[5.5rem]" aria-label="<?php echo e($seatLimited ? 'Limited seats' : 'Unlimited seats'); ?>">
                                <?php if($seatLimited): ?>
                                    <?php if($seatsFull): ?>
                                        <div class="text-[9px] font-black uppercase tracking-wider text-rose-600 leading-none">Registration</div>
                                        <div class="mt-0.5 text-[10px] font-extrabold uppercase text-rose-700">Closed</div>
                                    <?php else: ?>
                                        <div class="text-[9px] font-black uppercase tracking-wider text-[#351c42]/55 leading-none">Limited</div>
                                        <div class="mt-0.5 text-xs font-extrabold tabular-nums text-[#351c42]"><?php echo e($seatFilled); ?> / <?php echo e($seatCap > 0 ? $seatCap : '—'); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-[10px] font-black uppercase tracking-wide text-[#351c42] leading-tight py-0.5">Unlimited</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-3 text-sm md:text-base font-bold text-[#351c42]">
                            <?php echo e($event->title); ?>

                        </div>
                        <?php if($sortedDates->isNotEmpty()): ?>
                            <div class="mt-3 rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-3">
                                <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#965995]">Event schedule</p>
                                <ul class="mt-2 space-y-1.5">
                                    <?php $__currentLoopData = $sortedDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $slotStart = $dateRow->start_time ? \Illuminate\Support\Carbon::parse($dateRow->start_time)->format('h:i A') : null;
                                            $slotEnd = $dateRow->end_time ? \Illuminate\Support\Carbon::parse($dateRow->end_time)->format('h:i A') : null;
                                            $slotTime = $slotStart && $slotEnd ? ($slotStart . ' - ' . $slotEnd) : ($slotStart ?: ($slotEnd ?: 'Time TBA'));
                                        ?>
                                        <li class="flex flex-wrap items-center justify-between gap-2 text-xs font-semibold text-[#351c42]/80">
                                            <span><?php echo e($dateRow->event_date?->format('d M Y') ?? 'TBA'); ?></span>
                                            <span><?php echo e($slotTime); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <p class="mt-3 text-sm text-[#351c42]/80 leading-6">
                            <?php echo e($desc); ?>

                        </p>
                        <div class="mt-auto grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-[#f6f3e9] p-4 border border-[#351c42]/10">
                                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    Organizer
                                </div>
                                <div class="mt-2 font-bold text-[#351c42] text-sm"><?php echo e($organizer); ?></div>
                            </div>
                            <div class="rounded-2xl bg-[#f6f3e9] p-4 border border-[#351c42]/10">
                                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    Venue
                                </div>
                                <div class="mt-2 font-bold text-[#351c42] text-sm"><?php echo e($event->venue ?: 'Venue not specified'); ?></div>
                            </div>
                        </div>

                        <?php if(in_array($event->id, $interestedEventIds ?? [], true) || in_array($event->id, $guestInterestedEventIds ?? [], true)): ?>
                            <?php if($isAdminEvent): ?>
                                <div class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-5">
                                    <?php echo $__env->make('home.partials.event-interested-facepile-static', ['registeredCount' => $seatFilled], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <span class="min-w-0 shrink-0 text-sm font-extrabold text-[#fddc6a] sm:ml-auto">
                                        Interest registered
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="mt-6">
                                    <span class="inline-flex w-full items-center justify-center rounded-2xl border border-[#351c42]/20 bg-[#f6f3e9] px-5 py-3 text-sm font-extrabold text-[#351c42]/80 cursor-default">
                                        Interest registered
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php elseif($isAdminEvent): ?>
                            <?php if($seatsFull): ?>
                                <div class="mt-6 space-y-3 rounded-2xl border border-rose-200/80 bg-rose-50/90 px-4 py-4 sm:px-5">
                                    <p class="text-center text-sm font-extrabold text-rose-900">Registration closed</p>
                                    <p class="text-center text-xs font-semibold text-rose-800/90">This event has reached its seat limit (<?php echo e($seatFilled); ?> / <?php echo e($seatCap); ?>).</p>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center gap-3 rounded-2xl border border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-5">
                                    <?php echo $__env->make('home.partials.event-interested-facepile-static', ['registeredCount' => $seatFilled], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            <?php else: ?>
                                <div class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-5">
                                    <?php echo $__env->make('home.partials.event-interested-facepile-static', ['registeredCount' => $seatFilled], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <div class="flex min-w-0 flex-wrap items-center gap-2 sm:ml-auto">
                                        <?php if(auth()->guard()->check()): ?>
                                            <form
                                                method="POST"
                                                action="<?php echo e(route('member.events.interest', $event)); ?>"
                                                id="home-event-interest-form-<?php echo e($event->id); ?>"
                                                class="flex flex-wrap items-center justify-end"
                                                onsubmit="this.querySelector('button[type=submit]')?.setAttribute('disabled','disabled')"
                                            >
                                                <?php echo csrf_field(); ?>
                                                <button
                                                    type="submit"
                                                    class="inline-flex min-h-[2.1rem] items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#351c42] shadow-sm transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#351c42] disabled:cursor-not-allowed disabled:opacity-60"
                                                >
                                                    Interested
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button
                                                type="button"
                                                class="interest-open-btn inline-flex min-h-[2.1rem] items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#351c42] shadow-sm transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#351c42]"
                                                data-interest-url="<?php echo e(route('events.interest', $event)); ?>"
                                            >
                                                Interested
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="mt-6">
                                <?php if($seatsFull): ?>
                                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-center text-sm font-extrabold text-rose-900">
                                        Registration closed — seat limit reached (<?php echo e($seatFilled); ?> / <?php echo e($seatCap); ?>).
                                    </div>
                                <?php else: ?>
                                    <?php if(auth()->guard()->check()): ?>
                                        <form method="POST" action="<?php echo e(route('member.events.interest', $event)); ?>" class="w-full" onsubmit="this.querySelector('button[type=submit]')?.setAttribute('disabled','disabled')">
                                            <?php echo csrf_field(); ?>
                                            <button
                                                type="submit"
                                                class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-5 py-2.5 text-xs font-extrabold tracking-wide text-[#fddc6a] shadow-md shadow-[#351c42]/15 hover:brightness-105 transition-colors disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                Interested
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button
                                            type="button"
                                            class="interest-open-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-5 py-2.5 text-xs font-extrabold tracking-wide text-[#fddc6a] shadow-md shadow-[#351c42]/15 hover:brightness-105 transition-colors"
                                            data-interest-url="<?php echo e(route('events.interest', $event)); ?>"
                                        >
                                            Interested
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\home\partials\event-accordion-list.blade.php ENDPATH**/ ?>