<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member sign in — GNAT Association')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: "DM Sans", system-ui, sans-serif; }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
        .ml-page-bg {
            background-color: #f8f6fc;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(150, 89, 149, 0.18), transparent),
                radial-gradient(ellipse 60% 40% at 100% 0%, rgba(253, 220, 106, 0.15), transparent),
                radial-gradient(ellipse 50% 30% at 0% 100%, rgba(53, 28, 66, 0.08), transparent);
            min-height: 100vh;
        }
        .ml-glass-header {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(53, 28, 66, 0.08);
        }
        .ml-nav-link {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #5c5a6b;
            transition: color 0.2s ease;
        }
        .ml-nav-link:hover { color: #351c42; }
        body.ml-menu-open { overflow: hidden; }
        .ml-mobile-panel { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .ml-mobile-panel.is-open { max-height: 28rem; }
        .ml-tabs-track {
            display: flex;
            gap: 0.25rem;
            padding: 0.35rem;
            border-radius: 9999px;
            background: rgba(53, 28, 66, 0.06);
        }
        [data-auth-tab] {
            flex: 1;
            border-radius: 9999px;
            padding: 0.65rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: background 0.25s ease, color 0.25s ease, box-shadow 0.25s ease, transform 0.2s ease;
        }
        [data-auth-tab][aria-selected="false"] { color: #351c42; background: transparent; }
        [data-auth-tab][aria-selected="false"]:hover { background: rgba(255, 255, 255, 0.7); }
        [data-auth-tab][aria-selected="true"] {
            background: linear-gradient(135deg, #351c42 0%, #4a2660 100%);
            color: #fff;
            box-shadow: 0 4px 20px rgba(53, 28, 66, 0.35);
        }
        .ml-label { display: block; font-size: 0.8125rem; font-weight: 600; color: rgba(53, 28, 66, 0.78); margin-bottom: 0.5rem; }
        .ml-inp {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(255, 255, 255, 0.85);
            padding: 0.8125rem 1.125rem;
            font-size: 0.9375rem;
            color: #351c42;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .ml-inp::placeholder { color: rgba(53, 28, 66, 0.32); }
        .ml-inp:hover { border-color: rgba(150, 89, 149, 0.25); background: #fff; }
        .ml-inp:focus {
            border-color: rgba(150, 89, 149, 0.55);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
        }
        /* Single combined field: +91 prefix + 10-digit input (one border, full width for digits) */
        .ml-inp-phone-wrap {
            display: flex;
            align-items: stretch;
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(255, 255, 255, 0.85);
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .ml-inp-phone-wrap:hover {
            border-color: rgba(150, 89, 149, 0.25);
            background: #fff;
        }
        .ml-inp-phone-wrap:focus-within {
            border-color: rgba(150, 89, 149, 0.55);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
        }
        .ml-inp-phone-prefix {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            padding: 0.8125rem 0.5rem 0.8125rem 1rem;
            font-size: 0.9375rem;
            font-weight: 600;
            color: rgba(53, 28, 66, 0.55);
            border-right: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(53, 28, 66, 0.035);
            user-select: none;
            pointer-events: none;
        }
        .ml-inp-phone-field {
            flex: 1;
            min-width: 0;
            border: none;
            background: transparent;
            padding: 0.8125rem 1.125rem 0.8125rem 0.75rem;
            font-size: 0.9375rem;
            color: #351c42;
            outline: none;
        }
        .ml-inp-phone-field::placeholder { color: rgba(53, 28, 66, 0.32); }
        .ml-card-elevated {
            box-shadow: 0 4px 6px -1px rgba(53, 28, 66, 0.06), 0 24px 48px -12px rgba(53, 28, 66, 0.14);
        }
        .ml-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: 9999px;
            padding: 0.875rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, #351c42 0%, #4d2a5c 100%);
            color: #fddc6a;
            box-shadow: 0 8px 24px rgba(53, 28, 66, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .ml-btn-primary:hover { filter: brightness(1.06); box-shadow: 0 12px 28px rgba(53, 28, 66, 0.32); transform: translateY(-1px); }
        .ml-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.875rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            background: #fff;
            color: #351c42;
            border: 1px solid rgba(53, 28, 66, 0.14);
            cursor: pointer;
        }
        .ml-btn-secondary:hover { background: rgba(53, 28, 66, 0.04); border-color: rgba(53, 28, 66, 0.22); }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    </style>
    @stack('styles')
</head>
<body class="ml-page-bg text-[#351c42] antialiased" data-auth-default="{{ $defaultTab ?? 'signin' }}">
    <header class="sticky top-0 z-40 ml-glass-header">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3.5 lg:gap-6">
            <button type="button" class="flex shrink-0 rounded-xl p-2.5 transition-colors hover:bg-[#351c42]/5 lg:hidden -ml-2" data-ml-menu aria-expanded="false" aria-controls="ml-mobile-nav" aria-label="Open menu">
                <span class="flex w-[22px] flex-col gap-1.5" aria-hidden="true">
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                </span>
            </button>
            <a href="{{ route('home') }}" class="flex min-w-0 max-w-[min(48vw,200px)] shrink-0 items-center sm:max-w-[220px]" aria-label="GNAT Association home">
                <img src="{{ asset('images/logo.png') }}" alt="GNAT Association" class="h-8 w-auto max-h-11 max-w-full object-contain object-left sm:h-11" width="200" height="48" />
            </a>
            <nav class="hidden flex-1 items-center justify-center gap-6 lg:flex xl:gap-10" aria-label="Primary">
                <a href="{{ route('home') }}#home" class="ml-nav-link">Home</a>
                <a href="{{ route('home') }}#about2" class="ml-nav-link">About us</a>
                <a href="{{ route('home') }}#events" class="ml-nav-link">Events</a>
                <a href="{{ route('home') }}#gallery" class="ml-nav-link">Gallery</a>
                <a href="{{ route('home') }}#contact" class="ml-nav-link">Contact us</a>
            </nav>
            <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3">
                <a href="{{ route('member.login') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-[#351c42] shadow-md shadow-[#351c42]/8 ring-1 ring-[#351c42]/10 transition hover:bg-[#351c42] hover:text-white hover:ring-[#351c42] sm:h-11 sm:w-11" aria-label="Member login">
                    <svg class="h-[21px] w-[21px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill="currentColor" d="M12 11.5a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z"/>
                        <path fill="currentColor" d="M6 20.25v-.75c0-2.9 2.35-5.25 5.25-5.25h1.5c2.9 0 5.25 2.35 5.25 5.25v.75H6Z" opacity="0.92"/>
                    </svg>
                </a>
                <a href="{{ route('donations.index') }}" class="hidden rounded-full bg-gradient-to-r from-[#fddc6a] to-[#f5d56a] px-5 py-2.5 text-xs font-bold text-[#311742] shadow-lg shadow-amber-200/50 ring-1 ring-amber-200/60 transition hover:brightness-105 min-[400px]:inline-flex sm:text-sm">Donate</a>
            </div>
        </div>
        <div id="ml-mobile-nav" class="ml-mobile-panel border-t border-[#351c42]/10 bg-white/95 backdrop-blur-md lg:hidden" hidden>
            <nav class="flex flex-col px-4 py-4 pb-6" aria-label="Mobile">
                <a href="{{ route('home') }}#home" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Home</a>
                <a href="{{ route('home') }}#about2" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">About us</a>
                <a href="{{ route('home') }}#events" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Events</a>
                <a href="{{ route('home') }}#gallery" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Gallery</a>
                <a href="{{ route('home') }}#contact" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Contact us</a>
                <a href="{{ route('donations.index') }}" class="mt-4 rounded-full bg-[#351c42] py-3.5 text-center text-sm font-bold text-[#fddc6a] shadow-lg">Donate</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

