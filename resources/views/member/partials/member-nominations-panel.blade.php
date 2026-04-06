{{--
    Expects: $memberNominations, $nominationInterestPositionIds, $member
--}}
@php
    $interestSet = collect($nominationInterestPositionIds ?? []);
@endphp

<style>
    .nom-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(53, 28, 66, 0.1);
        background: #fff;
        box-shadow: 0 4px 24px -4px rgba(53, 28, 66, 0.08);
        overflow: hidden;
    }
    .nom-media {
        aspect-ratio: 4 / 3;
        max-height: 11rem;
    }
    @media (min-width: 768px) {
        .nom-media {
            aspect-ratio: auto;
            max-height: none;
        }
    }
</style>

<section id="member-nominations" class="scroll-mt-28 space-y-6">
    @if(session('nomination_success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900" role="status">{{ session('nomination_success') }}</div>
    @endif
    @if(session('nomination_error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert">{{ session('nomination_error') }}</div>
    @endif

    <div class="flex flex-col gap-6">
        @forelse($memberNominations as $nom)
            @php
                $coverUrl = $nom->cover_image_path ? asset('storage/' . ltrim($nom->cover_image_path, '/')) : null;
            @endphp
            <article class="nom-card">
                <div class="flex flex-col md:flex-row md:items-stretch">
                    {{-- Media: narrow column; image object-cover inside fixed bounds (no full-width hero) --}}
                    <div class="relative w-full shrink-0 overflow-hidden border-b border-[#351c42]/08 md:w-72 md:min-h-[220px] md:border-b-0 md:border-r md:border-[#351c42]/08">
                        @if($coverUrl)
                            <img src="{{ $coverUrl }}" alt="" class="nom-media w-full object-cover md:absolute md:inset-0 md:h-full" loading="lazy" />
                        @else
                            <div class="nom-media flex min-h-[11rem] flex-col items-center justify-center gap-3 bg-gradient-to-br from-[#351c42] via-[#4a2660] to-[#965995] p-6 md:absolute md:inset-0 md:min-h-full">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-[#fddc6a] shadow-inner">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </span>
                                <span class="text-center text-[10px] font-black uppercase tracking-[0.2em] text-white/80">Open roles</span>
                            </div>
                        @endif
                    </div>

                    {{-- Summary --}}
                    <div class="flex min-w-0 flex-1 flex-col justify-center gap-3 p-5 sm:p-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-[#965995]/12 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-[#965995]">Nomination</span>
                            <span class="text-[11px] font-semibold text-[#351c42]/45">#{{ $nom->id }}</span>
                        </div>
                        <h3 class="text-xl font-extrabold leading-tight tracking-tight text-[#351c42] sm:text-2xl">{{ $nom->title }}</h3>
                        @if($nom->terms)
                            <p class="line-clamp-3 text-sm leading-relaxed text-[#351c42]/70">{{ $nom->terms }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#351c42]/55">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#351c42]/10 px-2.5 py-1 text-[#351c42]">
                                <svg class="h-3.5 w-3.5 text-[#965995]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $nom->polling_date?->format('d M Y') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-[#965995]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ \Illuminate\Support\Carbon::parse($nom->polling_from)->format('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($nom->polling_to)->format('g:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Positions: compact list --}}
                <div class="border-t border-[#351c42]/08 bg-gradient-to-b from-[#faf8fc] to-white px-4 py-4 sm:px-6">
                    <p class="mb-3 text-[10px] font-black uppercase tracking-[0.2em] text-[#965995]">Positions you can join</p>
                    <ul class="divide-y divide-[#351c42]/08 rounded-xl border border-[#351c42]/08 bg-white">
                        @foreach($nom->positions as $pIdx => $pos)
                            <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#351c42] text-xs font-black text-[#fddc6a]" aria-hidden="true">{{ $pIdx + 1 }}</span>
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-[#351c42]">{{ $pos->position }}</p>
                                        <p class="mt-0.5 text-xs text-[#351c42]/45">{{ $pos->entries_count }} {{ $pos->entries_count === 1 ? 'member' : 'members' }} interested</p>
                                    </div>
                                </div>
                                <div class="shrink-0 sm:ml-4">
                                    @if($interestSet->contains($pos->id))
                                        <span class="inline-flex w-full items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-extrabold text-emerald-800 sm:w-auto">You’re registered</span>
                                    @else
                                        <form method="POST" action="{{ route('member.nominations.interest', [$nom, $pos]) }}" class="block w-full sm:inline sm:w-auto">
                                            @csrf
                                            <button type="submit" class="md-btn-interest w-full min-w-[10rem] sm:w-auto">I’m interested</button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#351c42]/20 bg-white/80 px-6 py-12 text-center">
                <p class="text-sm font-semibold text-[#351c42]/55">No open nominations at the moment.</p>
                <p class="mt-2 text-xs text-[#351c42]/40">Check back when the office publishes new roles.</p>
            </div>
        @endforelse
    </div>
</section>
