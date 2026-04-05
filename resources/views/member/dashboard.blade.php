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
                            <h2 id="digital-id-heading" class="text-lg font-extrabold tracking-tight text-[#351c42] sm:text-xl">Digital member ID</h2>
                        </div>
                        <p class="text-[11px] text-[#351c42]/50">Show at GNAT programs</p>
                    </div>
                    <div class="md-id-card rounded-xl p-4 text-white shadow-md sm:p-4">
                        <div class="relative z-[1] flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                            <div class="relative h-[4.5rem] w-[4.5rem] shrink-0 overflow-hidden rounded-xl border-2 border-[#fddc6a]/45 bg-white/10">
                                @if($member->passport_photo_path)
                                    <img src="{{ asset('storage/' . $member->passport_photo_path) }}" alt="" class="h-full w-full object-cover" width="72" height="72" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-[#fddc6a]/75">
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[9px] font-bold uppercase tracking-[0.28em] text-[#fddc6a]/85">GNAT Association</p>
                                <p class="mt-0.5 truncate text-base font-extrabold leading-snug sm:text-lg">{{ $member->name }}</p>
                                @if($member->designation?->name)
                                    <p class="mt-0.5 truncate text-xs text-white/70">{{ $member->designation->name }}</p>
                                @endif
                                <p class="mt-1.5 font-mono text-xs font-bold tracking-wide text-[#fddc6a]">GNAT-{{ str_pad((string) $member->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="flex shrink-0 flex-col gap-2 border-t border-white/10 pt-3 sm:border-t-0 sm:border-l sm:pl-4 sm:pt-0">
                                <span class="inline-flex w-fit rounded-full bg-emerald-400/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-100 ring-1 ring-emerald-300/35">
                                    {{ $sub ? 'Active' : 'No plan' }}
                                </span>
                                <dl class="grid gap-1 text-[11px] leading-tight text-white/90 sm:text-xs">
                                    <div class="flex justify-between gap-4 sm:flex-col sm:gap-0">
                                        <dt class="text-white/45">Valid</dt>
                                        <dd class="font-semibold tabular-nums">{{ $sub?->end_date?->format('d M Y') ?? '—' }}</dd>
                                    </div>
                                    <div class="flex justify-between gap-4 sm:flex-col sm:gap-0">
                                        <dt class="text-white/45">Since</dt>
                                        <dd class="font-semibold">{{ $member->created_at?->format('M Y') ?? '—' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
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
                    <div class="flex shrink-0 flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                        @if($showFullMemberMenu && $latestReceiptTransaction)
                            <a href="{{ route('member.subscription.invoice', $latestReceiptTransaction->id) }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center gap-2 rounded-full border-2 border-[#351c42]/20 bg-white px-5 py-2.5 text-center text-sm font-bold text-[#351c42] shadow-sm transition hover:border-[#965995]/40 hover:bg-[#faf8fc]">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Download latest receipt
                            </a>
                        @endif
                        @if($showFullMemberMenu && $canSeeMembership)
                            <a href="{{ route('member.subscription.index', array_filter(['type' => 'Renewal'])) }}" class="md-btn-pay text-center">Pay &amp; renew</a>
                        @endif
                    </div>
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

            <section aria-labelledby="history-heading">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 id="history-heading" class="text-xl font-bold text-[#351c42] sm:text-2xl">Subscription history</h2>
                    <input type="search" id="md-history-search" placeholder="Search…" class="rounded-full border border-[#351c42]/15 bg-white px-4 py-2 text-sm text-[#351c42] outline-none placeholder:text-[#351c42]/40 focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25" aria-label="Search history" />
                </div>
                <p class="mb-4 text-xs text-[#351c42]/50">Recent transactions · Download invoice when payment is successful.</p>

                <div class="space-y-4" id="md-history-list">
                    @forelse($transactions as $t)
                        @php
                            $status = strtolower((string) $t->status);
                            $planLabel = trim(($t->subscriptionPlan?->subscription_type ?? '') . ' · ' . ucfirst(str_replace('_', ' ', (string) ($t->subscriptionPlan?->payment_type ?? ''))));
                        @endphp
                        <article class="md-history-card overflow-hidden p-5 sm:p-6" data-history-row="{{ strtolower($planLabel . ' ' . $t->type . ' ' . $status . ' ' . $t->amount) }}">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex flex-wrap gap-3">
                                    <span class="rounded-lg bg-[#351c42]/10 px-2.5 py-1 text-xs font-bold text-[#351c42]">{{ $t->type }}</span>
                                    @if($planLabel)
                                        <span class="rounded-lg bg-[#965995]/15 px-2.5 py-1 text-xs font-semibold text-[#351c42]">{{ $t->subscriptionPlan?->subscription_type ?? 'Plan' }}</span>
                                    @endif
                                </div>
                                <div class="relative" data-history-menu-wrap>
                                    <button type="button" class="rounded-lg p-2 text-[#351c42]/60 hover:bg-[#351c42]/5" aria-label="Actions" data-history-menu-btn>⋮</button>
                                    <div class="absolute right-0 top-full z-10 mt-1 hidden min-w-[11rem] rounded-xl border border-[#351c42]/10 bg-white py-1 shadow-xl" data-history-menu>
                                        @if($status === 'successful')
                                            <a href="{{ route('member.subscription.invoice', $t->id) }}" target="_blank" class="block w-full px-4 py-2.5 text-left text-sm font-medium text-[#351c42] hover:bg-[#351c42]/5">Download receipt</a>
                                        @else
                                            <span class="block px-4 py-2.5 text-sm text-[#351c42]/45">Receipt when paid</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <p class="text-xs font-bold uppercase text-[#351c42]/45">Paid / created</p>
                                    <p class="mt-0.5 text-sm font-semibold">{{ optional($t->paid_at ?? $t->created_at)->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase text-[#351c42]/45">Reference</p>
                                    <p class="mt-0.5 break-all font-mono text-xs">{{ $t->razorpay_payment_id ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase text-[#351c42]/45">Amount</p>
                                    <p class="mt-0.5 text-sm font-bold">₹ {{ number_format((float) $t->amount, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase text-[#351c42]/45">Status</p>
                                    <p class="mt-1">
                                        @if($status === 'successful')
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800">Paid</span>
                                        @elseif($status === 'pending')
                                            <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-900">Pending</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700">{{ $t->status }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="md-history-card p-8 text-center text-sm text-[#351c42]/60">
                            No transactions yet. Use <strong>Pay now</strong> or <strong>Membership</strong> to subscribe.
                        </div>
                    @endforelse
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

            document.querySelectorAll("[data-history-menu-btn]").forEach((btn) => {
                const wrap = btn.closest("[data-history-menu-wrap]");
                const menu = wrap?.querySelector("[data-history-menu]");
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    document.querySelectorAll("[data-history-menu]").forEach((m) => {
                        if (m !== menu) m.classList.add("hidden");
                    });
                    menu?.classList.toggle("hidden");
                });
                menu?.addEventListener("click", (e) => e.stopPropagation());
            });
            document.addEventListener("click", () => {
                document.querySelectorAll("[data-history-menu]").forEach((m) => m.classList.add("hidden"));
            });

            const search = document.getElementById("md-history-search");
            search?.addEventListener("input", () => {
                const q = (search.value || "").toLowerCase().trim();
                document.querySelectorAll("[data-history-row]").forEach((row) => {
                    const hay = (row.getAttribute("data-history-row") || "").toLowerCase();
                    row.classList.toggle("hidden", q.length > 0 && !hay.includes(q));
                });
            });

            if (window.location.hash === "#member-dashboard-top") {
                document.getElementById("member-dashboard-top")?.scrollIntoView({ behavior: "smooth" });
            }
        })();
    </script>
</body>
</html>
