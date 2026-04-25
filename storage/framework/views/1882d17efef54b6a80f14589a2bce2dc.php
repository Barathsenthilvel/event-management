<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member | Create Account</title>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; }
        .fluid-bg { background: linear-gradient(120deg, #F0F4F8 0%, #E2E8F0 50%, #F8FAFC 100%); position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; }
        .light-glass-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(25px); border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 20px 50px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 overflow-hidden">
    <div class="fluid-bg"></div>
    <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-indigo-200/50 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-blue-200/50 rounded-full blur-[120px]"></div>

    <div class="w-full max-w-[1100px] light-glass-panel rounded-[48px] overflow-hidden">
        <div class="p-10 md:p-14 bg-white/40">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
                <span class="text-slate-900 font-bold text-xl tracking-tighter">MEMBER<span class="text-slate-500 font-light italic">.PORTAL</span></span>
            </div>

            <div class="flex items-end justify-between mb-8">
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Create Account</h1>
                <a href="<?php echo e(route('member.login')); ?>" class="text-sm font-bold text-slate-500 hover:text-indigo-600 underline underline-offset-4">Go back to Login?</a>
            </div>

            <form method="POST" action="<?php echo e(route('member.register.store')); ?>" class="space-y-8">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">First Name <span class="text-rose-500">*</span></label>
                        <input name="first_name" value="<?php echo e(old('first_name')); ?>" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                        <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-rose-600 font-semibold"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Last Name <span class="text-rose-500">*</span></label>
                        <input name="last_name" value="<?php echo e(old('last_name')); ?>" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                        <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-rose-600 font-semibold"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-rose-600 font-semibold"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Mobile No <span class="text-rose-500">*</span></label>
                        <div class="flex w-full overflow-hidden rounded-2xl border border-slate-200 bg-white/80 shadow-sm transition-all focus-within:border-indigo-500/50 focus-within:ring-4 focus-within:ring-indigo-500/5">
                            <span class="flex shrink-0 select-none items-center border-r border-slate-200 bg-slate-50/90 px-3 py-4 text-sm font-bold text-slate-600" aria-hidden="true">+91</span>
                            <input name="mobile" type="tel" inputmode="numeric" value="<?php echo e(old('mobile')); ?>" required
                                class="min-w-0 flex-1 border-0 bg-transparent px-4 py-4 text-slate-900 placeholder-slate-400 outline-none"
                                placeholder="10-digit mobile number" pattern="[0-9]{10}" minlength="10" maxlength="10" title="Enter exactly 10 digits">
                        </div>
                        <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-rose-600 font-semibold"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Password <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" required data-password-input
                                class="w-full bg-white/80 border border-slate-200 rounded-2xl py-4 pl-5 pr-14 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                placeholder="Enter">
                            <button type="button" class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600" data-password-toggle aria-label="Show password" aria-pressed="false">
                                <svg class="h-5 w-5" data-icon-show fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="hidden h-5 w-5" data-icon-hide fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-rose-600 font-semibold"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Confirm Password <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" required data-password-input
                                class="w-full bg-white/80 border border-slate-200 rounded-2xl py-4 pl-5 pr-14 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                placeholder="Enter">
                            <button type="button" class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600" data-password-toggle aria-label="Show password" aria-pressed="false">
                                <svg class="h-5 w-5" data-icon-show fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="hidden h-5 w-5" data-icon-hide fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 pt-4">
                    <a href="<?php echo e(route('member.login')); ?>"
                        class="w-full md:w-52 text-center py-4 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl font-bold transition-colors">Cancels</a>
                    <button type="submit"
                        class="w-full md:w-60 py-4 bg-slate-900 hover:bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-slate-200 hover:shadow-indigo-200 transition-all">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelectorAll("[data-password-toggle]").forEach((btn) => {
            const wrap = btn.closest(".relative");
            const input = wrap?.querySelector("[data-password-input]");
            const iconShow = btn.querySelector("[data-icon-show]");
            const iconHide = btn.querySelector("[data-icon-hide]");
            if (!input) return;
            btn.addEventListener("click", () => {
                const isHidden = input.type === "password";
                input.type = isHidden ? "text" : "password";
                btn.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
                btn.setAttribute("aria-pressed", isHidden ? "true" : "false");
                iconShow.classList.toggle("hidden", isHidden);
                iconHide.classList.toggle("hidden", !isHidden);
            });
        });
    </script>
</body>
</html>

<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\auth\register.blade.php ENDPATH**/ ?>