
<div id="donate-payment-success-modal"
     class="fixed inset-0 z-[210] hidden flex items-center justify-center p-4 sm:p-6"
     role="dialog"
     aria-modal="true"
     aria-labelledby="donate-payment-success-title"
     aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md" data-close-donate-payment-success tabindex="-1"></div>
    <div class="relative z-10 w-full max-w-[440px] rounded-[28px] border border-[#fddc6a]/25 shadow-[0_24px_80px_-12px_rgba(0,0,0,0.55)] overflow-hidden bg-gradient-to-b from-[#2d1b36] via-[#26152f] to-[#1a0f22]">
        <div class="p-6 sm:p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#fcd34d]/15 border border-[#fcd34d]/40 text-[#fcd34d]" aria-hidden="true">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <p class="mt-5 text-[11px] font-black uppercase tracking-[0.2em] text-[#fddc6a]/90">GNAT Association</p>
            <h2 id="donate-payment-success-title" class="mt-2 text-xl sm:text-2xl font-extrabold text-white tracking-tight">Thank you!</h2>
            <p id="donate-payment-success-message" class="mt-2 text-sm text-white/75 leading-relaxed"></p>
            <p id="donate-payment-success-amount" class="mt-4 text-2xl font-extrabold text-[#fcd34d] tabular-nums"></p>
            <p id="donate-payment-success-payment-id" class="mt-3 text-[11px] font-mono text-white/45 break-all"></p>
            <button type="button"
                    data-close-donate-payment-success
                    class="mt-8 w-full rounded-2xl bg-[#fcd34d] py-3.5 text-sm font-extrabold text-[#351c42] hover:bg-[#fde68a] transition-colors shadow-lg shadow-black/20">
                Close
            </button>
        </div>
    </div>
</div>

<div id="donate-payment-error-modal"
     class="fixed inset-0 z-[210] hidden flex items-center justify-center p-4 sm:p-6"
     role="dialog"
     aria-modal="true"
     aria-labelledby="donate-payment-error-title"
     aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md" data-close-donate-payment-error tabindex="-1"></div>
    <div class="relative z-10 w-full max-w-[420px] rounded-[28px] border border-white/15 shadow-[0_24px_80px_-12px_rgba(0,0,0,0.55)] overflow-hidden bg-gradient-to-b from-[#3d1f2a] via-[#2a1520] to-[#1a0f14]">
        <div class="p-6 sm:p-8">
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-rose-300/90">Payment</p>
            <h2 id="donate-payment-error-title" class="mt-2 text-lg font-extrabold text-white">Something went wrong</h2>
            <p id="donate-payment-error-message" class="mt-2 text-sm text-white/70 leading-relaxed"></p>
            <p id="donate-payment-error-detail" class="mt-3 text-xs text-white/50 whitespace-pre-wrap font-medium hidden"></p>
            <button type="button"
                    data-close-donate-payment-error
                    class="mt-6 w-full rounded-2xl bg-white/10 border border-white/15 py-3.5 text-sm font-extrabold text-white hover:bg-white/15 transition-colors">
                OK
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/home/partials/donate-payment-modals.blade.php ENDPATH**/ ?>