<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @include('home.partials.head')
    @include('home.partials.styles')
</head>
<body>
    @include('home.partials.header')

    @if(session('event_interest_success'))
        <div class="mx-auto max-w-7xl px-4 pt-4">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900" role="status">{{ session('event_interest_success') }}</div>
        </div>
    @endif
    @if(session('event_interest_error'))
        <div class="mx-auto max-w-7xl px-4 pt-4">
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert">{{ session('event_interest_error') }}</div>
        </div>
    @endif

    @include('home.sections.hero')
    @include('home.sections.service')
    @include('home.sections.banner-carousel')
    @include('home.sections.about')
    @include('home.sections.jobs')
    @include('home.sections.testimonials')
    @include('home.sections.events')
    @include('home.sections.donate')
    @include('home.sections.blog')
    @include('home.sections.gallery')

    @include('home.partials.footer')
    @include('home.partials.floating')

    @include('home.partials.event-interest-modal')
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
