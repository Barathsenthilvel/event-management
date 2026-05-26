<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery — GNAT Association</title>
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
    @include('home.partials.header')

    <section id="gallery" class="relative overflow-hidden bg-gradient-to-b from-[#faf8f5] via-white to-[#f3efe8] py-12 lg:py-16">
        <div class="pointer-events-none absolute -right-40 top-24 h-[28rem] w-[28rem] rounded-full bg-[#965995]/12 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-32 bottom-16 h-80 w-80 rounded-full bg-[#fddc6a]/25 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4">
            <div class="rounded-3xl border border-[#351c42]/10 bg-white/85 backdrop-blur p-5 md:p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">{{ $section->section_badge ?? 'Impact in pictures' }}</p>
                        <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">{{ $section->section_title ?? 'Our gallery' }}</h1>
                        <p class="mt-1 text-sm text-[#351c42]/65">{{ $section->section_description ?? 'Field moments from Aminjikarai and across our programs—outreach, learning spaces, and celebrations with the communities we serve.' }}</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('gallery.index') }}" class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto] lg:items-center">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <div class="relative">
                        <input type="search" name="q" value="{{ $q }}" placeholder="Search gallery title or description..."
                               class="w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25">
                    </div>
                    <button type="submit" class="rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                        Search
                    </button>

                    <div class="lg:col-span-2 flex flex-wrap items-center gap-2 pt-1">
                        @php
                            $tabs = [
                                'all' => 'All',
                                'programs' => 'Programs',
                                'events' => 'Events',
                                'community' => 'Community',
                            ];
                        @endphp
                        @foreach($tabs as $key => $label)
                            @php
                                $isOn = $category === $key;
                                $href = route('gallery.index', array_filter(['q' => $q, 'category' => $key]));
                            @endphp
                            <a href="{{ $href }}"
                               data-gallery-filter="{{ $key }}"
                               aria-pressed="{{ $isOn ? 'true' : 'false' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </div>

            @php
                $hasGalleryItems = $items->isNotEmpty() || (isset($eventGalleryItems) && $eventGalleryItems->isNotEmpty());
            @endphp
            @if(!$hasGalleryItems)
                <section class="mt-8 rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
                    <p class="text-sm font-bold text-[#351c42]/80">No gallery items found.</p>
                </section>
            @else
                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 lg:gap-5">
                    @if(in_array($category, ['all', 'events'], true) && isset($eventGalleryItems))
                        @foreach($eventGalleryItems as $item)
                            @include('home.partials.gallery-item', ['item' => $item, 'uniformGrid' => true])
                        @endforeach
                    @endif
                    @foreach($items as $item)
                        @include('home.partials.gallery-item', ['item' => $item, 'uniformGrid' => true])
                    @endforeach
                </div>

                <section class="mt-6 rounded-2xl border border-[#351c42]/10 bg-white p-4">
                    {{ $items->links() }}
                </section>
            @endif
        </div>
    </section>

    @include('home.partials.footer')
    @include('home.partials.floating')
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
