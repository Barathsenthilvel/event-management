@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <div class="flex gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 text-xs font-bold cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    onclick="event.preventDefault(); if(window.loadPageWithLoader) { window.loadPageWithLoader('{{ $paginator->previousPageUrl() }}'); } else { window.location.href = '{{ $paginator->previousPageUrl() }}'; } return false;"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors">...</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-600 text-white text-xs font-bold shadow-lg shadow-indigo-200">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                onclick="event.preventDefault(); if(window.loadPageWithLoader) { window.loadPageWithLoader('{{ $url }}'); } else { window.location.href = '{{ $url }}'; } return false;"
                                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    onclick="event.preventDefault(); if(window.loadPageWithLoader) { window.loadPageWithLoader('{{ $paginator->nextPageUrl() }}'); } else { window.location.href = '{{ $paginator->nextPageUrl() }}'; } return false;"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 text-xs font-bold cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif

