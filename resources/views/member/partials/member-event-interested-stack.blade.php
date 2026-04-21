{{-- Overlapping facepile: up to 5 member avatars + trailing “+” circle (reference UI). Expects $event with invites eager-loaded (optional). --}}
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
    $initials = static function (\App\Models\User $u): string {
        $fn = trim((string) $u->first_name);
        $ln = trim((string) $u->last_name);
        if ($fn !== '' || $ln !== '') {
            return strtoupper(\Illuminate\Support\Str::substr($fn, 0, 1).\Illuminate\Support\Str::substr($ln, 0, 1));
        }
        $name = trim((string) $u->name);

        return $name !== '' ? strtoupper(\Illuminate\Support\Str::substr($name, 0, 2)) : '?';
    };
    $ring = 'relative inline-flex shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] bg-[#4a2660] text-[10px] font-extrabold text-white shadow-sm ring-2 ring-[#351c42]';
    $size = 'h-8 w-8 sm:h-9 sm:w-9';
    $overlap = '-ml-2 sm:-ml-2.5';
    $placeholderFaces = [
        asset('images/facepile/1.jpg'),
        asset('images/facepile/2.jpg'),
        asset('images/facepile/3.jpg'),
        asset('images/facepile/4.jpg'),
        asset('images/facepile/5.jpg'),
    ];
@endphp
<div class="flex min-w-0 flex-1 items-center" aria-label="Members interested in this event">
    <div class="flex min-w-0 items-center overflow-x-auto py-0.5 pl-0.5 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @if ($stackUsers->isNotEmpty())
            @foreach ($stackUsers as $idx => $u)
                <span
                    class="{{ $idx > 0 ? $overlap.' ' : '' }}{{ $ring }} {{ $size }}"
                    style="z-index: {{ $idx + 1 }}"
                >
                    @if ($u->passport_photo_path)
                        <img
                            src="{{ asset('storage/'.$u->passport_photo_path) }}"
                            alt=""
                            class="h-full w-full object-cover"
                        />
                    @else
                        <span class="flex h-full w-full items-center justify-center bg-[#965995]/50">{{ $initials($u) }}</span>
                    @endif
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
            @foreach ($placeholderFaces as $i => $src)
                <span
                    class="{{ $i > 0 ? $overlap.' ' : '' }}relative inline-flex {{ $size }} shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] ring-2 ring-[#351c42] shadow-sm"
                    style="z-index: {{ $i + 1 }}"
                    aria-hidden="true"
                >
                    <img
                        src="{{ $src }}"
                        alt=""
                        width="36"
                        height="36"
                        class="h-full w-full object-cover"
                        loading="lazy"
                        decoding="async"
                    />
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
        {{ $totalInterested }} profiles
    </span>
</div>
