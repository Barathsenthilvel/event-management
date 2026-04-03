<section id="gallery" data-gallery-root class="relative scroll-mt-32 overflow-hidden bg-gradient-to-b from-[#faf8f5] via-white to-[#f3efe8] py-16 lg:py-24">
    <div class="pointer-events-none absolute -right-40 top-24 h-[28rem] w-[28rem] rounded-full bg-[#965995]/12 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -left-32 bottom-16 h-80 w-80 rounded-full bg-[#fddc6a]/25 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#351c42]/15 to-transparent" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4">
        <div class="flex flex-col gap-8 lg:gap-10">
            <div class="flex flex-col gap-3 min-[520px]:flex-row min-[520px]:items-start min-[520px]:justify-between min-[520px]:gap-6">
                <div class="min-w-0 max-w-xl">
                    <p class="text-xs font-bold tracking-[0.28em] uppercase text-[#965995]">Impact in pictures</p>
                    <h2 class="mt-3 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]">
                        Our <span class="relative inline-block">gallery
                            <span class="absolute -bottom-1 left-0 right-0 h-2.5 rounded-full bg-[#fddc6a]/90 -z-10" aria-hidden="true"></span>
                        </span>
                    </h2>
                </div>
                <a href="{{ url('/') }}#contact" class="shrink-0 self-start text-sm font-semibold text-[#965995] underline-offset-4 hover:text-[#351c42] hover:underline transition-colors min-[520px]:pt-8 sm:pt-10">
                    View more
                </a>
            </div>
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <p class="max-w-xl text-sm leading-relaxed text-[#351c42]/70 sm:text-base">
                    Field moments from Aminjikarai and across our programs—outreach, learning spaces, and celebrations with the communities we serve.
                </p>
                <div class="flex flex-wrap items-center gap-2 lg:justify-end lg:shrink-0" role="group" aria-label="Filter gallery by category">
                    @foreach ($gallery['filters'] as $idx => $filter)
                        <button
                            type="button"
                            data-gallery-filter="{{ $filter['key'] }}"
                            aria-pressed="{{ $filter['key'] === 'all' ? 'true' : 'false' }}"
                        >{{ $filter['label'] }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-12 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 lg:gap-5 lg:auto-rows-[minmax(11rem,1fr)]">
            @foreach ($gallery['items'] as $item)
                @php
                    $layout = $item['layout'];
                @endphp
                @if ($layout === 'hero')
                    <article data-gallery-item data-cat="{{ $item['cat'] }}" class="group relative col-span-2 row-span-2 min-h-[260px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#351c42]/5 shadow-lg ring-1 ring-black/5 sm:min-h-[320px] lg:min-h-0">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['alt'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-out group-hover:scale-105" width="800" height="600" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#351c42] via-[#351c42]/35 to-transparent opacity-95 transition duration-500 group-hover:via-[#351c42]/45"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a] sm:text-xs">{{ $item['eyebrow'] }}</p>
                            <h3 class="mt-1 text-xl font-extrabold text-white sm:text-2xl">{{ $item['title'] }}</h3>
                            <p class="mt-2 max-w-md text-sm text-white/80">{{ $item['text'] ?? '' }}</p>
                        </div>
                        <span class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur-md transition group-hover:bg-[#fddc6a] group-hover:text-[#351c42]" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M7 17L17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </article>
                @elseif ($layout === 'wide')
                    <article data-gallery-item data-cat="{{ $item['cat'] }}" class="group relative col-span-2 min-h-[140px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-md ring-1 ring-black/5 sm:min-h-[156px] lg:col-span-2 lg:min-h-0">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['alt'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" width="800" height="500" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-r from-[#351c42]/85 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 top-0 flex w-[70%] flex-col justify-end p-4 sm:p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]">{{ $item['eyebrow'] }}</p>
                            <h3 class="mt-0.5 text-lg font-extrabold text-white">{{ $item['title'] }}</h3>
                        </div>
                    </article>
                @elseif ($layout === 'banner')
                    <article data-gallery-item data-cat="{{ $item['cat'] }}" class="group relative col-span-2 min-h-[160px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#351c42] shadow-md ring-1 ring-black/5 sm:min-h-[180px] lg:col-span-2 lg:min-h-0">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['alt'] }}" class="absolute inset-0 h-full w-full object-cover opacity-60 mix-blend-overlay transition duration-700 group-hover:scale-105 group-hover:opacity-70" width="900" height="500" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-br from-[#965995]/40 to-[#351c42]"></div>
                        <div class="relative flex h-full flex-col justify-center p-5 sm:p-6">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]">{{ $item['eyebrow'] }}</p>
                            <h3 class="mt-1 text-xl font-extrabold text-white sm:text-2xl">{{ $item['title'] }}</h3>
                            <p class="mt-2 max-w-lg text-sm text-white/85">{{ $item['text'] ?? '' }}</p>
                        </div>
                    </article>
                @else
                    <article data-gallery-item data-cat="{{ $item['cat'] }}" class="group relative min-h-[140px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-md ring-1 ring-black/5 sm:min-h-[156px] lg:min-h-0">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['alt'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" width="600" height="600" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#351c42]/90 to-transparent opacity-90"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]">{{ $item['eyebrow'] }}</p>
                            <h3 class="text-base font-extrabold text-white">{{ $item['title'] }}</h3>
                        </div>
                    </article>
                @endif
            @endforeach
        </div>

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
                <a href="{{ url('/') }}#contact" class="inline-flex items-center gap-2 rounded-full border-2 border-[#351c42]/20 bg-transparent px-5 py-2.5 text-sm font-bold text-[#351c42] transition hover:border-[#965995] hover:text-[#965995]">
                    Contact team
                </a>
            </div>
        </div>
    </div>
</section>
