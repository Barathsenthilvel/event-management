<section id="blog" class="relative bg-[#f6f6f4] overflow-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4">
        <div class="grid gap-10 lg:grid-cols-[300px_1fr] items-start">
            <div class="lg:sticky lg:top-8">
                <div class="inline-flex items-center gap-2 text-[#351c42] text-sm font-medium">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#351c42]/10">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M12 13C12 8.5 8 5 3 5C3 10 7 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 13C12 8.5 16 5 21 5C21 10 17 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    Our Blog
                </div>

                <h2 class="mt-3 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]">
                    Insights &amp; Updates
                </h2>

                <p class="mt-6 text-[#351c42]/65 text-base leading-7 max-w-sm">
                    Stay informed with the latest news, stories, and updates from GNAT Association. Explore ideas and initiatives shaping our communities.
                </p>

                <button type="button" class="click-btn btn-style506 mt-8">
                    <span class="click-btn__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" aria-hidden="true">
                            <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="click-btn__label">Explore All Posts</span>
                </button>
            </div>

            <div>
                <div class="overflow-hidden" data-blog-viewport>
                    <div class="flex flex-nowrap gap-4 will-change-transform transition-transform duration-500 ease-out" data-blog-track>
                        @foreach ($blog['posts'] as $post)
                            <article class="shrink-0 min-w-[320px] max-w-[320px] rounded-2xl bg-white border border-[#351c42]/10 overflow-hidden shadow-sm">
                                <div class="relative h-56">
                                    <img src="{{ asset($post['image']) }}" alt="Blog post image" class="h-full w-full object-cover"/>
                                    <span class="absolute left-4 top-4 rounded-full bg-white/75 backdrop-blur px-3 py-1 text-xs font-semibold text-[#351c42]">{{ $post['tag'] }}</span>
                                    <div class="absolute right-0 bottom-0 rounded-tl-2xl bg-[#351c42] text-[#fddc6a] text-center px-4 py-2">
                                        <div class="text-4xl font-extrabold leading-none">{{ $post['day'] }}</div>
                                        <div class="text-lg font-semibold leading-none mt-1 text-white">{{ $post['month'] }}</div>
                                        <div class="mt-1 text-[10px] tracking-[0.35em] font-bold bg-[#fddc6a] text-[#351c42] rounded px-2 py-0.5">{{ $post['year'] }}</div>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <h3 class="text-3xl font-extrabold text-[#351c42]">{{ $post['title'] }}</h3>
                                    <p class="mt-2 text-[#351c42]/65 leading-6">{{ $post['excerpt'] }}</p>
                                    <div class="mt-4 flex items-center justify-between">
                                        <button type="button" class="click-btn click-btn--sm btn-style506">
                                            <span class="click-btn__icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                                                    <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <span class="click-btn__label">Read More</span>
                                        </button>
                                        <span class="text-sm font-semibold text-[#351c42]/70 inline-flex items-center gap-1.5">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M18 8L20 10L18 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 16L4 14L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M4 14H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            {{ $post['comments'] }}
                                        </span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 h-1.5 bg-[#351c42]/15 rounded-full overflow-hidden">
                    <div class="h-full bg-[#351c42] rounded-full transition-all duration-500" style="width: 25%;" data-blog-progress></div>
                </div>
            </div>
        </div>
    </div>
</section>
