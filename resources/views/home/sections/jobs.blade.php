<section id="jobs" class="relative bg-[#f6f6f4] overflow-hidden py-16 lg:py-24 scroll-mt-32">
    <div class="mx-auto max-w-6xl px-4 text-center">
        <div class="inline-flex items-center justify-center gap-2 text-sm font-semibold tracking-wide text-[#965995]">
            <span class="h-2.5 w-2.5 rounded-full bg-[#965995]"></span>
            {{ $jobs['eyebrow'] }}
        </div>
        <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-[#351c42]">{{ $jobs['title'] }}</h2>
        <p class="mt-4 text-[#351c42]/70 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
            {{ $jobs['text'] }}
            <a href="mailto:{{ $contact['email'] }}" class="font-semibold text-[#351c42] underline underline-offset-2 hover:text-[#965995]">{{ $contact['email'] }}</a>
            and tell us how you’d like to contribute.
        </p>
        <div class="mt-8">
            <a href="{{ route('member.login') }}#signup" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-6 py-3 text-sm font-bold text-[#fddc6a] shadow-lg shadow-[#351c42]/25 transition hover:brightness-105">
                Join Now
            </a>
        </div>
    </div>
</section>
