<div
    id="read-more-modal"
    class="fixed inset-0 z-[200] hidden items-center justify-center bg-[#351c42]/55 p-4 backdrop-blur-[2px]"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="read-more-modal-title"
>
    <div data-read-more-backdrop class="absolute inset-0 cursor-pointer" aria-hidden="true" title="Close"></div>
    <div class="relative w-full max-w-2xl rounded-3xl border border-white/20 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-[#351c42]/10 bg-[#faf9fc] px-5 py-4">
            <h3 id="read-more-modal-title" class="min-w-0 text-base font-extrabold text-[#351c42] sm:text-lg"></h3>
            <button
                type="button"
                data-close-read-more
                class="inline-flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-2xl text-[#351c42]/55 transition hover:bg-[#351c42]/5 hover:text-[#351c42]"
                aria-label="Close"
                title="Close"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6l-12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-5 py-5 sm:px-6">
            <dl id="read-more-modal-meta" class="mb-4 hidden space-y-2 rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-4 text-xs"></dl>
            <div id="read-more-modal-body" class="whitespace-pre-wrap break-words text-sm leading-relaxed text-[#351c42]/75"></div>
            <div id="read-more-modal-actions" class="mt-4 hidden flex-wrap items-center gap-2">
                <a
                    id="read-more-modal-document-link"
                    href="#"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hidden inline-flex items-center gap-2 rounded-full border border-[#351c42]/25 bg-white px-5 py-2.5 text-sm font-extrabold text-[#351c42] transition hover:bg-[#f5f1f8]"
                >
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#351c42]/10 text-[#351c42]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                            <path d="M12 5v10m0 0l-4-4m4 4l4-4M5 19h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                    <span>Download file</span>
                </a>
                <button
                    type="button"
                    id="read-more-modal-donate-btn"
                    data-open-donate-modal
                    data-read-more-donate
                    class="click-btn click-btn--sm btn-style506 hidden text-left"
                >
                    <span class="click-btn__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                            <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                    <span class="click-btn__label">Donate Now</span>
                </button>
            </div>
        </div>
        <div class="border-t border-[#351c42]/10 bg-white px-5 py-4 sm:px-6">
            <button
                type="button"
                data-close-read-more
                class="w-full cursor-pointer rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:bg-[#4a2660]"
                title="Close"
            >
                Close
            </button>
        </div>
    </div>
</div>

