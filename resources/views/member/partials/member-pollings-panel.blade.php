{{--
    Expects: $memberPollings, $pollingVotedPositionIds, $memberPollingVotes
--}}
@php
    $pollingResultStats = $pollingResultStats ?? [];
    $votedSet = collect($pollingVotedPositionIds ?? []);
    $votesByPosition = collect($memberPollingVotes ?? []);
    $thanksPollId = session('polling_thanks_poll_id');
    $thanksRelevant = session('polling_thanks_modal') && (
        $thanksPollId === null
        || $memberPollings->contains(fn ($p) => (int) $p->id === (int) $thanksPollId)
    );
    $successPollId = session('polling_success_poll_id');
    $showVoteSuccessBanner = session('polling_success')
        && ! session('polling_thanks_modal')
        && (
            $successPollId === null
            || $memberPollings->contains(fn ($p) => (int) $p->id === (int) $successPollId)
        );
@endphp

<section id="member-pollings" class="scroll-mt-28 space-y-6 rounded-2xl border border-[#351c42]/10 bg-white/90 p-5 shadow-md sm:p-6">
    <div class="flex flex-col gap-2 border-b border-[#351c42]/10 pb-4">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Governance</p>
        <h2 class="mt-1 text-xl font-extrabold text-[#351c42] sm:text-2xl">Polling</h2>
        <p class="mt-1 max-w-2xl text-sm text-[#351c42]/60">Cast your vote for each position during the scheduled window. Tap a name to choose — your selection is private; vote totals are not shown here.</p>
    </div>

    @if($showVoteSuccessBanner)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900" role="status">{{ session('polling_success') }}</div>
    @endif
    @if(session('polling_error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert">{{ session('polling_error') }}</div>
    @endif

    @forelse($memberPollings as $poll)
        @php
            $pollStartDate = $poll->polling_date?->format('Y-m-d');
            $pollEndDate = ($poll->polling_date_to ?? $poll->polling_date)?->format('Y-m-d');
            $start = $pollStartDate ? \Carbon\Carbon::parse($pollStartDate.' '.$poll->polling_from) : null;
            $end = $pollEndDate ? \Carbon\Carbon::parse($pollEndDate.' '.$poll->polling_to) : null;
            $open = $start && $end && now()->between($start, $end);
            $pollEndDateTimeIso = $end ? $end->toIso8601String() : null;
        @endphp
        <article class="rounded-2xl border border-[#351c42]/10 bg-[#f6f3e9] p-4 sm:p-5">
            <h3 class="text-lg font-bold text-[#351c42]">{{ $poll->title }}</h3>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <p class="text-xs font-semibold text-[#351c42]/55">
                    @if($poll->polling_date_to && $poll->polling_date_to->toDateString() !== $poll->polling_date->toDateString())
                        {{ $poll->polling_date?->format('d M Y') }} – {{ $poll->polling_date_to->format('d M Y') }}
                    @else
                        {{ $poll->polling_date?->format('d M Y') }}
                    @endif
                    · {{ \Illuminate\Support\Carbon::parse($poll->polling_from)->format('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($poll->polling_to)->format('g:i A') }}
                </p>
                @if($open)
                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold uppercase text-emerald-800">Open</span>
                @else
                    <span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-extrabold uppercase text-slate-700">Outside voting hours</span>
                @endif
                <span
                    class="inline-flex items-center rounded-full border border-sky-300/60 bg-sky-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-sky-800"
                    data-polling-countdown
                    data-countdown-prefix="Ends in"
                    data-countdown-end="{{ $pollEndDateTimeIso }}"
                >
                    Ends in --
                </span>
            </div>

            <ul class="mt-4 space-y-5">
                @foreach($poll->positions as $ppos)
                    @php
                        $hasCandidates = $ppos->candidates->isNotEmpty();
                        $votedThis = $votedSet->contains($ppos->id);
                        $myVote = $votesByPosition->get($ppos->id);
                    @endphp
                    <li class="rounded-xl border border-[#351c42]/10 bg-white p-4">
                        <p class="text-sm font-extrabold text-[#351c42]">{{ $ppos->position }}</p>

                        @if(!$hasCandidates)
                            <p class="mt-2 text-sm text-amber-800">Candidates are not listed for this position yet.</p>
                        @else
                            <ul class="mt-3 space-y-2">
                                @foreach($ppos->candidates as $cand)
                                    @php
                                        $isChosen = $votedThis && $myVote && (int) $myVote->candidate_user_id === (int) $cand->id;
                                        $fillPct = $votedThis ? ($isChosen ? 100 : 0) : 0;
                                    @endphp
                                    <li>
                                        @if($votedThis)
                                            <div class="relative overflow-hidden rounded-2xl border border-[#c4b5d5]/70 bg-[#faf7fd]">
                                                <div
                                                    class="absolute inset-y-0 left-0 rounded-2xl bg-gradient-to-r from-[#c9b6e0] via-[#d4c4e8] to-[#dfc9ee] transition-all duration-500"
                                                    style="width: {{ $fillPct }}%"
                                                ></div>
                                                <div class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                                    <div class="flex min-w-0 items-center gap-2">
                                                        <span class="truncate text-sm font-bold text-[#351c42]">{{ $cand->name }}</span>
                                                        @if($isChosen)
                                                            <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-[#351c42]/30 bg-white text-[#351c42]" title="Your vote">
                                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($isChosen)
                                                        <span class="shrink-0 text-[10px] font-black uppercase tracking-wide text-[#351c42]/60">Your vote</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif(!$open)
                                            <div class="relative overflow-hidden rounded-2xl border border-[#351c42]/15 bg-[#faf9fc] opacity-70">
                                                <div class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                                    <span class="truncate text-sm font-semibold text-[#351c42]/70">{{ $cand->name }}</span>
                                                    <span class="text-[10px] font-bold text-[#351c42]/45">Closed</span>
                                                </div>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('member.pollings.vote', $poll) }}" class="block">
                                                @csrf
                                                <input type="hidden" name="position_id" value="{{ $ppos->id }}">
                                                <input type="hidden" name="candidate_user_id" value="{{ $cand->id }}">
                                                <button type="submit" class="group relative w-full overflow-hidden rounded-2xl border border-[#c4b5d5]/80 bg-white text-left transition hover:border-[#965995]/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995]/40">
                                                    <div class="absolute inset-y-0 left-0 w-0 rounded-2xl bg-gradient-to-r from-[#e8dff5] to-[#ddd0ec] transition-all duration-300 group-hover:w-[12%]"></div>
                                                    <span class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                                        <span class="truncate text-sm font-bold text-[#351c42]">{{ $cand->name }}</span>
                                                        <span class="shrink-0 rounded-full bg-[#351c42] px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide text-[#fddc6a]">Vote</span>
                                                    </span>
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>

            @if(isset($pollingResultStats[$poll->id]) && $pollingResultStats[$poll->id] !== [])
                <div class="mt-6 space-y-4 rounded-2xl border border-emerald-200/80 bg-emerald-50/60 p-4">
                    <p class="text-[11px] font-extrabold uppercase tracking-wide text-emerald-900">Results (published by office)</p>
                    @foreach($poll->positions as $ppos)
                        @php $stats = $pollingResultStats[$poll->id][$ppos->id] ?? null; @endphp
                        @continue(!$stats)
                        <div class="rounded-xl border border-white/60 bg-white/90 p-3">
                            <p class="text-sm font-extrabold text-[#351c42]">{{ $ppos->position }}</p>
                            @if(! empty($stats['winner_name']))
                                <p class="mt-1 text-xs text-[#351c42]/80">Official winner: <strong class="text-[#351c42]">{{ $stats['winner_name'] }}</strong></p>
                            @endif
                            <ul class="mt-3 space-y-2">
                                @foreach($stats['candidates'] as $row)
                                    <li class="relative overflow-hidden rounded-xl border border-[#351c42]/10 bg-[#faf9fc]">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 rounded-xl bg-gradient-to-r from-emerald-200/80 to-emerald-100/40" style="width: {{ $row['bar_percent'] }}%"></div>
                                        <div class="relative flex items-center justify-between gap-2 px-3 py-2 text-xs">
                                            <span class="font-bold text-[#351c42]">{{ $row['name'] }}</span>
                                            <span class="tabular-nums font-extrabold text-[#351c42]">{{ $row['votes'] }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    @empty
        <p class="rounded-xl border border-dashed border-[#351c42]/20 bg-[#faf9fc] px-4 py-6 text-center text-sm font-semibold text-[#351c42]/55">No polls to show right now.</p>
    @endforelse

    {{-- Thank-you modal (after successful vote) --}}
    @if($thanksRelevant)
        <div
            id="polling-thanks-overlay"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#351c42]/50 p-4 backdrop-blur-[2px]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="polling-thanks-title"
        >
            <div class="w-full max-w-md rounded-3xl border border-[#351c42]/10 bg-white p-8 shadow-2xl">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 id="polling-thanks-title" class="mt-5 text-center text-xl font-extrabold text-[#351c42]">Thank you for voting</h3>
                <p class="mt-3 text-center text-sm leading-relaxed text-[#351c42]/65">
                    Your ballot has been recorded. The association may publish aggregated results according to its own schedule; individual votes stay private.
                </p>
                <button type="button" class="polling-thanks-dismiss mt-8 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:bg-[#4a2660]">
                    Continue
                </button>
            </div>
        </div>
        <script>
            (() => {
                const overlay = document.getElementById("polling-thanks-overlay");
                if (!overlay) return;
                function close() { overlay.classList.add("hidden"); }
                overlay.addEventListener("click", (e) => {
                    if (e.target === overlay || e.target.closest(".polling-thanks-dismiss")) close();
                });
                document.addEventListener("keydown", (e) => {
                    if (e.key === "Escape" && !overlay.classList.contains("hidden")) close();
                });
            })();
        </script>
    @endif
</section>
<script>
    (() => {
        const countdownNodes = Array.from(document.querySelectorAll("[data-polling-countdown]"));
        if (countdownNodes.length === 0) return;

        function formatCountdown(ms) {
            const totalSeconds = Math.floor(ms / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            if (days > 0) return `${days}d ${hours}h ${minutes}m`;
            if (hours > 0) return `${hours}h ${minutes}m ${seconds}s`;
            return `${minutes}m ${seconds}s`;
        }

        function refreshCountdowns() {
            const now = Date.now();
            countdownNodes.forEach((node) => {
                const end = node.getAttribute("data-countdown-end");
                const prefix = node.getAttribute("data-countdown-prefix") || "Ends in";
                if (!end) {
                    node.textContent = `${prefix} --`;
                    return;
                }
                const endMs = new Date(end).getTime();
                if (Number.isNaN(endMs)) {
                    node.textContent = `${prefix} --`;
                    return;
                }
                const diff = endMs - now;
                if (diff <= 0) {
                    node.textContent = "Closed";
                    return;
                }
                node.textContent = `${prefix} ${formatCountdown(diff)}`;
            });
        }

        refreshCountdowns();
        window.setInterval(refreshCountdowns, 1000);
    })();
</script>
