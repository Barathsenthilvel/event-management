{{-- Decorative facepile for public homepage: five placeholder circles + trailing “+” (no real user data). --}}
@php
    $size = 'h-8 w-8 sm:h-9 sm:w-9';
    $overlap = '-ml-2 sm:-ml-2.5';
@endphp
<div class="flex min-w-0 flex-1 items-center" aria-hidden="true">
    <div class="flex min-w-0 items-center overflow-x-auto py-0.5 pl-0.5 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @for ($i = 0; $i < 5; $i++)
            <span
                class="{{ $i > 0 ? $overlap.' ' : '' }}relative inline-flex {{ $size }} shrink-0 overflow-hidden rounded-full border-2 border-[#2dd4bf] bg-gradient-to-br from-[#965995]/45 to-[#351c42]/50 ring-2 ring-[#351c42] shadow-sm"
                style="z-index: {{ $i + 1 }}"
            ></span>
        @endfor
        <span
            class="{{ $overlap }} relative inline-flex {{ $size }} shrink-0 items-center justify-center rounded-full border-2 border-[#2dd4bf] bg-gradient-to-br from-[#5c3560] to-[#351c42] text-sm font-bold text-[#fddc6a] ring-2 ring-[#351c42] shadow-sm"
            style="z-index: 6"
        >+</span>
    </div>
</div>
