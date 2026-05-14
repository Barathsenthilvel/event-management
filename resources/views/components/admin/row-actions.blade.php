@props([
    'moreLabel' => 'More',
])

{{-- Two primary icon actions + optional kebab "More" menu (Alpine). Slots: $primary, $more --}}
<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-end gap-1.5']) }}
    x-data="{ open: false }"
    @keydown.escape.window="open = false">
    <div class="flex items-center gap-1.5 shrink-0">
        {{ $primary }}
    </div>
    @if(isset($more) && $more->isNotEmpty())
        <div class="relative shrink-0" @click.outside="open = false">
            <button type="button"
                @click="open = !open"
                :aria-expanded="open.toString()"
                class="group flex h-8 min-w-[2rem] items-center justify-center gap-0.5 rounded-lg border border-slate-200 bg-white px-1.5 text-slate-500 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700"
                title="{{ $moreLabel }}">
                <svg class="h-4 w-4 shrink-0 text-slate-500 group-hover:text-indigo-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/>
                </svg>
                <span class="hidden sm:inline max-w-[4rem] truncate text-[9px] font-black uppercase tracking-wide text-slate-500 group-hover:text-indigo-700">{{ $moreLabel }}</span>
                <span class="sr-only">{{ $moreLabel }}</span>
            </button>
            <div x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 translate-y-0.5"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute right-0 z-50 mt-1.5 w-56 max-w-[min(18rem,calc(100vw-2rem))] origin-top-right rounded-xl border border-slate-200/90 bg-white py-1 shadow-xl ring-1 ring-slate-900/5">
                <div class="max-h-72 overflow-y-auto py-0.5 text-left" @click="open = false">
                    {{ $more }}
                </div>
            </div>
        </div>
    @endif
</div>
