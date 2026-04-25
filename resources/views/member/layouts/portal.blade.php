@php
    $member = Auth::user();
    $hasActiveSubscription = $member?->activeSubscription()->exists();
    $canSeeMembership = $member && $member->profile_completed && $member->is_approved;
    $showFullMemberMenu = $canSeeMembership && $hasActiveSubscription;
    $firstName = $member?->first_name ?? 'Member';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member — GNAT Association')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('home.partials.styles')
    @include('member.partials.portal-styles')
    @stack('styles')
</head>
<body class="md-page-bg text-[#351c42] antialiased" id="top">
    @include('member.partials.member-portal-gate-modals', ['member' => $member, 'firstName' => $firstName])

    @include('member.partials.public-site-header')
    <div class="sticky top-0 z-30 flex items-center justify-between border-b border-[#351c42]/10 bg-[#faf9fc] px-4 py-2 lg:hidden">
        <span class="text-[10px] font-bold uppercase tracking-wide text-[#351c42]/45">Member</span>
        <button type="button" data-md-sidebar-toggle aria-expanded="false" aria-controls="md-sidebar" class="text-xs font-bold text-[#965995] hover:text-[#351c42]">Menu</button>
    </div>

    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:flex-row lg:gap-8 lg:py-10">
        @include('member.partials.member-portal-sidebar', ['showFullMemberMenu' => $showFullMemberMenu, 'canSeeMembership' => $canSeeMembership])

        <main class="min-w-0 flex-1 space-y-10 lg:space-y-12" id="@yield('portal_main_id', 'member-portal-main')">
            @if(session('member_gate_error'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-950">
                    {{ session('member_gate_error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @php
        $donate = config('homepage.donate', ['goal' => 500, 'default_amount' => 100]);
    @endphp
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('shared.read-more-modal')
    @include('home.partials.scripts')

    @include('member.partials.member-portal-drawer-script')
    @include('member.partials.event-interest-error-modal')
    @include('member.partials.event-interest-success-modal')
    @stack('scripts')
</body>
</html>
