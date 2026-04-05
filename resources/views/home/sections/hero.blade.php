<section id="home" class="relative bg-[#351c42] overflow-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-14 items-start">
            <div>
                <div class="inline-flex items-center gap-3 text-white">
                    <span class="text-sm font-semibold tracking-widest">{{ $hero['badge'] }}</span>
                </div>

                <h2 class="mt-4 text-4xl leading-[1.05] font-bold text-white">
                    {{ $hero['headline_line1'] }}<br />
                    {{ $hero['headline_line2'] }}
                </h2>

                <p class="mt-5 max-w-md text-sm leading-6 text-white/90">
                    {!! $hero['description_html'] !!}
                </p>

                <div class="mt-7 flex items-center gap-4">
                    <a href="{{ url('/') }}#volunteer" class="click-btn btn-style506">
                        <span class="click-btn__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-7 w-7" fill="none" aria-hidden="true">
                                <path d="M7 17L17 7" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 7H17V15" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="click-btn__label">Join Now</span>
                    </a>
                </div>

                <div class="mt-8 flex items-center gap-4" aria-label="{{ $hero['registered_count'] }} people registered with GNAT Association">
                    <div class="flex shrink-0 items-center">
                        <div class="relative z-[1] h-11 w-11 shrink-0 overflow-hidden rounded-full border-2 border-[#115e59] bg-[#351c42] ring-2 ring-[#351c42]">
                            <img class="h-full w-full object-cover" src="{{ asset($hero['avatar_image']) }}" alt="" width="44" height="44" loading="lazy" decoding="async" />
                        </div>
                        <div class="relative z-[2] -ml-3 h-11 w-11 shrink-0 overflow-hidden rounded-full border-2 border-[#115e59] bg-[#351c42] ring-2 ring-[#351c42]">
                            <img class="h-full w-full object-cover object-top" src="{{ asset($hero['avatar_image']) }}" alt="" width="44" height="44" loading="lazy" decoding="async" />
                        </div>
                        <div class="relative z-[3] -ml-3 flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-[#115e59] bg-[#351c42] ring-2 ring-[#351c42]">
                            <img class="absolute inset-0 h-full w-full scale-125 object-cover opacity-40 blur-sm" src="{{ asset($hero['avatar_image']) }}" alt="" width="44" height="44" loading="lazy" decoding="async" aria-hidden="true" />
                            <span class="relative text-xl font-light leading-none text-[#fddc6a]" aria-hidden="true">+</span>
                        </div>
                    </div>
                    <div class="min-w-0 border-l border-white/15 pl-4">
                        <p class="text-2xl font-extrabold leading-none tracking-tight text-white sm:text-[1.65rem]">{{ number_format($hero['registered_count']) }}</p>
                        <p class="mt-1.5 text-xs font-medium leading-tight text-white/65">{{ $hero['registered_label'] }}</p>
                    </div>
                </div>
            </div>

            <div id="volunteer" class="relative">
                <div id="volunteerStackScroll" class="relative">
                    <div class="sticky top-6">
                        <div id="volunteerStack" class="relative w-full">
                            @foreach ($volunteer_cards as $i => $card)
                                <article class="stack-card rounded-2xl bg-white/10 border border-white/20 shadow-sm overflow-hidden" style="--i:{{ $i }}">
                                    <div class="flex flex-col md:flex-row">
                                        <div class="p-6 md:w-7/12">
                                            <h3 class="text-white font-bold text-lg">{{ $card['title'] }}</h3>
                                            <ul class="mt-4 space-y-2 text-sm text-white/90">
                                                @foreach ($card['bullets'] as $bullet)
                                                    <li class="flex items-start gap-2">
                                                        <svg class="mt-0.5 h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        <span>{{ $bullet }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
