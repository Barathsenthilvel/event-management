{{-- Dark plum + yellow donation modal — opened via [data-open-donate-modal] --}}
@php
    $dm = $donate ?? config('homepage.donate', []);
@endphp
<div id="donate-modal"
     class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4 sm:p-6"
     role="dialog"
     aria-modal="true"
     aria-labelledby="donate-modal-title"
     aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity" data-close-donate-modal tabindex="-1"></div>
    <div class="relative z-10 w-full max-w-[520px] max-h-[min(92vh,720px)] overflow-y-auto rounded-[28px] border border-[#fddc6a]/20 shadow-[0_24px_80px_-12px_rgba(0,0,0,0.55)] bg-gradient-to-b from-[#2d1b36] via-[#26152f] to-[#1a0f22] p-6 sm:p-8">
        <button type="button"
                class="absolute right-4 top-4 z-10 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/15 bg-white/5 text-white/90 hover:bg-white/10 hover:text-white transition-colors"
                data-close-donate-modal
                aria-label="Close donation dialog">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6L6 18M6 6l12 12" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="pr-10">
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-[#fddc6a]/90">GNAT Donation</p>
            <h2 id="donate-modal-title" class="mt-1 text-xl sm:text-2xl font-extrabold text-white tracking-tight">Give &amp; change a life</h2>
            <p class="mt-2 text-sm text-white/65 leading-relaxed">Pick an amount or enter your own. Your support funds real programs in education, health, and community.</p>
        </div>

        <div id="modal-donate-amounts" class="mt-7 space-y-5">
            <div class="flex flex-col gap-3">
                <span class="text-sm font-bold text-white/95">Choose amount:</span>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($dm['amounts'] ?? [10, 25, 50, 100, 250] as $amt)
                        <button
                            type="button"
                            data-donate-amt="{{ $amt }}"
                            class="donate-amt-btn rounded-full bg-white/[0.07] hover:bg-white/15 px-4 py-2.5 text-sm font-bold border border-white/20 text-white transition-colors {{ (int) $amt === (int) ($dm['default_amount'] ?? 100) ? 'is-selected' : '' }}"
                        >₹{{ $amt }}</button>
                    @endforeach
                    <button type="button" data-donate-custom class="rounded-full border-2 border-[#fcd34d] text-[#fcd34d] px-4 py-2 text-sm font-bold inline-flex items-center gap-2 hover:bg-[#fcd34d]/10 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M9 10h6M16 14h-5"/>
                        </svg>
                        Custom Amount
                    </button>
                </div>
            </div>
            <div>
                <div class="h-2.5 rounded-full bg-black/40 overflow-hidden ring-1 ring-white/5">
                    <div class="donate-progress-bar h-full rounded-full bg-[#fcd34d] transition-all duration-500 shadow-[0_0_12px_rgba(252,211,77,0.35)]"
                         style="width: {{ (int) ($dm['bar_percent_demo'] ?? 52) }}%;"
                         data-donate-bar></div>
                </div>
                <p class="mt-2 text-xs text-white/50">GNAT Donation community goal (demo)</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 sm:items-stretch pt-1">
                <label class="relative flex-1 flex items-center rounded-2xl bg-white pl-12 pr-4 py-3.5 shadow-inner ring-1 ring-black/5">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#351c42]/10 text-[#351c42] font-bold text-base leading-none" aria-hidden="true">₹</span>
                    <input type="number" min="1" step="1" value="{{ (int) ($dm['default_amount'] ?? 100) }}" data-donate-input class="w-full min-w-0 border-0 bg-transparent text-[#351c42] text-lg font-bold outline-none focus:ring-0" />
                </label>
                <button type="button" data-donate-submit class="click-btn btn-style506 shrink-0 justify-center sm:min-w-[200px]">
                    <span class="click-btn__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                            <path d="M8 8l3 4-3 4M13 8l3 4-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="click-btn__label">Donate Now</span>
                </button>
            </div>
        </div>
    </div>
</div>
