<div
    id="read-more-modal"
    class="fixed inset-0 z-[200] hidden items-center justify-center bg-[#351c42]/55 p-4 backdrop-blur-[2px]"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="read-more-modal-title"
>
    <div data-read-more-backdrop class="absolute inset-0" aria-hidden="true"></div>
    <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl border border-white/20 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-[#351c42]/10 bg-[#faf9fc] px-5 py-4">
            <h3 id="read-more-modal-title" class="min-w-0 text-base font-extrabold text-[#351c42] sm:text-lg"></h3>
            <button
                type="button"
                data-close-read-more
                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-[#351c42]/55 transition hover:bg-[#351c42]/5 hover:text-[#351c42]"
                aria-label="Close"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6l-12 12"/>
                </svg>
            </button>
        </div>
        <div class="max-h-[70vh] overflow-y-auto px-5 py-5 sm:px-6">
            <dl id="read-more-modal-meta" class="mb-4 hidden space-y-2 rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-4 text-xs"></dl>
            <div id="read-more-modal-body" class="whitespace-pre-wrap text-sm leading-relaxed text-[#351c42]/75"></div>
        </div>
        <div class="border-t border-[#351c42]/10 bg-white px-5 py-4 sm:px-6">
            <button
                type="button"
                data-close-read-more
                class="w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:bg-[#4a2660]"
            >
                Close
            </button>
        </div>
    </div>
</div>

<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\shared\read-more-modal.blade.php ENDPATH**/ ?>