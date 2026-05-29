@php
    $actionUrl = ! empty($item['route']) ? route($item['route']) : route('contact');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item['label'] }} — GNAT Association</title>
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
    @include('home.partials.header')

    <main class="mx-auto max-w-3xl px-4 py-8 lg:py-12 space-y-8">
        <nav class="text-sm font-semibold text-[#351c42]/60">
            <a href="{{ route('activity') }}" class="text-[#965995] hover:text-[#351c42] hover:underline">Activity</a>
            <span class="mx-2">/</span>
            <span class="text-[#351c42]">{{ $item['label'] }}</span>
        </nav>

        <article class="rounded-3xl border border-[#351c42]/10 bg-white p-6 md:p-10 shadow-sm">
            <p class="text-sm font-bold text-[#351c42]/25 tabular-nums">{{ $item['num'] }}</p>
            <p class="mt-2 text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">{{ $activities['badge'] ?? 'Activity' }}</p>
            <h1 class="mt-2 text-2xl md:text-4xl font-extrabold tracking-tight text-[#351c42]">{{ $item['label'] }}</h1>
            <p class="mt-6 text-base md:text-lg leading-relaxed text-[#351c42]/75">{{ $item['description'] }}</p>

            @if(!empty($item['content']))
                <div class="mt-6 space-y-4 text-sm md:text-base leading-relaxed text-[#351c42]/70">
                    @foreach((array) $item['content'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @endif

            @if(!empty($item['button']) && !empty($item['route']))
                <a href="{{ $actionUrl }}"
                   class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] shadow-lg shadow-[#351c42]/15 transition hover:bg-[#4d2a5c]">
                    {{ $item['button'] }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M7 17L17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif
        </article>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            @if($prev)
                <a href="{{ route('activity.show', $prev['slug']) }}"
                   class="inline-flex items-center gap-2 text-sm font-bold text-[#965995] hover:text-[#351c42] hover:underline">
                    ← {{ $prev['label'] }}
                </a>
            @else
                <span></span>
            @endif
            @if($next)
                <a href="{{ route('activity.show', $next['slug']) }}"
                   class="inline-flex items-center gap-2 text-sm font-bold text-[#965995] hover:text-[#351c42] hover:underline sm:text-right">
                    {{ $next['label'] }} →
                </a>
            @endif
        </div>

        <p class="text-center">
            <a href="{{ route('activity') }}" class="text-sm font-semibold text-[#351c42]/60 hover:text-[#965995] underline-offset-4 hover:underline">
                View all activities
            </a>
        </p>
    </main>

    @include('home.partials.footer')
    @include('home.partials.floating')
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
