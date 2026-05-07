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
            $sortedDates = $event->dates->sortBy('event_date')->values();
            $firstDate = $sortedDates->first();
            $summaryDate = $firstDate?->event_date?->format('d M Y') ?? 'TBA';
            $extraDatesCount = max(0, $sortedDates->count() - 1);
            $day = $firstDate?->event_date?->format('d') ?? '—';
            $month = strtoupper((string) ($firstDate?->event_date?->format('M') ?? 'TBA'));
            $dateDetails = $sortedDates
                ->map(function ($d) {
                    $start = $d->start_time ? \Illuminate\Support\Carbon::parse($d->start_time)->format('h:i A') : null;
                    $end = $d->end_time ? \Illuminate\Support\Carbon::parse($d->end_time)->format('h:i A') : null;
                    $slot = $start && $end ? ($start . ' - ' . $end) : ($start ?: ($end ?: 'Time TBA'));
                    return [
                        'date' => $d->event_date?->format('d M Y') ?? 'TBA',
                        'slot' => $slot,
                    ];
                })
                ->values();
            $moreDatesTooltip = $dateDetails
                ->map(fn ($row) => $row['date'] . ' (' . $row['slot'] . ')')
                ->implode("\n");
            $timeSlots = $dateDetails
                ->pluck('slot')
                ->filter()
                ->unique()
                ->values();
            $timeRange = $timeSlots->count() > 1 ? 'Multiple time slots' : ($timeSlots->first() ?? 'Time TBA');
            $dateLabels = $dateDetails
                ->pluck('date')
                ->filter()
                ->unique()
                ->values();
            $scheduleChipText = $dateLabels->first() ?? 'Date TBA';
            if ($dateLabels->count() > 1) {
                $scheduleChipText .= ' +' . ($dateLabels->count() - 1) . ' more dates';
            }
            $organizer = $event->creator?->name ?? 'GNAT Team';
            $desc = trim(strip_tags((string) ($event->description ?? '')));
            $hasDesc = $desc !== '';
            $seatLimited = ($event->seat_mode ?? '') === 'limited';
            $seatFilled = (int) ($event->interested_count ?? 0);
            $seatCap = max(0, (int) ($event->seat_limit ?? 0));
            $seatPct = ($seatLimited && $seatCap > 0) ? min(100, (int) round((100 * $seatFilled) / $seatCap)) : 0;
            $isAdminEvent = filled($event->created_by_admin_id);
            $memberInterestReturn = request()->fullUrl();
            $interestClosed = ! $event->acceptsPublicAttendance();
            $attendCtaLabel = match ($event->status) {
                'live' => 'Attend now',
                'upcoming' => 'Attend',
                default => 'Register',
            };
        @endphp
        <div class="rounded-2xl bg-white border border-[#351c42]/10 overflow-x-hidden overflow-y-visible" data-events-accordion-item @if($expandAll) data-events-open="true" @endif>
            @if($hasDesc)
                <div class="hidden" data-readmore-source="{{ $event->id }}" aria-hidden="true">
                    <div data-readmore-title>{{ $event->title }}</div>
                    <div data-readmore-dates-content>
                        @forelse($dateDetails as $row)
                            <div class="flex justify-between gap-3 border-b border-[#351c42]/10 py-1.5 text-xs font-semibold text-[#351c42] last:border-0">
                                <span>{{ $row['date'] }}</span>
                                <span class="shrink-0 text-[#351c42]/70">{{ $row['slot'] }}</span>
                            </div>
                        @empty
                            <p class="text-xs font-semibold text-[#351c42]/70">Date TBA</p>
                        @endforelse
                    </div>
                    <div data-readmore-desc>{{ $desc }}</div>
                </div>
            @endif
            <button
                type="button"
                class="w-full text-left px-6 py-4 flex items-start justify-between gap-6 {{ $expandAll ? 'cursor-default' : 'cursor-pointer' }}"
                data-events-accordion-trigger
                aria-expanded="{{ $expandAll ? 'true' : 'false' }}"
            >
                <div class="min-w-0" data-events-header-summary>
                    <div class="flex items-center gap-3 text-xs font-semibold text-[#351c42]/70" data-events-header-time>
                        <span class="group/date-tip relative inline-flex max-w-full cursor-help items-center gap-2 outline-none" tabindex="0">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-[#351c42]"></span>
                            <span>{{ $summaryDate }}</span>
                            @if($extraDatesCount > 0)
                                <span class="inline-flex shrink-0 items-center rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[10px] font-extrabold text-indigo-700">
                                    +{{ $extraDatesCount }} more
                                </span>
                            @endif
                            <span class="pointer-events-none absolute left-0 top-[calc(100%+6px)] z-30 hidden w-max min-w-[12rem] max-w-[min(20rem,calc(100vw-2rem))] rounded-xl border border-[#351c42]/12 bg-white p-3 text-left shadow-xl ring-1 ring-black/5 group-hover/date-tip:block group-focus-within/date-tip:block">
                                <span class="text-[10px] font-black uppercase tracking-wider text-[#965995]">All dates</span>
                                <div class="mt-2 space-y-0">
                                    @forelse($dateDetails as $row)
                                        <div class="flex justify-between gap-3 border-b border-[#351c42]/8 py-1.5 text-[11px] font-semibold text-[#351c42] last:border-0">
                                            <span>{{ $row['date'] }}</span>
                                            <span class="shrink-0 text-[#351c42]/65">{{ $row['slot'] }}</span>
                                        </div>
                                    @empty
                                        <p class="text-[11px] text-[#351c42]/70">Date TBA</p>
                                    @endforelse
                                </div>
                            </span>
                        </span>
                        <span class="h-1 w-1 rounded-full bg-[#351c42]/30"></span>
                        <span>{{ $timeRange }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-2 min-w-0">
                        <span class="text-sm md:text-base font-bold text-[#351c42] truncate" data-events-header-title>
                            {{ $event->title }}
                        </span>
                        @if($event->status === 'live')
                            <span class="inline-flex shrink-0 items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-emerald-800">Live</span>
                        @elseif($event->status === 'completed')
                            <span class="inline-flex shrink-0 items-center rounded-full border border-[#351c42]/15 bg-[#f6f3e9] px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-[#351c42]/80">Completed</span>
                        @elseif($event->status === 'upcoming')
                            <span class="inline-flex shrink-0 items-center rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-indigo-800">Upcoming</span>
                        @endif
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
                <div class="mt-4 grid gap-5 md:grid-cols-[300px_1fr] items-stretch">
                    <div class="relative min-h-[13rem] rounded-2xl overflow-hidden border border-[#351c42]/10 bg-[#f6f3e9]">
                        <img src="{{ $cover }}" alt="{{ $event->title }}" class="absolute inset-0 h-full w-full object-cover" />

                        <div class="absolute left-4 top-4 rounded-full bg-[#fddc6a] px-3 py-2 text-center shadow-sm">
                            <div class="text-lg font-extrabold leading-none text-[#351c42]">{{ $day }}</div>
                            <div class="mt-0.5 text-[10px] font-extrabold tracking-widest leading-none text-[#965995] bg-white/70 rounded px-2 py-0.5 inline-block">{{ $month }}</div>
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-col h-full overflow-hidden">
                        <div class="flex items-center justify-between gap-3 w-full">
                            <div class="group/date-tip relative inline-flex max-w-[min(100%,14rem)] cursor-help items-center gap-2 rounded-full border border-[#351c42]/10 bg-white/70 px-3 py-1.5 outline-none sm:max-w-none" tabindex="0">
                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="min-w-0 truncate text-[11px] font-semibold text-[#351c42]/70">{{ $scheduleChipText }}</span>
                                <span class="pointer-events-none absolute left-0 top-[calc(100%+8px)] z-30 hidden w-max min-w-[14rem] max-w-[min(20rem,calc(100vw-2rem))] rounded-xl border border-[#351c42]/12 bg-white p-3 text-left shadow-xl ring-1 ring-black/5 group-hover/date-tip:block group-focus-within/date-tip:block">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-[#965995]">All dates</span>
                                    <div class="mt-2 space-y-0">
                                        @forelse($dateDetails as $row)
                                            <div class="flex justify-between gap-3 border-b border-[#351c42]/8 py-1.5 text-[11px] font-semibold text-[#351c42] last:border-0">
                                                <span>{{ $row['date'] }}</span>
                                                <span class="shrink-0 text-[#351c42]/65">{{ $row['slot'] }}</span>
                                            </div>
                                        @empty
                                            <p class="text-[11px] text-[#351c42]/70">Date TBA</p>
                                        @endforelse
                                    </div>
                                </span>
                            </div>
                            @if ($seatLimited)
                                <div
                                    class="ml-auto w-[6.75rem] shrink-0 rounded-2xl border border-[#351c42]/10 bg-gradient-to-b from-white to-[#faf8fc] px-2.5 py-2 shadow-sm ring-1 ring-[#351c42]/5 sm:w-[7.25rem]"
                                    role="group"
                                    aria-label="Seats {{ $seatFilled }} of {{ $seatCap > 0 ? $seatCap : '—' }} registered"
                                >
                                    <div class="flex items-baseline justify-between gap-1">
                                        <span class="text-[9px] font-bold uppercase tracking-wide text-[#965995]">Seats</span>
                                        <span class="text-[11px] font-extrabold tabular-nums leading-none text-[#351c42]">
                                            {{ $seatFilled }}<span class="mx-0.5 font-semibold text-[#351c42]/35">/</span>{{ $seatCap > 0 ? $seatCap : '—' }}
                                        </span>
                                    </div>
                                    <div
                                        class="mt-1.5 h-1 w-full overflow-hidden rounded-full bg-[#351c42]/10"
                                        role="progressbar"
                                        aria-valuemin="0"
                                        aria-valuemax="{{ $seatCap > 0 ? $seatCap : 1 }}"
                                        aria-valuenow="{{ min($seatFilled, $seatCap > 0 ? $seatCap : $seatFilled) }}"
                                        aria-label="Registration fill"
                                    >
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-[#965995] via-[#8a4d88] to-[#7a4678] transition-[width] duration-300"
                                            style="width: {{ $seatPct }}%"
                                        ></div>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="ml-auto inline-flex shrink-0 items-center gap-1.5 rounded-full border border-[#351c42]/10 bg-[#f6f3e9]/80 px-2.5 py-1.5 shadow-sm"
                                    aria-label="Unlimited seats"
                                >
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]" aria-hidden="true">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6.636 12.568C5.803 11.751 5.25 10.688 5.25 9.5 5.25 7.153 7.153 5.25 9.5 5.25c1.854 0 3.426 1.126 4.1 2.735.352.82.538 1.717.538 2.765s-.186 1.945-.538 2.765c-.774 1.609-2.246 2.735-4.1 2.735-1.182 0-2.26-.45-3.064-1.19"/>
                                            <path d="M17.364 12.568c.833-.817 1.386-1.88 1.386-3.068 0-2.347-1.903-4.25-4.25-4.25-1.854 0-3.426 1.126-4.1 2.735-.352.82-.538 1.717-.538 2.765s.186 1.945.538 2.765c.774 1.609 2.246 2.735 4.1 2.735 1.182 0 2.26-.45 3.064-1.19"/>
                                        </svg>
                                    </span>
                                    <span class="text-[10px] font-bold text-[#351c42]/80">Unlimited</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3 text-sm md:text-base font-bold text-[#351c42] break-words">
                            {{ $event->title }}
                        </div>
                        <!-- @if($sortedDates->isNotEmpty())
                            <div class="mt-3 rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-3">
                                <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#965995]">Event schedule</p>
                                <ul class="mt-2 space-y-1.5">
                                    @foreach($sortedDates as $dateRow)
                                        @php
                                            $slotStart = $dateRow->start_time ? \Illuminate\Support\Carbon::parse($dateRow->start_time)->format('h:i A') : null;
                                            $slotEnd = $dateRow->end_time ? \Illuminate\Support\Carbon::parse($dateRow->end_time)->format('h:i A') : null;
                                            $slotTime = $slotStart && $slotEnd ? ($slotStart . ' - ' . $slotEnd) : ($slotStart ?: ($slotEnd ?: 'Time TBA'));
                                        @endphp
                                        <li class="flex flex-wrap items-center justify-between gap-2 text-xs font-semibold text-[#351c42]/80">
                                            <span>{{ $dateRow->event_date?->format('d M Y') ?? 'TBA' }}</span>
                                            <span>{{ $slotTime }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif -->

                        @if($hasDesc)
                            <div class="mt-3 min-w-0 w-full max-w-full" data-desc-wrap data-readmore-event-id="{{ $event->id }}">
                                <p
                                    class="line-clamp-3 min-h-[4.5rem] w-full max-w-full min-w-0 overflow-hidden text-sm text-[#351c42]/80 leading-6 break-words break-all"
                                    data-desc-text
                                >
                                    {{ $desc }}
                                </p>
                                <button
                                    type="button"
                                    class="mt-2 inline-flex cursor-pointer items-center gap-1 text-xs font-extrabold text-[#965995] hover:text-[#351c42] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995]/40 rounded"
                                    data-desc-readmore
                                    aria-haspopup="dialog"
                                    aria-controls="home-event-readmore-modal"
                                    title="View full description"
                                >
                                    Read more
                                </button>
                            </div>
                        @endif
                        <div class="mt-auto grid gap-2.5 sm:grid-cols-2">
                            <div class="rounded-xl bg-[#f6f3e9] p-2.5 border border-[#351c42]/10">
                                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wide text-[#351c42]/70">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    Organizer
                                </div>
                                <div class="mt-1 font-bold text-[#351c42] text-xs leading-5 break-words">{{ $organizer }}</div>
                            </div>
                            <div class="rounded-xl bg-[#f6f3e9] p-2.5 border border-[#351c42]/10">
                                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wide text-[#351c42]/70">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    Venue
                                </div>
                                <div class="mt-1 font-bold text-[#351c42] text-xs leading-5 break-words">{{ $event->venue ?: 'Venue not specified' }}</div>
                            </div>
                        </div>

                        @if(in_array($event->id, $interestedEventIds ?? [], true) || in_array($event->id, $guestInterestedEventIds ?? [], true))
                            @if($isAdminEvent)
                                <div class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-5" data-readmore-footer>
                                    @include('home.partials.event-interested-facepile-static', ['registeredCount' => $seatFilled])
                                    <span class="min-w-0 shrink-0 text-sm font-extrabold text-[#fddc6a] sm:ml-auto">
                                        Interest registered
                                    </span>
                                </div>
                            @else
                                <div class="mt-6" data-readmore-footer>
                                    <span class="inline-flex w-full items-center justify-center rounded-2xl border border-[#351c42]/20 bg-[#f6f3e9] px-5 py-3 text-sm font-extrabold text-[#351c42]/80 cursor-default">
                                        Interest registered
                                    </span>
                                </div>
                            @endif
                        @elseif($interestClosed)
                            <div class="mt-6" data-readmore-footer>
                                <span class="inline-flex w-full items-center justify-center rounded-2xl border border-[#351c42]/15 bg-[#f6f3e9] px-5 py-3 text-sm font-extrabold text-[#351c42]/60 cursor-default">
                                    @if($event->status === 'cancelled')
                                        This event was cancelled
                                    @elseif($event->status === 'completed')
                                        Event completed — view only
                                    @else
                                        Registration closed
                                    @endif
                                </span>
                            </div>
                        @elseif($isAdminEvent)
                            <div class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-5" data-readmore-footer>
                                @include('home.partials.event-interested-facepile-static', ['registeredCount' => $seatFilled])
                                <div class="flex min-w-0 flex-wrap items-center gap-2 sm:ml-auto">
                                    @auth
                                        <button
                                            type="button"
                                            class="interest-open-btn cursor-pointer inline-flex min-h-[2.1rem] items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#351c42] shadow-sm transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#351c42]"
                                            data-interest-url="{{ route('events.interest', $event) }}"
                                            data-member-interest-url="{{ route('member.events.interest', $event) }}"
                                        >
                                            {{ $attendCtaLabel }}
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            class="interest-open-btn cursor-pointer inline-flex min-h-[2.1rem] items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#351c42] shadow-sm transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#351c42]"
                                            data-interest-url="{{ route('events.interest', $event) }}"
                                        >
                                            {{ $attendCtaLabel }}
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        @else
                            <div class="mt-6" data-readmore-footer>
                                @auth
                                    <button
                                        type="button"
                                        class="interest-open-btn cursor-pointer inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-5 py-2.5 text-xs font-extrabold tracking-wide text-[#fddc6a] shadow-md shadow-[#351c42]/15 hover:brightness-105 transition-colors"
                                        data-interest-url="{{ route('events.interest', $event) }}"
                                        data-member-interest-url="{{ route('member.events.interest', $event) }}"
                                    >
                                        {{ $attendCtaLabel }}
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        class="interest-open-btn cursor-pointer inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-5 py-2.5 text-xs font-extrabold tracking-wide text-[#fddc6a] shadow-md shadow-[#351c42]/15 hover:brightness-105 transition-colors"
                                        data-interest-url="{{ route('events.interest', $event) }}"
                                    >
                                        {{ $attendCtaLabel }}
                                    </button>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div
    id="home-event-readmore-modal"
    class="fixed inset-0 z-[190] hidden items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="home-readmore-modal-title"
    aria-hidden="true"
>
    <div class="absolute inset-0 cursor-pointer bg-[#351c42]/45" data-readmore-backdrop title="Close"></div>
    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-2xl shadow-[#351c42]/15">
        <div class="flex items-start justify-between gap-3">
            <h2 id="home-readmore-modal-title" class="min-w-0 flex-1 pr-2 text-lg font-extrabold leading-snug text-[#351c42]"></h2>
            <button type="button" class="shrink-0 cursor-pointer rounded-xl p-2 text-[#351c42]/50 hover:bg-[#351c42]/5 hover:text-[#351c42]" data-readmore-close aria-label="Close" title="Close">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
        </div>
        <p class="mt-4 text-[11px] font-black uppercase tracking-[0.14em] text-[#965995]">Event dates</p>
        <div id="home-readmore-modal-dates" class="mt-2 rounded-xl border border-[#351c42]/10 bg-[#faf9fc] p-3"></div>
        <p class="mt-4 text-[11px] font-black uppercase tracking-[0.14em] text-[#965995]">About this event</p>
        <div id="home-readmore-modal-desc" class="mt-2 max-w-full text-sm leading-relaxed text-[#351c42]/85 whitespace-pre-wrap break-words break-all"></div>
        <div id="home-readmore-modal-footer" class="mt-6"></div>
    </div>
</div>

<script>
    (function () {
        if (window.__eventDescToggleInit) return;
        window.__eventDescToggleInit = true;

        var readModal = document.getElementById('home-event-readmore-modal');
        var readTitle = document.getElementById('home-readmore-modal-title');
        var readDates = document.getElementById('home-readmore-modal-dates');
        var readDesc = document.getElementById('home-readmore-modal-desc');
        var readFooter = document.getElementById('home-readmore-modal-footer');

        function openReadMoreModal(eventId) {
            if (!readModal || !readTitle || !readDates || !readDesc || !readFooter) return;
            var source = document.querySelector('[data-readmore-source="' + eventId + '"]');
            if (!source) return;
            var item = source.closest('[data-events-accordion-item]');
            var footerSrc = item ? item.querySelector('[data-readmore-footer]') : null;
            var tEl = source.querySelector('[data-readmore-title]');
            var dCont = source.querySelector('[data-readmore-dates-content]');
            var descEl = source.querySelector('[data-readmore-desc]');
            readTitle.textContent = tEl ? tEl.textContent.trim() : '';
            readDates.innerHTML = dCont ? dCont.innerHTML : '';
            readDesc.textContent = descEl ? descEl.textContent : '';
            readFooter.innerHTML = '';
            if (footerSrc) {
                readFooter.appendChild(footerSrc.cloneNode(true));
            }
            readModal.classList.remove('hidden');
            readModal.classList.add('flex');
            readModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        function closeReadMoreModal() {
            if (!readModal) return;
            readModal.classList.add('hidden');
            readModal.classList.remove('flex');
            readModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            if (readFooter) readFooter.innerHTML = '';
        }

        /** Kept for accordion open hook; description preview uses CSS line-clamp + always-visible Read more. */
        var setupToggles = function () {};

        window.__refreshHomeEventDescReadMore = setupToggles;

        document.addEventListener('click', function (e) {
            var openBtn = e.target.closest('[data-desc-readmore]');
            if (openBtn) {
                var wrap = openBtn.closest('[data-desc-wrap]');
                var eid = wrap ? wrap.getAttribute('data-readmore-event-id') : null;
                if (eid) {
                    e.preventDefault();
                    e.stopPropagation();
                    openReadMoreModal(eid);
                }
                return;
            }
            if (e.target.closest('[data-readmore-close]') || e.target.closest('[data-readmore-backdrop]')) {
                closeReadMoreModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape' || !readModal || readModal.classList.contains('hidden')) return;
            closeReadMoreModal();
        });
    })();
</script>
