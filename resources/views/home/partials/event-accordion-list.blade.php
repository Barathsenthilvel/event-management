{{-- Expects: $events, $interestedEventIds, $guestInterestedEventIds; optional $expandAll (bool) = all rows open on /events page --}}
@php
    $expandAll = filter_var($expandAll ?? false, FILTER_VALIDATE_BOOLEAN);
@endphp
<div
    class="grid gap-4"
    @if($expandAll)
        data-events-expand-all="true"
    @else
        id="home-events-accordion"
    @endif
>
    @foreach($events as $event)
        @php
            $cover = $event->cover_image_path ? asset('storage/' . $event->cover_image_path) : asset('images/event1.jpg');
            $firstDate = $event->dates->first();
            $summaryDate = $firstDate?->event_date?->format('d M') ?? 'TBA';
            $day = $firstDate?->event_date?->format('d') ?? '—';
            $month = strtoupper((string) ($firstDate?->event_date?->format('M') ?? 'TBA'));
            $startT = ($firstDate && $firstDate->start_time)
                ? \Illuminate\Support\Carbon::parse($firstDate->start_time)->format('h:i A')
                : null;
            $endT = ($firstDate && $firstDate->end_time)
                ? \Illuminate\Support\Carbon::parse($firstDate->end_time)->format('h:i A')
                : null;
            $timeRange = $startT && $endT ? ($startT . ' - ' . $endT) : ($startT ?: ($endT ?: 'Time TBA'));
            $organizer = $event->creator?->name ?? 'GNAT Team';
            $desc = $event->description ?: 'Join us for this GNAT event. More details will be shared with registered members.';
            $seatLimited = ($event->seat_mode ?? '') === 'limited';
            $seatFilled = (int) ($event->invites_count ?? 0);
            $seatCap = max(0, (int) ($event->seat_limit ?? 0));
        @endphp
        <div class="rounded-2xl bg-white border border-[#351c42]/10 overflow-hidden" data-events-accordion-item @if($expandAll) data-events-open="true" @endif>
            <button
                type="button"
                class="w-full text-left px-6 py-4 flex items-start justify-between gap-6 {{ $expandAll ? 'cursor-default' : '' }}"
                data-events-accordion-trigger
                aria-expanded="{{ $expandAll ? 'true' : 'false' }}"
            >
                <div class="min-w-0" data-events-header-summary>
                    <div class="flex items-center gap-3 text-xs font-semibold text-[#351c42]/70" data-events-header-time>
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#351c42]"></span>
                            {{ $summaryDate }}
                        </span>
                        <span class="h-1 w-1 rounded-full bg-[#351c42]/30"></span>
                        <span>{{ $timeRange }}</span>
                    </div>
                    <div class="mt-2 text-sm md:text-base font-bold text-[#351c42] truncate" data-events-header-title>
                        {{ $event->title }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-full bg-[#351c42] text-white flex items-center justify-center flex-shrink-0" data-events-trigger-icon aria-hidden="true">
                        <svg data-events-plus class="w-4 h-4 {{ $expandAll ? 'hidden' : '' }}" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 1.5V14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <svg data-events-minus class="w-4 h-4 {{ $expandAll ? '' : 'hidden' }}" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </button>

            <div class="px-6 pb-6 pt-0 {{ $expandAll ? '' : 'hidden' }}" data-events-accordion-panel>
                <div class="mt-6 grid gap-8 md:grid-cols-[340px_1fr] items-stretch">
                    <div class="relative rounded-2xl overflow-hidden border border-[#351c42]/10 bg-[#f6f3e9]">
                        <img src="{{ $cover }}" alt="{{ $event->title }}" class="h-56 md:h-72 w-full object-cover" />

                        <div class="absolute left-4 top-4 rounded-full bg-[#fddc6a] px-3 py-2 text-center shadow-sm">
                            <div class="text-lg font-extrabold leading-none text-[#351c42]">{{ $day }}</div>
                            <div class="mt-0.5 text-[10px] font-extrabold tracking-widest leading-none text-[#965995] bg-white/70 rounded px-2 py-0.5 inline-block">{{ $month }}</div>
                        </div>
                    </div>

                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between gap-6 w-full">
                            <div class="inline-flex items-center gap-3 rounded-full border border-[#351c42]/10 bg-white/70 px-4 py-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="text-xs font-semibold text-[#351c42]/70">{{ $timeRange }}</span>
                            </div>
                            <div class="ml-auto shrink-0 rounded-full border border-[#351c42]/15 bg-white px-3 py-1.5 text-right shadow-sm min-w-[5.5rem]" aria-label="{{ $seatLimited ? 'Limited seats' : 'Unlimited seats' }}">
                                @if ($seatLimited)
                                    <div class="text-[9px] font-black uppercase tracking-wider text-[#351c42]/55 leading-none">Limited</div>
                                    <div class="mt-0.5 text-xs font-extrabold tabular-nums text-[#351c42]">{{ $seatFilled }} / {{ $seatCap > 0 ? $seatCap : '—' }}</div>
                                @else
                                    <div class="text-[10px] font-black uppercase tracking-wide text-[#351c42] leading-tight py-0.5">Unlimited</div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 text-sm md:text-base font-bold text-[#351c42]">
                            {{ $event->title }}
                        </div>

                        <p class="mt-3 text-sm text-[#351c42]/80 leading-6">
                            {{ $desc }}
                        </p>
                        <div class="mt-auto grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-[#f6f3e9] p-4 border border-[#351c42]/10">
                                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    Organizer
                                </div>
                                <div class="mt-2 font-bold text-[#351c42] text-sm">{{ $organizer }}</div>
                            </div>
                            <div class="rounded-2xl bg-[#f6f3e9] p-4 border border-[#351c42]/10">
                                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    Venue
                                </div>
                                <div class="mt-2 font-bold text-[#351c42] text-sm">{{ $event->venue ?: 'Venue not specified' }}</div>
                            </div>
                        </div>

                        <div class="mt-6">
                            @if(in_array($event->id, $interestedEventIds ?? [], true) || in_array($event->id, $guestInterestedEventIds ?? [], true))
                                <span class="inline-flex w-full items-center justify-center rounded-2xl border border-[#351c42]/20 bg-[#f6f3e9] px-5 py-3 text-sm font-extrabold text-[#351c42]/80 cursor-default">
                                    Interest registered
                                </span>
                            @else
                                <button
                                    type="button"
                                    class="interest-open-btn inline-flex w-full items-center justify-center rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-md shadow-[#351c42]/15 hover:bg-[#4d2a5c] transition-colors"
                                    data-interest-url="{{ route('events.interest', $event) }}"
                                    data-is-logged-in="{{ Auth::check() ? '1' : '0' }}"
                                    data-prefill-name="{{ e(Auth::check() ? (Auth::user()->name ?? '') : '') }}"
                                    data-prefill-email="{{ e(Auth::check() ? (Auth::user()->email ?? '') : '') }}"
                                    data-prefill-phone="{{ e(Auth::check() ? (Auth::user()->mobile ?? '') : '') }}"
                                >
                                    Interested
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
