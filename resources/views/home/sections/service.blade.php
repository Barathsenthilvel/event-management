@php
    $activityItems = $activities['items'] ?? $services ?? [];
@endphp
<section id="association-activity" class="relative bg-white overflow-hidden py-16 lg:py-24 scroll-mt-32">
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex flex-col lg:flex-row lg:items-start gap-10">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 text-sm font-semibold tracking-[0.2em] uppercase text-[#965995]">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#965995]" aria-hidden="true"></span>
                    {{ $activities['badge'] ?? 'Activity' }}
                </div>
                <h2 class="mt-4 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]">
                    {{ $activities['title'] ?? 'Programs & pathways' }}
                </h2>
            </div>
            <div class="lg:w-1/2">
                <p class="text-sm md:text-base leading-7 text-[#351c42]/70">
                    {{ $activities['intro'] ?? 'From fundraising to on-the-ground programs, GNAT Association helps supporters and partners give time, funds, and skills—with clear impact and trusted delivery.' }}
                </p>
                <a href="{{ route('activity') }}" class="mt-4 inline-flex text-sm font-semibold text-[#965995] underline-offset-4 hover:text-[#351c42] hover:underline">
                    View all activities
                </a>
            </div>
        </div>

        <div class="mt-10 border-t border-[#351c42]/10">
            <div class="grid md:grid-cols-2 divide-y-0">
                <div class="divide-y divide-[#351c42]/10">
                    @foreach (array_slice($activityItems, 0, 3) as $item)
                        @php
                            $activityUrl = ! empty($item['slug'])
                                ? route('activity.show', $item['slug'])
                                : route('activity');
                        @endphp
                        <a href="{{ $activityUrl }}" class="group flex items-center justify-between gap-4 py-6 px-2 md:px-6 rounded-xl -mx-1 md:-mx-2 no-underline text-inherit transition-all duration-300 ease-out hover:bg-[#351c42]/[0.05] hover:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#965995]" aria-label="{{ $item['label'] }}">
                            <div class="flex items-center gap-6 min-w-0">
                                <span class="text-sm font-bold text-[#351c42]/20 tabular-nums transition-colors duration-300 group-hover:text-[#351c42]/40">{{ $item['num'] }}</span>
                                <span class="text-base md:text-lg font-semibold text-[#351c42] transition-colors duration-300 group-hover:text-[#2a1533]">{{ $item['label'] }}</span>
                            </div>
                            <svg class="w-5 h-5 shrink-0 text-[#351c42]/35 transition-all duration-300 ease-out group-hover:text-[#965995] group-hover:translate-x-1 group-hover:-translate-y-1" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 17L17 7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 7H17V15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
                <div class="divide-y divide-[#351c42]/10 md:border-l md:border-[#351c42]/10">
                    @foreach (array_slice($activityItems, 3, 3) as $item)
                        @php
                            $activityUrl = ! empty($item['slug'])
                                ? route('activity.show', $item['slug'])
                                : route('activity');
                        @endphp
                        <a href="{{ $activityUrl }}" class="group flex items-center justify-between gap-4 py-6 px-2 md:px-6 rounded-xl -mx-1 md:-mx-2 no-underline text-inherit transition-all duration-300 ease-out hover:bg-[#351c42]/[0.05] hover:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#965995] md:pl-8" aria-label="{{ $item['label'] }}">
                            <div class="flex items-center gap-6 min-w-0">
                                <span class="text-sm font-bold text-[#351c42]/20 tabular-nums transition-colors duration-300 group-hover:text-[#351c42]/40">{{ $item['num'] }}</span>
                                <span class="text-base md:text-lg font-semibold text-[#351c42] transition-colors duration-300 group-hover:text-[#2a1533]">{{ $item['label'] }}</span>
                            </div>
                            <svg class="w-5 h-5 shrink-0 text-[#351c42]/35 transition-all duration-300 ease-out group-hover:text-[#965995] group-hover:translate-x-1 group-hover:-translate-y-1" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 17L17 7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 7H17V15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
