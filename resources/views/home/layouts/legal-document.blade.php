<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('document_title') — GNAT Association</title>
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
@include('home.partials.header')

<main class="mx-auto max-w-3xl px-4 py-10 md:py-14">
    <article class="relative overflow-hidden rounded-[28px] border border-[#351c42]/10 bg-white/90 backdrop-blur shadow-sm">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-[#fddc6a]/25 blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="absolute -left-16 bottom-0 h-48 w-48 rounded-full bg-[#965995]/10 blur-3xl pointer-events-none" aria-hidden="true"></div>

        <div class="relative px-6 py-8 md:px-10 md:py-11">
            <header class="border-b border-[#351c42]/10 pb-8 mb-8">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">GNAT document</p>
                <h1 class="mt-2 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">@yield('document_title')</h1>
                @hasSection('document_subtitle')
                    <p class="mt-2 text-sm text-[#351c42]/65">@yield('document_subtitle')</p>
                @endif
                @if(! empty($effectiveDate))
                    <p class="mt-4 inline-flex items-center gap-2 rounded-2xl border border-[#351c42]/10 bg-[#351c42]/[0.03] px-4 py-2 text-xs font-bold text-[#351c42]/80">
                        <span class="text-[#965995] uppercase tracking-wide">Effective date</span>
                        <span>{{ $effectiveDate }}</span>
                    </p>
                @endif
            </header>

            <div class="legal-document-body space-y-8 text-[#351c42]/85 text-sm md:text-[15px] leading-relaxed [&_h2]:text-base [&_h2]:md:text-lg [&_h2]:font-extrabold [&_h2]:text-[#351c42] [&_h2]:tracking-tight [&_h2]:scroll-mt-24 [&_ul]:mt-3 [&_ul]:space-y-2 [&_ul]:pl-5 [&_ul]:list-disc [&_ul]:marker:text-[#fddc6a]">
                @yield('document_body')
            </div>

            @hasSection('document_footer')
                <footer class="mt-10 pt-8 border-t border-[#351c42]/10 text-sm">
                    @yield('document_footer')
                </footer>
            @endif
        </div>
    </article>

    <p class="mt-8 text-center text-xs text-[#351c42]/50">
        <a href="{{ route('home') }}" class="font-semibold text-[#965995] hover:text-[#351c42] transition-colors">Back to home</a>
    </p>
</main>

@include('home.partials.footer')
@include('home.partials.floating')
@include('home.partials.donate-modal')
@include('home.partials.donate-payment-modals')
@include('home.partials.scripts')
</body>
</html>
