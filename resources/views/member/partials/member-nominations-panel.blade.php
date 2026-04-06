{{--
    Expects: $memberNominations, $nominationInterestPositionIds (collection of position ids), $member (auth user)
--}}
@php
    $interestSet = collect($nominationInterestPositionIds ?? []);
@endphp

<section id="member-nominations" class="scroll-mt-28 space-y-8 rounded-2xl border border-[#351c42]/10 bg-white/90 p-5 shadow-md sm:p-6">
    <div class="flex flex-col gap-2 border-b border-[#351c42]/10 pb-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Governance</p>
            <h2 class="mt-1 text-xl font-extrabold text-[#351c42] sm:text-2xl">Nominations</h2>
            <p class="mt-1 max-w-2xl text-sm text-[#351c42]/60">The office publishes open roles like an announcement. Members tap “I’m interested” to add themselves; interested members appear in the admin nomination list.</p>
        </div>
    </div>

    @if(session('nomination_success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900" role="status">{{ session('nomination_success') }}</div>
    @endif
    @if(session('nomination_error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert">{{ session('nomination_error') }}</div>
    @endif

    @forelse($memberNominations as $nom)
        <article class="rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-4 sm:p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-[#351c42]">{{ $nom->title }}</h3>
                    @if($nom->terms)
                        <p class="mt-2 text-sm leading-relaxed text-[#351c42]/70">{{ \Illuminate\Support\Str::limit($nom->terms, 220) }}</p>
                    @endif
                    <p class="mt-2 text-xs font-semibold text-[#351c42]/50">
                        Polling window · {{ $nom->polling_date?->format('d M Y') }} · {{ \Illuminate\Support\Carbon::parse($nom->polling_from)->format('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($nom->polling_to)->format('g:i A') }}
                    </p>
                </div>
            </div>
            <ul class="mt-4 space-y-3">
                @foreach($nom->positions as $pos)
                    <li class="flex flex-col gap-3 rounded-xl border border-[#351c42]/10 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="font-bold text-[#351c42]">{{ $pos->position }}</p>
                            <p class="text-xs text-[#351c42]/50">{{ $pos->entries_count }} member(s) interested</p>
                        </div>
                        <div class="shrink-0">
                            @if($interestSet->contains($pos->id))
                                <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-xs font-extrabold text-emerald-800">Interest registered</span>
                            @else
                                <form method="POST" action="{{ route('member.nominations.interest', [$nom, $pos]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="md-btn-interest">I’m interested</button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </article>
    @empty
        <p class="rounded-xl border border-dashed border-[#351c42]/20 bg-[#faf9fc] px-4 py-6 text-center text-sm font-semibold text-[#351c42]/55">No open nominations right now.</p>
    @endforelse
</section>
