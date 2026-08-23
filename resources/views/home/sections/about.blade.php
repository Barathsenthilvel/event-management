<section id="about2" class="relative scroll-mt-32 bg-[#351c42] overflow-hidden py-16 lg:py-24">
    <div class="pointer-events-none absolute -left-6 -top-8 h-28 w-28 rounded-b-[38px] border border-[#fddc6a]/35"></div>
    <div class="pointer-events-none absolute -right-10 -bottom-12 h-40 w-40 rounded-tl-[56px] border border-[#fddc6a]/35"></div>

    <div class="mx-auto max-w-7xl px-4">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-14 items-center">
            <div class="relative py-2 flex justify-center lg:justify-start">
                <!-- Ambient background glow -->
                <div class="pointer-events-none absolute -inset-4 rounded-[40px] bg-gradient-to-tr from-[#965995]/25 via-[#fddc6a]/15 to-transparent blur-3xl opacity-80" aria-hidden="true"></div>

                <div class="relative max-w-[480px] w-full">
                    <!-- Main Full Image Card (No cropping - natural height & full image display) -->
                    <div class="group relative overflow-hidden rounded-3xl border-2 border-white/20 bg-white/10 p-2 shadow-2xl backdrop-blur-md transition-all duration-500 hover:-translate-y-1 hover:shadow-[0_30px_65px_rgba(0,0,0,0.5)] hover:border-white/40">
                        <div class="overflow-hidden rounded-2xl bg-white/5">
                            <img
                                src="{{ asset($about['main_image'] ?? 'gnat-images/1000989135.png') }}"
                                alt="GNAT Association"
                                class="h-auto w-full object-contain transition-transform duration-700 ease-out group-hover:scale-[1.02]"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="inline-flex items-center gap-2 text-xs font-semibold tracking-wide text-white/90">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#fddc6a]"></span>
                    {{ $about['eyebrow'] }}
                </div>

                <h2 class="mt-4 text-3xl md:text-4xl font-extrabold leading-tight text-white">
                    @if(!empty($about['title_lines'][0]))
                        {{ $about['title_lines'][0] }}<br />
                    @endif
                    @if(!empty($about['title_lines'][1]))
                        {{ $about['title_lines'][1] }}
                    @endif
                    @if(!empty($about['title_highlight']))
                        <span class="relative inline-block">{{ $about['title_highlight'] }}
                            <span class="absolute left-0 right-0 -bottom-2 h-2 bg-[#fddc6a] rounded-full"></span>
                        </span>
                    @endif
                    @if(!empty($about['title_lines'][2]))
                        <br />{{ $about['title_lines'][2] }}
                    @endif
                </h2>

                <div class="mt-6 max-w-xl space-y-4 text-sm md:text-base leading-7 text-white/80">
                    @foreach ($about['intro_lines'] ?? [] as $line)
                        <p>{{ $line }}</p>
                    @endforeach

                    @if(!empty($about['impact_line']))
                        <p class="font-semibold text-[#fddc6a]">{{ $about['impact_line'] }}</p>
                    @endif

                    @if(!empty($about['principles_heading']))
                        <p class="text-white font-extrabold tracking-tight">{{ $about['principles_heading'] }}</p>
                    @endif

                    @if(!empty($about['principles']))
                        <ul class="space-y-2 border-l-2 border-[#fddc6a]/35 pl-4">
                            @foreach ($about['principles'] as $principle)
                                <li class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                                    <span class="font-extrabold text-[#fddc6a]">{{ $principle['label'] }}</span>
                                    <span class="text-white/50">=</span>
                                    <span class="text-white/90">{{ $principle['meaning'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-5">
                    <a href="{{ route('about') }}" class="click-btn btn-style506">
                        <span class="click-btn__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" aria-hidden="true">
                                <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="click-btn__label">More About Us</span>
                    </a>

                    <div class="inline-flex items-center gap-3 text-white">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#fddc6a]/70 text-[#fddc6a]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 16.92V20a2 2 0 0 1-2.18 2a19.8 19.8 0 0 1-8.63-3.07a19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18A2 2 0 0 1 4.11 2h3.09a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L8 9.83a16 16 0 0 0 6.17 6.17l1.38-1.37a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="text-sm">
                            <span class="block text-white/70">Call Any Time</span>
                            <span class="block font-bold text-white text-base leading-snug">
                                @foreach ($contact['phones'] as $idx => $phone)
                                    @if ($idx > 0)
                                        <span class="text-white/50 font-normal"> / </span>
                                    @endif
                                    <a href="tel:{{ $phone['tel'] }}" class="hover:text-[#fddc6a] transition-colors">{{ $phone['label'] }}</a>
                                @endforeach
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
