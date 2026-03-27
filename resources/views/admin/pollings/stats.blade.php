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
            <p class="text-xs text-slate-500">{{ optional($polling->polling_date)->format('d M Y') }}</p>
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

        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-100 p-6 space-y-8">
            @forelse($positionStats as $block)
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-lg font-extrabold text-slate-800">{{ $block['position']->position }}</h3>
                        <span class="px-3 py-1 rounded-md bg-blue-100 text-blue-700 text-sm font-black">{{ $block['total_votes'] }}</span>
                    </div>
                    @forelse($block['candidates'] as $candidate)
                        <div class="grid grid-cols-12 gap-3 items-center mb-3">
                            <div class="col-span-2 text-sm text-slate-700">{{ $candidate['name'] }}</div>
                            <div class="col-span-8">
                                <div class="w-full h-2.5 rounded bg-slate-200 overflow-hidden">
                                    <div class="h-full bg-indigo-500 rounded" style="width: {{ $candidate['percent'] }}%"></div>
                                </div>
                            </div>
                            <div class="col-span-2 text-sm font-bold text-slate-800">{{ $candidate['votes'] }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No votes yet for this position.</p>
                    @endforelse
                </div>
            @empty
                <p class="text-sm text-slate-500">No positions found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

