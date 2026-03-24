@php
    $member = Auth::user();
    $hasActiveSubscription = $member?->activeSubscription()->exists();
    $canSeeMembership = $member && $member->profile_completed && $member->is_approved;
    $sub = $activeSubscription ?? null;
    $firstName = $member?->first_name ?? 'Member';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Member dashboard — GNAT Donation</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: "DM Sans", system-ui, sans-serif; }
        [x-cloak] { display: none !important; }
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
            border: 1px solid rgba(37, 99, 235, 0.18);
            background: linear-gradient(180deg, #fff 0%, #fafbff 100%);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .md-plan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.35);
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            transition: filter 0.2s, transform 0.15s;
            text-decoration: none;
        }
        .md-btn-pay:hover { filter: brightness(1.05); transform: translateY(-1px); }
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
    </style>
</head>
<body class="md-page-bg text-[#351c42] antialiased" id="top">
    @if($member?->profile_completed && !$member?->is_approved)
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

    @if($profileIncomplete)
        <div x-data="{ open: true }" x-cloak>
            <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-2xl border-2 border-[#965995]/30 bg-white p-8 text-center shadow-2xl">
                    <h2 class="text-2xl font-bold text-[#351c42]">Hello, {{ $firstName }}</h2>
                    <p class="mt-4 text-sm leading-relaxed text-[#351c42]/75">Your profile is incomplete. Please complete it to be part of the GNAT member community.</p>
                    <a href="{{ route('member.profile.edit') }}" class="mx-auto mt-8 inline-flex min-w-[10rem] items-center justify-center rounded-full bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-6 py-2.5 text-sm font-bold text-[#fddc6a] shadow-lg shadow-[#351c42]/25 transition hover:brightness-105">Update profile</a>
                    <button type="button" @click="open = false" class="mt-4 block w-full text-sm font-semibold text-[#351c42]/50 hover:text-[#351c42]">Dismiss</button>
                </div>
            </div>
        </div>
    @endif

    <header class="sticky top-0 z-40 md-glass-header">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3.5 lg:gap-6">
            <button type="button" class="rounded-xl p-2.5 hover:bg-[#351c42]/5 lg:hidden -ml-2" data-md-sidebar-toggle aria-expanded="false" aria-controls="md-sidebar" aria-label="Open menu">
                <span class="flex w-[22px] flex-col gap-1.5" aria-hidden="true">
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                </span>
            </button>
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
            <div class="ml-auto relative" x-data="{ openProfile: false }">
                <button type="button"
                    @click="openProfile = !openProfile"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-[#351c42]/10 sm:h-11 sm:w-11"
                    aria-label="Account menu">
                    <svg class="h-5 w-5 text-[#351c42]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                <div x-show="openProfile" x-cloak @click.away="openProfile = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute right-0 mt-2 w-52 rounded-2xl border border-[#351c42]/10 bg-white shadow-xl p-2 z-50">
                    <a href="{{ route('member.profile.edit') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('member.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:flex-row lg:gap-8 lg:py-10">
        <aside
            id="md-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-[min(100%,280px)] -translate-x-full border-r border-[#351c42]/10 bg-white/95 p-5 shadow-2xl transition-transform duration-300 lg:static lg:z-0 lg:w-60 lg:translate-x-0 lg:rounded-2xl lg:border lg:bg-white/80 lg:p-4 lg:shadow-lg lg:shadow-[#351c42]/5"
        >
            <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-widest text-[#965995]">Menu</p>
            <nav class="flex flex-col gap-1" aria-label="Member">
                <a href="{{ url()->current() }}#top" class="md-sidebar-link is-active" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Dashboard</a>
                @if($canSeeMembership && $hasActiveSubscription)
                    <a href="{{ route('member.subscription.index') }}" class="md-sidebar-link" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Membership</a>
                    <a href="{{ route('home') }}#events" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Events</a>
                    <a href="#" class="md-sidebar-link opacity-60 pointer-events-none" tabindex="-1"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Meetings</a>
                    <a href="#" class="md-sidebar-link opacity-60 pointer-events-none" tabindex="-1"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Find e-books</a>
                    <a href="{{ route('home') }}#jobs" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Search jobs</a>
                    <a href="#" class="md-sidebar-link opacity-60 pointer-events-none" tabindex="-1"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Polling</a>
                    <a href="{{ route('home') }}#donate" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Donations</a>
                @else
                    <span class="md-sidebar-link cursor-default opacity-50" title="Available after profile completion and approval"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Membership &amp; more</span>
                @endif
                <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Profile</a>
                <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Account settings</a>
            </nav>
            <form method="POST" action="{{ route('member.logout') }}" class="mt-8 border-t border-[#351c42]/10 pt-4">
                @csrf
                <button type="submit" class="md-sidebar-link w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Log out
                </button>
            </form>
        </aside>
        <div id="md-sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden" aria-hidden="true"></div>

        <main class="min-w-0 flex-1 space-y-10 lg:space-y-12" id="member-dashboard-main">
            <header id="member-dashboard-top" class="scroll-mt-28">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Account</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Member dashboard</h1>
            </header>

            <section class="rounded-2xl border-2 border-blue-200/80 bg-white p-8 text-center shadow-lg shadow-blue-500/5 sm:p-10">
                <h2 class="text-2xl font-bold text-[#351c42] sm:text-3xl">Hello, {{ $firstName }}</h2>
                <p class="mt-4 text-lg font-semibold text-[#351c42] sm:text-xl">Your membership fee <span class="text-blue-600">700 INR</span> / year</p>
                <p class="mx-auto mt-2 max-w-md text-sm text-[#351c42]/65">(Typical new member bundle: subscription + registration — pay via plans below.)</p>
                @if($canSeeMembership)
                    <a href="{{ route('member.subscription.index') }}" class="md-btn-pay mx-auto mt-8 inline-flex px-10">Pay now</a>
                @else
                    <a href="{{ route('member.profile.edit') }}" class="md-btn-pay mx-auto mt-8 inline-flex px-10">Complete profile first</a>
                @endif
            </section>

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
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">No active plan</span>
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
                    @if($canSeeMembership)
                        <a href="{{ route('member.subscription.index', array_filter(['type' => 'Renewal'])) }}" class="md-btn-pay shrink-0 text-center">Pay &amp; renew</a>
                    @endif
                </div>
            </section>

            <section aria-labelledby="plans-heading">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 id="plans-heading" class="text-xl font-bold text-[#351c42] sm:text-2xl">Subscription plans</h2>
                        <p class="mt-1 text-sm text-[#351c42]/60">Choose a plan from the admin-configured list. Payment is handled on the next step.</p>
                    </div>
                    @if($canSeeMembership)
                        <a href="{{ route('member.subscription.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">View all plans →</a>
                    @endif
                </div>

                <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-blue-600">New members</h3>
                <div class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article class="md-plan-card p-6 sm:col-span-2 lg:col-span-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-blue-600">New member · Full year</p>
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

                <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-blue-600">Renewal options</h3>
                <p class="mb-4 text-sm text-[#351c42]/55">Monthly, quarterly, and yearly renewal amounts are defined in admin. Open the membership page to pay.</p>
                <div class="flex flex-wrap gap-3">
                    @if($canSeeMembership)
                        <a href="{{ route('member.subscription.index', ['type' => 'Renewal']) }}" class="md-btn-pay">Renewal plans</a>
                    @endif
                </div>
            </section>

            <section aria-labelledby="history-heading">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 id="history-heading" class="text-xl font-bold text-[#351c42] sm:text-2xl">Subscription history</h2>
                    <input type="search" id="md-history-search" placeholder="Search…" class="rounded-full border border-[#351c42]/15 bg-white px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20" aria-label="Search history" />
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
                                    <span class="rounded-lg bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-900">{{ $t->type }}</span>
                                    @if($planLabel)
                                        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-[#351c42]">{{ $t->subscriptionPlan?->subscription_type ?? 'Plan' }}</span>
                                    @endif
                                </div>
                                <div class="relative" data-history-menu-wrap>
                                    <button type="button" class="rounded-lg p-2 text-[#351c42]/60 hover:bg-[#351c42]/5" aria-label="Actions" data-history-menu-btn>⋮</button>
                                    <div class="absolute right-0 top-full z-10 mt-1 hidden min-w-[11rem] rounded-xl border border-[#351c42]/10 bg-white py-1 shadow-xl" data-history-menu>
                                        @if($status === 'successful')
                                            <a href="{{ route('member.subscription.invoice', $t->id) }}" target="_blank" class="block w-full px-4 py-2.5 text-left text-sm font-medium hover:bg-slate-50">Download receipt</a>
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
        </main>
    </div>

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
