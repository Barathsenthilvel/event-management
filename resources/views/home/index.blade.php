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

    @include('home.sections.hero')
    @include('home.sections.banner-carousel')
    @include('home.sections.testimonials')
    @include('home.sections.about')
    @include('home.sections.events')
    @include('home.sections.donate')
    @include('home.sections.service')
    @include('home.sections.blog')
    @include('home.sections.gallery')
    @include('home.sections.jobs')

    @include('home.partials.footer')
    @include('home.partials.floating')

    @include('home.partials.scripts')
</body>
</html>
