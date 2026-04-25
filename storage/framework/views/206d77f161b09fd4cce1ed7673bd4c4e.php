<section id="events" class="relative scroll-mt-32 bg-white overflow-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex flex-col gap-3 min-[520px]:flex-row min-[520px]:items-start min-[520px]:justify-between min-[520px]:gap-6">
            <div class="min-w-0 max-w-xl">
                <div class="inline-flex items-center gap-2 text-sm font-semibold tracking-wide text-[#965995]">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#965995]"></span>
                    EVENTS
                </div>
                <h2 class="mt-3 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]">
                    Upcoming Events<br />
                    <span class="text-[#965995]">Don’t Miss Out</span>
                </h2>
            </div>
            <a href="<?php echo e(route('events.index')); ?>" class="shrink-0 self-start text-sm font-semibold text-[#965995] underline-offset-4 hover:text-[#351c42] hover:underline transition-colors min-[520px]:pt-8 sm:pt-10">
                View more
            </a>
        </div>

        <div class="mt-10">
            <?php if(!isset($homeEvents) || $homeEvents->isEmpty()): ?>
                <p class="rounded-2xl border border-dashed border-[#351c42]/20 bg-[#f8f6fa] px-6 py-10 text-center text-sm font-semibold text-[#351c42]/75">
                    No upcoming events right now. Check back soon or browse the full list.
                </p>
            <?php else: ?>
                <?php echo $__env->make('home.partials.event-accordion-list', [
                    'events' => $homeEvents,
                    'interestedEventIds' => $interestedEventIds ?? [],
                    'guestInterestedEventIds' => $guestInterestedEventIds ?? [],
                    'expandAll' => false,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\home\sections\events.blade.php ENDPATH**/ ?>