@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-xs font-semibold text-[#351c42]/60">
            Showing <span class="font-bold text-[#351c42]">{{ $paginator->firstItem() ?? 0 }}</span> to <span class="font-bold text-[#351c42]">{{ $paginator->lastItem() ?? 0 }}</span> of <span class="font-bold text-[#351c42]">{{ $paginator->total() }}</span> posts
        </p>

        <div class="flex items-center gap-1.5 flex-wrap">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#351c42]/10 bg-white text-[#351c42]/30 cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#351c42]/15 bg-white text-[#351c42] hover:bg-[#351c42]/5 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-9 w-9 items-center justify-center text-xs font-bold text-[#351c42]/40">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-9 min-w-[2.25rem] px-3 items-center justify-center rounded-xl bg-[#351c42] text-[#fddc6a] text-xs font-black shadow-md shadow-[#351c42]/20">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-9 min-w-[2.25rem] px-3 items-center justify-center rounded-xl border border-[#351c42]/15 bg-white text-xs font-bold text-[#351c42] hover:bg-[#351c42]/5 transition">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#351c42]/15 bg-white text-[#351c42] hover:bg-[#351c42]/5 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#351c42]/10 bg-white text-[#351c42]/30 cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
