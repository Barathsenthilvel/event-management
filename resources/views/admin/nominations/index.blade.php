@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manage Nominations</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Create and run nomination polls.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full md:w-auto md:flex-1 md:max-w-3xl">
                <form method="GET" class="flex items-center gap-2 w-full sm:max-w-md min-w-0">
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
    </div>

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
                    @forelse($nominations as $nomination)
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
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.nominations.submissions', $nomination->id) }}" title="More Details"
                                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.nominations.edit', $nomination->id) }}" title="Modify Nomination"
                                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" /></svg>
                                    </a>
                                    <a href="{{ route('admin.nominations.alert', $nomination->id) }}" title="Nomination Alert"
                                       class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-6 0v5m6 0H9" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.nominations.cancel', $nomination->id) }}">
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
        <div class="mt-4">{{ $nominations->links() }}</div>
    </div>
</div>
@endsection

