@php
    $formattedCode = $memberCode ?? ('GNAT-9715-' . str_pad((string) ($member->id ?? 1), 4, '0', STR_PAD_LEFT));
    $hasCards = (!empty($hasCards) || (!empty($member) && app(\App\Services\MemberIdCardService::class)->cardsExist($member)))
        && !empty($idCardUrls['front']) 
        && !empty($idCardUrls['back']);
    $subCard = $activeSubscription ?? ($member?->activeSubscription ?? $member?->subscriptions()->latest('id')->first());
    $validTillStr = $subCard ? $subCard->formattedValidTillDate() : '—';
@endphp

<article class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-[#351c42]/15 bg-gradient-to-br from-[#132c48] via-[#0d2238] to-[#081726] p-4 text-white shadow-xl sm:p-5"
         x-data="{
             side: 'front',
             menuOpen: false,
             modalOpen: false,
             modalSide: 'front'
         }">
    {{-- Top Bar: Title & Side Switcher --}}
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-3">
        <div class="flex items-center gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#fddc6a]/15 text-[#fddc6a] ring-1 ring-[#fddc6a]/30">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 00-.894.553L7.382 6H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V8a1 1 0 000-2h-3.382l-1.724-3.447A1 1 0 0010 2zm0 8a2 2 0 100 4 2 2 0 000-4z" clip-rule="evenodd"/>
                </svg>
            </span>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#fddc6a]">Digital Credential</p>
                <h3 class="text-sm font-extrabold text-white">GNAT Official ID Card</h3>
            </div>
        </div>

        {{-- Front / Back Toggle Buttons --}}
        @if($hasCards)
            <div class="inline-flex rounded-lg bg-black/30 p-1 ring-1 ring-white/10 text-xs">
                <button type="button"
                        @click="side = 'front'"
                        :class="side === 'front' ? 'bg-[#fddc6a] text-[#0e2b45] font-bold shadow-sm' : 'text-white/75 hover:text-white font-medium'"
                        class="rounded-md px-3 py-1 text-xs transition-all duration-150">
                    Front Side
                </button>
                <button type="button"
                        @click="side = 'back'"
                        :class="side === 'back' ? 'bg-[#fddc6a] text-[#0e2b45] font-bold shadow-sm' : 'text-white/75 hover:text-white font-medium'"
                        class="rounded-md px-3 py-1 text-xs transition-all duration-150">
                    Back Side
                </button>
            </div>
        @endif
    </div>

    {{-- Main Preview Area --}}
    <div class="my-3 flex flex-col items-center justify-center">
        @if($hasCards)
            <div class="group relative w-full max-w-[240px] sm:max-w-[260px] aspect-[1845/3137] overflow-hidden rounded-xl bg-[#091522] shadow-2xl ring-1 ring-white/20 transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.6)]">
                {{-- Front Side Image --}}
                <img src="{{ $idCardUrls['front'] }}"
                     alt="GNAT Member ID Card Front — {{ $member->name }}"
                     x-show="side === 'front'"
                     class="absolute inset-0 h-full w-full object-contain transition-opacity duration-200"
                     loading="eager">

                {{-- Back Side Image --}}
                <img src="{{ $idCardUrls['back'] }}"
                     alt="GNAT Member ID Card Back — {{ $member->name }}"
                     x-show="side === 'back'"
                     class="absolute inset-0 h-full w-full object-contain transition-opacity duration-200"
                     loading="lazy">

                {{-- Hover / Enlarge Overlay --}}
                <button type="button"
                        @click="modalOpen = true; modalSide = side"
                        class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded-lg bg-black/75 px-2.5 py-1.5 text-[11px] font-semibold text-white/95 backdrop-blur-md ring-1 ring-white/20 opacity-90 transition-all hover:bg-black hover:opacity-100 hover:scale-105"
                        title="Click to view full high-resolution ID card">
                    <svg class="h-3.5 w-3.5 text-[#fddc6a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                    <span>View HD</span>
                </button>
            </div>
        @else
            {{-- Elegant Fallback if generating --}}
            <div class="relative w-full max-w-[280px] rounded-xl border border-white/15 bg-gradient-to-b from-white/10 to-white/5 p-4 text-center">
                <div class="mx-auto h-20 w-20 overflow-hidden rounded-full border-2 border-[#fddc6a] bg-[#0d9488]/30">
                    @include('partials.user-letter-avatar', ['user' => $member, 'class' => 'h-full w-full text-2xl border-0'])
                </div>
                <h4 class="mt-3 text-base font-bold text-white">{{ $member->name }}</h4>
                <p class="text-xs text-white/75">{{ $member->designation?->name ?? ($member->profile_type ?? 'Member') }}</p>
                <p class="mt-2 font-mono text-xs font-bold text-[#fddc6a]">{{ $formattedCode }}</p>
            </div>
        @endif
    </div>

    {{-- Bottom Action & Download Controls --}}
    <div class="border-t border-white/10 pt-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            {{-- Verification status & ID --}}
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="font-mono text-xs font-extrabold tracking-wide text-[#fddc6a]">{{ $formattedCode }}</span>
                </div>
                <p class="text-[10px] text-white/60">Verified Official GNAT Credential · Valid till: <span class="font-semibold text-[#fddc6a]">{{ $validTillStr }}</span></p>
            </div>

            {{-- Download Actions --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('member.id-card.download', ['side' => 'combined']) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-[#fddc6a] px-3.5 py-1.5 text-xs font-bold text-[#0e2b45] shadow transition-all hover:bg-[#ffe58f] hover:shadow-md active:scale-95"
                   title="Download full ID card with Front and Back sides side-by-side">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Download ID</span>
                </a>

                {{-- Download options dropdown --}}
                <div class="relative" @click.outside="menuOpen = false">
                    <button type="button"
                            @click="menuOpen = !menuOpen"
                            class="inline-flex items-center rounded-lg border border-white/20 bg-white/10 px-2 py-1.5 text-xs font-semibold text-white transition hover:bg-white/20"
                            aria-label="More download options">
                        <svg class="h-3.5 w-3.5 transition-transform duration-150" :class="menuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="menuOpen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 bottom-full mb-2 w-48 rounded-xl border border-white/15 bg-[#0e2338] p-1.5 text-xs shadow-2xl ring-1 ring-black/40 z-30">
                        <a href="{{ route('member.id-card.download', ['side' => 'combined']) }}"
                           class="flex items-center gap-2 rounded-lg px-2.5 py-2 font-semibold text-[#fddc6a] hover:bg-white/10 transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Full Card (Front &amp; Back)</span>
                        </a>
                        <a href="{{ route('member.id-card.download', ['side' => 'front']) }}"
                           class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-white/90 hover:bg-white/10 hover:text-white transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Front Side Only</span>
                        </a>
                        <a href="{{ route('member.id-card.download', ['side' => 'back']) }}"
                           class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-white/90 hover:bg-white/10 hover:text-white transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Back Side Only</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- High-Resolution Fullscreen Modal --}}
    @if($hasCards)
        <div x-show="modalOpen"
             x-cloak
             @keydown.escape.window="modalOpen = false"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-3 sm:p-6 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="relative flex max-h-[95vh] w-full max-w-4xl flex-col rounded-2xl border border-white/20 bg-[#0c1e30] p-4 text-white shadow-2xl sm:p-6"
                 @click.outside="modalOpen = false">
                
                {{-- Modal Header --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/15 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-white sm:text-lg">GNAT Official Member ID Card</h3>
                        <p class="font-mono text-xs font-bold text-[#fddc6a]">{{ $formattedCode }} — {{ $member->name }}</p>
                    </div>

                    {{-- View Switcher Pills Inside Modal --}}
                    <div class="flex items-center gap-2">
                        <div class="inline-flex rounded-lg bg-black/40 p-1 text-xs">
                            <button type="button"
                                    @click="modalSide = 'front'"
                                    :class="modalSide === 'front' ? 'bg-[#fddc6a] text-[#0e2b45] font-bold shadow' : 'text-white/75 hover:text-white'"
                                    class="rounded-md px-3 py-1.5 transition">
                                Front
                            </button>
                            <button type="button"
                                    @click="modalSide = 'back'"
                                    :class="modalSide === 'back' ? 'bg-[#fddc6a] text-[#0e2b45] font-bold shadow' : 'text-white/75 hover:text-white'"
                                    class="rounded-md px-3 py-1.5 transition">
                                Back
                            </button>
                            <button type="button"
                                    @click="modalSide = 'combined'"
                                    :class="modalSide === 'combined' ? 'bg-[#fddc6a] text-[#0e2b45] font-bold shadow' : 'text-white/75 hover:text-white'"
                                    class="rounded-md px-3 py-1.5 transition">
                                Combined (Both)
                            </button>
                        </div>

                        {{-- Close Modal Button --}}
                        <button type="button"
                                @click="modalOpen = false"
                                class="rounded-lg p-2 text-white/70 transition hover:bg-white/10 hover:text-white"
                                aria-label="Close modal">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Image Body --}}
                <div class="my-4 flex flex-1 items-center justify-center overflow-auto p-2">
                    <img src="{{ $idCardUrls['front'] }}"
                         alt="GNAT Member ID Card Front"
                         x-show="modalSide === 'front'"
                         class="max-h-[68vh] w-auto rounded-xl shadow-2xl ring-1 ring-white/20 object-contain">

                    <img src="{{ $idCardUrls['back'] }}"
                         alt="GNAT Member ID Card Back"
                         x-show="modalSide === 'back'"
                         class="max-h-[68vh] w-auto rounded-xl shadow-2xl ring-1 ring-white/20 object-contain">

                    <img src="{{ $idCardUrls['combined'] }}"
                         alt="GNAT Member ID Card Combined"
                         x-show="modalSide === 'combined'"
                         class="max-h-[68vh] w-auto rounded-xl shadow-2xl ring-1 ring-white/20 object-contain">
                </div>

                {{-- Modal Footer --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/15 pt-4">
                    <span class="text-xs text-white/60">High Resolution Print-Ready Digital Credential</span>
                    <div class="flex items-center gap-2">
                        <a :href="'{{ url('/member/id-card/download') }}/' + modalSide"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-[#fddc6a] px-4 py-2 text-xs font-bold text-[#0e2b45] shadow transition hover:bg-[#ffe58f]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Download This View</span>
                        </a>
                        <button type="button"
                                @click="modalOpen = false"
                                class="rounded-lg border border-white/20 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/10">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</article>

