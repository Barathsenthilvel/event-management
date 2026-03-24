<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member area — GNAT Donation')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
        .md-page-bg {
            background-color: #f6f7fb;
            background-image:
                radial-gradient(ellipse 70% 45% at 50% -15%, rgba(37, 99, 235, 0.08), transparent),
                radial-gradient(ellipse 50% 35% at 100% 20%, rgba(150, 89, 149, 0.1), transparent);
            min-height: 100vh;
        }
        .md-glass-header {
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(53, 28, 66, 0.07);
        }
        .md-nav-link {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #5c5a6b;
        }
        .md-nav-link:hover { color: #351c42; }
        .md-sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            border-radius: 0.875rem;
            padding: 0.65rem 0.9rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(53, 28, 66, 0.72);
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }
        .md-sidebar-link:hover { background: rgba(53, 28, 66, 0.06); color: #351c42; }
        .md-sidebar-link.is-active {
            background: linear-gradient(135deg, rgba(53, 28, 66, 0.12), rgba(150, 89, 149, 0.1));
            color: #351c42;
            box-shadow: inset 0 0 0 1px rgba(53, 28, 66, 0.08);
        }
    </style>
    @stack('styles')
</head>
<body class="md-page-bg text-[#351c42] antialiased">
    <header class="sticky top-0 z-40 md-glass-header">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3.5 lg:gap-6">
            <a href="{{ route('home') }}" class="flex min-w-0 max-w-[200px] shrink-0 sm:max-w-[220px]" aria-label="Home">
                <img src="{{ asset('logo.png') }}" alt="GNAT Donation" class="h-8 w-auto max-h-11 object-contain sm:h-11" width="200" height="48" />
            </a>
            <nav class="hidden flex-1 justify-center gap-6 lg:flex xl:gap-9" aria-label="Primary">
                <a href="{{ route('home') }}#home" class="md-nav-link">Home</a>
                <a href="{{ route('home') }}#about2" class="md-nav-link">About us</a>
                <a href="{{ route('home') }}#events" class="md-nav-link">Events</a>
                <a href="{{ route('home') }}#gallery" class="md-nav-link">Gallery</a>
                <a href="{{ route('home') }}#contact" class="md-nav-link">Contact us</a>
            </nav>
            <a href="{{ route('member.dashboard') }}" class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-[#351c42]/10 sm:h-11 sm:w-11" aria-label="Account">
                <svg class="h-5 w-5 text-[#351c42]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </a>
        </div>
    </header>

    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:flex-row lg:gap-8 lg:py-10">
        <aside class="lg:w-60 lg:rounded-2xl lg:border lg:border-[#351c42]/10 lg:bg-white/80 lg:p-4 lg:shadow-lg lg:shadow-[#351c42]/5">
            <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-widest text-[#965995]">Menu</p>
            <nav class="flex flex-col gap-1" aria-label="Member">
                <a href="{{ route('member.dashboard') }}" class="md-sidebar-link {{ request()->routeIs('member.dashboard') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.dashboard') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Dashboard</a>
                <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link {{ request()->routeIs('member.profile.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.profile.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Profile</a>
                <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Account settings</a>
            </nav>
            <form method="POST" action="{{ route('member.logout') }}" class="mt-8 border-t border-[#351c42]/10 pt-4">
                @csrf
                <button type="submit" class="md-sidebar-link w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Log out
                </button>
            </form>
        </aside>

        <main class="min-w-0 flex-1">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
