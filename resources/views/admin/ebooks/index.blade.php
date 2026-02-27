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
        <div class="flex items-center gap-2">
            <button class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M7 7h10M7 12h6M7 17h3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="relative">
                <input
                    type="text"
                    placeholder="Search"
                    class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 w-60"
                >
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="flex-1 overflow-y-auto custom-scroll">
        <table class="w-full text-left border-separate border-spacing-y-2">
            <thead>
            <tr class="text-[11px] font-bold text-slate-600 uppercase tracking-widest bg-rose-50">
                <th class="px-6 py-3 rounded-l-xl">Hospital</th>
                <th class="px-6 py-3">Job Info</th>
                <th class="px-6 py-3 text-center">Promote Front</th>
                <th class="px-6 py-3">Created On / By</th>
                <th class="px-6 py-3">Last Updated</th>
                <th class="px-6 py-3 text-center">Display Status</th>
                <th class="px-6 py-3 text-right rounded-r-xl">Action</th>
            </tr>
            </thead>
            <tbody class="text-xs text-slate-700">
            @foreach($ebooks as $ebook)
                <tr class="bg-white shadow-sm rounded-2xl">
                    <td class="px-6 py-3 rounded-l-2xl align-middle">
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
                                <p class="text-[11px] font-semibold text-slate-600">{{ $ebook['hospital'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3 align-middle">
                        <p class="font-semibold">{{ $ebook['title'] }}</p>
                        <p class="text-[11px] text-slate-400">{{ $ebook['code'] }}</p>
                    </td>
                    <td class="px-6 py-3 text-center align-middle">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only" {{ $ebook['promote_front'] ? 'checked' : '' }}>
                            <span class="w-10 h-5 bg-emerald-400/60 rounded-full relative shadow-inner">
                                <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform
                                    {{ $ebook['promote_front'] ? 'translate-x-5' : '' }}"></span>
                            </span>
                        </label>
                    </td>
                    <td class="px-6 py-3 align-middle">
                        <p class="text-[11px] text-slate-500">Date</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $ebook['created_by'] }}</p>
                    </td>
                    <td class="px-6 py-3 align-middle">
                        <p class="text-[11px] text-slate-500">Date</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $ebook['updated_by'] }}</p>
                    </td>
                    <td class="px-6 py-3 text-center align-middle">
                        @if($ebook['status'] === 'active')
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
                    <td class="px-6 py-3 text-right rounded-r-2xl align-middle">
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                    class="p-2 rounded-full hover:bg-slate-100 text-slate-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="5" cy="12" r="1.5" />
                                    <circle cx="12" cy="12" r="1.5" />
                                    <circle cx="19" cy="12" r="1.5" />
                                </svg>
                            </button>
                            <div
                                x-show="open"
                                @click.away="open = false"
                                x-transition
                                class="origin-top-right absolute right-0 mt-2 w-40 rounded-xl shadow-lg bg-white ring-1 ring-black/5 z-10 text-xs">
                                <button class="block w-full text-left px-4 py-2 hover:bg-slate-50">Modify</button>
                                <button class="block w-full text-left px-4 py-2 hover:bg-slate-50">Delete List</button>
                                <button class="block w-full text-left px-4 py-2 hover:bg-slate-50">Update Status</button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

