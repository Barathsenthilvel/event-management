{{-- Overlapping facepile: up to 5 member letter avatars + trailing “+” circle. Expects $event with invites eager-loaded (optional). --}}
@php
    $maxFaces = 5;
    $interestUsers = collect();
    if ($event->relationLoaded('invites')) {
        $interestUsers = $event->invites
            ->filter(fn ($inv) => $inv->participation_status === 'interested' && $inv->user)
            ->map(fn ($inv) => $inv->user)
            ->unique('id')
            ->values();
    }
    $totalInterested = max((int) ($event->interested_count ?? 0), $interestUsers->count());
    $stackUsers = $interestUsers->take($maxFaces);
    $overflowCount = max(0, $totalInterested - $stackUsers->count());
    $ring = 'relative inline-flex shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] shadow-sm ring-2 ring-[#351c42]';
    $size = 'h-8 w-8 sm:h-9 sm:w-9';
    $overlap = '-ml-2 sm:-ml-2.5';
    $demoLetters = ['G', 'N', 'A', 'T', 'M'];
@endphp
<div class="flex min-w-0 flex-1 items-center" aria-label="Members interested in this event">
    <div class="flex min-w-0 items-center overflow-x-auto py-0.5 pl-0.5 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @if ($stackUsers->isNotEmpty())
            @foreach ($stackUsers as $idx => $u)
                <span
                    class="{{ $idx > 0 ? $overlap.' ' : '' }}{{ $ring }} {{ $size }}"
                    style="z-index: {{ $idx + 1 }}"
                >
                    @include('partials.user-letter-avatar', ['user' => $u, 'class' => 'h-full w-full text-[10px] sm:text-xs'])
                </span>
            @endforeach
            <span
                class="{{ $overlap }} {{ $ring }} {{ $size }} items-center justify-center bg-gradient-to-br from-[#5c3560] to-[#351c42] text-sm font-bold text-[#fddc6a]"
                style="z-index: {{ $stackUsers->count() + 1 }}"
            >
                @if ($overflowCount > 0)
                    +{{ $overflowCount }}
                @else
                    +
                @endif
            </span>
        @else
            @foreach ($demoLetters as $i => $letter)
                <span
                    class="{{ $i > 0 ? $overlap.' ' : '' }}relative inline-flex {{ $size }} shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] ring-2 ring-[#351c42] shadow-sm"
                    style="z-index: {{ $i + 1 }}"
                    aria-hidden="true"
                >
                    @include('partials.user-letter-avatar', ['letter' => $letter, 'class' => 'h-full w-full text-[10px] sm:text-xs'])
                </span>
            @endforeach
            <span
                class="{{ $overlap }} inline-flex {{ $size }} items-center justify-center rounded-full border-2 border-[#2dd4bf] bg-gradient-to-br from-[#5c3560] to-[#351c42] text-sm font-bold text-[#fddc6a] ring-2 ring-[#351c42]"
                style="z-index: 6"
                aria-hidden="true"
            >+</span>
        @endif
    </div>
    <span class="ml-2 shrink-0 text-[10px] font-black uppercase tracking-wide text-white/85">
        {{ $totalInterested }}+ Participation
    </span>
</div>
