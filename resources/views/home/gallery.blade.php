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
                        <p class="mt-1 text-sm text-[#351c42]/65 leading-relaxed line-clamp-2">{{ $section->section_description ?? 'Field moments from Aminjikarai and across our programs—outreach, learning spaces, and celebrations with the communities we serve.' }}</p>
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

            @if($galleryItems->isEmpty())
                <section class="mt-8 rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
                    <p class="text-sm font-bold text-[#351c42]/80">No gallery items found.</p>
                </section>
            @else
                @php
                    $lightboxImages = $galleryItems->map(function ($item) {
                        $isModel = is_object($item) && method_exists($item, 'getAttribute');
                        return [
                            'src'   => $isModel
                                ? asset('storage/' . ltrim((string) $item->image_path, '/'))
                                : asset($item['image']),
                            'title' => $isModel ? $item->title : ($item['title'] ?? ''),
                            'cat'   => $isModel ? ucfirst($item->category_key) : ucfirst($item['cat'] ?? ''),
                        ];
                    })->values()->all();
                @endphp

                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 lg:gap-5">
                    @foreach($galleryItems as $index => $item)
                        @php
                            $isModel = is_object($item) && method_exists($item, 'getAttribute');
                            $imgSrc  = $isModel
                                ? asset('storage/' . ltrim((string) $item->image_path, '/'))
                                : asset($item['image']);
                            $imgAlt  = $isModel ? ($item->alt_text ?: $item->title) : ($item['alt'] ?? ($item['title'] ?? 'Gallery image'));
                            $imgTitle = $isModel ? $item->title : ($item['title'] ?? '');
                            $imgCat  = $isModel ? ucfirst($item->category_key) : ucfirst($item['cat'] ?? '');
                        @endphp
                        <div class="relative group aspect-[4/3] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-md ring-1 ring-black/5 cursor-pointer"
                             data-lightbox-index="{{ $index }}"
                             onclick="openGalleryLightbox({{ $index }})">
                            <img src="{{ $imgSrc }}" alt="{{ $imgAlt }}"
                                 class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                 loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#351c42]/80 via-[#351c42]/20 to-transparent opacity-90"></div>
                            <div class="absolute inset-x-0 bottom-0 p-3">
                                <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]">{{ $imgCat }}</p>
                                <p class="text-sm font-extrabold text-white leading-tight line-clamp-2">{{ $imgTitle }}</p>
                            </div>
                            <span class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/25 text-white backdrop-blur-sm shadow-sm transition-all duration-200 hover:bg-[#fddc6a] hover:text-[#351c42]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                                </svg>
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Lightbox --}}
    <div id="gallery-lightbox"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-sm"
         style="display:none!important"
         role="dialog" aria-modal="true" aria-label="Image viewer">

        {{-- Close --}}
        <button onclick="closeGalleryLightbox()"
                class="absolute right-4 top-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>

        {{-- Prev --}}
        <button id="lb-prev"
                onclick="stepGalleryLightbox(-1)"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>

        {{-- Next --}}
        <button id="lb-next"
                onclick="stepGalleryLightbox(1)"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>

        {{-- Image --}}
        <div class="relative flex flex-col items-center justify-center w-full h-full px-16 py-14">
            <img id="lb-img" src="" alt=""
                 class="max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl transition-opacity duration-200" />
            <div class="mt-4 text-center">
                <p id="lb-cat" class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#fddc6a]"></p>
                <p id="lb-title" class="mt-1 text-base font-extrabold text-white"></p>
                <p id="lb-counter" class="mt-1 text-xs text-white/50"></p>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const images = @json($lightboxImages ?? []);
        let current = 0;
        const lb = document.getElementById('gallery-lightbox');
        const lbImg = document.getElementById('lb-img');
        const lbTitle = document.getElementById('lb-title');
        const lbCat = document.getElementById('lb-cat');
        const lbCounter = document.getElementById('lb-counter');

        window.openGalleryLightbox = function (index) {
            current = index;
            render();
            lb.style.cssText = '';
            lb.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeGalleryLightbox = function () {
            lb.style.display = 'none';
            document.body.style.overflow = '';
        };

        window.stepGalleryLightbox = function (dir) {
            current = (current + dir + images.length) % images.length;
            render();
        };

        function render() {
            if (!images.length) return;
            const img = images[current];
            lbImg.style.opacity = '0';
            setTimeout(() => {
                lbImg.src = img.src;
                lbImg.alt = img.title || '';
                if (lbTitle) lbTitle.textContent = img.title || '';
                if (lbCat)   lbCat.textContent   = img.cat   || '';
                if (lbCounter) lbCounter.textContent = (current + 1) + ' / ' + images.length;
                document.getElementById('lb-prev').style.display = images.length <= 1 ? 'none' : '';
                document.getElementById('lb-next').style.display = images.length <= 1 ? 'none' : '';
                lbImg.style.opacity = '1';
            }, 100);
        }

        document.addEventListener('keydown', function (e) {
            if (lb.style.display === 'none') return;
            if (e.key === 'Escape')      closeGalleryLightbox();
            if (e.key === 'ArrowLeft')   stepGalleryLightbox(-1);
            if (e.key === 'ArrowRight')  stepGalleryLightbox(1);
        });

        lb.addEventListener('click', function (e) {
            if (e.target === lb) closeGalleryLightbox();
        });
    })();
    </script>

    @include('home.partials.footer')
    @include('home.partials.floating')
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
