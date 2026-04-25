<a href="<?php echo e(url('/')); ?>#home" class="back-to-top-floating" aria-label="Back to top">
    <span class="back-to-top-floating__bar" aria-hidden="true"></span>
    <span class="back-to-top-floating__text">Back to top</span>
</a>

<div
    id="welcome-bottom-card"
    class="welcome-bottom-card"
    role="region"
    aria-label="Welcome message"
    aria-hidden="true"
    hidden
>
    <div class="relative overflow-hidden rounded-2xl border border-[#351c42]/12 bg-white shadow-[0_12px_40px_rgba(53,28,66,0.18)] ring-1 ring-black/5">
        <div class="absolute left-0 top-0 h-full w-1 bg-gradient-to-b from-[#965995] to-[#351c42]" aria-hidden="true"></div>
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-[#fddc6a]/35 blur-2xl" aria-hidden="true"></div>
        <div class="relative pl-5 pr-4 py-4 sm:pl-6 sm:pr-5 sm:py-5">
            <button
                type="button"
                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full text-[#351c42]/50 transition hover:bg-[#351c42]/8 hover:text-[#351c42]"
                aria-label="Dismiss welcome message"
                data-welcome-dismiss
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Hello there</p>
            <p class="mt-1.5 text-lg font-extrabold leading-snug text-[#351c42]">Welcome to GNAT Association</p>
            <p class="mt-2 text-sm leading-relaxed text-[#351c42]/75">
                Thanks for visiting—explore our programs, see upcoming events, and join us in turning kindness into lasting community impact.
            </p>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('donations.index')); ?>" class="inline-flex items-center gap-1.5 rounded-full bg-[#351c42] px-4 py-2 text-xs font-bold text-[#fddc6a] transition hover:bg-[#2a1533]">
                    Give today
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="<?php echo e(url('/')); ?>#about2" class="text-xs font-semibold text-[#965995] underline-offset-2 hover:text-[#351c42] hover:underline">
                    Learn about us
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/home/partials/floating.blade.php ENDPATH**/ ?>