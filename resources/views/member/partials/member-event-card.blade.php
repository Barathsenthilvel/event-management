{{--
    Member event card — same layout as public events expanded row (image + details + footer bar).
    @param \App\Models\Event $event
    @param string $mode — 'tracking' | 'list'
    For tracking: pass $invite (\App\Models\EventInvite)
    For list: pass $alreadyInterested (bool), $myInvite (?EventInvite), $seatsFull (bool)
--}}
@php
    $cover = $event->cover_image_path ? asset('storage/' . $event->cover_image_path) : asset('images/event1.jpg');
    $firstDate = $event->dates->first();
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
    $seatCap = max(0, (int) ($event->seat_limit ?? 0));
    $filled = (int) ($event->interested_count ?? 0);
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
                        <div class="text-[9px] font-black uppercase tracking-wider text-[#351c42]/55 leading-none">Limited</div>
                        <div class="mt-0.5 text-xs font-extrabold tabular-nums text-[#351c42]">{{ $filled }} / {{ $seatCap > 0 ? $seatCap : '—' }}</div>
                    @else
                        <div class="py-0.5 text-[10px] font-black uppercase tracking-wide leading-tight text-[#351c42]">Unlimited</div>
                    @endif
                </div>
            </div>

            <p class="mt-1 text-[11px] font-bold uppercase tracking-wide text-[#965995]">{{ strtoupper((string) $event->status) }}</p>
            <h3 class="mt-1 text-base font-bold text-[#351c42] sm:text-lg">{{ $event->title }}</h3>
            <p class="mt-2 text-sm leading-relaxed text-[#351c42]/80">{{ Str::limit($desc, 220) }}</p>

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

    {{-- Full-width footer bar (same as homepage “Interest registered” strip) --}}
    <div class="border-t border-[#351c42]/10 bg-[#f6f3e9] px-4 py-3 sm:px-6">
        @if($mode === 'tracking' && isset($invite))
            @php $ps = $invite->participation_status; @endphp
            @if($ps === 'participated')
                @php
                    $evDone = $event->status === 'completed';
                    $certReady = $evDone && !empty($event->template_pdf_path);
                @endphp
                <div class="flex flex-col items-center gap-2 text-center sm:flex-row sm:justify-between sm:text-left">
                    <span class="text-sm font-extrabold text-[#351c42]/85">Attended</span>
                    @if($certReady)
                        <a href="{{ route('member.events.certificate', $event) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-[#351c42] px-4 py-2.5 text-sm font-bold text-[#fddc6a] transition hover:brightness-105 sm:w-auto">Download certificate</a>
                    @elseif($evDone)
                        <span class="text-xs font-semibold text-[#351c42]/55">Certificate will be uploaded soon.</span>
                    @else
                        <span class="text-xs font-semibold text-[#351c42]/55">Certificate available after the event is completed.</span>
                    @endif
                </div>
            @elseif($ps === 'not_participated')
                <p class="text-center text-sm font-extrabold text-[#351c42]/75">Did not attend</p>
            @else
                <p class="text-center text-sm font-extrabold text-[#351c42]/75">Interest registered</p>
                <p class="mt-1 text-center text-xs text-[#351c42]/55">We’ll update when your attendance is confirmed.</p>
            @endif
        @else
            @php
                $alreadyInterested = $alreadyInterested ?? false;
                $myInvite = $myInvite ?? null;
                $seatsFull = $seatsFull ?? false;
            @endphp
            @if($alreadyInterested && $myInvite)
                @if($myInvite->participation_status === 'participated')
                    @php
                        $evDoneList = $event->status === 'completed';
                        $certReadyList = $evDoneList && !empty($event->template_pdf_path);
                    @endphp
                    <div class="flex flex-col items-center gap-2 sm:flex-row sm:justify-between">
                        <span class="text-sm font-extrabold text-[#351c42]/85">Attended</span>
                        @if($certReadyList)
                            <a href="{{ route('member.events.certificate', $event) }}" class="text-sm font-bold text-[#965995] underline-offset-2 hover:text-[#351c42] hover:underline">Download certificate</a>
                        @elseif($evDoneList)
                            <span class="text-xs font-semibold text-[#351c42]/55">Certificate coming soon</span>
                        @else
                            <span class="text-xs font-semibold text-[#351c42]/55">Certificate after event completes</span>
                        @endif
                    </div>
                @elseif($myInvite->participation_status === 'not_participated')
                    <p class="text-center text-sm font-extrabold text-[#351c42]/75">Did not attend</p>
                @else
                    <p class="text-center text-sm font-extrabold text-[#351c42]/75">Interest registered</p>
                @endif
            @elseif($alreadyInterested)
                <p class="text-center text-sm font-extrabold text-[#351c42]/75">Interest registered</p>
            @elseif($seatsFull)
                <p class="text-center text-sm font-extrabold text-amber-800">Seat limit reached</p>
            @else
                <form method="POST" action="{{ route('member.events.interest', $event) }}" class="flex justify-center">
                    @csrf
                    <button type="submit" class="md-btn-interest w-full max-w-xs sm:w-auto">Interested</button>
                </form>
            @endif
        @endif
    </div>
</article>
