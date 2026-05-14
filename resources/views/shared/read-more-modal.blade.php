<div
    id="read-more-modal"
    class="fixed inset-0 z-[200] hidden items-center justify-center bg-[#351c42]/55 p-4 backdrop-blur-[2px]"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="read-more-modal-title"
>
    <div data-read-more-backdrop class="absolute inset-0 cursor-pointer" aria-hidden="true" title="Close"></div>
    <div class="relative flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-3xl border border-white/20 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 rounded-t-3xl border-b border-[#351c42]/10 bg-[#faf9fc] px-5 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('home') }}" class="hidden shrink-0 sm:inline-flex" aria-label="GNAT Association home" title="GNAT Association">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="GNAT Association"
                        class="h-10 w-auto max-w-[120px] object-contain"
                        width="120"
                        height="40"
                        loading="lazy"
                    />
                </a>
                <h3 id="read-more-modal-title" class="min-w-0 text-base font-extrabold text-[#351c42] sm:text-lg"></h3>
            </div>
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
        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">
            <dl id="read-more-modal-meta" class="mb-4 hidden space-y-2 rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-4 text-xs"></dl>
            <div id="read-more-modal-body" class="whitespace-pre-wrap break-words text-sm leading-relaxed text-[#351c42]/75"></div>
            <div id="read-more-modal-actions" class="mt-4 hidden flex flex-wrap items-center gap-2">
                <a
                    id="read-more-modal-document-link"
                    href="#"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hidden inline-flex min-h-[2.75rem] shrink-0 items-center gap-2 rounded-full border border-[#351c42]/25 bg-white px-4 py-2 text-sm font-extrabold text-[#351c42] transition hover:bg-[#f5f1f8]"
                >
                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#351c42]/10 text-[#351c42]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" aria-hidden="true">
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
                    class="click-btn click-btn--sm btn-style506 hidden shrink-0 text-left"
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
        <div id="read-more-modal-job-actions" class="hidden shrink-0 rounded-b-3xl border-t border-[#351c42]/10 bg-[#faf9fc] px-5 py-4 sm:px-6">
            <p class="mb-3 text-[11px] font-black uppercase tracking-wide text-[#351c42]/45">Actions</p>
            <div class="flex flex-wrap items-stretch gap-2 sm:items-center">
                <form id="read-more-modal-job-save-form" method="POST" action="#" class="inline-flex shrink-0">
                    @csrf
                    <button
                        type="submit"
                        id="read-more-modal-job-save-add"
                        class="inline-flex h-full min-h-[2.75rem] items-center justify-center rounded-xl border border-[#351c42]/15 bg-white px-4 py-2.5 text-sm font-extrabold text-[#351c42]/80 transition hover:border-[#351c42]/30 hover:bg-[#351c42]/5"
                    >
                        <svg class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M6 3h12a1 1 0 011 1v17l-7-4-7 4V4a1 1 0 011-1z" />
                        </svg>
                        Save job
                    </button>
                    <button
                        type="submit"
                        id="read-more-modal-job-save-remove"
                        class="hidden inline-flex h-full min-h-[2.75rem] items-center justify-center rounded-xl border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-extrabold text-amber-800 transition hover:bg-amber-100"
                    >
                        <svg class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M6 3h12a1 1 0 011 1v17l-7-4-7 4V4a1 1 0 011-1z" />
                        </svg>
                        Remove from saved
                    </button>
                </form>
                <button
                    type="button"
                    id="read-more-modal-job-apply-btn"
                    class="job-apply-open-btn inline-flex min-h-[2.75rem] flex-1 items-center justify-center rounded-xl bg-[#351c42] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#291331] sm:min-w-[10rem]"
                >
                    Apply now
                </button>
                <button
                    type="button"
                    id="read-more-modal-job-applied-badge"
                    disabled
                    class="hidden inline-flex min-h-[2.75rem] flex-1 cursor-not-allowed items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-extrabold text-emerald-800 sm:min-w-[10rem]"
                >
                    Applied
                </button>
            </div>
        </div>
    </div>
</div>

