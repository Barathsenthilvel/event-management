@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1">
            <h1 class="text-xl font-extrabold text-slate-900 uppercase break-words">{{ $nomination->title }}</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Interest submissions for this nomination</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('admin.nominations.show', $nomination) }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-extrabold text-slate-800 hover:bg-slate-50">View nomination (read-only)</a>
            <a href="{{ route('admin.nominations.report', $nomination->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Download Report</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5 lg:items-start">
        <div class="lg:col-span-1 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <p class="text-[11px] font-extrabold uppercase tracking-wide text-slate-600 mb-3">Position-wise summary</p>
            <ul class="space-y-0 divide-y divide-slate-100">
                @foreach($positions as $pos)
                    <li class="flex flex-col gap-2 py-3 first:pt-0 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                        <p class="min-w-0 flex-1 text-sm font-extrabold text-slate-800 break-words">{{ $pos->position }}</p>
                        <div class="flex shrink-0 items-center gap-1.5" title="Interested count / Not interested count">
                            <span class="rounded-md bg-emerald-100 px-2 py-1 text-[10px] font-black tabular-nums text-emerald-800">{{ $pos->interested_entries_count }}</span>
                            <span class="rounded-md bg-slate-200 px-2 py-1 text-[10px] font-black tabular-nums text-slate-700">{{ $pos->not_interested_entries_count }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="min-w-0 lg:col-span-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="flex min-w-0 flex-1 flex-col gap-1 sm:max-w-xs">
                    <label for="filter-response" class="text-[10px] font-extrabold uppercase tracking-wide text-slate-500">Response</label>
                    <select id="filter-response" name="response" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700">
                        <option value="all" {{ ($response ?? 'all') === 'all' ? 'selected' : '' }}>All responses</option>
                        <option value="interested" {{ ($response ?? 'all') === 'interested' ? 'selected' : '' }}>Interested only</option>
                        <option value="not_interested" {{ ($response ?? 'all') === 'not_interested' ? 'selected' : '' }}>Not interested only</option>
                    </select>
                </div>
                <div class="flex min-w-0 flex-1 flex-col gap-1 sm:max-w-md">
                    <label for="filter-position" class="text-[10px] font-extrabold uppercase tracking-wide text-slate-500">Position</label>
                    <select id="filter-position" name="position_id" class="w-full max-w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700">
                        <option value="0">All positions</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ (int)($positionId ?? 0) === (int)$pos->id ? 'selected' : '' }}>
                                {{ $pos->position }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex w-full gap-2 sm:w-auto sm:shrink-0">
                    <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-black text-white hover:bg-slate-800 sm:w-auto">Apply</button>
                </div>
            </form>

            <div class="space-y-3">
                @forelse($entries as $entry)
                    <article class="overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                            <div class="flex min-w-0 gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-sm font-extrabold text-slate-700" aria-hidden="true">
                                    {{ strtoupper(substr($entry->user->name ?? 'M', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1 space-y-1">
                                    <p class="text-sm font-extrabold text-slate-900 break-words">{{ $entry->user->name ?? 'Member' }}</p>
                                    <p class="text-xs font-semibold text-slate-500 break-words">{{ $entry->position->position ?? '—' }}</p>
                                    <p class="break-all text-xs text-slate-600">{{ $entry->user->email ?? '—' }}</p>
                                    <p class="text-xs text-slate-600">{{ $entry->user->mobile ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col gap-2 border-t border-slate-100 pt-3 sm:border-t-0 sm:pt-0 sm:text-right">
                                @if(($entry->response_status ?? 'interested') === 'not_interested')
                                    <span class="inline-flex w-fit rounded-full bg-slate-200 px-3 py-1 text-[11px] font-black text-slate-800 sm:ml-auto">Not interested</span>
                                @else
                                    <span class="inline-flex w-fit rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-black text-emerald-800 sm:ml-auto">Interested</span>
                                @endif
                                <p class="text-[11px] font-semibold text-slate-600">{{ optional($entry->submitted_at)->format('d M Y, h:i A') ?: '—' }}</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-10 text-center text-sm font-bold text-slate-500">No submissions found.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $entries->links() }}</div>
        </div>
    </div>
</div>
@endsection
