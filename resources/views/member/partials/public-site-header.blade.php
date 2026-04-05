{{-- Same top bar + drawer as the public homepage (see home/partials/header.blade.php). --}}
@php
    $hp = config('homepage', []);
    $logo = $hp['logo'] ?? ['src' => 'logo.png', 'alt' => 'GNAT Association'];
    $nav = $hp['nav'] ?? [];
    $contact = $hp['contact'] ?? [
        'email' => '',
        'address' => '',
        'phones' => [],
    ];
@endphp
@include('home.partials.header')
