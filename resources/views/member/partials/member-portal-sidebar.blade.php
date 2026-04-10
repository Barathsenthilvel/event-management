{{-- Expects: $showFullMemberMenu, $canSeeMembership --}}
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
            <a href="{{ route('member.nominations.index') }}" class="md-sidebar-link {{ request()->routeIs('member.nominations.index') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.nominations.index') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> Nominations</a>
            <a href="{{ route('home') }}#jobs" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Search jobs</a>
            <a href="{{ route('member.pollings.index') }}" class="md-sidebar-link {{ request()->routeIs('member.pollings.index') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.pollings.index') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> Polling</a>
            <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Profile</a>
            <a href="{{ route('member.password.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Change password</a>
        @elseif($canSeeMembership)
            <p class="mb-2 rounded-xl bg-[#965995]/10 px-3 py-2 text-xs font-semibold leading-relaxed text-[#351c42]/85">Please purchase a membership plan to unlock the full member menu.</p>
            <a href="{{ route('member.subscription.index') }}" class="md-sidebar-link {{ request()->routeIs('member.subscription.*') ? 'is-active' : '' }}" data-md-nav><span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('member.subscription.*') ? 'bg-[#965995]' : 'bg-[#351c42]/25' }}"></span> Membership</a>
        @else
            <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link is-active" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Profile</a>
            <a href="{{ route('member.password.edit') }}" class="md-sidebar-link" data-md-nav><span class="h-1.5 w-1.5 rounded-full bg-[#351c42]/25"></span> Change password</a>
        @endif
    </nav>
    @include('member.partials.sidebar-logout')
</aside>
<div id="md-sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden" aria-hidden="true"></div>
