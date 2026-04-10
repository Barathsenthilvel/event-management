@php
    $member = Auth::user();
    $hasActiveSubscription = $member?->activeSubscription()->exists();
    $canSeeMembership = $member && $member->profile_completed && $member->is_approved;
    /** Full sidebar + events / e-books / history / digital ID */
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
    <title>Dashboard — GNAT Association</title>
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
        .md-plan-card {
            border-radius: 1.25rem;
            border: 1px solid rgba(53, 28, 66, 0.12);
            background: linear-gradient(180deg, #fff 0%, rgba(150, 89, 149, 0.04) 100%);
            box-shadow: 0 4px 20px rgba(53, 28, 66, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .md-plan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(53, 28, 66, 0.1);
            border-color: rgba(150, 89, 149, 0.35);
        }
        .md-history-card {
            border-radius: 1.25rem;
            border: 1px solid rgba(53, 28, 66, 0.08);
            background: #fff;
            box-shadow: 0 2px 12px rgba(53, 28, 66, 0.05);
        }
        .md-btn-pay {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.65rem 1.35rem;
            font-size: 0.8125rem;
            font-weight: 700;
            background: linear-gradient(135deg, #351c42 0%, #4d2a5c 100%);
            color: #fddc6a;
            box-shadow: 0 6px 18px rgba(53, 28, 66, 0.35);
            transition: filter 0.2s, transform 0.15s;
            text-decoration: none;
        }
        .md-btn-pay:hover { filter: brightness(1.06); transform: translateY(-1px); }
        .md-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(8px);
        }
        .md-modal-overlay.is-open { display: flex; }
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
        .md-id-card {
            position: relative;
            overflow: hidden;
            border-radius: 1.35rem;
            border: 1px solid rgba(253, 220, 106, 0.35);
            background:
                radial-gradient(ellipse 120% 80% at 100% 0%, rgba(150, 89, 149, 0.45), transparent 55%),
                radial-gradient(ellipse 90% 60% at 0% 100%, rgba(253, 220, 106, 0.12), transparent 50%),
                linear-gradient(155deg, #24122e 0%, #351c42 42%, #3d2149 100%);
            box-shadow: 0 20px 50px rgba(53, 28, 66, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }
        .md-id-card::after {
            content: "";
            position: absolute;
            inset: -40% -20% auto auto;
            width: 60%;
            height: 80%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.04));
            transform: rotate(12deg);
            pointer-events: none;
        }
        .md-id-card-classic {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid rgba(253, 220, 106, 0.35);
            background:
                radial-gradient(ellipse 120% 80% at 100% 0%, rgba(150, 89, 149, 0.26), transparent 55%),
                linear-gradient(155deg, #2b1635 0%, #351c42 50%, #47245a 100%);
            box-shadow: 0 14px 30px rgba(53, 28, 66, 0.32);
        }
        .md-id-card-classic::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0));
            pointer-events: none;
        }
        .md-kebab-menu { position: relative; }
        .md-kebab-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 0.35rem);
            min-width: 12rem;
            border-radius: 0.85rem;
            border: 1px solid rgba(53, 28, 66, 0.12);
            background: #fff;
            box-shadow: 0 12px 28px rgba(53, 28, 66, 0.18);
            padding: 0.3rem;
            display: none;
            z-index: 10;
        }
        .md-kebab-dropdown.is-open { display: block; }
        .md-announce-card {
            border-radius: 1.1rem;
            border: 1px solid rgba(253, 220, 106, 0.28);
            background:
                radial-gradient(ellipse 120% 85% at 100% 0%, rgba(150, 89, 149, 0.3), transparent 60%),
                #351c42;
            box-shadow: 0 12px 28px rgba(53, 28, 66, 0.35);
        }
        .md-btn-interest-card {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background: linear-gradient(135deg, #fddc6a 0%, #f3c94b 100%);
            color: #351c42;
            font-size: 0.875rem;
            font-weight: 800;
            padding: 0.625rem 1.25rem;
            box-shadow: 0 8px 18px rgba(243, 201, 75, 0.35);
            transition: transform 0.15s ease, filter 0.2s ease;
        }
        .md-btn-interest-card:hover {
            filter: brightness(1.03);
            transform: translateY(-1px);
        }
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
                    <a href="{{ route('home') }}#jobs" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Search jobs</a>
                    <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Profile</a>
                    <a href="{{ route('member.password.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Change password</a>
                @elseif($canSeeMembership)
                    <p class="mb-2 rounded-xl bg-[#965995]/10 px-3 py-2 text-xs font-semibold leading-relaxed text-[#351c42]/85">Choose and pay for a plan in the main area. The full menu unlocks after your subscription is active. Use Profile and Change password below.</p>
                    <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link {{ request()->routeIs('member.profile.*') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.profile.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> Profile</a>
                    <a href="{{ route('member.password.edit') }}" class="md-sidebar-link {{ request()->routeIs('member.password.*') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.password.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> Change password</a>
                @else
                    <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link is-active" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Profile</a>
                    <a href="{{ route('member.password.edit') }}" class="md-sidebar-link" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Change password</a>
                @endif
            </nav>
            @include('member.partials.sidebar-logout')
        </aside>
        <div id="md-sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden" aria-hidden="true"></div>

        <main class="min-w-0 flex-1 space-y-10 lg:space-y-12" id="member-dashboard-main">
            @if(session('member_gate_error'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-950">
                    {{ session('member_gate_error') }}
                </div>
            @endif

            <header id="member-dashboard-top" class="scroll-mt-28">
                @if($canSeeMembership && !$showFullMemberMenu)
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Membership</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Choose your subscription plan</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#351c42]/70">Your account is approved. Select a plan below and complete payment. After your membership is active, the full menu (dashboard, e-books, events, and more) will unlock.</p>
                @else
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Account</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Member dashboard</h1>
                @endif
            </header>

            @if($showFullMemberMenu && ($showNominationPrompt || $showPollingPrompt))
                <section class="pointer-events-none fixed bottom-4 right-4 z-[95] flex w-[min(92vw,24rem)] flex-col gap-3 sm:bottom-6 sm:right-6" aria-label="Member announcements">
                    @if($showNominationPrompt && $nominationPrompt)
                        <article class="md-announce-card pointer-events-auto p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#fddc6a]">Hello there</p>
                                    <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-white">Nominations open now</h3>
                                    <p class="mt-2 text-sm leading-relaxed text-white/85">{{ $nominationPrompt->title }}</p>
                                    <p class="mt-1 text-xs font-semibold text-white/70">
                                        From: {{ $nominationPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $nominationPrompt->polling_from ? \Carbon\Carbon::parse($nominationPrompt->polling_from)->format('h:i A') : '—' }}
                                    </p>
                                    <p class="mt-0.5 text-xs font-semibold text-white/70">
                                        To: {{ $nominationPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $nominationPrompt->polling_to ? \Carbon\Carbon::parse($nominationPrompt->polling_to)->format('h:i A') : '—' }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('member.dashboard.announcements.dismiss') }}">
                                    @csrf
                                    <input type="hidden" name="type" value="nomination">
                                    <input type="hidden" name="next" value="{{ route('member.dashboard') }}">
                                    <button type="submit" class="rounded-full p-1.5 text-white/70 transition hover:bg-white/10 hover:text-white" aria-label="Dismiss nomination prompt">✕</button>
                                </form>
                            </div>
                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                @php
                                    $nextNominationPosition = $nominationPrompt->positions->first(
                                        fn ($position) => !collect($nominationInterestedPositionIds ?? [])->contains($position->id)
                                    );
                                @endphp
                                @if($nextNominationPosition)
                                    <form method="POST" action="{{ route('member.nominations.interest', [$nominationPrompt, $nextNominationPosition]) }}">
                                        @csrf
                                        <button type="submit" class="md-btn-interest-card">I'm interested</button>
                                    </form>
                                @endif
                                <button type="button" data-popup-open="nomination-info-modal" class="text-sm font-bold text-[#fddc6a] hover:text-[#ffe79d]">View positions</button>
                            </div>
                        </article>
                    @endif

                    @if($showPollingPrompt && $pollingPrompt)
                        <article class="md-announce-card pointer-events-auto p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#fddc6a]">Hello there</p>
                                    <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-white">Polling is live</h3>
                                    <p class="mt-2 text-sm leading-relaxed text-white/85">{{ $pollingPrompt->title }}</p>
                                    <p class="mt-1 text-xs font-semibold text-white/70">
                                        From: {{ $pollingPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $pollingPrompt->polling_from ? \Carbon\Carbon::parse($pollingPrompt->polling_from)->format('h:i A') : '—' }}
                                    </p>
                                    <p class="mt-0.5 text-xs font-semibold text-white/70">
                                        To: {{ $pollingPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $pollingPrompt->polling_to ? \Carbon\Carbon::parse($pollingPrompt->polling_to)->format('h:i A') : '—' }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('member.dashboard.announcements.dismiss') }}">
                                    @csrf
                                    <input type="hidden" name="type" value="polling">
                                    <input type="hidden" name="next" value="{{ route('member.dashboard') }}">
                                    <button type="submit" class="rounded-full p-1.5 text-white/70 transition hover:bg-white/10 hover:text-white" aria-label="Dismiss polling prompt">✕</button>
                                </form>
                            </div>
                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <form method="POST" action="{{ route('member.dashboard.announcements.dismiss') }}">
                                    @csrf
                                    <input type="hidden" name="type" value="polling">
                                    <input type="hidden" name="next" value="{{ route('member.dashboard') }}">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#351c42] px-5 py-2.5 text-sm font-bold text-[#fddc6a] shadow-md shadow-[#351c42]/20">Got it</button>
                                </form>
                                <button type="button" data-popup-open="polling-info-modal" class="text-sm font-bold text-[#fddc6a] hover:text-[#ffe79d]">Learn more</button>
                            </div>
                        </article>
                    @endif
                </section>
            @endif

            @if(!$canSeeMembership || $showFullMemberMenu)
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Account summary">
                {{-- Profile --}}
                <article class="relative overflow-hidden rounded-2xl border border-[#351c42]/10 bg-white shadow-md transition hover:border-[#965995]/35 hover:shadow-lg">
                    <div class="h-1 bg-gradient-to-r from-[#965995] to-[#351c42]"></div>
                    <div class="p-5 pt-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/45">Profile</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#965995]/12 text-[#965995]" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold tracking-tight text-[#351c42]">{{ $member?->profile_completed ? 'Completed' : 'Incomplete' }}</p>
                        <p class="mt-2 inline-flex items-center gap-1 rounded-full bg-[#f6f3e9] px-2.5 py-1 text-[11px] font-semibold text-[#351c42]/75">
                            @if($member?->profile_completed)
                                <svg class="h-3.5 w-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Details on file
                            @else
                                <svg class="h-3.5 w-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Finish your profile
                            @endif
                        </p>
                    </div>
                </article>
                {{-- Approval --}}
                <article class="relative overflow-hidden rounded-2xl border border-[#351c42]/10 bg-white shadow-md transition hover:border-emerald-300/60 hover:shadow-lg">
                    <div class="h-1 {{ $member?->is_approved ? 'bg-gradient-to-r from-emerald-400 to-teal-600' : 'bg-gradient-to-r from-amber-300 to-amber-600' }}"></div>
                    <div class="p-5 pt-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/45">Approval</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $member?->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold tracking-tight {{ $member?->is_approved ? 'text-emerald-800' : 'text-amber-800' }}">{{ $member?->is_approved ? 'Approved' : 'Pending' }}</p>
                        <p class="mt-2 text-[11px] leading-snug text-[#351c42]/55">{{ $member?->is_approved ? 'Your account is cleared by the office.' : 'We will notify you when review is complete.' }}</p>
                    </div>
                </article>
                {{-- Name --}}
                <article class="relative overflow-hidden rounded-2xl border border-[#351c42]/10 bg-gradient-to-br from-white to-[#faf8fc] shadow-md transition hover:border-[#351c42]/20 hover:shadow-lg">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-[#965995]/10 blur-2xl"></div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/45">Member name</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#351c42] text-[#fddc6a]" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 break-words text-xl font-extrabold leading-tight text-[#351c42] sm:text-2xl" title="{{ $member?->name ?? '-' }}">{{ $member?->name ?? '—' }}</p>
                        <p class="mt-2 text-[11px] text-[#351c42]/50">As registered with GNAT</p>
                    </div>
                </article>
                {{-- Donations --}}
                <article class="relative overflow-hidden rounded-2xl border border-[#fddc6a]/40 bg-[#f7f6f0] shadow-md transition hover:shadow-lg sm:col-span-2 lg:col-span-1">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_100%_0%,rgba(150,89,149,0.08),transparent)]"></div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/50">Donations</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#351c42] text-[#fddc6a]" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <p class="mt-2 font-mono text-3xl font-extrabold tabular-nums tracking-tight text-[#351c42]">₹{{ number_format((float) ($memberDonationsTotal ?? 0), 0) }}</p>
                        <p class="mt-2 text-[11px] font-medium text-[#351c42]/55">Your total successful gifts to GNAT</p>
                    </div>
                </article>
            </section>
            @endif

            @if($showFullMemberMenu)
                <section aria-labelledby="digital-id-heading" class="scroll-mt-28">
                    <div class="mb-3 flex flex-wrap items-end justify-between gap-2">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#965995]">Membership</p>
                            <h2 id="digital-id-heading" class="text-lg font-extrabold tracking-tight text-[#351c42] sm:text-xl">Digital member ID &amp; subscription</h2>
                        </div>
                        <p class="text-[11px] text-[#351c42]/50">Show your ID and latest plan details</p>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <article class="md-id-card-classic p-0 text-white shadow-md">
                            <div class="relative z-[1] border-b border-white/15 bg-[#4c2b5d] px-4 py-1.5 text-center">
                                <p class="text-sm font-black uppercase tracking-[0.2em] text-white/95">ID Card</p>
                            </div>
                            <div class="relative z-[1] grid grid-cols-[4.5rem_minmax(0,1fr)_2rem] items-center gap-3 px-4 py-3">
                                <div class="relative h-[4.25rem] w-[4.25rem] shrink-0 overflow-hidden rounded-md border border-[#fddc6a]/40 bg-white/10">
                                    @if($member->passport_photo_path)
                                        <img src="{{ asset('storage/' . $member->passport_photo_path) }}" alt="" class="h-full w-full object-cover" width="72" height="72" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-[#fddc6a]/75">
                                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-[13px] font-extrabold leading-snug text-white">{{ $member->name }}</p>
                                    @if($member->designation?->name)
                                        <p class="mt-0.5 truncate text-[11px] text-white/75">{{ $member->designation->name }}</p>
                                    @endif
                                    <p class="mt-1 font-mono text-[11px] font-bold tracking-wide text-[#fddc6a]">GNAT-{{ str_pad((string) $member->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <p class="mt-0.5 text-[10px] text-white/75">Valid till: {{ $sub?->end_date?->format('d M Y') ?? '—' }}</p>
                                </div>
                                <div class="flex h-[4.25rem] flex-col justify-between rounded-sm bg-black/20 px-1 py-1">
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                    <span class="block h-[2px] w-full bg-[#fddc6a]/85"></span>
                                </div>
                            </div>
                        </article>

                        <article class="rounded-xl border border-[#351c42]/10 bg-white p-4 shadow-md sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#965995]">Subscription purchased</p>
                                    <h3 class="mt-1 text-base font-extrabold text-[#351c42]">Current plan</h3>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="inline-flex w-fit rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $sub ? 'bg-emerald-100 text-emerald-800' : 'bg-[#965995]/15 text-[#351c42]/80' }}">
                                        {{ $sub ? 'Active' : 'No plan' }}
                                    </span>
                                    <div class="md-kebab-menu" data-sub-menu-wrap>
                                        <button type="button" class="rounded-lg p-1.5 text-[#351c42]/60 hover:bg-[#351c42]/5" aria-label="Subscription actions" data-sub-menu-btn>⋮</button>
                                        <div class="md-kebab-dropdown" data-sub-menu>
                                            @if($latestReceiptTransaction)
                                                <a href="{{ route('member.subscription.invoice', $latestReceiptTransaction->id) }}" target="_blank" rel="noopener" class="block rounded-lg px-3 py-2 text-sm font-semibold text-[#351c42] hover:bg-[#351c42]/5">
                                                    Download receipt
                                                </a>
                                            @else
                                                <span class="block rounded-lg px-3 py-2 text-sm text-[#351c42]/45">No receipt yet</span>
                                            @endif
                                            @if($showFullMemberMenu && $canSeeMembership)
                                                <a href="{{ route('member.subscription.index', ['type' => 'Renewal']) }}" class="mt-0.5 block rounded-lg px-3 py-2 text-sm font-semibold text-[#351c42] hover:bg-[#351c42]/5">
                                                    Pay &amp; renew
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <dl class="mt-4 grid gap-3 text-sm text-[#351c42] sm:grid-cols-2">
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Plan purchased</dt>
                                    <dd class="mt-1 font-semibold">
                                        @if($sub)
                                            {{ $sub->subscription_type }} · {{ ucfirst(str_replace('_', ' ', (string) $sub->payment_type)) }}
                                        @else
                                            —
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Valid till</dt>
                                    <dd class="mt-1 font-semibold tabular-nums">{{ $sub?->end_date?->format('d M Y') ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Purchased on</dt>
                                    <dd class="mt-1 font-semibold tabular-nums">{{ $sub?->start_date?->format('d M Y') ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Member since</dt>
                                    <dd class="mt-1 font-semibold">{{ $member->created_at?->format('M Y') ?? '—' }}</dd>
                                </div>
                            </dl>
                        </article>
                    </div>
                </section>
            @endif

            @if($showFullMemberMenu)
            <section id="section-membership" class="scroll-mt-28 rounded-2xl border border-[#351c42]/10 bg-white/90 p-6 shadow-md sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-[#351c42]/50">From / to</p>
                            <p class="mt-1 font-semibold text-[#351c42]">
                                @if($sub)
                                    {{ $sub->start_date?->format('d M Y') }} — {{ $sub->end_date?->format('d M Y') }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-[#351c42]/50">Membership ends</p>
                            <p class="mt-1">
                                @if($sub?->end_date)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">{{ $sub->end_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-sm text-[#351c42]/50">—</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-[#351c42]/50">Status</p>
                            <p class="mt-1">
                                @if($sub)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">Active</span>
                                @else
                                    <span class="rounded-full bg-[#965995]/15 px-2.5 py-1 text-xs font-bold text-[#351c42]/80">No active plan</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-[#351c42]/50">Plan</p>
                            <p class="mt-1 text-sm font-semibold text-[#351c42]">
                                @if($sub)
                                    {{ $sub->subscription_type }} · {{ ucfirst(str_replace('_', ' ', (string) $sub->payment_type)) }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="hidden shrink-0 sm:block"></div>
                </div>
            </section>
            @endif

            <section aria-labelledby="plans-heading">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 id="plans-heading" class="text-xl font-bold text-[#351c42] sm:text-2xl">Subscription plans</h2>
                        <p class="mt-1 text-sm text-[#351c42]/60">Choose a plan from the admin-configured list. Payment is handled on the next step.</p>
                    </div>
                    @if($canSeeMembership)
                        <a href="{{ route('member.subscription.index') }}" class="text-sm font-bold text-[#965995] hover:text-[#351c42]">View all plans →</a>
                    @endif
                </div>

                @if($showFullMemberMenu)
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-[#965995]">Renewal plans</h3>
                    @if(($renewalPlans ?? collect())->isNotEmpty())
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($renewalPlans as $plan)
                                @php
                                    $paymentLabel = match($plan->payment_type) {
                                        'monthly' => 'Monthly',
                                        'quarterly' => 'Quarterly',
                                        'half_yearly' => 'Half yearly',
                                        'yearly' => 'Yearly',
                                        default => ucfirst(str_replace('_', ' ', (string) $plan->payment_type)),
                                    };
                                @endphp
                                <article class="md-plan-card p-5">
                                    <p class="text-xs font-bold uppercase tracking-wide text-[#965995]">Renewal</p>
                                    <p class="mt-2 text-xl font-extrabold text-[#351c42]">{{ $paymentLabel }}</p>
                                    <p class="mt-1 text-sm text-[#351c42]/65">Membership fee: ₹{{ number_format((float) $plan->membership_fee, 0) }}</p>
                                    <a href="{{ route('member.subscription.index', ['type' => 'Renewal']) }}" class="md-btn-pay mt-5 inline-flex w-full justify-center">Choose plan</a>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-[#351c42]/10 bg-white px-5 py-4 text-sm font-semibold text-[#351c42]/70">
                            No renewal found.
                        </div>
                    @endif
                @else
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-[#965995]">New members</h3>
                    <div class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <article class="md-plan-card p-6 sm:col-span-2 lg:col-span-1">
                            <p class="text-xs font-bold uppercase tracking-wide text-[#965995]">New member · Full year</p>
                            <p class="mt-2 text-2xl font-extrabold text-[#351c42]">From admin settings</p>
                            <p class="mt-1 text-sm text-[#351c42]/65">Pricing is set under Membership in admin.</p>
                            <ul class="mt-4 space-y-1.5 text-sm text-[#351c42]/75">
                                <li>Includes registration where applicable</li>
                                <li>Pay securely via Razorpay</li>
                            </ul>
                            @if($canSeeMembership)
                                <a href="{{ route('member.subscription.index', ['type' => 'New']) }}" class="md-btn-pay mt-6 inline-flex w-full justify-center sm:w-auto">Choose plan</a>
                            @else
                                <span class="mt-6 inline-block text-sm font-semibold text-[#351c42]/45">Complete profile &amp; approval required</span>
                            @endif
                        </article>
                    </div>

                    <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-[#965995]">Renewal options</h3>
                    <p class="mb-4 text-sm text-[#351c42]/55">Monthly, quarterly, and yearly renewal amounts are defined in admin. Open the membership page to pay.</p>
                    <div class="flex flex-wrap gap-3">
                        @if($canSeeMembership)
                            <a href="{{ route('member.subscription.index', ['type' => 'Renewal']) }}" class="md-btn-pay">Renewal plans</a>
                        @endif
                    </div>
                @endif
            </section>
            @if($showFullMemberMenu)
            <section class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-5 shadow-md sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Events</p>
                        <h2 class="mt-1 text-lg font-bold text-[#351c42]">Interested in GNAT events?</h2>
                        <p class="mt-1 text-sm text-[#351c42]/60">Register interest, see attendance, and download certificates on the member events page.</p>
                    </div>
                    <a href="{{ route('member.events.index') }}" class="md-btn-pay shrink-0 text-center">Open events page</a>
                </div>
            </section>
            @endif
        </main>
    </div>

    @php
        $donate = config('homepage.donate', ['goal' => 500, 'default_amount' => 100]);
    @endphp
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')

    @if($showFullMemberMenu && $showNominationPrompt && $nominationPrompt)
        <div id="nomination-info-modal" class="md-modal-overlay" data-popup-modal>
            <div class="w-full max-w-lg rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#965995]">Nominations details</p>
                        <h3 class="mt-1 text-xl font-extrabold text-[#351c42]">{{ $nominationPrompt->title }}</h3>
                    </div>
                    <button type="button" data-popup-close class="rounded-full p-1.5 text-[#351c42]/50 hover:bg-[#351c42]/5">✕</button>
                </div>
                <div class="mt-4 space-y-2 text-sm text-[#351c42]/80">
                    <p><span class="font-bold text-[#351c42]">From:</span> {{ $nominationPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $nominationPrompt->polling_from ? \Carbon\Carbon::parse($nominationPrompt->polling_from)->format('h:i A') : '—' }}</p>
                    <p><span class="font-bold text-[#351c42]">To:</span> {{ $nominationPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $nominationPrompt->polling_to ? \Carbon\Carbon::parse($nominationPrompt->polling_to)->format('h:i A') : '—' }}</p>
                </div>
                <div class="mt-4 space-y-3">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-[#965995]">Open positions</p>
                    <div class="max-h-56 space-y-2 overflow-auto pr-1">
                        @forelse($nominationPrompt->positions as $position)
                            @php $isInterested = collect($nominationInterestedPositionIds ?? [])->contains($position->id); @endphp
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-[#351c42]/10 bg-[#faf9fc] px-3 py-2.5">
                                <p class="text-sm font-semibold text-[#351c42]">{{ $position->title }}</p>
                                @if($isInterested)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">Interested</span>
                                @else
                                    <form method="POST" action="{{ route('member.nominations.interest', [$nominationPrompt, $position]) }}">
                                        @csrf
                                        <button type="submit" class="rounded-full bg-[#351c42] px-3 py-1.5 text-xs font-bold text-[#fddc6a]">Interested</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="rounded-xl border border-[#351c42]/10 bg-[#faf9fc] px-3 py-2 text-sm text-[#351c42]/70">No positions configured yet.</p>
                        @endforelse
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="button" data-popup-close class="rounded-full bg-[#351c42] px-5 py-2 text-sm font-bold text-[#fddc6a]">Done</button>
                </div>
            </div>
        </div>
    @endif

    @if($showFullMemberMenu && $showPollingPrompt && $pollingPrompt)
        <div id="polling-info-modal" class="md-modal-overlay" data-popup-modal>
            <div class="w-full max-w-lg rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#965995]">Polling details</p>
                        <h3 class="mt-1 text-xl font-extrabold text-[#351c42]">{{ $pollingPrompt->title }}</h3>
                    </div>
                    <button type="button" data-popup-close class="rounded-full p-1.5 text-[#351c42]/50 hover:bg-[#351c42]/5">✕</button>
                </div>
                <div class="mt-4 space-y-2 text-sm text-[#351c42]/80">
                    <p><span class="font-bold text-[#351c42]">From:</span> {{ $pollingPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $pollingPrompt->polling_from ? \Carbon\Carbon::parse($pollingPrompt->polling_from)->format('h:i A') : '—' }}</p>
                    <p><span class="font-bold text-[#351c42]">To:</span> {{ $pollingPrompt->polling_date?->format('d M Y') ?? '—' }} {{ $pollingPrompt->polling_to ? \Carbon\Carbon::parse($pollingPrompt->polling_to)->format('h:i A') : '—' }}</p>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="button" data-popup-close class="rounded-full bg-[#351c42] px-5 py-2 text-sm font-bold text-[#fddc6a]">Close</button>
                </div>
            </div>
        </div>
    @endif

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

            document.querySelectorAll("[data-sub-menu-btn]").forEach((btn) => {
                const wrap = btn.closest("[data-sub-menu-wrap]");
                const menu = wrap?.querySelector("[data-sub-menu]");
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    document.querySelectorAll("[data-sub-menu]").forEach((m) => {
                        if (m !== menu) m.classList.remove("is-open");
                    });
                    menu?.classList.toggle("is-open");
                });
            });
            document.addEventListener("click", () => {
                document.querySelectorAll("[data-sub-menu]").forEach((m) => m.classList.remove("is-open"));
            });

            document.querySelectorAll("[data-popup-open]").forEach((btn) => {
                btn.addEventListener("click", () => {
                    const id = btn.getAttribute("data-popup-open");
                    const modal = id ? document.getElementById(id) : null;
                    modal?.classList.add("is-open");
                });
            });
            document.querySelectorAll("[data-popup-close]").forEach((btn) => {
                btn.addEventListener("click", () => {
                    btn.closest("[data-popup-modal]")?.classList.remove("is-open");
                });
            });
            document.querySelectorAll("[data-popup-modal]").forEach((modal) => {
                modal.addEventListener("click", (e) => {
                    if (e.target === modal) modal.classList.remove("is-open");
                });
            });

            if (window.location.hash === "#member-dashboard-top") {
                document.getElementById("member-dashboard-top")?.scrollIntoView({ behavior: "smooth" });
            }
        })();
    </script>
</body>
</html>
