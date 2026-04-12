@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[24px] border border-white bg-linear-to-br from-white via-white to-indigo-50/40 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Pending Member Approvals</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">Members who completed their profile and are waiting for approval.</p>
            </div>
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name/email/mobile..."
                    class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20 w-64">
                <button class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-900">Requests</p>
                <p class="text-[11px] font-bold text-slate-500 mt-1">Total: {{ $members->total() }}</p>
            </div>
        </div>

        @if($members->count() === 0)
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No pending approvals</p>
                <p class="mt-1 text-xs font-bold text-slate-500">All completed member profiles are already approved.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="px-6 py-4">Member</th>
                            <th class="px-6 py-4">Contact</th>
                            <th class="px-6 py-4">Profile</th>
                            <th class="px-6 py-4 text-center w-14">View</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($members as $m)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center font-black text-indigo-700">
                                            {{ strtoupper(substr($m->name ?? 'ME', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-extrabold text-slate-900 truncate">{{ $m->name }}</p>
                                            <p class="text-[11px] font-bold text-slate-500 truncate">{{ $m->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[11px] font-bold text-slate-700">{{ $m->mobile ?? '—' }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">ID: {{ $m->id }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-xl border border-amber-100 bg-amber-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-amber-800">
                                        Completed • Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.members.pending-approvals.show', $m) }}"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-[#965995]/40 hover:bg-[#965995]/10 hover:text-[#351c42]"
                                       title="View full profile"
                                       aria-label="View full profile for {{ $m->name }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.members.pending-approvals.approve', $m->id) }}" onsubmit="return confirm('Approve this member? They will be able to sign in and purchase membership plans.');">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-extrabold shadow-lg transition-all">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.members.pending-approvals.reject', $m->id) }}">
                                            @csrf
                                            <button class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-extrabold shadow-sm transition-all">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-50">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

