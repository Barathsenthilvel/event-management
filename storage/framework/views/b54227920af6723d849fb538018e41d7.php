
<?php
    $maxFaces = 5;
    $interestUsers = collect();
    if ($event->relationLoaded('invites')) {
        $interestUsers = $event->invites
            ->filter(fn ($inv) => $inv->participation_status === 'interested' && $inv->user)
            ->map(fn ($inv) => $inv->user)
            ->unique('id')
            ->values();
    }
    $totalInterested = max((int) ($event->interested_count ?? 0), $interestUsers->count());
    $stackUsers = $interestUsers->take($maxFaces);
    $overflowCount = max(0, $totalInterested - $stackUsers->count());
    $initials = static function (\App\Models\User $u): string {
        $fn = trim((string) $u->first_name);
        $ln = trim((string) $u->last_name);
        if ($fn !== '' || $ln !== '') {
            return strtoupper(\Illuminate\Support\Str::substr($fn, 0, 1).\Illuminate\Support\Str::substr($ln, 0, 1));
        }
        $name = trim((string) $u->name);

        return $name !== '' ? strtoupper(\Illuminate\Support\Str::substr($name, 0, 2)) : '?';
    };
    $ring = 'relative inline-flex shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] bg-[#4a2660] text-[10px] font-extrabold text-white shadow-sm ring-2 ring-[#351c42]';
    $size = 'h-8 w-8 sm:h-9 sm:w-9';
    $overlap = '-ml-2 sm:-ml-2.5';
    $placeholderFaces = [
        asset('images/facepile/1.jpg'),
        asset('images/facepile/2.jpg'),
        asset('images/facepile/3.jpg'),
        asset('images/facepile/4.jpg'),
        asset('images/facepile/5.jpg'),
    ];
?>
<div class="flex min-w-0 flex-1 items-center" aria-label="Members interested in this event">
    <div class="flex min-w-0 items-center overflow-x-auto py-0.5 pl-0.5 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <?php if($stackUsers->isNotEmpty()): ?>
            <?php $__currentLoopData = $stackUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span
                    class="<?php echo e($idx > 0 ? $overlap.' ' : ''); ?><?php echo e($ring); ?> <?php echo e($size); ?>"
                    style="z-index: <?php echo e($idx + 1); ?>"
                >
                    <?php if($u->passport_photo_path): ?>
                        <img
                            src="<?php echo e(asset('storage/'.$u->passport_photo_path)); ?>"
                            alt=""
                            class="h-full w-full object-cover"
                        />
                    <?php else: ?>
                        <span class="flex h-full w-full items-center justify-center bg-[#965995]/50"><?php echo e($initials($u)); ?></span>
                    <?php endif; ?>
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <span
                class="<?php echo e($overlap); ?> <?php echo e($ring); ?> <?php echo e($size); ?> items-center justify-center bg-gradient-to-br from-[#5c3560] to-[#351c42] text-sm font-bold text-[#fddc6a]"
                style="z-index: <?php echo e($stackUsers->count() + 1); ?>"
            >
                <?php if($overflowCount > 0): ?>
                    +<?php echo e($overflowCount); ?>

                <?php else: ?>
                    +
                <?php endif; ?>
            </span>
        <?php else: ?>
            <?php $__currentLoopData = $placeholderFaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span
                    class="<?php echo e($i > 0 ? $overlap.' ' : ''); ?>relative inline-flex <?php echo e($size); ?> shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] ring-2 ring-[#351c42] shadow-sm"
                    style="z-index: <?php echo e($i + 1); ?>"
                    aria-hidden="true"
                >
                    <img
                        src="<?php echo e($src); ?>"
                        alt=""
                        width="36"
                        height="36"
                        class="h-full w-full object-cover"
                        loading="lazy"
                        decoding="async"
                    />
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <span
                class="<?php echo e($overlap); ?> inline-flex <?php echo e($size); ?> items-center justify-center rounded-full border-2 border-[#2dd4bf] bg-gradient-to-br from-[#5c3560] to-[#351c42] text-sm font-bold text-[#fddc6a] ring-2 ring-[#351c42]"
                style="z-index: 6"
                aria-hidden="true"
            >+</span>
        <?php endif; ?>
    </div>
    <span class="ml-2 shrink-0 text-[10px] font-black uppercase tracking-wide text-white/85">
        <?php echo e($totalInterested); ?> profiles
    </span>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\partials\member-event-interested-stack.blade.php ENDPATH**/ ?>