<footer id="contact" class="relative bg-[#351c42] text-white overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 pt-16 pb-12 grid gap-12 lg:grid-cols-4 lg:gap-10">
        <div>
            <h3 class="text-lg font-bold text-white relative inline-block pb-2 mb-4">
                About GNAT Donation
                <span class="absolute left-0 bottom-0 h-0.5 w-12 bg-[#fddc6a] rounded-full" aria-hidden="true"></span>
            </h3>
            <p class="text-sm text-white/75 leading-relaxed">
                GNAT Donation connects caring people with trusted programs in education, health, and community support—so every gift and volunteer hour creates real, lasting change.
            </p>
            <a
                href="https://www.google.com/maps/search/?api=1&query={{ $contact['maps_query'] }}"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#fddc6a] hover:text-white transition-colors"
            >
                View Map
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <div>
            <h3 class="text-lg font-bold text-white relative inline-block pb-2 mb-4">
                Get In Touch!
                <span class="absolute left-0 bottom-0 h-0.5 w-12 bg-[#fddc6a] rounded-full" aria-hidden="true"></span>
            </h3>
            <ul class="space-y-4 text-sm text-white/85">
                <li class="flex gap-3">
                    <span class="mt-0.5 shrink-0 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#fddc6a]/15 text-[#fddc6a]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>No. 36/76, Thiruveethi Amman Kovil 2nd Street,<br />Aminjikarai, Chennai&nbsp;600029</span>
                </li>
                <li class="flex gap-3">
                    <span class="mt-0.5 shrink-0 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#fddc6a]/15 text-[#fddc6a]">
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
                <li class="flex gap-3">
                    <span class="mt-0.5 shrink-0 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#fddc6a]/15 text-[#fddc6a]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M4 4h16v16H4z" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="m22 6-10 7L2 6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <a href="mailto:{{ $contact['email'] }}" class="hover:text-[#fddc6a] transition-colors break-all">{{ $contact['email'] }}</a>
                </li>
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-bold text-white relative inline-block pb-2 mb-4">
                Quick Links
                <span class="absolute left-0 bottom-0 h-0.5 w-12 bg-[#fddc6a] rounded-full" aria-hidden="true"></span>
            </h3>
            <ul class="space-y-2.5 text-sm font-medium">
                <li><a href="{{ url('/') }}#about2" class="text-white/80 hover:text-[#fddc6a] transition-colors">About Us</a></li>
                <li><button type="button" data-open-donate-modal class="text-white/80 hover:text-[#fddc6a] transition-colors text-left bg-transparent border-0 p-0 cursor-pointer font-medium">Give Donation</button></li>
                <li><a href="{{ url('/') }}#association-activity" class="text-white/80 hover:text-[#fddc6a] transition-colors">Association Activity</a></li>
                <li><a href="{{ url('/') }}#events" class="text-white/80 hover:text-[#fddc6a] transition-colors">Our Campaign</a></li>
                <li><a href="{{ url('/') }}#gallery" class="text-white/80 hover:text-[#fddc6a] transition-colors">Gallery</a></li>
                <li><a href="{{ url('/') }}#contact" class="text-white/80 hover:text-[#fddc6a] transition-colors">Contact Us</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-bold text-white relative inline-block pb-2 mb-4">
                Subscribe Now
                <span class="absolute left-0 bottom-0 h-0.5 w-12 bg-[#fddc6a] rounded-full" aria-hidden="true"></span>
            </h3>
            <p class="text-sm text-white/75 leading-relaxed mb-4">Don’t miss our future updates. Get subscribed today!</p>
            <form class="flex flex-col gap-3" action="#" method="get" onsubmit="return false;" data-footer-newsletter>
                <div class="flex rounded-xl overflow-hidden border border-white/15 bg-[#2a1635] focus-within:border-[#fddc6a]/60">
                    <label class="sr-only" for="footer-subscribe-email">Email</label>
                    <input
                        id="footer-subscribe-email"
                        type="email"
                        placeholder="Enter Mail"
                        class="flex-1 min-w-0 bg-transparent px-4 py-3 text-sm text-white placeholder:text-white/40 outline-none"
                        required
                    />
                    <button type="submit" class="shrink-0 px-4 bg-[#351c42] hover:bg-[#4a2a56] text-[#fddc6a] border-l border-white/10 transition-colors" aria-label="Subscribe">
                        <svg class="h-5 w-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <label class="flex items-start gap-2 cursor-pointer text-xs text-white/60">
                    <input type="checkbox" name="terms" class="mt-0.5 rounded border-white/30 bg-transparent text-[#fddc6a] focus:ring-[#fddc6a]" required />
                    <span>I accept terms &amp; conditions</span>
                </label>
            </form>
        </div>
    </div>

    <div class="relative border-t border-black/10 bg-gradient-to-b from-[#f0d78c] via-[#fddc6a] to-[#e8c45c]">
        <div class="mx-auto max-w-7xl px-4 py-5 flex flex-col md:flex-row items-center justify-between gap-5 text-[#351c42]">
            <a href="{{ url('/') }}#home" class="shrink-0">
                <img src="{{ asset($logo['src']) }}" alt="{{ $logo['alt'] }}" class="h-10 sm:h-11 w-auto max-w-[200px] object-contain object-left" width="200" height="48" loading="lazy" />
            </a>
            <p class="text-sm font-semibold text-center order-last md:order-none">
                © Copyright <span id="footer-year">{{ date('Y') }}</span> GNAT Donation. All Rights Reserved.
            </p>
            <div class="flex items-center gap-2 shrink-0">
                <a href="#" class="h-9 w-9 rounded-full bg-[#351c42]/10 flex items-center justify-center text-[#351c42] hover:bg-[#351c42] hover:text-[#fddc6a] transition-colors" aria-label="Facebook">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="h-9 w-9 rounded-full bg-[#351c42]/10 flex items-center justify-center text-[#351c42] hover:bg-[#351c42] hover:text-[#fddc6a] transition-colors" aria-label="Twitter">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="#" class="h-9 w-9 rounded-full bg-[#351c42]/10 flex items-center justify-center text-[#351c42] hover:bg-[#351c42] hover:text-[#fddc6a] transition-colors" aria-label="LinkedIn">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
                <a href="#" class="h-9 w-9 rounded-full bg-[#351c42]/10 flex items-center justify-center text-[#351c42] hover:bg-[#351c42] hover:text-[#fddc6a] transition-colors" aria-label="YouTube">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>
