@php
    $galleryTitle = $gallery['section_title'] ?? 'Our gallery';
    $galleryTitleMatch = preg_match('/^(.+?)\s+(gallery)$/iu', $galleryTitle, $galleryTitleParts);
@endphp
<section id="gallery" data-gallery-root class="relative scroll-mt-32 overflow-hidden bg-gradient-to-b from-[#faf8f5] via-white to-[#f3efe8] py-16 lg:py-24">
    <div class="pointer-events-none absolute -right-40 top-24 h-[28rem] w-[28rem] rounded-full bg-[#965995]/12 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -left-32 bottom-16 h-80 w-80 rounded-full bg-[#fddc6a]/25 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#351c42]/15 to-transparent" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4">
        <div class="flex flex-col gap-8 lg:gap-10">
            <div class="flex flex-col gap-3 min-[520px]:flex-row min-[520px]:items-start min-[520px]:justify-between min-[520px]:gap-6">
                <div class="min-w-0 max-w-xl">
                    <div class="inline-flex items-center gap-2 text-xs font-bold tracking-[0.28em] uppercase text-[#965995]">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-[#965995]" aria-hidden="true"></span>
                        {{ $gallery['section_badge'] ?? 'Impact in pictures' }}
                    </div>
                    <h2 class="mt-3 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]">
                        @if($galleryTitleMatch)
                            {{ trim($galleryTitleParts[1]) }}
                            <span class="relative inline-block">{{ $galleryTitleParts[2] }}
                                <span class="absolute -bottom-1 left-0 right-0 h-2.5 rounded-full bg-[#fddc6a]/90 -z-10" aria-hidden="true"></span>
                            </span>
                        @else
                            {{ $galleryTitle }}
                        @endif
                    </h2>
                </div>
                <a href="{{ route('gallery.index') }}" data-gallery-view-more class="shrink-0 self-start text-sm font-semibold text-[#965995] underline-offset-4 hover:text-[#351c42] hover:underline transition-colors min-[520px]:pt-8 sm:pt-10" aria-label="View more all gallery photos">
                    View more
                </a>
            </div>
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <p class="max-w-xl text-sm leading-relaxed text-[#351c42]/70 sm:text-base line-clamp-2">
                    {{ $gallery['section_description'] ?? 'Field moments from Aminjikarai and across our programs—outreach, learning spaces, and celebrations with the communities we serve.' }}
                </p>
                <div class="flex flex-wrap items-center gap-2 lg:justify-end lg:shrink-0" role="group" aria-label="Filter gallery by category">
                    @foreach ($gallery['filters'] as $filter)
                        <button
                            type="button"
                            data-gallery-filter="{{ $filter['key'] }}"
                            aria-pressed="{{ $filter['key'] === 'all' ? 'true' : 'false' }}"
                        >{{ $filter['label'] }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        @if(collect($gallery['items'] ?? [])->isNotEmpty())
            <div class="mt-12 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 lg:gap-5 lg:auto-rows-[minmax(11rem,1fr)]" data-gallery-grid>
                @foreach ($gallery['items'] as $item)
                    @include('home.partials.gallery-item', ['item' => $item, 'filterable' => true, 'enableLightbox' => true])
                @endforeach
            </div>
            @include('home.partials.gallery-lightbox')
        @else
            <div class="mt-12 rounded-2xl border border-dashed border-[#351c42]/20 bg-white px-6 py-12 text-center">
                <p class="text-sm font-semibold text-[#351c42]/80">No gallery items to show yet.</p>
                <a href="{{ route('gallery.index') }}" class="mt-4 inline-flex text-sm font-bold text-[#965995] hover:text-[#351c42] underline-offset-4 hover:underline">
                    View gallery page
                </a>
            </div>
        @endif

        <div class="mt-12 flex flex-col items-center justify-between gap-6 rounded-3xl border border-[#351c42]/10 bg-white/80 p-6 shadow-sm backdrop-blur-sm sm:flex-row sm:p-8">
            <div class="text-center sm:text-left">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Visit us</p>
                <p class="mt-2 text-sm font-semibold text-[#351c42] sm:text-base">{{ $contact['address'] }}</p>
                <p class="mt-2 text-sm text-[#351c42]/70">
                    @foreach ($contact['phones'] as $idx => $phone)
                        @if ($idx > 0)<span class="mx-2 text-[#351c42]/30">|</span>@endif
                        <a href="tel:{{ $phone['tel'] }}" class="font-semibold text-[#351c42] hover:text-[#965995]">{{ $phone['label'] }}</a>
                    @endforeach
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="https://www.google.com/maps/search/?api=1&query={{ $contact['maps_query'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border-2 border-[#351c42] bg-[#351c42] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#2a1533]">
                    Open in Maps
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-full border-2 border-[#351c42]/20 bg-transparent px-5 py-2.5 text-sm font-bold text-[#351c42] transition hover:border-[#965995] hover:text-[#965995]">
                    Contact team
                </a>
            </div>
        </div>
    </div>
</section>
