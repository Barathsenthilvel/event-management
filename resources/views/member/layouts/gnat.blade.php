<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member area — GNAT Association')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('home.partials.styles')
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
        .site-header-main {
            background: linear-gradient(180deg, #f5f3f9 0%, #eae7f3 100%);
            border-bottom: 1px solid rgba(53, 28, 66, 0.09);
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.6) inset;
        }
        .md-nav-link {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #5c5a6b;
            transition: color 0.2s ease;
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
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="md-page-bg text-[#351c42] antialiased">
    @php
        $gnatMember = Auth::user();
        $gnatCanSeeMembership = $gnatMember && $gnatMember->profile_completed && $gnatMember->is_approved;
        $gnatHasActiveSubscription = $gnatMember && $gnatMember->activeSubscription()->exists();
        $gnatPortalUnlocked = $gnatCanSeeMembership && $gnatHasActiveSubscription;
    @endphp
    @include('member.partials.public-site-header')

    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:flex-row lg:gap-8 lg:py-10">
        <aside class="lg:w-60 lg:rounded-2xl lg:border lg:border-[#351c42]/10 lg:bg-white/80 lg:p-4 lg:shadow-lg lg:shadow-[#351c42]/5">
            <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-widest text-[#965995]">{{ $gnatPortalUnlocked ? 'Menu' : ($gnatCanSeeMembership ? 'Membership' : 'Account') }}</p>
            <nav class="flex flex-col gap-1" aria-label="Member">
                @if($gnatPortalUnlocked)
                    <a href="{{ route('member.dashboard') }}" class="md-sidebar-link {{ request()->routeIs('member.dashboard') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.dashboard') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Dashboard</a>
                    <a href="{{ route('member.events.index') }}" class="md-sidebar-link {{ request()->routeIs('member.events.index') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.events.index') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Events</a>
                    <a href="{{ route('member.nominations.index') }}" class="md-sidebar-link {{ request()->routeIs('member.nominations.index') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.nominations.index') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Nominations</a>
                    <a href="{{ route('member.pollings.index') }}" class="md-sidebar-link {{ request()->routeIs('member.pollings.index') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.pollings.index') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Polling</a>
                    <a href="{{ route('member.ebooks.index') }}" class="md-sidebar-link {{ request()->routeIs('member.ebooks.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.ebooks.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> E-Books</a>
                    <a href="{{ route('member.subscription.index') }}" class="md-sidebar-link {{ request()->routeIs('member.subscription.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.subscription.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Membership</a>
                    <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link {{ request()->routeIs('member.profile.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.profile.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Profile</a>
                    <a href="{{ route('member.password.edit') }}" class="md-sidebar-link {{ request()->routeIs('member.password.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.password.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Change password</a>
                @elseif($gnatCanSeeMembership)
                    <p class="mb-2 rounded-xl bg-[#965995]/10 px-3 py-2 text-xs font-semibold leading-relaxed text-[#351c42]/85">Please purchase a membership plan to unlock the full member menu.</p>
                    <a href="{{ route('member.subscription.index') }}" class="md-sidebar-link {{ request()->routeIs('member.subscription.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.subscription.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Membership</a>
                @else
                    <a href="{{ route('member.dashboard') }}" class="md-sidebar-link {{ request()->routeIs('member.dashboard') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.dashboard') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Overview</a>
                    <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link {{ request()->routeIs('member.profile.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.profile.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Profile</a>
                    <a href="{{ route('member.password.edit') }}" class="md-sidebar-link {{ request()->routeIs('member.password.*') ? 'is-active' : '' }}"><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.password.*') ? 'bg-[#965995]' : 'bg-slate-300' }}"></span> Change password</a>
                @endif
            </nav>
            @include('member.partials.sidebar-logout')
        </aside>

        <main class="min-w-0 flex-1">
            @yield('content')
        </main>
    </div>

    @php
        $donate = config('homepage.donate', ['goal' => 500, 'default_amount' => 100]);
    @endphp
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')

    @stack('scripts')
</body>
</html>
