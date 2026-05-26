<section id="blog" class="relative bg-[#f6f6f4] overflow-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4">
        <div class="grid gap-10 lg:grid-cols-[300px_1fr] items-start">
            <div class="lg:sticky lg:top-8">
                <div class="inline-flex items-center gap-2 text-sm font-semibold tracking-wide text-[#965995]">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#965995]" aria-hidden="true"></span>
                    {{ $blog['section_badge'] ?? 'Our blog' }}
                </div>

                <h2 class="mt-3 text-3xl md:text-4xl font-extrabold leading-tight text-[#351c42]">
                    {{ $blog['section_title'] ?? 'Insights & Updates' }}
                </h2>

                <p class="mt-6 text-[#351c42]/65 text-base leading-7 max-w-sm">
                    {{ $blog['section_description'] ?? 'Stay informed with the latest news, stories, and updates from GNAT Association. Explore ideas and initiatives shaping our communities.' }}
                </p>

                <a href="{{ route('blogs.index') }}" class="click-btn btn-style506 mt-8 inline-flex">
                    <span class="click-btn__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" aria-hidden="true">
                            <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="click-btn__label">{{ $blog['section_button_text'] ?? 'Explore All Posts' }}</span>
                </a>
            </div>

            <div>
                @if(collect($blog['posts'] ?? [])->isNotEmpty())
                    <div class="overflow-hidden" data-blog-viewport>
                        <div class="flex flex-nowrap gap-4 will-change-transform transition-transform duration-500 ease-out" data-blog-track>
                            @foreach ($blog['posts'] as $post)
                                @include('home.partials.blog-card', ['post' => $post, 'carousel' => true])
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-5 h-1.5 bg-[#351c42]/15 rounded-full overflow-hidden">
                        <div class="h-full bg-[#351c42] rounded-full transition-all duration-500" style="width: 25%;" data-blog-progress></div>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-[#351c42]/20 bg-white px-6 py-12 text-center">
                        <p class="text-sm font-semibold text-[#351c42]/80">No blog posts to show yet.</p>
                        <a href="{{ route('blogs.index') }}" class="mt-4 inline-flex text-sm font-bold text-[#965995] hover:text-[#351c42] underline-offset-4 hover:underline">
                            View blogs page
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
