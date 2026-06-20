@props([
    'href' => '#',
    'title' => '',
    'routeName' => null,
    'active' => false,
    'badges' => null,
])

@php
    $badges = $badges ?? $sidebarMenuBadges->badgesForMenuItem($routeName, $title);
@endphp

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'flex items-center justify-between gap-2 py-2 pr-1 transition-colors '.($active ? 'text-indigo-400 font-bold' : 'hover:text-white')]) }}>
    <span class="min-w-0 truncate">{{ $title }}</span>
    @include('admin.partials.sidebar-menu-badges', ['badges' => $badges])
</a>
