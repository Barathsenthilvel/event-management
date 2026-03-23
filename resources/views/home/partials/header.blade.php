<header class="sticky top-0 z-40">
    <div class="site-header-main">
        <div class="mx-auto max-w-7xl px-4 py-3 lg:py-3.5 flex items-center gap-3 lg:gap-6">
            <button
                type="button"
                class="shrink-0 p-2 rounded-lg hover:bg-[#351c42]/5 transition-colors -ml-2"
                data-hamburger
                aria-expanded="false"
                aria-controls="site-drawer"
                aria-label="Open menu"
            >
                <span class="hamburger-inner" aria-hidden="true">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </span>
            </button>

            <a href="{{ url('/') }}#home" class="flex items-center shrink-0 min-w-0 max-w-[min(52vw,200px)] sm:max-w-[220px]" aria-label="GNAT Donation home">
                <img
                    src="{{ asset($logo['src']) }}"
                    alt="{{ $logo['alt'] }}"
                    class="h-8 sm:h-11 w-auto max-h-12 max-w-full object-contain object-left"
                    width="200"
                    height="48"
                    loading="eager"
                />
            </a>

            <nav class="hidden lg:flex flex-1 justify-center items-center gap-6 xl:gap-8 text-sm font-semibold text-[#3d4d5c]" aria-label="Primary">
                @foreach ($nav as $link)
                    <a href="{{ url('/') }}{{ $link['href'] }}" class="hover:text-[#351c42] transition-colors">{{ $link['label'] }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 sm:gap-3 ml-auto shrink-0">
                <a
                    href="{{ route('member.login') }}"
                    class="member-login-btn"
                    aria-label="Member Login"
                    title="Member Login"
                >
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill="currentColor" d="M12 11.5a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z"/>
                        <path fill="currentColor" d="M6 20.25v-.75c0-2.9 2.35-5.25 5.25-5.25h1.5c2.9 0 5.25 2.35 5.25 5.25v.75H6Z" opacity="0.92"/>
                        <circle cx="17.75" cy="16.75" r="3.15" stroke="currentColor" stroke-width="1.25" fill="none"/>
                        <path stroke="currentColor" stroke-width="1.35" stroke-linecap="round" d="M17.75 15.15v3.2M16.15 16.75h3.2"/>
                    </svg>
                </a>
                <a href="{{ url('/') }}#donate" class="click-btn click-btn--nav btn-style506 max-[380px]:scale-90 max-[380px]:origin-right">
                    <span class="click-btn__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                            <path d="M8 8l3 4-3 4M13 8l3 4-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="click-btn__label">Donate Now</span>
                </a>
            </div>
        </div>
    </div>
</header>

<div class="site-drawer-overlay" data-drawer-overlay aria-hidden="true"></div>
<aside
    id="site-drawer"
    class="site-drawer"
    role="dialog"
    aria-modal="true"
    aria-label="GNAT Donation menu"
    aria-hidden="true"
>
    <div class="flex items-center justify-between gap-4 p-5 border-b border-white/10 shrink-0">
        <a href="{{ url('/') }}#home" class="flex items-center gap-2 min-w-0 rounded-lg bg-white/95 px-2.5 py-1.5 shadow-sm" aria-label="GNAT Donation home">
            <img
                src="{{ asset($logo['src']) }}"
                alt="{{ $logo['alt'] }}"
                class="h-8 w-auto max-w-[160px] object-contain object-left"
                width="160"
                height="40"
                loading="eager"
            />
        </a>
        <button
            type="button"
            class="h-10 w-10 rounded-full border border-white/20 text-white flex items-center justify-center hover:bg-white/10 transition-colors"
            data-drawer-close
            aria-label="Close menu"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6L6 18M6 6l12 12" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto overscroll-contain p-5 pb-8">
        <p class="text-white/75 text-sm leading-relaxed">
            <strong class="text-white">GNAT Donation</strong> connects generous donors with trusted programs in education, health, and community support—so every contribution creates lasting impact.
        </p>

        <h3 class="mt-8 text-[#fddc6a] text-xs font-extrabold tracking-[0.2em] uppercase">Information</h3>
        <ul class="mt-4 space-y-4 text-white/90 text-sm">
            <li class="flex gap-3">
                <span class="shrink-0 mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-[#fddc6a]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>{{ $contact['address'] }}</span>
            </li>
            <li class="flex gap-3">
                <span class="shrink-0 mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-[#fddc6a]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 4h16v16H4z" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="m22 6-10 7L2 6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <a href="mailto:{{ $contact['email'] }}" class="hover:text-[#fddc6a] transition-colors">{{ $contact['email'] }}</a>
            </li>
            <li class="flex gap-3">
                <span class="shrink-0 mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-[#fddc6a]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M22 16.92V20a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3.09a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L8 9.83a16 16 0 0 0 6.17 6.17l1.38-1.38a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="flex flex-col gap-1">
                    @foreach ($contact['phones'] as $phone)
                        <a href="tel:{{ $phone['tel'] }}" class="hover:text-[#fddc6a] transition-colors">{{ $phone['label'] }}</a>
                    @endforeach
                </span>
            </li>
        </ul>

        <div class="mt-8 flex items-center gap-3">
            <a href="#" class="h-10 w-10 rounded-full border border-white/20 flex items-center justify-center text-white hover:bg-[#fddc6a] hover:text-[#351c42] hover:border-[#fddc6a] transition-colors" aria-label="Facebook">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="#" class="h-10 w-10 rounded-full border border-white/20 flex items-center justify-center text-white hover:bg-[#fddc6a] hover:text-[#351c42] hover:border-[#fddc6a] transition-colors" aria-label="Twitter">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="#" class="h-10 w-10 rounded-full border border-white/20 flex items-center justify-center text-white hover:bg-[#fddc6a] hover:text-[#351c42] hover:border-[#fddc6a] transition-colors" aria-label="LinkedIn">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
            <a href="#" class="h-10 w-10 rounded-full border border-white/20 flex items-center justify-center text-white hover:bg-[#fddc6a] hover:text-[#351c42] hover:border-[#fddc6a] transition-colors" aria-label="YouTube">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
        </div>

        <h3 class="mt-10 text-[#fddc6a] text-xs font-extrabold tracking-[0.2em] uppercase">Newsletter Subscribe</h3>
        <form class="mt-3 flex gap-2" action="#" method="get" onsubmit="return false;">
            <label class="sr-only" for="drawer-newsletter-email">Email address</label>
            <input
                id="drawer-newsletter-email"
                type="email"
                placeholder="Email Address"
                class="flex-1 min-w-0 rounded-xl bg-[#2a1635] border border-white/15 px-4 py-3 text-sm text-white placeholder:text-white/40 outline-none focus:border-[#fddc6a]"
            />
            <button type="submit" class="shrink-0 h-12 w-12 rounded-xl bg-[#fddc6a] text-[#351c42] flex items-center justify-center hover:bg-[#ffe082] transition-colors" aria-label="Subscribe">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </form>

        <nav class="mt-10 flex flex-col gap-1 border-t border-white/10 pt-8" aria-label="Mobile">
            @foreach ($nav as $link)
                <a href="{{ url('/') }}{{ $link['href'] }}" class="py-2.5 text-white font-bold border-b border-white/5 hover:text-[#fddc6a]">{{ $link['label'] }}</a>
            @endforeach
            <a href="{{ url('/') }}#donate" class="py-2.5 text-white font-bold border-b border-white/5 hover:text-[#fddc6a]">Donate</a>
            <a href="{{ route('member.login') }}" class="mt-4 inline-flex items-center gap-3 text-white font-bold hover:text-[#fddc6a] transition-colors">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-[#fddc6a] border border-white/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill="currentColor" d="M12 11.5a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z"/>
                        <path fill="currentColor" d="M6 20.25v-.75c0-2.9 2.35-5.25 5.25-5.25h1.5c2.9 0 5.25 2.35 5.25 5.25v.75H6Z" opacity="0.92"/>
                        <circle cx="17.75" cy="16.75" r="3.15" stroke="currentColor" stroke-width="1.25" fill="none"/>
                        <path stroke="currentColor" stroke-width="1.35" stroke-linecap="round" d="M17.75 15.15v3.2M16.15 16.75h3.2"/>
                    </svg>
                </span>
                Member Login
            </a>
            <a href="{{ url('/') }}#donate" class="mt-3 click-btn click-btn--nav btn-style506 !w-full max-w-full">
                <span class="click-btn__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" aria-hidden="true">
                        <path d="M8 8l3 4-3 4M13 8l3 4-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="click-btn__label">Donate Now</span>
            </a>
        </nav>
    </div>
</aside>
