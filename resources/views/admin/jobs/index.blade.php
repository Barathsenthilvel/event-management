@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manage Jobs</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Home / Jobs</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <form method="GET" class="flex w-full min-w-0 flex-wrap items-center gap-2 sm:max-w-3xl">
                <div class="relative flex-1 min-w-0">
                    <input type="search" name="q" value="{{ $q }}" placeholder="Search"
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select name="hospital" class="min-w-[13rem] rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">All hospitals</option>
                    @foreach(($hospitalSuggestions ?? []) as $hospitalOption)
                        <option value="{{ $hospitalOption }}" {{ ($hospital ?? '') === $hospitalOption ? 'selected' : '' }}>{{ $hospitalOption }}</option>
                    @endforeach
                </select>
                <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
                @if(($q ?? '') !== '' || ($hospital ?? '') !== '')
                    <a href="{{ route('admin.jobs.index') }}" class="shrink-0 px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-extrabold text-slate-700">Reset</a>
                @endif
            </form>
            <div class="flex shrink-0 justify-end">
                <a href="{{ route('admin.jobs.create') }}" class="inline-flex px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">+ Add</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-rose-50">
                    <tr>
                        <th class="px-4 py-3">Hospital</th>
                        <th class="px-4 py-3">Job Info</th>
                        <th class="px-4 py-3">Applied</th>
                        <th class="px-4 py-3">Promote Front</th>
                        <th class="px-4 py-3">Created On / By</th>
                        <th class="px-4 py-3">Last Updated</th>
                        <th class="px-4 py-3">Listing Status</th>
                        <th class="px-4 py-3">Display Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jobs as $job)
                        <tr>
                            <td class="px-4 py-3">{{ $job->hospital ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <p class="font-extrabold text-slate-800">{{ $job->title }}</p>
                                <p class="text-[11px] text-slate-500">{{ $job->code }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $job->applications_count }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.jobs.toggle-promote', $job->id) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $job->promote_front ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $job->promote_front ? 'ON' : 'OFF' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $job->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500">{{ $job->creator->name ?? 'Admin' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $job->updated_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500">{{ $job->creator->name ?? 'Admin' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.jobs.toggle-listing', $job->id) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $job->listing_status === 'listed' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($job->listing_status) }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.jobs.toggle-status', $job->id) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $job->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $job->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.jobs.applications', $job->id) }}" title="More Details"
                                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.jobs.edit', $job->id) }}" title="Modify Job"
                                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" /></svg>
                                    </a>
                                    <a href="{{ route('admin.jobs.alert', $job->id) }}" title="Invite / Alert Members"
                                       class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-6 0v5m6 0H9" /></svg>
                                    </a>
                                    <form id="admin-delete-job-{{ $job->id }}" method="POST" action="{{ route('admin.jobs.destroy', $job->id) }}" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" title="Delete Job"
                                            data-delete-form="admin-delete-job-{{ $job->id }}"
                                            data-delete-title="Delete this job listing?"
                                            data-delete-message="Applications linked to this job may become inaccessible."
                                            onclick="adminOpenDeleteModalFromEl(this)"
                                            class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-500 font-bold">No jobs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection

