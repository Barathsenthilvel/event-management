{{--
    Public homepage facepile: five letter avatars + overflow badge (no stock photos).
    Optional: $registeredCount (int) — when > 5, last circle shows +N for the remainder.
--}}
@php
    $size = 'h-8 w-8 sm:h-9 sm:w-9';
    $overlap = '-ml-2 sm:-ml-2.5';
    $registeredCount = max(0, (int) ($registeredCount ?? 0));
    $overflow = $registeredCount > 5 ? max(0, $registeredCount - 5) : 0;
    $letters = ['G', 'N', 'A', 'T', 'M'];
@endphp
<div class="flex min-w-0 flex-1 items-center" @if($registeredCount > 0) aria-label="{{ $registeredCount }} registered" @else aria-hidden="true" @endif>
    <div class="flex min-w-0 items-center overflow-x-auto py-0.5 pl-0.5 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @foreach ($letters as $i => $letter)
            <span
                class="{{ $i > 0 ? $overlap.' ' : '' }}relative inline-flex {{ $size }} shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] ring-2 ring-[#351c42] shadow-sm"
                style="z-index: {{ $i + 1 }}"
            >
                @include('partials.user-letter-avatar', ['letter' => $letter, 'class' => 'h-full w-full text-[10px] sm:text-xs'])
            </span>
        @endforeach
        <span
            class="{{ $overlap }} relative inline-flex {{ $size }} shrink-0 items-center justify-center rounded-full border-2 border-[#2dd4bf] bg-gradient-to-br from-[#5c3560] to-[#351c42] text-xs font-bold text-[#fddc6a] ring-2 ring-[#351c42] shadow-sm sm:text-sm"
            style="z-index: 6"
        >@if($overflow > 0)+{{ $overflow }}@else+@endif</span>
    </div>
    <span class="ml-2 shrink-0 text-[10px] font-black uppercase tracking-wide text-white/85">
        {{ $registeredCount }}+ Participation
    </span>
</div>
