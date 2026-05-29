<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity — GNAT Association</title>
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
    @include('home.partials.header')

    <main class="mx-auto max-w-7xl px-4 py-8 lg:py-12 space-y-10">
        <header class="relative overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white/90 backdrop-blur p-6 md:p-10 shadow-sm text-center lg:text-left max-w-3xl">
            <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">{{ $activities['badge'] ?? 'Activity' }}</p>
            <h1 class="mt-2 text-2xl md:text-4xl font-extrabold tracking-tight text-[#351c42]">{{ $activities['title'] ?? 'Programs & pathways' }}</h1>
            @if(!empty($activities['subtitle']))
                <p class="mt-3 text-lg font-bold text-[#965995]">{{ $activities['subtitle'] }}</p>
            @endif
            @if(!empty($activities['intro']))
                <p class="mt-4 text-sm md:text-base leading-relaxed text-[#351c42]/75">{{ $activities['intro'] }}</p>
            @endif
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">
            @foreach($items as $index => $item)
                @php
                    $isPurple = ((int) floor($index / 2) + ($index % 2)) % 2 === 1;
                    $pageUrl = route('activity.show', $item['slug']);
                @endphp

                @if($isPurple)
                    <a href="{{ $pageUrl }}" class="group relative block overflow-hidden rounded-3xl border border-[#351c42]/10 bg-gradient-to-br from-[#351c42] to-[#4d2a5c] p-6 md:p-8 text-white shadow-md no-underline transition hover:shadow-lg hover:brightness-105">
                        <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10" aria-hidden="true"></div>
                        <div class="pointer-events-none absolute -bottom-8 -left-8 h-28 w-28 rounded-full bg-[#965995]/40" aria-hidden="true"></div>
                        <div class="relative">
                            <p class="text-sm font-bold text-white/40 tabular-nums">{{ $item['num'] }}</p>
                            <h2 class="mt-2 text-xl md:text-2xl font-extrabold leading-snug group-hover:text-[#fddc6a] transition-colors">{{ $item['label'] }}</h2>
                            <p class="mt-4 text-sm md:text-base leading-relaxed text-white/85">{{ $item['description'] }}</p>
                        </div>
                    </a>
                @else
                    <a href="{{ $pageUrl }}" class="group block rounded-3xl border border-[#351c42]/10 bg-white p-6 md:p-8 shadow-sm no-underline text-inherit transition hover:shadow-md hover:border-[#965995]/25">
                        <p class="text-sm font-bold text-[#351c42]/25 tabular-nums">{{ $item['num'] }}</p>
                        <h2 class="mt-2 text-xl md:text-2xl font-extrabold text-[#351c42] group-hover:text-[#965995] transition-colors">{{ $item['label'] }}</h2>
                        <p class="mt-4 text-sm md:text-base leading-relaxed text-[#351c42]/70">{{ $item['description'] }}</p>
                    </a>
                @endif
            @endforeach
        </div>
    </main>

    @include('home.partials.footer')
    @include('home.partials.floating')
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
