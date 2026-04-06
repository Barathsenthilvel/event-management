@extends('admin.layouts.app')

@section('content')
@php
    $tab = $tab ?? 'nominations';
@endphp
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manage Nominations</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Create and run nomination polls.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full md:w-auto md:flex-1 md:max-w-3xl">
                <form method="GET" class="flex items-center gap-2 w-full sm:max-w-md min-w-0">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative flex-1 min-w-0">
                        <input type="search" name="q" value="{{ $q }}" placeholder="Search"
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
                </form>
                <div class="flex shrink-0 justify-end">
                    <a href="{{ route('admin.nominations.create') }}" class="inline-flex px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">+ Add</a>
                </div>
            </div>
        </div>

        <div class="mt-5 inline-flex rounded-full bg-slate-100 p-1 text-[11px] font-black uppercase tracking-widest">
            <a href="{{ route('admin.nominations.index', array_filter(['tab' => 'nominations', 'q' => $q])) }}"
               class="rounded-full px-4 py-2 transition {{ $tab === 'nominations' ? 'bg-slate-900 text-white shadow' : 'text-slate-600 hover:text-slate-900' }}">
                All nominations
            </a>
            <a href="{{ route('admin.nominations.index', array_filter(['tab' => 'interests', 'q' => $q])) }}"
               class="rounded-full px-4 py-2 transition {{ $tab === 'interests' ? 'bg-slate-900 text-white shadow' : 'text-slate-600 hover:text-slate-900' }}">
                Interested members
                <span class="ml-1 rounded-md bg-white/20 px-1.5 py-0.5 text-[10px] font-black tabular-nums">{{ $interestCount ?? 0 }}</span>
            </a>
        </div>
    </div>

    @if($tab === 'interests')
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-rose-50">
                        <tr>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Nomination</th>
                            <th class="px-4 py-3">Position</th>
                            <th class="px-4 py-3">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($interestEntries ?? [] as $entry)
                            <tr>
                                <td class="px-4 py-3 font-extrabold text-slate-800">{{ $entry->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-[11px] text-slate-600">
                                    <span class="block">{{ $entry->user->email ?? '—' }}</span>
                                    <span class="block text-slate-500">{{ $entry->user->mobile ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $entry->nomination->title ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $entry->position->position ?? '—' }}</td>
                                <td class="px-4 py-3 text-[11px] text-slate-600">{{ optional($entry->submitted_at)->format('d M Y h:i A') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 font-bold">No interest entries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($interestEntries) && $interestEntries->hasPages())
                <div class="mt-4">{{ $interestEntries->links() }}</div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-rose-50">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Polling</th>
                            <th class="px-4 py-3">Positions</th>
                            <th class="px-4 py-3">Entries</th>
                            <th class="px-4 py-3">Created On / By</th>
                            <th class="px-4 py-3">Display</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($nominations ?? [] as $nomination)
                            <tr>
                                <td class="px-4 py-3 font-extrabold text-slate-800">{{ $nomination->title }}</td>
                                <td class="px-4 py-3">
                                    <p>{{ optional($nomination->polling_date)->format('d M Y') }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $nomination->polling_from }} - {{ $nomination->polling_to }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $nomination->positions->count() }}</td>
                                <td class="px-4 py-3">{{ $nomination->entries_count }}</td>
                                <td class="px-4 py-3">
                                    <p>{{ $nomination->created_at->format('d M Y') }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $nomination->creator->name ?? 'Admin' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.nominations.toggle-status', $nomination->id) }}">
                                        @csrf
                                        <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $nomination->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $nomination->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $nomination->status === 'active' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $nomination->status === 'draft' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $nomination->status === 'closed' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $nomination->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : '' }}">
                                        {{ $nomination->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1.5 flex-wrap justify-end">
                                        <a href="{{ route('admin.nominations.show', $nomination->id) }}" title="View details (read-only)"
                                           class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.nominations.submissions', $nomination->id) }}" title="Interested members list"
                                           class="w-8 h-8 rounded-lg border border-indigo-100 bg-indigo-50 text-indigo-800 hover:bg-indigo-100 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.nominations.edit', $nomination->id) }}" title="Modify Nomination"
                                           class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" /></svg>
                                        </a>
                                        <a href="{{ route('admin.nominations.alert', $nomination->id) }}" title="Nomination Alert"
                                           class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-6 0v5m6 0H9" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.nominations.cancel', $nomination->id) }}" class="inline">
                                            @csrf
                                            <button title="Cancel Nomination" class="w-8 h-8 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </form>
                                        <form id="admin-delete-nomination-{{ $nomination->id }}" method="POST" action="{{ route('admin.nominations.destroy', $nomination->id) }}" class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" title="Delete Nomination"
                                                data-delete-form="admin-delete-nomination-{{ $nomination->id }}"
                                                data-delete-title="Delete this nomination?"
                                                data-delete-message="This will remove the nomination and related submissions from the system."
                                                onclick="adminOpenDeleteModalFromEl(this)"
                                                class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500 font-bold">No nominations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($nominations) && $nominations->hasPages())
                <div class="mt-4">{{ $nominations->links() }}</div>
            @endif
        </div>
    @endif
</div>
@endsection
