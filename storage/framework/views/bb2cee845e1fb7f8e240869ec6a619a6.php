<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events — GNAT Association</title>
    <?php echo $__env->make('home.partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
    <?php echo $__env->make('home.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="mx-auto max-w-7xl px-4 py-8 space-y-7">
        <?php if(session('success')): ?>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900" role="status"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900" role="status"><?php echo e(session('info')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-900" role="alert"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php if(session('event_interest_error')): ?>
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert"><?php echo e(session('event_interest_error')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-900" role="alert">
                <ul class="list-disc list-inside space-y-0.5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="rounded-3xl border border-[#351c42]/10 bg-white/85 backdrop-blur p-5 md:p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">Events</p>
                    <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">Browse Events</h1>
                    <p class="mt-1 text-sm text-[#351c42]/65">Search, filter by status, and preview event details.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('events.index')); ?>" class="rounded-2xl border border-[#351c42]/15 bg-white px-4 py-2 text-xs font-extrabold text-[#351c42] hover:bg-[#351c42]/5">Reset</a>
                </div>
            </div>

            <form method="GET" class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto] lg:items-center">
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#351c42]/35">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
                            <circle cx="11" cy="11" r="7" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="<?php echo e($q); ?>"
                        placeholder="Search events… (title, venue, description)"
                        class="w-full rounded-2xl border border-[#351c42]/15 bg-white pl-11 pr-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25"
                    >
                </div>

                <button type="submit" class="rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                    Search
                </button>

                <div class="lg:col-span-2 flex flex-wrap items-center gap-2 pt-1">
                    <input type="hidden" name="status" value="<?php echo e($status); ?>">
                    <?php
                        $tabs = [
                            'all' => 'All',
                            'upcoming' => 'Upcoming',
                            'live' => 'Live',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ];
                    ?>
                    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isOn = $status === $key;
                            $href = route('events.index', array_filter(['q' => $q, 'status' => $key]));
                        ?>
                        <a href="<?php echo e($href); ?>"
                           class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-extrabold border transition-all
                                <?php echo e($isOn ? 'bg-[#351c42] border-[#351c42] text-[#fddc6a] shadow-md shadow-[#351c42]/20' : 'bg-white border-[#351c42]/15 text-[#351c42]/75 hover:bg-[#351c42]/5 hover:text-[#351c42]'); ?>">
                            <span class="h-2 w-2 rounded-full <?php echo e($isOn ? 'bg-[#fddc6a]' : 'bg-[#965995]/40'); ?>"></span>
                            <?php echo e($label); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </form>
        </section>

        <?php if($events->count() === 0): ?>
            <section class="rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
                <p class="text-sm font-bold text-[#351c42]/80">No events found for this filter.</p>
            </section>
        <?php else: ?>
            <?php echo $__env->make('home.partials.event-accordion-list', [
                'events' => $events,
                'interestedEventIds' => $interestedEventIds ?? [],
                'guestInterestedEventIds' => $guestInterestedEventIds ?? [],
                'expandAll' => true,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <section class="mt-6 rounded-2xl border border-[#351c42]/10 bg-white p-4">
                <?php echo e($events->links()); ?>

            </section>
        <?php endif; ?>
    </main>

    <?php echo $__env->make('home.partials.event-interest-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.event-interest-success-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.donate-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.donate-payment-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/home/events.blade.php ENDPATH**/ ?>