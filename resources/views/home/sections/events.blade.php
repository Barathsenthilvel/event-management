<section id="events" class="relative bg-white overflow-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4">
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 text-sm font-semibold tracking-wide text-[#965995]">
                <span class="h-2.5 w-2.5 rounded-full bg-[#965995]"></span>
                EVENTS
            </div>
            <div class="flex flex-col gap-3 min-[480px]:flex-row min-[480px]:items-start min-[480px]:justify-between min-[480px]:gap-6">
                <h2 class="text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42] min-w-0 flex-1">
                    Upcoming Events<br />
                    <span class="text-[#965995]">Don’t Miss Out</span>
                </h2>
                <a href="{{ url('/') }}#contact" class="shrink-0 text-sm font-semibold text-[#965995] underline-offset-4 hover:text-[#351c42] hover:underline transition-colors min-[480px]:pt-1 md:pt-1.5">
                    View more
                </a>
            </div>
        </div>

        <div class="mt-10 grid gap-4">
            @foreach ($events as $event)
                <div class="rounded-2xl bg-white border border-[#351c42]/10 overflow-hidden" data-events-accordion-item>
                    <button
                        type="button"
                        class="w-full text-left px-6 py-4 flex items-start justify-between gap-6"
                        data-events-accordion-trigger
                        aria-expanded="false"
                    >
                        <div class="min-w-0" data-events-header-summary>
                            <div class="flex items-center gap-3 text-xs font-semibold text-[#351c42]/70" data-events-header-time>
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-[#351c42]"></span>
                                    {{ $event['summary_date'] }}
                                </span>
                                <span class="h-1 w-1 rounded-full bg-[#351c42]/30"></span>
                                <span>{{ $event['time'] }}</span>
                            </div>
                            <div class="mt-2 text-sm md:text-base font-bold text-[#351c42] truncate" data-events-header-title>
                                {{ $event['summary_title'] }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="h-9 w-9 rounded-full bg-[#351c42] text-white flex items-center justify-center flex-shrink-0" data-events-trigger-icon aria-hidden="true">
                                <svg data-events-plus class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 1.5V14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <svg data-events-minus class="w-4 h-4 hidden" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div class="px-6 pb-6 pt-0 hidden" data-events-accordion-panel>
                        <div class="mt-6 grid gap-8 md:grid-cols-[340px_1fr] items-stretch">
                            <div class="relative rounded-2xl overflow-hidden border border-[#351c42]/10 bg-[#f6f3e9]">
                                <img src="{{ asset($event['image']) }}" alt="Event image" class="h-56 md:h-72 w-full object-cover" />

                                <div class="absolute left-4 top-4 {{ $event['badge_rounded'] ?? 'rounded-full' }} {{ $event['badge_bg'] ?? 'bg-[#fddc6a]' }} px-3 py-2 text-center shadow-sm">
                                    <div class="text-lg font-extrabold leading-none text-[#351c42]">{{ $event['badge_day'] }}</div>
                                    <div class="mt-0.5 text-[10px] font-extrabold tracking-widest leading-none text-[#965995] bg-white/70 rounded px-2 py-0.5 inline-block">{{ $event['badge_month'] }}</div>
                                </div>
                            </div>

                            <div class="flex flex-col h-full">
                                <div class="flex items-center justify-between gap-6 w-full">
                                    <div class="inline-flex items-center gap-3 rounded-full border border-[#351c42]/10 bg-white/70 px-4 py-2">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <span class="text-xs font-semibold text-[#351c42]/70">{{ $event['time'] }}</span>
                                    </div>
                                    <div class="h-9 w-9 rounded-full bg-[#351c42] text-white flex items-center justify-center flex-shrink-0 ml-auto" data-events-panel-icon aria-hidden="true">
                                        <svg data-events-plus class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 1.5V14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <svg data-events-minus class="w-4 h-4 hidden" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.5 8H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-3 text-sm md:text-base font-bold text-[#351c42]">
                                    {{ $event['summary_title'] }}
                                </div>

                                <p class="mt-3 text-sm text-[#351c42]/80 leading-6">
                                    {{ $event['description'] }}
                                </p>
                                <div class="mt-auto grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-[#f6f3e9] p-4 border border-[#351c42]/10">
                                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            Organizer
                                        </div>
                                        <div class="mt-2 font-bold text-[#351c42] text-sm">{{ $event['organizer'] }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-[#f6f3e9] p-4 border border-[#351c42]/10">
                                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#351c42]/70">
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#965995]/15 text-[#965995]">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            Venue
                                        </div>
                                        <div class="mt-2 font-bold text-[#351c42] text-sm">{{ $event['venue'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
