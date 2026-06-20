@php
    $badges = $badges ?? [];
    $badgeService = $sidebarMenuBadges ?? app(\App\Services\AdminSidebarMenuBadgeService::class);
@endphp
@if(!empty($badges))
    <span x-show="sidebarOpen" x-cloak class="inline-flex shrink-0 items-center gap-1">
        @foreach($badges as $badge)
            <span
                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-lg {{ $badgeService->badgeColorClass($badge['color']) }} text-white text-[9px] font-black leading-none"
                title="{{ $badge['title'] }}"
            >{{ $badgeService->formatCount((int) $badge['count']) }}</span>
        @endforeach
    </span>
@endif
