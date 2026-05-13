{{--
    Member event card — same layout as public events expanded row (image + details + footer bar).
    @param \App\Models\Event $event
    @param string $mode — 'tracking' | 'list'
    For tracking: pass $invite (\App\Models\EventInvite)
    For list: pass $alreadyInterested (bool), $myInvite (?EventInvite)
--}}
@php
    $cover = $event->cover_image_path ? asset('storage/' . $event->cover_image_path) : asset('images/event1.jpg');
    $sortedDates = $event->dates->sortBy('event_date')->values();
    $firstDate = $sortedDates->first();
    $primaryDate = $firstDate?->event_date?->format('d M Y') ?? 'TBA';
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
    $seatCap = max(0, (int) ($event->seat_limit ?? 0));
    $filled = (int) ($event->interested_count ?? 0);
    $seatPct = ($seatLimited && $seatCap > 0) ? min(100, (int) round((100 * $filled) / $seatCap)) : 0;
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
                <div class="inline-flex max-w-full cursor-help items-center gap-3 rounded-full border border-[#351c42]/10 bg-white/80 px-3 py-2 sm:px-4"
                     title="{{ $moreDatesTooltip }}">
                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="text-xs font-semibold text-[#351c42]/70">{{ $scheduleChipText }}</span>
                </div>
                @if ($seatLimited)
                    <div
                        class="w-[6.75rem] shrink-0 rounded-2xl border border-[#351c42]/10 bg-gradient-to-b from-white to-[#faf8fc] px-2.5 py-2 shadow-sm ring-1 ring-[#351c42]/5 sm:w-[7.25rem]"
                        role="group"
                        aria-label="Seats {{ $filled }} of {{ $seatCap > 0 ? $seatCap : '—' }} registered"
                    >
                        <div class="flex items-baseline justify-between gap-1">
                            <span class="text-[9px] font-bold uppercase tracking-wide text-[#965995]">Seats</span>
                            <span class="text-[11px] font-extrabold tabular-nums leading-none text-[#351c42]">
                                {{ $filled }}<span class="mx-0.5 font-semibold text-[#351c42]/35">/</span>{{ $seatCap > 0 ? $seatCap : '—' }}
                            </span>
                        </div>
                        <div
                            class="mt-1.5 h-1 w-full overflow-hidden rounded-full bg-[#351c42]/10"
                            role="progressbar"
                            aria-valuemin="0"
                            aria-valuemax="{{ $seatCap > 0 ? $seatCap : 1 }}"
                            aria-valuenow="{{ min($filled, $seatCap > 0 ? $seatCap : $filled) }}"
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
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-[#351c42]/10 bg-[#f6f3e9]/80 px-2.5 py-1.5 shadow-sm"
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

            @php $eventStatusKey = strtolower((string) $event->status); @endphp
            <div class="mt-1 flex flex-wrap items-center gap-2">
                @if($eventStatusKey === 'live')
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300 bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-emerald-800">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </span>
                        Live
                    </span>
                @elseif($eventStatusKey === 'upcoming')
                    <span class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-indigo-800">Upcoming</span>
                @elseif($eventStatusKey === 'completed')
                    <span class="inline-flex items-center rounded-full border border-[#351c42]/15 bg-[#f6f3e9] px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-[#351c42]/80">Completed</span>
                @elseif($eventStatusKey === 'cancelled')
                    <span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-rose-700">Cancelled</span>
                @elseif($eventStatusKey !== '')
                    <span class="inline-flex items-center rounded-full border border-[#351c42]/15 bg-white px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-[#351c42]/75">{{ strtoupper((string) $event->status) }}</span>
                @endif
            </div>
            @if($eventStatusKey !== 'live')
                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold text-[#351c42]/70">
                    <span>{{ $primaryDate }}</span>
                    @if($extraDatesCount > 0)
                        <span class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[10px] font-extrabold text-indigo-700 cursor-help"
                              title="{{ $moreDatesTooltip }}">
                            +{{ $extraDatesCount }} more
                        </span>
                    @endif
                </div>
            @endif
            <h3 class="mt-1 text-base font-bold text-[#351c42] break-words sm:text-lg">{{ $event->title }}</h3>
            @if($hasDesc)
                <div class="mt-2" data-desc-wrap>
                    <p
                        class="min-h-[4.5rem] text-sm leading-relaxed text-[#351c42]/80 break-all"
                        data-desc-text
                        style="display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3;overflow:hidden;"
                    >
                        {{ $desc }}
                    </p>
                    <button
                        type="button"
                        class="mt-2 hidden cursor-pointer items-center text-xs font-extrabold text-[#965995] hover:text-[#351c42]"
                        data-desc-toggle
                        aria-expanded="false"
                        title="Read full description"
                    >
                        Read more
                    </button>
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
                    <p class="mt-2 text-sm font-bold text-[#351c42] break-words">{{ $organizer }}</p>
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
                    <p class="mt-2 text-sm font-bold text-[#351c42] break-words">{{ $event->venue ?: 'Venue not specified' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer: light strip for attendance / certificate; dark bar with member avatar stack + action for interest --}}
    @if($mode === 'tracking' && isset($invite))
        @php
            $ps = $invite->participation_status;
            $inviteConfirmed = $invite->has_confirmed_interest ?? true;
        @endphp
        @php
            $attendanceQrUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'admin.events.attendance.consume',
                now()->addDays(30),
                ['event' => $event->id, 'source' => 'invite', 'entryId' => $invite->id]
            );
        @endphp
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
        @elseif(! $inviteConfirmed)
            <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                @include('member.partials.member-event-interested-stack', ['event' => $event])
                <div class="min-w-0 shrink-0 sm:ml-auto text-right">
                    <p class="text-sm font-extrabold text-[#fddc6a]">You’re invited</p>
                    <p class="mt-0.5 text-xs text-white/75">Confirm on the portal to register for this event.</p>
                </div>
                <form method="POST" action="{{ route('member.events.interest', $event) }}" class="shrink-0 w-full sm:w-auto sm:ml-2" onsubmit="this.querySelector('button[type=submit]')?.setAttribute('disabled','disabled')">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex w-full min-h-[2.1rem] cursor-pointer items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#351c42] shadow-sm transition hover:brightness-105 sm:w-auto"
                    >
                        Confirm attendance
                    </button>
                </form>
            </div>
        @else
            <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                @include('member.partials.member-event-interested-stack', ['event' => $event])
                <div class="min-w-0 shrink-0 text-right sm:ml-auto">
                    <p class="text-sm font-extrabold text-[#fddc6a]">Interest registered</p>
                    <p class="mt-0.5 text-xs text-white/70">We’ll update when your attendance is confirmed.</p>
                </div>
                <button
                    type="button"
                    class="inline-flex min-h-[2.1rem] cursor-pointer items-center justify-center rounded-full border border-[#fddc6a]/55 bg-transparent px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#fddc6a] transition hover:bg-[#fddc6a]/10 sm:ml-2"
                    data-open-attendance-qr
                    data-qr-title="{{ $event->title }}"
                    data-qr-value="{{ $attendanceQrUrl }}"
                >
                    Show Entry QR
                </button>
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
            @elseif(! ($myInvite->has_confirmed_interest ?? true))
                <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                    @include('member.partials.member-event-interested-stack', ['event' => $event])
                    <p class="min-w-0 shrink-0 text-sm font-extrabold text-[#fddc6a] sm:ml-auto">You’re invited</p>
                    <form method="POST" action="{{ route('member.events.interest', $event) }}" class="shrink-0 w-full sm:w-auto" onsubmit="this.querySelector('button[type=submit]')?.setAttribute('disabled','disabled')">
                        @csrf
                        <button type="submit" class="inline-flex w-full min-h-[2.1rem] cursor-pointer items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold text-[#351c42] shadow-sm transition hover:brightness-105 sm:w-auto">Confirm attendance</button>
                    </form>
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
        @else
            @php
                $memberAttendLabel = match ($event->status ?? '') {
                    'live' => 'Attend now',
                    'upcoming' => 'Attend',
                    default => 'Register',
                };
            @endphp
            <div class="flex flex-wrap items-center gap-3 border-t border-[#351c42]/20 bg-[#351c42] px-4 py-3 sm:px-6 rounded-b-2xl">
                @include('member.partials.member-event-interested-stack', ['event' => $event])
                <form method="POST" action="{{ route('member.events.interest', $event) }}" class="shrink-0 sm:ml-auto" onsubmit="this.querySelector('button[type=submit]')?.setAttribute('disabled','disabled')">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex min-h-[2.1rem] min-w-[6.75rem] cursor-pointer items-center justify-center rounded-full border border-[#fddc6a]/55 bg-gradient-to-r from-[#fddc6a] to-[#f6cf61] px-4 py-1.5 text-xs font-extrabold tracking-wide text-[#351c42] shadow-sm transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-[#351c42] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ $memberAttendLabel }}
                    </button>
                </form>
            </div>
        @endif
    @endif
</article>

<script>
    (function () {
        if (window.__memberEventDescToggleInit) return;
        window.__memberEventDescToggleInit = true;

        var setupToggles = function () {
            document.querySelectorAll('[data-desc-wrap]').forEach(function (wrap) {
                var text = wrap.querySelector('[data-desc-text]');
                var btn = wrap.querySelector('[data-desc-toggle]');
                if (!text || !btn) return;
                if (text.scrollHeight > text.clientHeight + 2) {
                    btn.classList.remove('hidden');
                    btn.classList.add('inline-flex');
                } else {
                    text.style.display = '';
                    text.style.webkitBoxOrient = '';
                    text.style.webkitLineClamp = '';
                    text.style.overflow = '';
                    btn.remove();
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupToggles);
        } else {
            setupToggles();
        }

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-desc-toggle]');
            if (!btn) return;
            var wrap = btn.closest('[data-desc-wrap]');
            var text = wrap ? wrap.querySelector('[data-desc-text]') : null;
            if (!text) return;

            var expanded = btn.getAttribute('aria-expanded') === 'true';
            if (expanded) {
                text.style.display = '-webkit-box';
                text.style.webkitBoxOrient = 'vertical';
                text.style.webkitLineClamp = '3';
                text.style.overflow = 'hidden';
                btn.setAttribute('aria-expanded', 'false');
                btn.textContent = 'Read more';
                btn.setAttribute('title', 'Read full description');
            } else {
                text.style.display = '';
                text.style.webkitBoxOrient = '';
                text.style.webkitLineClamp = '';
                text.style.overflow = '';
                btn.setAttribute('aria-expanded', 'true');
                btn.textContent = 'Show less';
                btn.setAttribute('title', 'Show less text');
            }
        });
    })();
</script>
