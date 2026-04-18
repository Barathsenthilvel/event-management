{{--
    Dashboard popup — compact polling card (matches nomination popup style).
    Expects: $poll (Polling model with positions + candidates), $pollingDashboardVotedIds, $pollingDashboardVotes
--}}
@php
    $votedSet = collect($pollingDashboardVotedIds ?? []);
    $votesByPosition = collect($pollingDashboardVotes ?? []);
    $pollStartDate = $poll->polling_date?->format('Y-m-d');
    $pollEndDate = ($poll->polling_date_to ?? $poll->polling_date)?->format('Y-m-d');
    $start = $pollStartDate ? \Carbon\Carbon::parse($pollStartDate.' '.$poll->polling_from) : null;
    $end = $pollEndDate ? \Carbon\Carbon::parse($pollEndDate.' '.$poll->polling_to) : null;
    $open = $start && $end && now()->between($start, $end);
    $pollEndDateTimeIso = $end ? $end->toIso8601String() : null;

    $nextPosition = $poll->positions->first(fn ($p) => ! $votedSet->contains($p->id));
@endphp

<article class="md-announce-card md-popup-compact p-3">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 pr-2">
            <p class="md-popup-subtitle text-[11px] font-bold uppercase tracking-[0.18em] text-white/75">Polling</p>
            <h3 class="md-popup-title mt-1 break-all text-2xl font-extrabold leading-[1.15] tracking-tight text-white">{{ $poll->title }}</h3>
            <p class="md-popup-meta mt-1.5 text-[12px] font-semibold text-white/70">
                {{ $poll->polling_date?->format('d M Y') ?? '—' }}
                @if($poll->polling_date_to && $poll->polling_date_to->toDateString() !== $poll->polling_date->toDateString())
                    – {{ $poll->polling_date_to?->format('d M Y') ?? '—' }}
                @endif
                · {{ \Illuminate\Support\Carbon::parse($poll->polling_from)->format('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($poll->polling_to)->format('g:i A') }}
                @if($open)
                    <span class="ml-2 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold uppercase text-emerald-800">Open</span>
                @else
                    <span class="ml-2 inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-extrabold uppercase text-slate-700">Closed</span>
                @endif
            </p>
            <p class="mt-2">
                <span
                    class="inline-flex items-center rounded-full border border-sky-300/60 bg-sky-500/20 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-sky-100"
                    data-dashboard-countdown
                    data-countdown-prefix="Ends in"
                    data-countdown-end="{{ $pollEndDateTimeIso }}"
                >
                    Ends in --
                </span>
            </p>
        </div>
        <button
            type="button"
            class="rounded-full p-1.5 text-white/70 transition hover:bg-white/10 hover:text-white"
            aria-label="Hide polling prompt"
            data-dashboard-dismiss-polling
            data-polling-id="{{ $poll->id }}"
        >✕</button>
    </div>

    <div class="mt-2.5 border-t border-white/10 pt-2.5">
        <p class="mb-2 text-[10px] font-black uppercase tracking-[0.16em] text-[#fddc6a]/90">Vote now</p>

        @if(!$nextPosition)
            <p class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white/75">You have voted for all positions in this poll.</p>
        @else
            @php
                $hasCandidates = $nextPosition->candidates->isNotEmpty();
                $myVote = $votesByPosition->get($nextPosition->id);
            @endphp
            <div class="md-nom-announce-row">
                <p class="w-full break-all text-sm font-bold leading-snug text-[#351c42]">{{ $nextPosition->position }}</p>

                @if(!$hasCandidates)
                    <p class="w-full text-[12px] font-semibold text-amber-800">Candidates are not listed for this position yet.</p>
                @else
                    <div class="flex w-full flex-col gap-2 max-h-28 overflow-y-auto pr-0.5">
                        @foreach($nextPosition->candidates as $cand)
                            @php
                                $isChosen = $myVote && (int) $myVote->candidate_user_id === (int) $cand->id;
                            @endphp
                            @if(!$open)
                                <div class="flex items-center justify-between rounded-xl border border-[#351c42]/12 bg-white/80 px-3 py-2 text-xs font-semibold text-[#351c42]/70">
                                    <span class="truncate">{{ $cand->name }}</span>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-[#351c42]/45">Closed</span>
                                </div>
                            @elseif($isChosen)
                                <div class="flex items-center justify-between rounded-xl border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-800">
                                    <span class="truncate">{{ $cand->name }}</span>
                                    <span class="text-[10px] font-black uppercase tracking-wider">Selected</span>
                                </div>
                            @else
                                <form method="POST" action="{{ route('member.pollings.vote', $poll) }}" class="block">
                                    @csrf
                                    <input type="hidden" name="position_id" value="{{ $nextPosition->id }}">
                                    <input type="hidden" name="candidate_user_id" value="{{ $cand->id }}">
                                    <button type="submit" class="flex w-full items-center justify-between rounded-xl border border-[#351c42]/12 bg-white px-3 py-2 text-left text-xs font-semibold text-[#351c42] transition hover:bg-[#f5f1f9]">
                                        <span class="truncate">{{ $cand->name }}</span>
                                        <span class="rounded-full bg-[#24122e] px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide text-[#fddc6a]">Vote</span>
                                    </button>
                                </form>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</article>

