@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-start justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 uppercase">{{ $polling->title }}</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Posted On Date & Time</p>
        </div>
        <a href="{{ route('admin.pollings.report', $polling->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Download Report</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 p-4">
            <p class="text-sm font-extrabold text-slate-800 mb-2">{{ $polling->title }}</p>
            <p class="text-xs text-slate-500">
                @if($polling->polling_date_to && $polling->polling_date_to->toDateString() !== $polling->polling_date->toDateString())
                    {{ optional($polling->polling_date)->format('d M Y') }} – {{ $polling->polling_date_to->format('d M Y') }}
                @else
                    {{ optional($polling->polling_date)->format('d M Y') }}
                @endif
            </p>
            <p class="text-xs text-slate-500">{{ $polling->polling_from }} - {{ $polling->polling_to }}</p>
            <div class="mt-3">
                <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $polling->polling_status === 'live' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ $polling->polling_status === 'live' ? 'In Live' : 'Ends' }}
                </span>
            </div>
            <div class="mt-4 text-xs font-bold text-slate-700">Show Stats
                <span class="ml-2 px-2 py-0.5 rounded-full {{ $polling->show_stats ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ $polling->show_stats ? 'ON' : 'OFF' }}
                </span>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-100 p-6 space-y-10">
            @forelse($positionStats as $block)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <h3 class="text-lg font-extrabold text-[#351c42]">{{ $block['position']->position }}</h3>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $block['total_votes'] }} {{ $block['total_votes'] === 1 ? 'vote' : 'votes' }}</span>
                    </div>
                    @forelse($block['candidates'] as $candidate)
                        <div class="relative mb-3 overflow-hidden rounded-2xl border border-[#c4b5d5]/60 bg-[#f3eef9]/50">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 rounded-2xl bg-gradient-to-r from-[#d4c4e8] to-[#c9b6e0]"
                                style="width: {{ $candidate['bar_percent'] }}%"
                            ></div>
                            <div class="relative flex items-center justify-between gap-3 px-4 py-3.5">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="truncate text-sm font-bold text-[#351c42]">{{ $candidate['name'] }}</span>
                                </div>
                                <span class="shrink-0 text-sm font-black tabular-nums text-[#351c42]">{{ $candidate['votes'] }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No candidates listed for this position.</p>
                    @endforelse
                </div>
            @empty
                <p class="text-sm text-slate-500">No positions found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
