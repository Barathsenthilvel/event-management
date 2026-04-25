<?php
    $isEdit = isset($item) && $item;
    $action = $isEdit ? route('admin.home-galleries.update', $item->id) : route('admin.home-galleries.store');
    $imageUrl = ($isEdit && !empty($item->image_path)) ? asset('storage/' . ltrim((string) $item->image_path, '/')) : '';
?>

<form action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data"
      class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-6">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <div class="space-y-5">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">
                    Title <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </label>
                <input type="text" name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"
                       required>
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Category <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                    <select name="category_key" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        <?php ($cat = old('category_key', $item->category_key ?? 'programs')); ?>
                        <option value="programs" <?php echo e($cat === 'programs' ? 'selected' : ''); ?>>Programs</option>
                        <option value="events" <?php echo e($cat === 'events' ? 'selected' : ''); ?>>Events</option>
                        <option value="community" <?php echo e($cat === 'community' ? 'selected' : ''); ?>>Community</option>
                    </select>
                    <?php $__errorArgs = ['category_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Layout <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                    <select name="layout_type" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        <?php ($layout = old('layout_type', $item->layout_type ?? 'cell')); ?>
                        <option value="hero" <?php echo e($layout === 'hero' ? 'selected' : ''); ?>>Hero</option>
                        <option value="wide" <?php echo e($layout === 'wide' ? 'selected' : ''); ?>>Wide</option>
                        <option value="banner" <?php echo e($layout === 'banner' ? 'selected' : ''); ?>>Banner</option>
                        <option value="cell" <?php echo e($layout === 'cell' ? 'selected' : ''); ?>>Cell</option>
                    </select>
                    <?php $__errorArgs = ['layout_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Eyebrow</label>
                <input type="text" name="eyebrow" value="<?php echo e(old('eyebrow', $item->eyebrow ?? '')); ?>"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                <?php $__errorArgs = ['eyebrow'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Alt text</label>
                <input type="text" name="alt_text" value="<?php echo e(old('alt_text', $item->alt_text ?? '')); ?>"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                <?php $__errorArgs = ['alt_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Sort order</label>
                    <input type="number" name="sort_order" min="0" value="<?php echo e(old('sort_order', $item->sort_order ?? 0)); ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Description text</label>
                <textarea name="description_text" rows="4"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"><?php echo e(old('description_text', $item->description_text ?? '')); ?></textarea>
                <?php $__errorArgs = ['description_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="space-y-5">
            <div class="border border-slate-200 rounded-2xl px-5 py-4">
                <p class="text-sm font-semibold text-slate-800 mb-3">Gallery Image</p>

                <?php if($imageUrl): ?>
                    <div class="mb-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <img src="<?php echo e($imageUrl); ?>" alt="" class="h-28 w-full rounded-lg object-cover border border-slate-100">
                    </div>
                <?php endif; ?>

                <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                    <div class="text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                            <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-slate-700">
                        Upload Image
                        <?php if(!$isEdit): ?><?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endif; ?>
                    </span>
                    <input type="file" name="image" class="hidden" accept="image/*" <?php if(!$isEdit): ?> required <?php endif; ?>>
                </label>
                <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $item->is_active ?? true) ? 'checked' : ''); ?>>
                Display this gallery item
            </label>
        </div>
    </div>

    <div class="mt-8 flex gap-3">
        <a href="<?php echo e(route('admin.home-galleries.index')); ?>"
           class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center border border-slate-200">
            Cancel
        </a>
        <button type="submit"
                class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
            <?php echo e($isEdit ? 'Update Gallery Item' : 'Create Gallery Item'); ?>

        </button>
    </div>
</form>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\home-galleries\_form.blade.php ENDPATH**/ ?>