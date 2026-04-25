<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="Pagination Navigation">
        <div class="flex gap-1">
            
            <?php if($paginator->onFirstPage()): ?>
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 text-xs font-bold cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>"
                    onclick="event.preventDefault(); if(window.loadPageWithLoader) { window.loadPageWithLoader('<?php echo e($paginator->previousPageUrl()); ?>'); } else { window.location.href = '<?php echo e($paginator->previousPageUrl()); ?>'; } return false;"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            <?php endif; ?>

            
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(is_string($element)): ?>
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors">...</span>
                <?php endif; ?>

                
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-600 text-white text-xs font-bold shadow-lg shadow-indigo-200"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>"
                                onclick="event.preventDefault(); if(window.loadPageWithLoader) { window.loadPageWithLoader('<?php echo e($url); ?>'); } else { window.location.href = '<?php echo e($url); ?>'; } return false;"
                                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>"
                    onclick="event.preventDefault(); if(window.loadPageWithLoader) { window.loadPageWithLoader('<?php echo e($paginator->nextPageUrl()); ?>'); } else { window.location.href = '<?php echo e($paginator->nextPageUrl()); ?>'; } return false;"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            <?php else: ?>
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 text-xs font-bold cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>

<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\pagination\simple-tailwind.blade.php ENDPATH**/ ?>