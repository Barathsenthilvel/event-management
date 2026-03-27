@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-start justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 uppercase">{{ $nomination->title }}</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Posted On Date & Time</p>
        </div>
        <a href="{{ route('admin.nominations.report', $nomination->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Download Report</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-100 p-4 space-y-3">
            @foreach($positions as $pos)
                <div class="flex items-center justify-between">
                    <p class="text-sm font-extrabold text-slate-800">{{ $pos->position }}</p>
                    <span class="px-2.5 py-1 rounded-md bg-blue-100 text-blue-700 text-xs font-black">{{ $pos->entries_count }}</span>
                </div>
            @endforeach
        </div>
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-100 p-4">
            <div class="flex items-center justify-end mb-3">
                <form method="GET">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-56 outline-none focus:ring-2 focus:ring-indigo-200">
                </form>
            </div>
            <div class="space-y-2">
                @forelse($entries as $entry)
                    <div class="flex items-center gap-3 border border-slate-200 rounded-xl p-3">
                        <div class="w-8 h-8 rounded border border-slate-400 flex items-center justify-center text-sm">{{ strtoupper(substr($entry->user->name ?? 'M', 0, 1)) }}</div>
                        <div class="w-48">
                            <p class="text-sm font-extrabold">{{ $entry->user->name ?? 'Member' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $entry->position->position ?? '-' }}</p>
                        </div>
                        <div class="w-44 text-[11px] text-slate-600">{{ $entry->user->email ?? '-' }}<br>{{ $entry->user->mobile ?? '-' }}</div>
                        <div class="w-28 text-[11px] text-slate-700">Member</div>
                        <div class="w-40 text-[11px] text-slate-700">{{ optional($entry->submitted_at)->format('d M Y h:i A') ?: '-' }}</div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 font-bold">No submissions found.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $entries->links() }}</div>
        </div>
    </div>
</div>
@endsection

