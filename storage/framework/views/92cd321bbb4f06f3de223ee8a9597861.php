
<?php
    $size = 'h-8 w-8 sm:h-9 sm:w-9';
    $overlap = '-ml-2 sm:-ml-2.5';
    $registeredCount = max(0, (int) ($registeredCount ?? 0));
    $overflow = $registeredCount > 5 ? max(0, $registeredCount - 5) : 0;
    $faceUrls = [
        asset('images/facepile/1.jpg'),
        asset('images/facepile/2.jpg'),
        asset('images/facepile/3.jpg'),
        asset('images/facepile/4.jpg'),
        asset('images/facepile/5.jpg'),
    ];
?>
<div class="flex min-w-0 flex-1 items-center" <?php if($registeredCount > 0): ?> aria-label="<?php echo e($registeredCount); ?> registered" <?php else: ?> aria-hidden="true" <?php endif; ?>>
    <div class="flex min-w-0 items-center overflow-x-auto py-0.5 pl-0.5 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <?php $__currentLoopData = $faceUrls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span
                class="<?php echo e($i > 0 ? $overlap.' ' : ''); ?>relative inline-flex <?php echo e($size); ?> shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] ring-2 ring-[#351c42] shadow-sm"
                style="z-index: <?php echo e($i + 1); ?>"
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
            class="<?php echo e($overlap); ?> relative inline-flex <?php echo e($size); ?> shrink-0 items-center justify-center rounded-full border-2 border-[#2dd4bf] bg-gradient-to-br from-[#5c3560] to-[#351c42] text-xs font-bold text-[#fddc6a] ring-2 ring-[#351c42] shadow-sm sm:text-sm"
            style="z-index: 6"
        ><?php if($overflow > 0): ?>+<?php echo e($overflow); ?><?php else: ?>+<?php endif; ?></span>
    </div>
    <span class="ml-2 shrink-0 text-[10px] font-black uppercase tracking-wide text-white/85">
        <?php echo e($registeredCount); ?> profiles
    </span>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\home\partials\event-interested-facepile-static.blade.php ENDPATH**/ ?>