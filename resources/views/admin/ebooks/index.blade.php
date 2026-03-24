@extends('admin.layouts.app')

@section('content')
<div class="h-full flex flex-col p-6 gap-4">
    <!-- Page Header -->
    <div>
        <h1 class="text-xl font-bold text-slate-900">Manage E-Books</h1>
        <p class="text-xs text-slate-500 mt-1">Home / <span class="font-semibold text-indigo-600">E-Books</span></p>
    </div>

    <!-- Search + Add -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebooks.create') }}"
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
                    <th class="px-6 py-4">Hospital</th>
                    <th class="px-6 py-4">E-Book Info</th>
                    <th class="px-6 py-4 text-center">Promote Front</th>
                    <th class="px-6 py-4">Created On / By</th>
                    <th class="px-6 py-4">Last Updated</th>
                    <th class="px-6 py-4 text-center">Display Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($ebooks as $ebook)
                <tr>
                    <td class="px-6 py-4 align-middle">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                     viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
                                    <path d="M10 11l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold text-slate-600">{{ $ebook->hospital ?: '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="font-semibold">{{ $ebook->title }}</p>
                        <p class="text-[11px] text-slate-400">{{ $ebook->code }}</p>
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        <form method="POST" action="{{ route('admin.ebooks.toggle-promote', $ebook->id) }}" class="inline-flex">
                            @csrf
                            <button class="inline-flex items-center cursor-pointer">
                                <span class="w-10 h-5 {{ $ebook->promote_front ? 'bg-emerald-400/60' : 'bg-slate-300/70' }} rounded-full relative shadow-inner">
                                <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform
                                    {{ $ebook->promote_front ? 'translate-x-5' : '' }}"></span>
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] text-slate-500">{{ $ebook->created_at->format('d M Y') }}</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $ebook->creator->name ?? 'Admin' }}</p>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] text-slate-500">{{ $ebook->updated_at->format('d M Y') }}</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $ebook->creator->name ?? 'Admin' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        @if($ebook->is_active)
                            <span
                                class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-emerald-500 text-white text-[11px] font-semibold">
                                Active
                            </span>
                        @else
                            <span
                                class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-rose-500 text-white text-[11px] font-semibold">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right align-middle">
                        <div class="inline-flex items-center justify-end gap-2">
                            <a href="{{ route('admin.ebooks.edit', $ebook->id) }}"
                               title="Modify"
                               class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.ebooks.toggle-status', $ebook->id) }}" class="inline-flex">
                                @csrf
                                <button title="Toggle Status"
                                    class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.ebooks.destroy', $ebook->id) }}" class="inline-flex" onsubmit="return confirm('Delete this e-book?')">
                                @csrf
                                @method('DELETE')
                                <button title="Delete"
                                    class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
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
                    <td colspan="7" class="px-6 py-10 text-center text-slate-500 font-semibold">No e-books found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $ebooks->links() }}
        </div>
    </div>
</div>
@endsection

