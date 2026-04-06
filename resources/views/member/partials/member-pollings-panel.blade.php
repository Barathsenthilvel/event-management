{{--
    Expects: $memberPollings, $pollingVotedPositionIds (collection of position ids)
--}}
@php
    $votedSet = collect($pollingVotedPositionIds ?? []);
@endphp

<section id="member-pollings" class="scroll-mt-28 space-y-6 rounded-2xl border border-[#351c42]/10 bg-white/90 p-5 shadow-md sm:p-6">
    <div class="flex flex-col gap-2 border-b border-[#351c42]/10 pb-4">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Governance</p>
        <h2 class="mt-1 text-xl font-extrabold text-[#351c42] sm:text-2xl">Polling</h2>
        <p class="mt-1 max-w-2xl text-sm text-[#351c42]/60">Cast your vote for positions where candidates have been added by the office. One vote per position.</p>
    </div>

    @if(session('polling_success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900" role="status">{{ session('polling_success') }}</div>
    @endif
    @if(session('polling_error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert">{{ session('polling_error') }}</div>
    @endif

    @forelse($memberPollings as $poll)
        @php
            $pollDate = $poll->polling_date?->format('Y-m-d');
            $start = $pollDate ? \Carbon\Carbon::parse($pollDate.' '.$poll->polling_from) : null;
            $end = $pollDate ? \Carbon\Carbon::parse($pollDate.' '.$poll->polling_to) : null;
            $open = $start && $end && now()->between($start, $end);
        @endphp
        <article class="rounded-2xl border border-[#351c42]/10 bg-[#f6f3e9] p-4 sm:p-5">
            <h3 class="text-lg font-bold text-[#351c42]">{{ $poll->title }}</h3>
            <p class="mt-1 text-xs font-semibold text-[#351c42]/55">
                {{ $poll->polling_date?->format('d M Y') }}
                · {{ \Illuminate\Support\Carbon::parse($poll->polling_from)->format('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($poll->polling_to)->format('g:i A') }}
                @if($open)
                    <span class="ml-2 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold uppercase text-emerald-800">Open</span>
                @else
                    <span class="ml-2 inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-extrabold uppercase text-slate-700">Outside voting hours</span>
                @endif
            </p>
            <ul class="mt-4 space-y-3">
                @foreach($poll->positions as $ppos)
                    <li class="flex flex-col gap-3 rounded-xl border border-[#351c42]/10 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-bold text-[#351c42]">{{ $ppos->position }}</p>
                            @if($ppos->member)
                                <p class="text-sm text-[#351c42]/75">Candidate: <span class="font-semibold text-[#351c42]">{{ $ppos->member->name }}</span></p>
                            @else
                                <p class="text-sm text-amber-800">Candidate not assigned yet.</p>
                            @endif
                        </div>
                        <div class="shrink-0">
                            @if($votedSet->contains($ppos->id))
                                <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-xs font-extrabold text-emerald-800">Voted</span>
                            @elseif(!$ppos->member_user_id)
                                <span class="text-xs font-semibold text-[#351c42]/45">—</span>
                            @elseif(!$open)
                                <span class="text-xs font-semibold text-[#351c42]/50">Available during scheduled time</span>
                            @else
                                <form method="POST" action="{{ route('member.pollings.vote', $poll) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="position_id" value="{{ $ppos->id }}" />
                                    <button type="submit" class="md-btn-interest">Vote</button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </article>
    @empty
        <p class="rounded-xl border border-dashed border-[#351c42]/20 bg-[#faf9fc] px-4 py-6 text-center text-sm font-semibold text-[#351c42]/55">No live polls right now.</p>
    @endforelse
</section>
