<?php
    $member = Auth::user();
    $hasActiveSubscription = $member?->activeSubscription()->exists();
    $canSeeMembership = $member && $member->profile_completed && $member->is_approved;
    /** Full sidebar + events / e-books / history / digital ID */
    $showFullMemberMenu = $canSeeMembership && $hasActiveSubscription;
    $sub = $activeSubscription ?? null;
    $firstName = $member?->first_name ?? 'Member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Dashboard — GNAT Association</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->make('home.partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
            z-index: 110;
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
        .md-popup-compact {
            padding: 0.8rem !important;
        }
        .md-popup-compact .md-popup-title {
            font-size: 1.25rem;
            line-height: 1.15;
        }
        .md-popup-compact .md-popup-subtitle {
            font-size: 0.7rem;
            letter-spacing: 0.16em;
        }
        .md-popup-compact .md-popup-meta {
            font-size: 0.7rem;
        }
        .md-popup-compact .md-nom-announce-row {
            padding: 0.5rem 0.65rem;
            border-radius: 1rem;
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
            text-decoration: none;
        }
        .md-btn-interest-card:hover {
            filter: brightness(1.03);
            transform: translateY(-1px);
        }
        .md-nom-announce-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            border-radius: 1.15rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(248, 246, 242, 0.98);
            padding: 0.75rem 1rem;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.85) inset;
            flex-wrap: wrap;
        }
        .md-nom-announce-row .md-nom-interest-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.45rem 1rem;
            font-size: 0.75rem;
            font-weight: 800;
            background: #24122e;
            color: #fddc6a;
            border: none;
            cursor: pointer;
            transition: filter 0.15s ease, transform 0.15s ease;
        }
        .md-nom-announce-row .md-nom-interest-pill:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="md-page-bg text-[#351c42] antialiased" id="top">
    <?php if(!$member?->profile_completed): ?>
        <div x-data x-cloak>
            <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-2xl border-2 border-[#965995]/30 bg-white p-8 text-center shadow-2xl">
                    <h2 class="text-2xl font-bold text-[#351c42]">Hello, <?php echo e($firstName); ?></h2>
                    <p class="mt-4 text-sm leading-relaxed text-[#351c42]/75">Your profile is incomplete. Please complete it to be part of the GNAT member community.</p>
                    <a href="<?php echo e(route('member.profile.edit')); ?>" class="mx-auto mt-8 inline-flex min-w-[10rem] items-center justify-center rounded-full bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-6 py-2.5 text-sm font-bold text-[#fddc6a] shadow-lg shadow-[#351c42]/25 transition hover:brightness-105">Update profile</a>
                </div>
            </div>
        </div>
    <?php elseif(!$member?->is_approved): ?>
        <div x-data x-cloak>
            <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
                <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-[#351c42]/10 bg-white p-8 shadow-2xl">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#965995]">Approval pending</p>
                    <h3 class="mt-2 text-xl font-extrabold text-[#351c42]">Please wait for admin approval</h3>
                    <p class="mt-3 text-sm text-[#351c42]/75">We received your profile. Once approved, you can purchase membership plans.</p>
                    <div class="mt-6 flex flex-wrap justify-end gap-3">
                        <a href="<?php echo e(route('member.profile.edit')); ?>" class="rounded-full bg-[#351c42] px-5 py-2.5 text-sm font-bold text-[#fddc6a]">Review profile</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php echo $__env->make('member.partials.public-site-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="sticky top-0 z-30 flex items-center justify-between border-b border-[#351c42]/10 bg-[#faf9fc] px-4 py-2 lg:hidden">
        <span class="text-[10px] font-bold uppercase tracking-wide text-[#351c42]/45">Member</span>
        <button type="button" data-md-sidebar-toggle aria-expanded="false" aria-controls="md-sidebar" class="text-xs font-bold text-[#965995] hover:text-[#351c42]">Menu</button>
    </div>

    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:flex-row lg:gap-8 lg:py-10">
        <aside
            id="md-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-[min(100%,280px)] -translate-x-full border-r border-[#351c42]/10 bg-white/95 p-5 shadow-2xl transition-transform duration-300 lg:static lg:z-0 lg:w-60 lg:translate-x-0 lg:rounded-2xl lg:border lg:bg-white/80 lg:p-4 lg:shadow-lg lg:shadow-[#351c42]/5"
        >
            <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-widest text-[#965995]">
                <?php if(!$canSeeMembership): ?>
                    Account
                <?php elseif(!$hasActiveSubscription): ?>
                    Membership
                <?php else: ?>
                    Menu
                <?php endif; ?>
            </p>
            <nav class="flex flex-col gap-1" aria-label="Member">
                <?php if(!$canSeeMembership): ?>
                    <a href="<?php echo e(route('member.profile.edit')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.profile.edit') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.profile.edit') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> My profile</a>
                <?php elseif(!$hasActiveSubscription): ?>
                    <a href="<?php echo e(route('member.subscription.index', ['type' => 'New'])); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.subscription.*') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.subscription.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> Subscription plans</a>
                <?php else: ?>
                    <a href="<?php echo e(route('member.dashboard')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.dashboard') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.dashboard') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> Dashboard</a>
                    <a href="<?php echo e(route('member.ebooks.index')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.ebooks.*') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.ebooks.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> E-Books</a>
                    <a href="<?php echo e(route('member.subscription.index')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.subscription.*') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.subscription.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> Membership</a>
                    <a href="<?php echo e(route('member.events.index')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.events.index') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.events.index') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> Events</a>
                    <a href="<?php echo e(route('member.nominations.index')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.nominations.index') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.nominations.index') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> Nominations</a>
                    <a href="<?php echo e(route('home')); ?>#jobs" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Search jobs</a>
                    <a href="<?php echo e(route('member.profile.edit')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.profile.*') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.profile.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> Profile</a>
                    <a href="<?php echo e(route('member.password.edit')); ?>" class="md-sidebar-link <?php echo e(request()->routeIs('member.password.*') ? 'is-active' : ''); ?>" data-md-nav><span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('member.password.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25'); ?>"></span> Change password</a>
                <?php endif; ?>
            </nav>
            <?php echo $__env->make('member.partials.sidebar-logout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </aside>
        <div id="md-sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden" aria-hidden="true"></div>

        <main class="min-w-0 flex-1 space-y-10 lg:space-y-12" id="member-dashboard-main">
            <?php
                $showNomThanks = (bool) session('nomination_thanks_modal');
                $nomThanksType = (string) session('nomination_thanks_type', '');
                $nomThanksNomId = (int) session('nomination_thanks_nomination_id', 0);
                $nomThanksPos = (string) session('nomination_thanks_position', '');

                $showPollThanks = (bool) session('polling_thanks_modal');
                $pollThanksPollId = (int) session('polling_thanks_poll_id', 0);
                $pollThanksPosId = (int) session('polling_thanks_position_id', 0);
            ?>

            <?php if($showNomThanks): ?>
                <div id="nomination-thanks-modal" class="fixed inset-0 z-[120] flex items-center justify-center bg-[#351c42]/55 p-4 backdrop-blur-[2px]" role="dialog" aria-modal="true">
                    <div class="w-full max-w-md rounded-3xl border border-[#351c42]/10 bg-white p-7 shadow-2xl">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl <?php echo e($nomThanksType === 'not_interested' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-700'); ?>">
                            <?php if($nomThanksType === 'not_interested'): ?>
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <?php else: ?>
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <?php endif; ?>
                        </div>
                        <h3 class="mt-5 text-center text-xl font-extrabold text-[#351c42]">
                            <?php echo e($nomThanksType === 'not_interested' ? 'Response saved' : 'Thanks for your interest'); ?>

                        </h3>
                        <p class="mt-3 text-center text-sm leading-relaxed text-[#351c42]/65">
                            <?php if($nomThanksType === 'not_interested'): ?>
                                No problem — we won’t count you for this role.
                            <?php else: ?>
                                Great — we recorded your interest for this role.
                            <?php endif; ?>
                            <?php if($nomThanksPos !== ''): ?>
                                <span class="block mt-1 font-semibold text-[#351c42]"><?php echo e($nomThanksPos); ?></span>
                            <?php endif; ?>
                        </p>
                        <button
                            type="button"
                            class="mt-7 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:bg-[#4a2660]"
                            data-thanks-close="nomination"
                            data-nomination-id="<?php echo e($nomThanksNomId); ?>"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($showPollThanks): ?>
                <div id="polling-thanks-modal" class="fixed inset-0 z-[120] flex items-center justify-center bg-[#351c42]/55 p-4 backdrop-blur-[2px]" role="dialog" aria-modal="true">
                    <div class="w-full max-w-md rounded-3xl border border-[#351c42]/10 bg-white p-7 shadow-2xl">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="mt-5 text-center text-xl font-extrabold text-[#351c42]">Thank you for voting</h3>
                        <p class="mt-3 text-center text-sm leading-relaxed text-[#351c42]/65">
                            Your vote has been recorded. You can return anytime to vote on other positions while polling is open.
                        </p>
                        <button
                            type="button"
                            class="mt-7 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:bg-[#4a2660]"
                            data-thanks-close="polling"
                            data-polling-id="<?php echo e($pollThanksPollId); ?>"
                            data-position-id="<?php echo e($pollThanksPosId); ?>"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($showFullMemberMenu && $showPollingWinnerDashboard): ?>
                <?php
                    $winnerPopup = $dashboardWinnerPolls->first();
                    $winnerPoll = $winnerPopup['polling'] ?? null;
                    $winnerRows = $winnerPopup['winners'] ?? collect();
                ?>
                <?php if($winnerPoll && $winnerRows->isNotEmpty()): ?>
                    <div id="polling-winner-modal" class="fixed inset-0 z-[121] flex items-center justify-center bg-[#351c42]/55 p-4 backdrop-blur-[2px]" role="dialog" aria-modal="true" aria-labelledby="polling-winner-title">
                        <div class="w-full max-w-lg rounded-3xl border border-[#351c42]/10 bg-white p-7 shadow-2xl">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <h3 id="polling-winner-title" class="mt-5 text-center text-xl font-extrabold text-[#351c42]">Polling winner announced</h3>
                            <p class="mt-1 text-center text-sm font-semibold text-[#351c42]/75"><?php echo e($winnerPoll->title); ?></p>
                            <div class="mt-4 space-y-2 rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-4">
                                <?php $__currentLoopData = $winnerRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between gap-3 border-b border-[#351c42]/10 pb-2 text-sm last:border-b-0 last:pb-0">
                                        <span class="font-semibold text-[#351c42]/70"><?php echo e($row['position']); ?></span>
                                        <span class="font-extrabold text-emerald-800"><?php echo e($row['winner_name']); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <button
                                type="button"
                                id="polling-winner-close"
                                data-winner-close
                                data-polling-id="<?php echo e($winnerPoll->id); ?>"
                                class="mt-7 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:bg-[#4a2660]"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if(session('member_gate_error')): ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-950">
                    <?php echo e(session('member_gate_error')); ?>

                </div>
            <?php endif; ?>

            <header id="member-dashboard-top" class="scroll-mt-28">
                <?php if($canSeeMembership && !$showFullMemberMenu): ?>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Membership</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Choose your subscription plan</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#351c42]/70">Your account is approved. Select a plan below and complete payment. After your membership is active, the full menu (dashboard, e-books, events, and more) will unlock.</p>
                <?php else: ?>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Account</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Member dashboard</h1>
                <?php endif; ?>
            </header>

            <?php if($showFullMemberMenu && $showPollingDashboard): ?>
                <section
                    id="dashboard-polling-slot"
                    data-queue-root-kind="polling"
                    data-popup-stack="polling"
                    class="pointer-events-none fixed right-4 z-[94] flex w-[min(92vw,24rem)] flex-col gap-3 sm:right-6 <?php if($showNominationDashboard): ?> bottom-[26rem] sm:bottom-[28rem] <?php else: ?> bottom-4 sm:bottom-6 <?php endif; ?>"
                >
                    <?php $__currentLoopData = $dashboardPolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pollRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $dashPoll = $pollRow['polling'];
                            $dashTotalPositions = $dashPoll->positions->count();
                            $dashVotedCount = collect($pollRow['pollingDashboardVotedIds'] ?? [])
                                ->intersect($dashPoll->positions->pluck('id'))
                                ->count();
                            $dashPollCompleted = $dashTotalPositions > 0 && $dashVotedCount >= $dashTotalPositions;
                        ?>
                        <div
                            id="dashboard-polling-card-<?php echo e($dashPoll->id); ?>"
                            class="dashboard-polling-card pointer-events-auto <?php if(!$loop->first): ?> hidden <?php endif; ?>"
                            data-queue-item
                            data-queue-kind="polling"
                            data-queue-completed="<?php echo e($dashPollCompleted ? '1' : '0'); ?>"
                        >
                            <?php echo $__env->make('member.partials.dashboard-polling-popup', [
                                'poll' => $dashPoll,
                                'pollingDashboardVotedIds' => $pollRow['pollingDashboardVotedIds'],
                                'pollingDashboardVotes' => $pollRow['pollingDashboardVotes'],
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if($dashboardPolls->count() > 1): ?>
                                <div class="mt-2 flex justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.14em] text-white/90 transition hover:bg-white/20"
                                        data-queue-next
                                        data-queue-kind="polling"
                                    >
                                        View more
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </section>
            <?php endif; ?>

            <?php if($showFullMemberMenu && $showNominationDashboard): ?>
                <section id="dashboard-nomination-stack" data-queue-root-kind="nomination" data-popup-stack="nomination" class="pointer-events-none fixed bottom-4 right-4 z-[95] flex w-[min(92vw,24rem)] flex-col gap-3 sm:bottom-6 sm:right-6" aria-label="Member announcements">
                    <?php $__currentLoopData = $dashboardNominations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nomRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $nominationPrompt = $nomRow['nomination'];
                            $interestedNominationPositionIds = collect($nomRow['interestedPositionIds'] ?? []);
                            $dismissedNominationPositionIds = collect($nomRow['dismissedPositionIds'] ?? []);
                            $nominationTotalPositions = $nominationPrompt->positions->count();
                            $nominationHandledCount = $nominationPrompt->positions
                                ->filter(fn ($position) => $interestedNominationPositionIds->contains($position->id) || $dismissedNominationPositionIds->contains($position->id))
                                ->count();
                            $nominationEndDate = ($nominationPrompt->polling_date_to ?? $nominationPrompt->polling_date)?->format('Y-m-d');
                            $nominationEndDateTimeIso = $nominationEndDate && $nominationPrompt->polling_to
                                ? \Illuminate\Support\Carbon::parse($nominationEndDate.' '.$nominationPrompt->polling_to)->toIso8601String()
                                : null;
                        ?>
                        <div
                            id="dashboard-nomination-card-<?php echo e($nominationPrompt->id); ?>"
                            class="dashboard-nomination-card pointer-events-auto <?php if(!$loop->first): ?> hidden <?php endif; ?>"
                            data-queue-item
                            data-queue-kind="nomination"
                            data-queue-completed="<?php echo e($nominationTotalPositions > 0 && $nominationHandledCount >= $nominationTotalPositions ? '1' : '0'); ?>"
                        >
                            <article class="md-announce-card md-popup-compact p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0 pr-2">
                                        <h3 class="md-popup-title mt-1 break-all text-2xl font-extrabold leading-[1.15] tracking-tight text-white">Nomination Alert</h3>
                                        <p class="mt-1 text-sm font-semibold text-white/85 break-all"><?php echo e($nominationPrompt->title); ?></p>
                                        <p class="md-popup-meta mt-2 text-[12px] font-semibold text-white/70">
                                            <?php echo e($nominationPrompt->polling_date?->format('d M Y') ?? '—'); ?>

                                            <?php if(($nominationPrompt->polling_date_to ?? $nominationPrompt->polling_date)?->toDateString() !== $nominationPrompt->polling_date?->toDateString()): ?>
                                                – <?php echo e(($nominationPrompt->polling_date_to ?? $nominationPrompt->polling_date)?->format('d M Y') ?? '—'); ?>

                                            <?php endif; ?>
                                        </p>
                                        <p class="mt-2">
                                            <span
                                                class="inline-flex items-center rounded-full border border-sky-300/60 bg-sky-500/20 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-sky-100"
                                                data-dashboard-countdown
                                                data-countdown-prefix="Ends in"
                                                data-countdown-end="<?php echo e($nominationEndDateTimeIso); ?>"
                                            >
                                                Ends in --
                                            </span>
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        class="rounded-full p-1.5 text-white/70 transition hover:bg-white/10 hover:text-white"
                                        aria-label="Dismiss nomination prompt"
                                        data-dashboard-dismiss-nomination
                                        data-nomination-id="<?php echo e($nominationPrompt->id); ?>"
                                    >✕</button>
                                </div>
                                <div class="mt-3 border-t border-white/10 pt-3">
                                    <p class="mb-2 text-[10px] font-black uppercase tracking-[0.16em] text-[#fddc6a]/90">Open positions</p>
                                    <div class="max-h-60 space-y-2 overflow-y-auto pr-0.5" data-nom-positions-wrap>
                                        <?php
                                            $firstPendingPositionId = $nominationPrompt->positions
                                                ->first(function ($position) use ($interestedNominationPositionIds, $dismissedNominationPositionIds) {
                                                    return ! $interestedNominationPositionIds->contains($position->id)
                                                        && ! $dismissedNominationPositionIds->contains($position->id);
                                                })?->id;
                                        ?>
                                        <?php $__empty_1 = true; $__currentLoopData = $nominationPrompt->positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div
                                                class="md-nom-announce-row <?php if($firstPendingPositionId ? ((int) $position->id !== (int) $firstPendingPositionId) : !$loop->first): ?> hidden <?php endif; ?>"
                                                data-nom-position-row
                                            >
                                                <p class="w-full break-all text-sm font-bold leading-snug text-[#351c42]"><?php echo e($position->position); ?></p>
                                                <div class="flex w-full flex-wrap items-center justify-between gap-2">
                                                    <?php if($interestedNominationPositionIds->contains($position->id)): ?>
                                                        <span class="inline-flex items-center rounded-full border border-emerald-300 bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-700">
                                                            Interested sent
                                                        </span>
                                                    <?php elseif($dismissedNominationPositionIds->contains($position->id)): ?>
                                                        <span class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-700">
                                                            Not interested
                                                        </span>
                                                    <?php else: ?>
                                                        <div class="inline-flex flex-wrap items-center gap-1.5">
                                                            <form method="POST" action="<?php echo e(route('member.nominations.interest', [$nominationPrompt, $position])); ?>" class="inline">
                                                                <?php echo csrf_field(); ?>
                                                                <button type="submit" class="md-nom-interest-pill">Interested</button>
                                                            </form>
                                                            <form method="POST" action="<?php echo e(route('member.nominations.not-interested', [$nominationPrompt, $position])); ?>" class="inline">
                                                                <?php echo csrf_field(); ?>
                                                                <button
                                                                    type="submit"
                                                                    class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-700 transition hover:bg-slate-200"
                                                                >
                                                                    Not interested
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                    <button
                                                        type="button"
                                                        data-popup-open="nomination-detail-modal-<?php echo e($nominationPrompt->id); ?>"
                                                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#351c42]/14 bg-white text-[#351c42] shadow-sm transition hover:border-[#965995]/40 hover:bg-[#965995]/8"
                                                        aria-label="View nomination details"
                                                    >
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white/75">No positions listed yet.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                                    $morePositions = max(0, (int) $nominationPrompt->positions->count() - 1);
                                    $moreNominations = max(0, (int) $dashboardNominations->count() - 1);
                                ?>
                                <?php if($morePositions > 0 || $moreNominations > 0): ?>
                                    <div class="mt-3 flex flex-wrap items-center justify-end gap-2">
                                        <?php if($morePositions > 0): ?>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.14em] text-white/90 transition hover:bg-white/20"
                                                data-nom-toggle
                                            >
                                                View positions (<?php echo e($morePositions); ?>)
                                            </button>
                                        <?php endif; ?>
                                        <?php if($moreNominations > 0): ?>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.14em] text-white/90 transition hover:bg-white/20"
                                                data-queue-next
                                                data-queue-kind="nomination"
                                            >
                                                View more nominations (<?php echo e($moreNominations); ?>)
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </section>
            <?php endif; ?>

            <?php if($showFullMemberMenu): ?>
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Account summary">
                
                <article class="relative overflow-hidden rounded-2xl border border-[#351c42]/10 bg-white shadow-md transition hover:border-[#965995]/35 hover:shadow-lg">
                    <div class="h-1 bg-gradient-to-r from-[#965995] to-[#351c42]"></div>
                    <div class="p-5 pt-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/45">Profile</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#965995]/12 text-[#965995]" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold tracking-tight text-[#351c42]"><?php echo e($member?->profile_completed ? 'Completed' : 'Incomplete'); ?></p>
                        <p class="mt-2 inline-flex items-center gap-1 rounded-full bg-[#f6f3e9] px-2.5 py-1 text-[11px] font-semibold text-[#351c42]/75">
                            <?php if($member?->profile_completed): ?>
                                <svg class="h-3.5 w-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Details on file
                            <?php else: ?>
                                <svg class="h-3.5 w-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Finish your profile
                            <?php endif; ?>
                        </p>
                    </div>
                </article>
                
                <article class="relative overflow-hidden rounded-2xl border border-[#351c42]/10 bg-white shadow-md transition hover:border-emerald-300/60 hover:shadow-lg">
                    <div class="h-1 <?php echo e($member?->is_approved ? 'bg-gradient-to-r from-emerald-400 to-teal-600' : 'bg-gradient-to-r from-amber-300 to-amber-600'); ?>"></div>
                    <div class="p-5 pt-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/45">Approval</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl <?php echo e($member?->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800'); ?>" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold tracking-tight <?php echo e($member?->is_approved ? 'text-emerald-800' : 'text-amber-800'); ?>"><?php echo e($member?->is_approved ? 'Approved' : 'Pending'); ?></p>
                        <p class="mt-2 text-[11px] leading-snug text-[#351c42]/55"><?php echo e($member?->is_approved ? 'Your account is cleared by the office.' : 'We will notify you when review is complete.'); ?></p>
                    </div>
                </article>
                
                <article class="relative overflow-hidden rounded-2xl border border-[#351c42]/10 bg-gradient-to-br from-white to-[#faf8fc] shadow-md transition hover:border-[#351c42]/20 hover:shadow-lg">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-[#965995]/10 blur-2xl"></div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/45">Member name</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#351c42] text-[#fddc6a]" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 break-words text-xl font-extrabold leading-tight text-[#351c42] sm:text-2xl" title="<?php echo e($member?->name ?? '-'); ?>"><?php echo e($member?->name ?? '—'); ?></p>
                        <p class="mt-2 text-[11px] text-[#351c42]/50">As registered with GNAT</p>
                    </div>
                </article>
                
                <article class="relative overflow-hidden rounded-2xl border border-[#fddc6a]/40 bg-[#f7f6f0] shadow-md transition hover:shadow-lg sm:col-span-2 lg:col-span-1">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_100%_0%,rgba(150,89,149,0.08),transparent)]"></div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#351c42]/50">Donations</p>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#351c42] text-[#fddc6a]" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <p class="mt-2 font-mono text-3xl font-extrabold tabular-nums tracking-tight text-[#351c42]">₹<?php echo e(number_format((float) ($memberDonationsTotal ?? 0), 0)); ?></p>
                        <p class="mt-2 text-[11px] font-medium text-[#351c42]/55">Your total successful gifts to GNAT</p>
                    </div>
                </article>
            </section>
            <?php endif; ?>

            <?php if($showFullMemberMenu): ?>
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
                                    <?php if($member->passport_photo_path): ?>
                                        <img src="<?php echo e(asset('storage/' . $member->passport_photo_path)); ?>" alt="" class="h-full w-full object-cover" width="72" height="72" />
                                    <?php else: ?>
                                        <div class="flex h-full w-full items-center justify-center text-[#fddc6a]/75">
                                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-[13px] font-extrabold leading-snug text-white"><?php echo e($member->name); ?></p>
                                    <?php if($member->designation?->name): ?>
                                        <p class="mt-0.5 truncate text-[11px] text-white/75"><?php echo e($member->designation->name); ?></p>
                                    <?php endif; ?>
                                    <p class="mt-1 font-mono text-[11px] font-bold tracking-wide text-[#fddc6a]">GNAT-<?php echo e(str_pad((string) $member->id, 6, '0', STR_PAD_LEFT)); ?></p>
                                    <p class="mt-0.5 text-[10px] text-white/75">Valid till: <?php echo e($sub?->end_date?->format('d M Y') ?? '—'); ?></p>
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
                                    <span class="inline-flex w-fit rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide <?php echo e($sub ? 'bg-emerald-100 text-emerald-800' : 'bg-[#965995]/15 text-[#351c42]/80'); ?>">
                                        <?php echo e($sub ? 'Active' : 'No plan'); ?>

                                    </span>
                                    <div class="md-kebab-menu" data-sub-menu-wrap>
                                        <button type="button" class="rounded-lg p-1.5 text-[#351c42]/60 hover:bg-[#351c42]/5" aria-label="Subscription actions" data-sub-menu-btn>⋮</button>
                                        <div class="md-kebab-dropdown" data-sub-menu>
                                            <?php if($latestReceiptTransaction): ?>
                                                <a href="<?php echo e(route('member.subscription.invoice', $latestReceiptTransaction->id)); ?>" target="_blank" rel="noopener" class="block rounded-lg px-3 py-2 text-sm font-semibold text-[#351c42] hover:bg-[#351c42]/5">
                                                    Download receipt
                                                </a>
                                            <?php else: ?>
                                                <span class="block rounded-lg px-3 py-2 text-sm text-[#351c42]/45">No receipt yet</span>
                                            <?php endif; ?>
                                            <?php if($showFullMemberMenu && $canSeeMembership): ?>
                                                <a href="<?php echo e(route('member.subscription.index', ['type' => 'Renewal'])); ?>" class="mt-0.5 block rounded-lg px-3 py-2 text-sm font-semibold text-[#351c42] hover:bg-[#351c42]/5">
                                                    Pay &amp; renew
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <dl class="mt-4 grid gap-3 text-sm text-[#351c42] sm:grid-cols-2">
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Plan purchased</dt>
                                    <dd class="mt-1 font-semibold">
                                        <?php if($sub): ?>
                                            <?php echo e($sub->subscription_type); ?> · <?php echo e(ucfirst(str_replace('_', ' ', (string) $sub->payment_type))); ?>

                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Valid till</dt>
                                    <dd class="mt-1 font-semibold tabular-nums"><?php echo e($sub?->end_date?->format('d M Y') ?? '—'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Purchased on</dt>
                                    <dd class="mt-1 font-semibold tabular-nums"><?php echo e($sub?->start_date?->format('d M Y') ?? '—'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Member since</dt>
                                    <dd class="mt-1 font-semibold"><?php echo e($member->created_at?->format('M Y') ?? '—'); ?></dd>
                                </div>
                            </dl>
                        </article>
                    </div>
                </section>
            <?php endif; ?>

            <?php if($canSeeMembership): ?>
            <section aria-labelledby="plans-heading">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 id="plans-heading" class="text-xl font-bold text-[#351c42] sm:text-2xl">Subscription plans</h2>
                        <p class="mt-1 text-sm text-[#351c42]/60">
                            <?php if($hasActiveSubscription): ?>
                                Your membership is active. When it is time to renew, use renewal plans only.
                            <?php else: ?>
                                Your account is approved. Choose a new subscription plan to activate membership.
                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="<?php echo e(route('member.subscription.index')); ?>" class="text-sm font-bold text-[#965995] hover:text-[#351c42]">Open membership page →</a>
                </div>

                <?php if(!$hasActiveSubscription): ?>
                <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-[#965995]">New members</h3>
                <div class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article class="md-plan-card p-6 sm:col-span-2 lg:col-span-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-[#965995]">New member · Full year</p>
                        <p class="mt-2 text-2xl font-extrabold text-[#351c42]">Subscription plan</p>
                        <p class="mt-1 text-sm text-[#351c42]/65">Choose and purchase your membership plan.</p>
                        <ul class="mt-4 space-y-1.5 text-sm text-[#351c42]/75">
                            <li>Includes registration where applicable</li>
                            <li>Pay securely via Razorpay</li>
                        </ul>
                        <a href="<?php echo e(route('member.subscription.index', ['type' => 'New'])); ?>" class="md-btn-pay mt-6 inline-flex w-full justify-center sm:w-auto">Choose plan</a>
                    </article>
                </div>
                <?php endif; ?>

                <?php if($hasActiveSubscription): ?>
                <h3 class="mb-4 text-sm font-bold uppercase tracking-widest text-[#965995]">Renewal options</h3>
                <p class="mb-4 text-sm text-[#351c42]/55">Your current subscription is active. Use renewal plans for your next membership period.</p>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php $__empty_1 = true; $__currentLoopData = $renewalPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $cycleLabel = match($plan->payment_type) {
                                'monthly' => 'Monthly',
                                'bi_monthly' => 'Bi - Monthly',
                                'quarterly' => 'Quarterly',
                                'half_yearly' => 'Half Yearly',
                                'yearly' => 'Yearly',
                                default => ucfirst((string) $plan->payment_type),
                            };
                        ?>
                        <article class="md-plan-card p-6">
                            <p class="text-xs font-bold uppercase tracking-wide text-[#965995]">Renewal · <?php echo e($cycleLabel); ?></p>
                            <p class="mt-2 text-2xl font-extrabold text-[#351c42]">₹<?php echo e(number_format((float) $plan->membership_fee, 0)); ?></p>
                            <p class="mt-1 text-sm text-[#351c42]/65"><?php echo e((int) ($plan->grace_period ?? 0)); ?> days grace period</p>
                            <a href="<?php echo e(route('member.subscription.index', ['type' => 'Renewal'])); ?>" class="md-btn-pay mt-6 inline-flex w-full justify-center sm:w-auto">Choose renewal</a>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border border-dashed border-[#351c42]/15 bg-white/70 p-8 text-center">
                            <p class="text-sm font-bold text-[#351c42]/70">No renewal plans available right now. Please contact admin.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
            <?php if($showFullMemberMenu): ?>
            <section class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-5 shadow-md sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Events</p>
                        <h2 class="mt-1 text-lg font-bold text-[#351c42]">Interested in GNAT events?</h2>
                        <p class="mt-1 text-sm text-[#351c42]/60">Register interest, see attendance, and download certificates on the member events page.</p>
                    </div>
                    <a href="<?php echo e(route('member.events.index')); ?>" class="md-btn-pay shrink-0 text-center">Open events page</a>
                </div>
            </section>
            <?php endif; ?>
        </main>
    </div>

    <?php
        $donate = config('homepage.donate', ['goal' => 500, 'default_amount' => 100]);
    ?>
    <?php echo $__env->make('home.partials.donate-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.donate-payment-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('home.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($showFullMemberMenu && $showNominationDashboard): ?>
        <?php $__currentLoopData = $dashboardNominations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nomRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $nominationPrompt = $nomRow['nomination'];
                $nomDetailCover = $nominationPrompt->cover_image_path ? asset('storage/' . ltrim($nominationPrompt->cover_image_path, '/')) : null;
                $nomDetailBanner = $nominationPrompt->banner_image_path ? asset('storage/' . ltrim($nominationPrompt->banner_image_path, '/')) : null;
                $nomStatusLabel = match ($nominationPrompt->status) {
                    'draft' => 'Draft',
                    'active' => 'Active',
                    'closed' => 'Closed',
                    'cancelled' => 'Cancelled',
                    default => ucfirst((string) $nominationPrompt->status),
                };
            ?>
            <div id="nomination-detail-modal-<?php echo e($nominationPrompt->id); ?>" class="md-modal-overlay" data-popup-modal role="dialog" aria-modal="true" aria-labelledby="nom-detail-title-<?php echo e($nominationPrompt->id); ?>">
                <div class="max-h-[min(92vh,44rem)] w-full max-w-2xl overflow-hidden rounded-2xl border border-[#351c42]/12 bg-white shadow-2xl ring-1 ring-black/5">
                    <div class="relative overflow-hidden bg-gradient-to-br from-[#4c2b5d] via-[#351c42] to-[#2a1536] px-6 pb-10 pt-6 text-white sm:px-8">
                        <?php if($nomDetailBanner): ?>
                            <div class="pointer-events-none absolute inset-0 opacity-25">
                                <img src="<?php echo e($nomDetailBanner); ?>" alt="" class="h-full w-full object-cover" loading="lazy">
                            </div>
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#351c42] via-[#351c42]/80 to-transparent"></div>
                        <?php endif; ?>
                        <div class="relative flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-[#fddc6a]/90">Nomination</p>
                                <h2 id="nom-detail-title-<?php echo e($nominationPrompt->id); ?>" class="mt-2 text-xl font-extrabold leading-tight tracking-tight text-white sm:text-2xl"><?php echo e($nominationPrompt->title); ?></h2>
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold text-[#fddc6a] backdrop-blur-sm"><?php echo e($nomStatusLabel); ?></span>
                                    <span class="inline-flex rounded-full border border-white/20 bg-black/20 px-3 py-1 text-[11px] font-semibold text-white/90 backdrop-blur-sm">
                                        <?php echo e($nominationPrompt->is_active ? 'Visible' : 'Hidden'); ?>

                                    </span>
                                </div>
                            </div>
                            <button type="button" data-popup-close class="shrink-0 rounded-full border border-white/20 bg-white/10 p-2 text-white/90 transition hover:bg-white/20" aria-label="Close">✕</button>
                        </div>
                    </div>
                    <div class="max-h-[calc(min(92vh,44rem)-11rem)] overflow-y-auto px-6 py-6 sm:px-8">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-4">
                                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#965995]">Interest window</p>
                                <dl class="mt-3 space-y-3 text-sm">
                                    <div class="flex gap-3">
                                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#965995]/15 text-[#965995]" aria-hidden="true">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                        <div>
                                            <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Opens</dt>
                                            <dd class="mt-0.5 font-semibold text-[#351c42]"><?php echo e($nominationPrompt->polling_date?->format('l, d M Y') ?? '—'); ?></dd>
                                            <dd class="text-xs text-[#351c42]/65"><?php echo e($nominationPrompt->polling_from ? \Carbon\Carbon::parse($nominationPrompt->polling_from)->format('h:i A') : ''); ?></dd>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#965995]/15 text-[#965995]" aria-hidden="true">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </span>
                                        <div>
                                            <dt class="text-[11px] font-bold uppercase tracking-wide text-[#351c42]/45">Closes</dt>
                                            <dd class="mt-0.5 font-semibold text-[#351c42]"><?php echo e(($nominationPrompt->polling_date_to ?? $nominationPrompt->polling_date)?->format('l, d M Y') ?? '—'); ?></dd>
                                            <dd class="text-xs text-[#351c42]/65"><?php echo e($nominationPrompt->polling_to ? \Carbon\Carbon::parse($nominationPrompt->polling_to)->format('h:i A') : ''); ?></dd>
                                        </div>
                                    </div>
                                </dl>
                            </div>
                            <div class="rounded-2xl border border-[#351c42]/10 bg-white p-4">
                                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#965995]">Open roles</p>
                                <?php if($nominationPrompt->positions->isEmpty()): ?>
                                    <p class="mt-3 text-sm text-[#351c42]/55">No positions listed yet.</p>
                                <?php else: ?>
                                    <ul class="mt-3 flex flex-wrap gap-2">
                                        <?php $__currentLoopData = $nominationPrompt->positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="inline-flex max-w-full items-center rounded-full border border-[#351c42]/12 bg-[#faf9fc] px-3 py-1.5 text-xs font-semibold text-[#351c42]"><?php echo e($p->position); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-6">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#965995]">Terms &amp; conditions</p>
                            <div class="mt-2 max-h-52 overflow-y-auto whitespace-pre-line text-left rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] p-4 text-sm leading-relaxed text-[#351c42]/90">
                                <?php echo e($nominationPrompt->terms ? $nominationPrompt->terms : 'No additional terms were provided for this nomination.'); ?>

                            </div>
                        </div>
                        <div class="mt-6">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#965995]">Media</p>
                            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                <figure class="overflow-hidden rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] shadow-sm">
                                    <figcaption class="border-b border-[#351c42]/08 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-[#351c42]/50">Cover</figcaption>
                                    <?php if($nomDetailCover): ?>
                                        <img src="<?php echo e($nomDetailCover); ?>" alt="Nomination cover" class="aspect-[4/3] w-full object-cover" loading="lazy">
                                    <?php else: ?>
                                        <div class="flex aspect-[4/3] items-center justify-center px-3 text-center text-xs text-[#351c42]/45">No cover image</div>
                                    <?php endif; ?>
                                </figure>
                                <figure class="overflow-hidden rounded-2xl border border-[#351c42]/10 bg-[#faf9fc] shadow-sm">
                                    <figcaption class="border-b border-[#351c42]/08 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-[#351c42]/50">Banner</figcaption>
                                    <?php if($nomDetailBanner): ?>
                                        <img src="<?php echo e($nomDetailBanner); ?>" alt="Nomination banner" class="aspect-[4/3] w-full object-cover" loading="lazy">
                                    <?php else: ?>
                                        <div class="flex aspect-[4/3] items-center justify-center px-3 text-center text-xs text-[#351c42]/45">No banner image</div>
                                    <?php endif; ?>
                                </figure>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-[#351c42]/08 bg-[#faf9fc]/80 px-6 py-4 sm:px-8">
                        <button type="button" data-popup-close class="rounded-full bg-[#351c42] px-6 py-2.5 text-sm font-bold text-[#fddc6a] shadow-md transition hover:brightness-110">Close</button>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <script>
        (() => {
            const sidebar = document.getElementById("md-sidebar");
            const toggle = document.querySelector("[data-md-sidebar-toggle]");
            const backdrop = document.getElementById("md-sidebar-backdrop");
            const countdownNodes = Array.from(document.querySelectorAll("[data-dashboard-countdown]"));

            function formatCountdown(ms) {
                const totalSeconds = Math.floor(ms / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                if (days > 0) return `${days}d ${hours}h ${minutes}m`;
                if (hours > 0) return `${hours}h ${minutes}m ${seconds}s`;
                return `${minutes}m ${seconds}s`;
            }

            function refreshCountdowns() {
                const now = Date.now();
                countdownNodes.forEach((node) => {
                    const end = node.getAttribute("data-countdown-end");
                    const prefix = node.getAttribute("data-countdown-prefix") || "Ends in";
                    if (!end) {
                        node.textContent = `${prefix} --`;
                        return;
                    }
                    const endMs = new Date(end).getTime();
                    if (Number.isNaN(endMs)) {
                        node.textContent = `${prefix} --`;
                        return;
                    }
                    const diff = endMs - now;
                    if (diff <= 0) {
                        node.textContent = "Closed";
                        return;
                    }
                    node.textContent = `${prefix} ${formatCountdown(diff)}`;
                });
            }

            async function persistDismiss(type, entityId) {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
                try {
                    await fetch(<?php echo json_encode(route('member.dashboard.announcements.dismiss'), 15, 512) ?>, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrf,
                            "Accept": "application/json",
                        },
                        body: JSON.stringify({
                            type,
                            entity_id: Number(entityId),
                        }),
                    });
                } catch (_) {
                    // Ignore network errors; UI still closes immediately.
                }
            }

            if (countdownNodes.length > 0) {
                refreshCountdowns();
                window.setInterval(refreshCountdowns, 1000);
            }

            function queueItems(kind) {
                return Array.from(document.querySelectorAll(`[data-queue-item][data-queue-kind="${kind}"]`));
            }

            function availableQueueItems(kind) {
                return queueItems(kind).filter((el) => el.getAttribute("data-queue-dismissed") !== "1");
            }

            function queueRoot(kind) {
                return document.querySelector(`[data-queue-root-kind="${kind}"]`);
            }

            function queueExpanded(kind) {
                return queueRoot(kind)?.getAttribute("data-queue-expanded") === "1";
            }

            function updateQueueButton(kind) {
                const items = availableQueueItems(kind);
                items.forEach((el) => {
                    const btn = el.querySelector(`[data-queue-next][data-queue-kind="${kind}"]`);
                    if (!btn) return;
                    if (items.length <= 1 || queueExpanded(kind)) {
                        btn.classList.add("hidden");
                        return;
                    }
                    btn.classList.remove("hidden");
                    btn.textContent = `View more (${items.length - 1})`;
                });
            }

            function showQueueItem(kind, targetEl) {
                const items = availableQueueItems(kind);
                items.forEach((el) => {
                    el.classList.toggle("hidden", el !== targetEl);
                });
                updateQueueButton(kind);
            }

            function expandQueue(kind) {
                const items = availableQueueItems(kind);
                if (items.length === 0) return;
                queueRoot(kind)?.setAttribute("data-queue-expanded", "1");
                items.forEach((el) => el.classList.remove("hidden"));
                updateQueueButton(kind);
            }

            function initQueue(kind) {
                const items = availableQueueItems(kind);
                if (items.length === 0) return;
                queueRoot(kind)?.setAttribute("data-queue-expanded", "0");
                const firstIncomplete = items.find((el) => el.getAttribute("data-queue-completed") !== "1");
                showQueueItem(kind, firstIncomplete || items[0]);
            }

            initQueue("nomination");
            initQueue("polling");

            function visibleCards(selector) {
                return Array.from(document.querySelectorAll(selector)).filter((el) => !el.classList.contains("hidden"));
            }

            function updatePopupStack() {
                const nominationRoot = document.querySelector('[data-popup-stack="nomination"]');
                const pollingRoot = document.querySelector('[data-popup-stack="polling"]');
                if (!nominationRoot || !pollingRoot) return;

                const anyNomVisible = visibleCards(".dashboard-nomination-card").length > 0;
                const anyPolVisible = visibleCards(".dashboard-polling-card").length > 0;

                // Nomination always wants to sit at bottom-right when visible.
                if (anyNomVisible) {
                    nominationRoot.style.bottom = "";
                }

                // Polling sits above nomination when nomination visible; otherwise bottom-right.
                if (!anyPolVisible) return;
                if (!anyNomVisible) {
                    pollingRoot.style.bottom = "";
                    pollingRoot.classList.remove("bottom-[26rem]", "sm:bottom-[28rem]");
                    pollingRoot.classList.add("bottom-4", "sm:bottom-6");
                    return;
                }

                // If nominations visible, compute and stack above it.
                const nomHeight = nominationRoot.getBoundingClientRect().height;
                const gap = 12;
                const baseBottom = window.matchMedia("(min-width: 640px)").matches ? 24 : 16; // sm:bottom-6 vs bottom-4
                pollingRoot.classList.remove("bottom-4", "sm:bottom-6");
                pollingRoot.style.bottom = `${baseBottom + nomHeight + gap}px`;
            }

            updatePopupStack();
            window.addEventListener("resize", updatePopupStack);

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

            document.addEventListener(
                "click",
                (e) => {
                    const thanksBtn = e.target.closest("[data-thanks-close]");
                    if (thanksBtn) {
                        e.preventDefault();
                        const type = thanksBtn.getAttribute("data-thanks-close");
                        if (type === "nomination") {
                            document.getElementById("nomination-thanks-modal")?.remove();
                            if (queueExpanded("nomination")) expandQueue("nomination");
                            else initQueue("nomination");
                            updatePopupStack();
                            return;
                        }
                        if (type === "polling") {
                            document.getElementById("polling-thanks-modal")?.remove();
                            if (queueExpanded("polling")) expandQueue("polling");
                            else initQueue("polling");
                            updatePopupStack();
                            return;
                        }
                    }
                    const closeBtn = e.target.closest("[data-popup-close]");
                    if (closeBtn) {
                        e.preventDefault();
                        e.stopPropagation();
                        closeBtn.closest("[data-popup-modal]")?.classList.remove("is-open");
                        return;
                    }
                    const openBtn = e.target.closest("[data-popup-open]");
                    if (openBtn) {
                        const id = openBtn.getAttribute("data-popup-open");
                        const modal = id ? document.getElementById(id) : null;
                        modal?.classList.add("is-open");
                        return;
                    }
                    const nextBtn = e.target.closest("[data-queue-next]");
                    if (nextBtn) {
                        const kind = nextBtn.getAttribute("data-queue-kind");
                        if (!kind) return;
                        e.preventDefault();
                        expandQueue(kind);
                        return;
                    }
                    const nomToggle = e.target.closest("[data-nom-toggle]");
                    if (nomToggle) {
                        e.preventDefault();
                        const card = nomToggle.closest(".dashboard-nomination-card");
                        if (!card) return;
                        const rows = Array.from(card.querySelectorAll("[data-nom-position-row]"));
                        if (rows.length <= 1) return;
                        const expanded = rows.some((r, idx) => idx > 0 && !r.classList.contains("hidden"));
                        rows.forEach((r, idx) => {
                            if (idx === 0) return;
                            r.classList.toggle("hidden", expanded);
                        });
                        nomToggle.textContent = expanded ? `View positions (${rows.length - 1})` : "View less";
                        updatePopupStack();
                        return;
                    }
                },
                true
            );
            document.querySelectorAll("[data-popup-modal]").forEach((modal) => {
                modal.addEventListener("click", (e) => {
                    if (e.target === modal) modal.classList.remove("is-open");
                });
            });

            if (window.location.hash === "#member-dashboard-top") {
                document.getElementById("member-dashboard-top")?.scrollIntoView({ behavior: "smooth" });
            }

            document.addEventListener(
                "click",
                (e) => {
                    const nomBtn = e.target.closest("[data-dashboard-dismiss-nomination]");
                    if (nomBtn) {
                        e.preventDefault();
                        const id = nomBtn.getAttribute("data-nomination-id");
                        if (!id) return;
                        const card = document.getElementById(`dashboard-nomination-card-${id}`);
                        if (card) {
                            // Temporary hide only (current page). On next dashboard visit it appears again.
                            card.setAttribute("data-queue-dismissed", "1");
                            card.classList.add("hidden");
                        }
                        document.getElementById(`nomination-detail-modal-${id}`)?.classList.remove("is-open");
                        if (queueExpanded("nomination")) {
                            expandQueue("nomination");
                        } else {
                            initQueue("nomination");
                        }
                        updatePopupStack();
                        return;
                    }
                    const polBtn = e.target.closest("[data-dashboard-dismiss-polling]");
                    if (polBtn) {
                        e.preventDefault();
                        const pid = polBtn.getAttribute("data-polling-id");
                        if (!pid) return;
                        const card = document.getElementById(`dashboard-polling-card-${pid}`);
                        if (card) {
                            // Temporary hide only (current page). On next dashboard visit it appears again.
                            card.setAttribute("data-queue-dismissed", "1");
                            card.classList.add("hidden");
                        }
                        if (queueExpanded("polling")) {
                            expandQueue("polling");
                        } else {
                            initQueue("polling");
                        }
                        updatePopupStack();
                        return;
                    }
                    const winnerBtn = e.target.closest("[data-winner-close]");
                    if (winnerBtn) {
                        e.preventDefault();
                        const pid = winnerBtn.getAttribute("data-polling-id");
                        document.getElementById("polling-winner-modal")?.remove();
                        if (pid) {
                            persistDismiss("polling_winner", pid);
                        }
                    }
                },
                true
            );
        })();
    </script>
</body>
</html>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\dashboard.blade.php ENDPATH**/ ?>