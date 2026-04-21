{{--
    Public homepage facepile: five static member-style photos + overflow badge.
    Optional: $registeredCount (int) — when > 5, last circle shows +N for the remainder.
    Images: public/images/facepile/1.jpg … 5.jpg (decorative placeholders).
--}}
@php
    $size = 'h-8 w-8 sm:h-9 sm:w-9';
    $overlap = '-ml-2 sm:-ml-2.5';
    $registeredCount = max(0, (int) ($registeredCount ?? 0));
    $overflow = $registeredCount > 5 ? max(0, $registeredCount - 5) : 0;
    $faceUrls = [
        asset('images/facepile/1.jpg'),
        asset('images/facepile/2.jpg'),
        asset('images/facepile/3.jpg'),
        asset('images/facepile/4.jpg'),
        asset('images/facepile/5.jpg'),
    ];
@endphp
<div class="flex min-w-0 flex-1 items-center" @if($registeredCount > 0) aria-label="{{ $registeredCount }} registered" @else aria-hidden="true" @endif>
    <div class="flex min-w-0 items-center overflow-x-auto py-0.5 pl-0.5 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @foreach ($faceUrls as $i => $src)
            <span
                class="{{ $i > 0 ? $overlap.' ' : '' }}relative inline-flex {{ $size }} shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] ring-2 ring-[#351c42] shadow-sm"
                style="z-index: {{ $i + 1 }}"
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
            class="{{ $overlap }} relative inline-flex {{ $size }} shrink-0 items-center justify-center rounded-full border-2 border-[#2dd4bf] bg-gradient-to-br from-[#5c3560] to-[#351c42] text-xs font-bold text-[#fddc6a] ring-2 ring-[#351c42] shadow-sm sm:text-sm"
            style="z-index: 6"
        >@if($overflow > 0)+{{ $overflow }}@else+@endif</span>
    </div>
    <span class="ml-2 shrink-0 text-[10px] font-black uppercase tracking-wide text-white/85">
        {{ $registeredCount }} profiles
    </span>
</div>
