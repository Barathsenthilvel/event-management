{{--
    Member event card — same layout as public events expanded row (image + details + footer bar).
    @param \App\Models\Event $event
    @param string $mode — 'tracking' | 'list'
    For tracking: pass $invite (\App\Models\EventInvite)
    For list: pass $alreadyInterested (bool), $myInvite (?EventInvite), $seatsFull (bool)
--}}
@php
    $cover = $event->cover_image_path ? asset('storage/' . $event->cover_image_path) : asset('images/event1.jpg');
    $sortedDates = $event->dates->sortBy('event_date')->values();
    $firstDate = $sortedDates->first();
    $day = $firstDate?->event_date?->format('d') ?? '—';
    $month = strtoupper((string) ($firstDate?->event_date?->format('M') ?? 'TBA'));
    $timeSlots = $sortedDates
        ->map(function ($d) {
            $start = $d->start_time ? \Illuminate\Support\Carbon::parse($d->start_time)->format('h:i A') : null;
            $end = $d->end_time ? \Illuminate\Support\Carbon::parse($d->end_time)->format('h:i A') : null;
            return $start && $end ? ($start . ' - ' . $end) : ($start ?: ($end ?: 'Time TBA'));
        })
        ->filter()
        ->unique()
        ->values();
    $timeRange = $timeSlots->count() > 1 ? 'Multiple time slots' : ($timeSlots->first() ?? 'Time TBA');
    $organizer = $event->creator?->name ?? 'GNAT Team';
    $desc = $event->description ?: 'Join us for this GNAT event. More details will be shared with registered members.';
    $seatLimited = ($event->seat_mode ?? '') === 'limited';
    $seatCap = max(0, (int) ($event->seat_limit ?? 0));
    $filled = (int) ($event->interested_count ?? 0);
    $seatsFullComputed = $seatLimited && $seatCap > 0 && $filled >= $seatCap;
    $seatsFull = isset($seatsFull) ? (bool) $seatsFull : $seatsFullComputed;
@endphp
<article class="overflow-hidden rounded-2xl border border-[#351c42]/10 bg-white shadow-sm">
    <div class="grid gap-6 p-4 sm:p-6 md:grid-cols-[minmax(0,280px)_1fr] md:items-stretch">
        <div class="relative min-h-[12rem] overflow-hidden rounded-2xl border border-[#351c42]/10 bg-[#f6f3e9] md:min-h-[14rem]">
            <img src="{{ $cover }}" alt="{{ $event->title }}" class="h-full w-full object-cover" loading="lazy" />
            <div class="absolute left-3 top-3 rounded-full bg-[#fddc6a] px-3 py-2 text-center shadow-sm">
                <div class="text-lg font-extrabold leading-none text-[#351c42]">{{ $day }}</div>
                <div class="mt-0.5 inline-block rounded bg-white/70 px-2 py-0.5 text-[10px] font-extrabold tracking-widest text-[#965995]">{{ $month }}</div>
            </div>
        </div>

        <div class="flex min-w-0 flex-col">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="inline-flex max-w-full items-center gap-3 rounded-full border border-[#351c42]/10 bg-white/80 px-3 py-2 sm:px-4">
                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="text-xs font-semibold text-[#351c42]/70">{{ $timeRange }}</span>
                </div>
                <div class="shrink-0 rounded-full border border-[#351c42]/15 bg-white px-3 py-1.5 text-right shadow-sm" aria-label="{{ $seatLimited ? 'Limited seats' : 'Unlimited seats' }}">
                    @if ($seatLimited)
                        @if($seatsFull)
                            <div class="text-[9px] font-black uppercase tracking-wider text-rose-600 leading-none">Registration</div>
                            <div class="mt-0.5 text-[10px] font-extrabold uppercase text-rose-800">Closed</div>
                        @else
                            <div class="text-[9px] font-black uppercase tracking-wider text-[#351c42]/55 leading-none">Limited</div>
                            <div class="mt-0.5 text-xs font-extrabold tabular-nums text-[#351c42]">{{ $filled }} / {{ $seatCap > 0 ? $seatCap : '—' }}</div>
                        @endif
                    @else
                        <div class="py-0.5 text-[10px] font-black uppercase tracking-wide leading-tight text-[#351c42]">Unlimited</div>
                    @endif
                </div>
            </div>

            <p class="mt-1 text-[11px] font-bold uppercase tracking-wide text-[#965995]">{{ strtoupper((string) $event->status) }}</p>
            <h3 class="mt-1 text-base font-bold text-[#351c42] sm:text-lg">{{ $event->title }}</h3>
            <p class="mt-2 text-sm leading-relaxed text-[#351c42]/80">{{ Str::limit($desc, 220) }}</p>
            @if($sortedDates->isNotEmpty())
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
            @endif

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-[#351c42]/10 bg-[#f6f3e9] p-4">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Organizer
                    </div>
                    <p class="mt-2 text-sm font-bold text-[#351c42]">{{ $organizer }}</p>
                </div>
                <div class="rounded-2xl border border-[#351c42]/10 bg-[#f6f3e9] p-4">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Venue
                    </div>
                    <p class="mt-2 text-sm font-bold text-[#351c42]">{{ $event->venue ?: 'Venue not specified' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer: light strip for attendance / certificate; dark bar with member avatar stack + action for interest --}}
    @if($mode === 'tracking' && isset($invite))
        @php $ps = $invite->participation_status; @endphp
        @if($ps === 'participated')
            <div class="border-t border-[#351c42]/10 bg-[#f6f3e9] px-4 py-3 sm:px-6">
                @php $certReady = ! empty($event->template_pdf_path); @endphp
                <div class="flex flex-col items-center gap-2 text-center sm:flex-row sm:justify-between sm:text-left">
                    <span class="text-sm font-extrabold text-[#351c42]/85">Attended</span>
                    @if($certReady)
                        <a href="{{ route('member.events.certificate', $event) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-[#351c42] px-4 py-2.5 text-sm font-bold text-[#fddc6a] transition hover:brightness-105 sm:w-auto">Download certificate</a>
                    @else
                        <span class="text-xs font-semibold text-[#351c42]/55">Certificate will be uploaded soon.</span>
                    @endif
                </div>
            </div>
        @elseif($ps === 'not_participated')
            <div class="border-t border-[#351c42]/10 bg-[#f6f3e9] px-4 py-3 sm:px-6">
                <p class="text-center text-sm font-extrabold text-[#351c42]/75">Did not attend</p>
            </div>
        @else
            <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                @include('member.partials.member-event-interested-stack', ['event' => $event])
                <div class="min-w-0 shrink-0 text-right sm:ml-auto">
                    <p class="text-sm font-extrabold text-[#fddc6a]">Interest registered</p>
                    <p class="mt-0.5 text-xs text-white/70">We’ll update when your attendance is confirmed.</p>
                </div>
            </div>
        @endif
    @else
        @php
            $alreadyInterested = $alreadyInterested ?? false;
            $myInvite = $myInvite ?? null;
        @endphp
        @if($alreadyInterested && $myInvite)
            @if($myInvite->participation_status === 'participated')
                <div class="border-t border-[#351c42]/10 bg-[#f6f3e9] px-4 py-3 sm:px-6">
                    @php $certReadyList = ! empty($event->template_pdf_path); @endphp
                    <div class="flex flex-col items-center gap-2 sm:flex-row sm:justify-between">
                        <span class="text-sm font-extrabold text-[#351c42]/85">Attended</span>
                        @if($certReadyList)
                            <a href="{{ route('member.events.certificate', $event) }}" class="text-sm font-bold text-[#965995] underline-offset-2 hover:text-[#351c42] hover:underline">Download certificate</a>
                        @else
                            <span class="text-xs font-semibold text-[#351c42]/55">Certificate coming soon</span>
                        @endif
                    </div>
                </div>
            @elseif($myInvite->participation_status === 'not_participated')
                <div class="border-t border-[#351c42]/10 bg-[#f6f3e9] px-4 py-3 sm:px-6">
                    <p class="text-center text-sm font-extrabold text-[#351c42]/75">Did not attend</p>
                </div>
            @else
                <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                    @include('member.partials.member-event-interested-stack', ['event' => $event])
                    <p class="min-w-0 shrink-0 text-sm font-extrabold text-[#fddc6a] sm:ml-auto">Interest registered</p>
                </div>
            @endif
        @elseif($alreadyInterested)
            <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                @include('member.partials.member-event-interested-stack', ['event' => $event])
                <p class="min-w-0 shrink-0 text-sm font-extrabold text-[#fddc6a] sm:ml-auto">Interest registered</p>
            </div>
        @elseif($seatsFull)
            <div class="rounded-b-2xl border-t border-rose-200 bg-rose-50 px-4 py-3 sm:px-6">
                <p class="text-center text-sm font-extrabold text-rose-900">Registration closed</p>
                <p class="mt-1 text-center text-xs font-semibold text-rose-800/90">Seat limit reached ({{ $filled }} / {{ $seatCap }}).</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6">
                @include('member.partials.member-event-interested-stack', ['event' => $event])
            </div>
        @else
            <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                @include('member.partials.member-event-interested-stack', ['event' => $event])
                <form method="POST" action="{{ route('member.events.interest', $event) }}" class="shrink-0 sm:ml-auto" onsubmit="this.querySelector('button[type=submit]')?.setAttribute('disabled','disabled')">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex min-h-[2.1rem] min-w-[6.75rem] items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#351c42] shadow-sm transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#351c42] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        Interested
                    </button>
                </form>
            </div>
        @endif
    @endif
</article>
