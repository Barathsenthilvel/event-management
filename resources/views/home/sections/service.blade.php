<section id="service" class="relative bg-white overflow-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex flex-col lg:flex-row lg:items-start gap-10">
            <div class="flex-1">
                <p class="text-sm font-semibold tracking-[0.2em] uppercase text-[#965995]">Our best service</p>
                <h2 class="mt-4 text-4xl md:text-5xl font-extrabold leading-[1.1] text-[#351c42]">
                    How GNAT Donation<br />
                    <span class="relative inline-block mt-1">
                        serves our communities
                        <span class="absolute left-0 right-0 -bottom-2 h-2.5 bg-[#fddc6a] rounded-full -z-10" aria-hidden="true"></span>
                    </span>
                </h2>
            </div>
            <p class="lg:w-1/2 text-sm md:text-base leading-7 text-[#351c42]/70">
                From fundraising to on-the-ground programs, GNAT Donation helps supporters and partners give time, funds, and skills—with clear impact and trusted delivery.
            </p>
        </div>

        <div class="mt-10 border-t border-[#351c42]/10">
            <div class="grid md:grid-cols-2 divide-y-0">
                <div class="divide-y divide-[#351c42]/10">
                    @foreach (array_slice($services, 0, 3) as $svc)
                        <a href="{{ url('/') }}#contact" class="group flex items-center justify-between gap-4 py-6 px-2 md:px-6 rounded-xl -mx-1 md:-mx-2 no-underline text-inherit transition-all duration-300 ease-out hover:bg-[#351c42]/[0.05] hover:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#965995]" aria-label="{{ $svc['label'] }} — get in touch">
                            <div class="flex items-center gap-6 min-w-0">
                                <span class="text-sm font-bold text-[#351c42]/20 tabular-nums transition-colors duration-300 group-hover:text-[#351c42]/40">{{ $svc['num'] }}</span>
                                <span class="text-base md:text-lg font-semibold text-[#351c42] transition-colors duration-300 group-hover:text-[#2a1533]">{{ $svc['label'] }}</span>
                            </div>
                            <svg class="w-5 h-5 shrink-0 text-[#351c42]/35 transition-all duration-300 ease-out group-hover:text-[#965995] group-hover:translate-x-1 group-hover:-translate-y-1" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 17L17 7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 7H17V15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
                <div class="divide-y divide-[#351c42]/10 md:border-l md:border-[#351c42]/10">
                    @foreach (array_slice($services, 3, 3) as $svc)
                        <a href="{{ url('/') }}#contact" class="group flex items-center justify-between gap-4 py-6 px-2 md:px-6 rounded-xl -mx-1 md:-mx-2 no-underline text-inherit transition-all duration-300 ease-out hover:bg-[#351c42]/[0.05] hover:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#965995] md:pl-8" aria-label="{{ $svc['label'] }} — get in touch">
                            <div class="flex items-center gap-6 min-w-0">
                                <span class="text-sm font-bold text-[#351c42]/20 tabular-nums transition-colors duration-300 group-hover:text-[#351c42]/40">{{ $svc['num'] }}</span>
                                <span class="text-base md:text-lg font-semibold text-[#351c42] transition-colors duration-300 group-hover:text-[#2a1533]">{{ $svc['label'] }}</span>
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
