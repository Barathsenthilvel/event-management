@php
    $isModel = is_object($post) && method_exists($post, 'getAttribute');
    $day = null;
    $month = null;
    $year = null;

    if ($isModel) {
        $imageUrl = asset('storage/' . ltrim((string) $post->image_path, '/'));
        $tag = $post->tag ?: 'Blog';
        $title = $post->title;
        $excerpt = $post->excerpt;
        $readMoreUrl = $post->read_more_url ?: '#';
        $commentsCount = $post->comments_count;
        $publishedAt = $post->published_at;
    } else {
        $imageUrl = asset($post['image']);
        $tag = $post['tag'] ?? 'Blog';
        $title = $post['title'];
        $excerpt = $post['excerpt'];
        $readMoreUrl = $post['read_more_url'] ?? '#';
        $commentsCount = $post['comments'] ?? 0;
        $publishedAt = isset($post['day'], $post['month'], $post['year'])
            ? null
            : ($post['published_at'] ?? null);
        $day = $post['day'] ?? null;
        $month = $post['month'] ?? null;
        $year = $post['year'] ?? null;
    }

    $headingTag = ($heading ?? 'h3') === 'h2' ? 'h2' : 'h3';

    $readMoreContent = trim((string) ($excerpt ?? ''));
    $publishedLabel = $isModel && $publishedAt
        ? $publishedAt->format('d M Y')
        : (($day || $month || $year) ? trim(implode(' ', array_filter([$day, $month, $year]))) : '');
    $readMoreMeta = array_values(array_filter([
        ['label' => 'Tag', 'value' => $tag ?? ''],
        ['label' => 'Published', 'value' => $publishedLabel],
        ['label' => 'Comments', 'value' => isset($commentsCount) ? (string) $commentsCount : ''],
    ], fn ($item) => ($item['value'] ?? '') !== ''));
    $readMorePopupContent = $readMoreContent !== ''
        ? $readMoreContent
        : 'Full details for this post will be available soon.';
    $externalReadMoreUrl = trim((string) ($readMoreUrl ?? ''));
    $hasExternalReadMoreUrl = $externalReadMoreUrl !== '' && $externalReadMoreUrl !== '#';
@endphp

<article @class([
    'rounded-2xl bg-white border border-[#351c42]/10 overflow-hidden shadow-md',
    'w-[320px] shrink-0 min-w-[320px] max-w-[320px]' => $carousel ?? false,
    'w-full' => ! ($carousel ?? false),
])>
    <div class="relative h-56">
        <img src="{{ $imageUrl }}" alt="{{ $title }}" class="h-full w-full object-cover" loading="lazy" />
        <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-[#351c42]">{{ $tag }}</span>
        @if($isModel && $publishedAt)
            <div class="absolute right-0 bottom-0 rounded-tl-2xl bg-[#351c42] text-[#fddc6a] text-center px-4 py-2 min-w-[4.5rem]">
                <div class="text-4xl font-extrabold leading-none">{{ $publishedAt->format('d') }}</div>
                <div class="text-lg font-semibold leading-none mt-1 text-white">{{ $publishedAt->format('M') }}</div>
                <div class="mt-1 inline-block rounded-full bg-[#fddc6a] px-2.5 py-0.5 text-[10px] font-bold text-[#351c42]">{{ $publishedAt->format('Y') }}</div>
            </div>
        @elseif(! $isModel && ($day || $month || $year))
            <div class="absolute right-0 bottom-0 rounded-tl-2xl bg-[#351c42] text-[#fddc6a] text-center px-4 py-2 min-w-[4.5rem]">
                <div class="text-4xl font-extrabold leading-none">{{ $day }}</div>
                <div class="text-lg font-semibold leading-none mt-1 text-white">{{ $month }}</div>
                <div class="mt-1 inline-block rounded-full bg-[#fddc6a] px-2.5 py-0.5 text-[10px] font-bold text-[#351c42]">{{ $year }}</div>
            </div>
        @endif
    </div>
    <div class="p-5">
        @if($headingTag === 'h2')
            <h2 class="text-2xl font-extrabold leading-tight text-[#351c42] line-clamp-2">{{ $title }}</h2>
        @else
            <h3 class="text-2xl font-extrabold leading-tight text-[#351c42] line-clamp-2">{{ $title }}</h3>
        @endif
        <p class="mt-2 text-sm text-[#351c42]/65 leading-6 line-clamp-3">{{ $excerpt }}</p>
        <div class="mt-4 flex items-center justify-between gap-3">
            <button
                type="button"
                data-read-more
                data-read-more-title="{{ e($title) }}"
                data-read-more-content='@json($readMorePopupContent)'
                data-read-more-meta='@json($readMoreMeta)'
                @if($hasExternalReadMoreUrl)
                    data-read-more-document-url="{{ e($externalReadMoreUrl) }}"
                    data-read-more-document-label="Open article"
                @endif
                class="click-btn click-btn--sm btn-style506 shrink-0"
                title="Read more"
            >
                <span class="click-btn__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                        <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="click-btn__label">Read More</span>
            </button>
            <span class="text-sm font-semibold text-[#351c42]/70 inline-flex items-center gap-1.5 shrink-0">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8L20 10L18 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 16L4 14L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 14H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ $commentsCount }}
            </span>
        </div>
    </div>
</article>
