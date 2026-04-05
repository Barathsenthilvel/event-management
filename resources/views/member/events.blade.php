@php
    $member = Auth::user();
    $hasActiveSubscription = $member?->activeSubscription()->exists();
    $canSeeMembership = $member && $member->profile_completed && $member->is_approved;
    $showFullMemberMenu = $canSeeMembership && $hasActiveSubscription;
    $sub = $activeSubscription ?? null;
    $firstName = $member?->first_name ?? 'Member';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Events — GNAT Association</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('home.partials.styles')
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: "DM Sans", system-ui, sans-serif; }
        [x-cloak] { display: none !important; }
        .md-page-bg {
            background-color: #f8f6fa;
            background-image:
                radial-gradient(ellipse 70% 45% at 50% -15%, rgba(53, 28, 66, 0.09), transparent),
                radial-gradient(ellipse 50% 35% at 100% 20%, rgba(150, 89, 149, 0.12), transparent);
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
        body.md-drawer-open { overflow: hidden; }
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
        }
        .md-sidebar-link:hover { background: rgba(53, 28, 66, 0.06); color: #351c42; }
        .md-sidebar-link.is-active {
            background: linear-gradient(135deg, rgba(53, 28, 66, 0.12), rgba(150, 89, 149, 0.1));
            color: #351c42;
            box-shadow: inset 0 0 0 1px rgba(53, 28, 66, 0.08);
        }
        .md-btn-interest {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            background: #351c42;
            color: #fddc6a;
            transition: transform 0.15s ease, filter 0.2s ease;
        }
        .md-btn-interest:hover { filter: brightness(1.06); transform: translateY(-1px); }
    </style>
</head>
<body class="md-page-bg text-[#351c42] antialiased" id="top">
    @if(!$member?->profile_completed)
        <div x-data x-cloak>
            <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-2xl border-2 border-[#965995]/30 bg-white p-8 text-center shadow-2xl">
                    <h2 class="text-2xl font-bold text-[#351c42]">Hello, {{ $firstName }}</h2>
                    <p class="mt-4 text-sm leading-relaxed text-[#351c42]/75">Your profile is incomplete. Please complete it to be part of the GNAT member community.</p>
                    <a href="{{ route('member.profile.edit') }}" class="mx-auto mt-8 inline-flex min-w-[10rem] items-center justify-center rounded-full bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-6 py-2.5 text-sm font-bold text-[#fddc6a] shadow-lg shadow-[#351c42]/25 transition hover:brightness-105">Update profile</a>
                </div>
            </div>
        </div>
    @elseif(!$member?->is_approved)
        <div x-data="{ open: true }" x-cloak>
            <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
                <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-[#351c42]/10 bg-white p-8 shadow-2xl">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#965995]">Approval pending</p>
                    <h3 class="mt-2 text-xl font-extrabold text-[#351c42]">Please wait for admin approval</h3>
                    <p class="mt-3 text-sm text-[#351c42]/75">We received your profile. Once approved, you can purchase membership plans.</p>
                    <div class="mt-6 flex flex-wrap justify-end gap-3">
                        <a href="{{ route('member.profile.edit') }}" class="rounded-full border border-[#351c42]/15 px-5 py-2.5 text-sm font-bold text-[#351c42] hover:bg-[#351c42]/5">Review profile</a>
                        <button type="button" @click="open = false" class="rounded-full bg-[#351c42] px-5 py-2.5 text-sm font-bold text-[#fddc6a]">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('member.partials.public-site-header')
    <div class="sticky top-0 z-30 flex items-center justify-between border-b border-[#351c42]/10 bg-[#faf9fc] px-4 py-2 lg:hidden">
        <span class="text-[10px] font-bold uppercase tracking-wide text-[#351c42]/45">Member</span>
        <button type="button" data-md-sidebar-toggle aria-expanded="false" aria-controls="md-sidebar" class="text-xs font-bold text-[#965995] hover:text-[#351c42]">Menu</button>
    </div>

    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:flex-row lg:gap-8 lg:py-10">
        <aside
            id="md-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-[min(100%,280px)] -translate-x-full border-r border-[#351c42]/10 bg-white/95 p-5 shadow-2xl transition-transform duration-300 lg:static lg:z-0 lg:w-60 lg:translate-x-0 lg:rounded-2xl lg:border lg:bg-white/80 lg:p-4 lg:shadow-lg lg:shadow-[#351c42]/5"
        >
            <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-widest text-[#965995]">{{ $showFullMemberMenu ? 'Menu' : ($canSeeMembership ? 'Membership' : 'Account') }}</p>
            <nav class="flex flex-col gap-1" aria-label="Member">
                @if($showFullMemberMenu)
                    <a href="{{ route('member.dashboard') }}" class="md-sidebar-link {{ request()->routeIs('member.dashboard') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.dashboard') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> Dashboard</a>
                    <a href="{{ route('member.ebooks.index') }}" class="md-sidebar-link {{ request()->routeIs('member.ebooks.*') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.ebooks.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> E-Books</a>
                    <a href="{{ route('member.subscription.index') }}" class="md-sidebar-link" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Membership</a>
                    <a href="{{ route('member.events.index') }}" class="md-sidebar-link {{ request()->routeIs('member.events.index') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.events.index') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> Events</a>
                    <a href="#" class="md-sidebar-link opacity-60 pointer-events-none" tabindex="-1"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Meetings</a>
                    <a href="{{ route('home') }}#jobs" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Search jobs</a>
                    <a href="#" class="md-sidebar-link opacity-60 pointer-events-none" tabindex="-1"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Polling</a>
                    <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Profile</a>
                    <a href="{{ route('member.password.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Change password</a>
                @elseif($canSeeMembership)
                    <p class="mb-2 rounded-xl bg-[#965995]/10 px-3 py-2 text-xs font-semibold leading-relaxed text-[#351c42]/85">Choose and pay for a plan below. The full member menu appears after your subscription is active. Use the member sidebar for profile and account settings.</p>
                    <a href="{{ route('member.subscription.index') }}" class="md-sidebar-link is-active" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Membership plans</a>
                @else
                    <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link is-active" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Profile</a>
                    <a href="{{ route('member.password.edit') }}" class="md-sidebar-link" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Change password</a>
                @endif
            </nav>
            @include('member.partials.sidebar-logout')
        </aside>
        <div id="md-sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden" aria-hidden="true"></div>

        <main class="min-w-0 flex-1 space-y-10 lg:space-y-12" id="member-events-main">
            @if(session('member_gate_error'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-950">
                    {{ session('member_gate_error') }}
                </div>
            @endif

            <header class="scroll-mt-28 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Events</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Your events</h1>
                    <p class="mt-1 max-w-2xl text-sm text-[#351c42]/65">Register interest and track attendance and certificates here.</p>
                </div>
                <a href="{{ route('member.dashboard') }}" class="shrink-0 text-sm font-semibold text-[#965995] hover:text-[#351c42]">← Back to dashboard</a>
            </header>

            @include('member.partials.member-events-panel', [
                'events' => $events,
                'interestedEventIds' => $interestedEventIds,
                'myEventInvites' => $myEventInvites,
                'inviteByEventId' => $inviteByEventId,
            ])
        </main>
    </div>

    @php
        $donate = config('homepage.donate', ['goal' => 500, 'default_amount' => 100]);
    @endphp
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')

    <script>
        (() => {
            const sidebar = document.getElementById("md-sidebar");
            const toggle = document.querySelector("[data-md-sidebar-toggle]");
            const backdrop = document.getElementById("md-sidebar-backdrop");

            function closeSidebar() {
                sidebar?.classList.add("-translate-x-full");
                backdrop?.classList.add("hidden");
                toggle?.setAttribute("aria-expanded", "false");
                document.body.classList.remove("md-drawer-open");
            }
            function openSidebar() {
                sidebar?.classList.remove("-translate-x-full");
                backdrop?.classList.remove("hidden");
                toggle?.setAttribute("aria-expanded", "true");
                document.body.classList.add("md-drawer-open");
            }

            toggle?.addEventListener("click", () => {
                const open = toggle.getAttribute("aria-expanded") === "true";
                if (open) closeSidebar();
                else openSidebar();
            });
            backdrop?.addEventListener("click", closeSidebar);
            window.addEventListener("resize", () => {
                if (window.innerWidth >= 1024) closeSidebar();
            });

            document.querySelectorAll("[data-md-nav]").forEach((a) => {
                a.addEventListener("click", () => {
                    if (window.innerWidth < 1024) closeSidebar();
                    document.querySelectorAll("[data-md-nav]").forEach((l) => l.classList.remove("is-active"));
                    a.classList.add("is-active");
                });
            });
        })();
    </script>
</body>
</html>
