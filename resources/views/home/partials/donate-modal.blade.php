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
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-[#fddc6a]/90">GNAT Association</p>
            <h2 id="donate-modal-title" class="mt-1 text-xl sm:text-2xl font-extrabold text-white tracking-tight">Give &amp; change a life</h2>
            <p class="mt-2 text-sm text-white/65 leading-relaxed">Your support funds real programs in education, health, and community.</p>
        </div>

        <input type="hidden" id="donate-context-donation-id" value="" autocomplete="off" />

        @auth
            <div class="mt-5 rounded-2xl border border-[#fddc6a]/25 bg-white/5 px-4 py-3 text-sm text-white/90">
                <span class="font-semibold text-[#fcd34d]">Signed in as {{ auth()->user()->name }}</span>
                <span class="text-white/70"> — your donation will be saved with your member email and mobile on file.</span>
            </div>
        @else
            <div id="donate-step-details" class="mt-6 space-y-4">
                <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm leading-relaxed text-white/85">
                    <p class="font-semibold text-[#fcd34d]">Already a GNAT member?</p>
                    <p class="mt-1 text-white/70">Please <a href="{{ route('member.login', ['return' => url()->current()]) }}" class="font-bold text-white underline-offset-2 hover:underline">log in and pay</a> so we can link your donation to your profile.</p>
                    <p class="mt-3 font-semibold text-white/90">Paying as a guest?</p>
                    <p class="mt-1 text-white/65">Enter your details below, then choose an amount. Interested in membership? <a href="{{ route('member.register') }}" class="font-bold text-[#fcd34d] underline-offset-2 hover:underline">Sign up here</a>.</p>
                </div>
                <div class="space-y-3">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-wider text-white/55">Full name</span>
                        <input type="text" data-donate-detail="name" autocomplete="name" required
                            class="mt-1.5 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-medium text-white placeholder:text-white/35 outline-none focus:border-[#fcd34d]/50 focus:ring-2 focus:ring-[#fcd34d]/20" placeholder="Your name" />
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-wider text-white/55">Mobile number</span>
                        <input type="tel" data-donate-detail="mobile" inputmode="numeric" autocomplete="tel" required
                            class="mt-1.5 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-medium text-white placeholder:text-white/35 outline-none focus:border-[#fcd34d]/50 focus:ring-2 focus:ring-[#fcd34d]/20" placeholder="10-digit mobile" />
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-wider text-white/55">Email</span>
                        <input type="email" data-donate-detail="email" autocomplete="email" required
                            class="mt-1.5 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-medium text-white placeholder:text-white/35 outline-none focus:border-[#fcd34d]/50 focus:ring-2 focus:ring-[#fcd34d]/20" placeholder="you@email.com" />
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                        <input type="checkbox" data-donate-wants-member class="mt-1 h-4 w-4 shrink-0 rounded border-white/30 bg-white/10 text-[#fcd34d] focus:ring-[#fcd34d]" />
                        <span class="text-sm text-white/80">I’m interested in becoming a GNAT member (we may follow up with you).</span>
                    </label>
                </div>
                <button type="button" data-donate-continue-details
                    class="w-full rounded-2xl bg-[#fcd34d] py-3.5 text-sm font-extrabold text-[#351c42] shadow-lg hover:bg-[#fde68a] transition-colors">
                    Continue to choose amount
                </button>
            </div>
        @endauth

        <div id="donate-step-amounts-wrapper" class="@guest hidden @endguest">
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
                <p class="mt-2 text-xs text-white/50">GNAT Association community goal (demo)</p>
            </div>
            <div class="flex flex-col gap-3 pt-1">
                @guest
                <button type="button" data-donate-back-details class="self-start text-xs font-bold text-[#fcd34d] underline-offset-2 hover:underline">
                    ← Edit your details
                </button>
                @endguest
                <div class="flex flex-col sm:flex-row gap-3 sm:items-stretch">
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
                    <span class="click-btn__label">Pay securely</span>
                </button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
