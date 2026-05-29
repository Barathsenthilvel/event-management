@php
    $isModel = is_object($item) && method_exists($item, 'getAttribute');

    if ($isModel) {
        $layout = $item->layout_type;
        $cat = $item->category_key;
        $imageUrl = asset('storage/' . ltrim((string) $item->image_path, '/'));
        $alt = $item->alt_text ?: $item->title;
        $eyebrow = $item->eyebrow ?: ucfirst((string) $item->category_key);
        $title = $item->title;
        $text = $item->description_text;
    } else {
        $layout = $item['layout'] ?? 'cell';
        $cat = $item['cat'] ?? 'programs';
        $imageUrl = asset($item['image']);
        $alt = $item['alt'] ?? ($item['title'] ?? 'Gallery image');
        $eyebrow = $item['eyebrow'] ?? ucfirst($cat);
        $title = $item['title'] ?? '';
        $text = $item['text'] ?? null;
    }

    $title = preg_replace('/\s\(\d+\)$/', '', (string) $title) ?: (string) $title;

    $filterable = $filterable ?? false;
    $uniformGrid = $uniformGrid ?? false;
    $enableLightbox = $enableLightbox ?? false;
    if ($uniformGrid) {
        $layout = 'cell';
    }
    $isCategoryPrimary = $isModel
        ? (bool) $item->is_category_primary
        : (bool) ($item['is_category_primary'] ?? false);
    $fromEvent = $isModel ? false : (bool) ($item['from_event'] ?? false);
    $sortOrder = $isModel ? (int) $item->sort_order : (int) ($item['sort_order'] ?? 0);
    $filterAttrs = $filterable
        ? ' data-gallery-item data-cat="' . e($cat) . '" data-category-primary="' . ($isCategoryPrimary ? '1' : '0') . '" data-from-event="' . ($fromEvent ? '1' : '0') . '" data-sort-order="' . $sortOrder . '"'
        : '';
    $lightboxAttrs = $enableLightbox
        ? ' data-gallery-lightbox-item data-lightbox-src="' . e($imageUrl) . '" data-lightbox-title="' . e($title) . '" data-lightbox-cat="' . e($eyebrow) . '"'
        : '';
    $clickableClass = $enableLightbox ? ' cursor-pointer' : '';
@endphp

@if ($layout === 'hero')
    <article{!! $filterAttrs !!}{!! $lightboxAttrs !!} class="group relative col-span-2 row-span-2 min-h-[260px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#351c42]/5 shadow-lg ring-1 ring-black/5 sm:min-h-[320px] lg:min-h-0{{ $clickableClass }}">
        <img src="{{ $imageUrl }}" alt="{{ $alt }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-out group-hover:scale-105 pointer-events-none" width="800" height="600" loading="lazy" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#351c42] via-[#351c42]/35 to-transparent opacity-95 transition duration-500 group-hover:via-[#351c42]/45 pointer-events-none"></div>
        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 pointer-events-none">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a] sm:text-xs">{{ $eyebrow }}</p>
            <h3 class="mt-1 text-xl font-extrabold text-white sm:text-2xl line-clamp-2">{{ $title }}</h3>
            @if($text)
                <p class="mt-2 max-w-md text-sm text-white/80 line-clamp-2">{{ $text }}</p>
            @endif
        </div>
        @if($enableLightbox)
            @include('home.partials.gallery-view-icon')
        @endif
    </article>
@elseif ($layout === 'wide')
    <article{!! $filterAttrs !!}{!! $lightboxAttrs !!} class="group relative col-span-2 min-h-[140px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-md ring-1 ring-black/5 sm:min-h-[156px] lg:col-span-2 lg:min-h-0{{ $clickableClass }}">
        <img src="{{ $imageUrl }}" alt="{{ $alt }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105 pointer-events-none" width="800" height="500" loading="lazy" />
        <div class="absolute inset-0 bg-gradient-to-r from-[#351c42]/85 to-transparent pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 top-0 flex w-[70%] flex-col justify-end p-4 sm:p-5 pointer-events-none">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]">{{ $eyebrow }}</p>
            <h3 class="mt-0.5 text-lg font-extrabold text-white line-clamp-2">{{ $title }}</h3>
        </div>
        @if($enableLightbox)
            @include('home.partials.gallery-view-icon')
        @endif
    </article>
@elseif ($layout === 'banner')
    <article{!! $filterAttrs !!}{!! $lightboxAttrs !!} class="group relative col-span-2 min-h-[160px] overflow-hidden rounded-3xl border border-[#351c42]/10 bg-[#351c42] shadow-md ring-1 ring-black/5 sm:min-h-[180px] lg:col-span-2 lg:min-h-0{{ $clickableClass }}">
        <img src="{{ $imageUrl }}" alt="{{ $alt }}" class="absolute inset-0 h-full w-full object-cover opacity-60 mix-blend-overlay transition duration-700 group-hover:scale-105 group-hover:opacity-70 pointer-events-none" width="900" height="500" loading="lazy" />
        <div class="absolute inset-0 bg-gradient-to-br from-[#965995]/40 to-[#351c42] pointer-events-none"></div>
        <div class="relative flex h-full flex-col justify-center p-5 sm:p-6 pointer-events-none">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]">{{ $eyebrow }}</p>
            <h3 class="mt-1 text-xl font-extrabold text-white sm:text-2xl line-clamp-2">{{ $title }}</h3>
            @if($text)
                <p class="mt-2 max-w-lg text-sm text-white/85 line-clamp-2">{{ $text }}</p>
            @endif
        </div>
        @if($enableLightbox)
            @include('home.partials.gallery-view-icon')
        @endif
    </article>
@else
    <article{!! $filterAttrs !!}{!! $lightboxAttrs !!} class="group relative overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-md ring-1 ring-black/5 {{ $uniformGrid ? 'aspect-[4/3] min-h-[140px]' : 'min-h-[140px] sm:min-h-[156px] lg:min-h-0' }}{{ $enableLightbox ? ' cursor-pointer' : '' }}">
        <img src="{{ $imageUrl }}" alt="{{ $alt }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105 pointer-events-none" width="600" height="600" loading="lazy" />
        <div class="absolute inset-0 bg-gradient-to-t from-[#351c42]/90 to-transparent opacity-90 pointer-events-none"></div>
        <div class="absolute inset-x-0 bottom-0 p-4 pointer-events-none">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#fddc6a]">{{ $eyebrow }}</p>
            <h3 class="text-base font-extrabold text-white line-clamp-2">{{ $title }}</h3>
        </div>
        @if($enableLightbox)
            @include('home.partials.gallery-view-icon')
        @endif
    </article>
@endif
