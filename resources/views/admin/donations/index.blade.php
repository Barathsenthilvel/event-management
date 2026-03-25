@extends('admin.layouts.app')

@section('content')
<div class="h-full flex flex-col p-6 gap-4">
    <!-- Page Header -->
    <div>
        <h1 class="text-xl font-bold text-slate-900">Manage Donations</h1>
        <p class="text-xs text-slate-500 mt-1">Create and manage donation listings.</p>
    </div>

    <!-- Search + Add -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.donations.create') }}"
               class="bg-[#0f172a] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-bold transition-all shadow-md">
                + Add
            </a>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <div class="relative">
                <input
                    name="q"
                    type="text"
                    placeholder="Search"
                    value="{{ $q ?? '' }}"
                    class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 w-60"
                >
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
        </form>
    </div>

    <!-- Table -->
    <div class="flex-1 overflow-y-auto custom-scroll">
        <table class="min-w-full text-left text-xs">
            <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                <tr>
                    <th class="px-6 py-4">Purpose</th>
                    <th class="px-6 py-4">Job Info</th>
                    <th class="px-6 py-4 text-center">Promote Front</th>
                    <th class="px-6 py-4">Created On / By</th>
                    <th class="px-6 py-4">Last Updated</th>
                    <th class="px-6 py-4 text-center">Display Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($donations as $donation)
                <tr>
                    <td class="px-6 py-4 align-middle">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6L9 17l-5-5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold text-slate-900">{{ $donation->purpose }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="font-semibold">{{ $donation->short_description ?: '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        <form method="POST" action="{{ route('admin.donations.toggle-promote', $donation->id) }}" class="inline-flex">
                            @csrf
                            <button class="inline-flex items-center cursor-pointer" type="submit">
                                <span class="w-10 h-5 {{ $donation->promote_front ? 'bg-emerald-400/60' : 'bg-slate-300/70' }} rounded-full relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform
                                        {{ $donation->promote_front ? 'translate-x-5' : '' }}"></span>
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] font-bold text-slate-500">{{ $donation->created_at->format('d M Y') }}</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $donation->creator->name ?? 'Admin' }}</p>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] font-bold text-slate-500">{{ $donation->updated_at->format('d M Y') }}</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $donation->creator->name ?? 'Admin' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        <form method="POST" action="{{ route('admin.donations.toggle-status', $donation->id) }}" class="inline-flex">
                            @csrf
                            <button title="Toggle Display Status" type="submit" class="inline-flex items-center cursor-pointer">
                                <span class="w-10 h-5 {{ $donation->is_active ? 'bg-emerald-400/60' : 'bg-slate-300/70' }} rounded-full relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform
                                        {{ $donation->is_active ? 'translate-x-5' : '' }}"></span>
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right align-middle">
                        <div class="inline-flex items-center justify-end gap-2">
                            <a href="{{ route('admin.donations.edit', $donation->id) }}"
                               title="Modify"
                               class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.donations.toggle-status', $donation->id) }}" class="inline-flex">
                                @csrf
                                <button title="Toggle Status"
                                        class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center"
                                        type="submit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.donations.destroy', $donation->id) }}" class="inline-flex"
                                  onsubmit="return confirm('Delete this donation?')">
                                @csrf
                                @method('DELETE')
                                <button title="Delete"
                                        class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center"
                                        type="submit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-slate-500 font-semibold">
                        No donations found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $donations->links() }}
        </div>
    </div>
</div>
@endsection

