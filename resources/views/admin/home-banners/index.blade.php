@extends('admin.layouts.app')

@section('content')
<div class="h-full flex flex-col p-6 gap-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Homepage Banners</h1>
        <p class="text-xs text-slate-500 mt-1">Add and manage banner slides shown on the home page.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex items-center gap-2 w-full sm:max-w-md min-w-0">
            <div class="relative flex-1 min-w-0">
                <input
                    name="q"
                    type="search"
                    placeholder="Search banners"
                    value="{{ $q ?? '' }}"
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-xs outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
        </form>
        <div class="flex shrink-0 justify-end">
            <a href="{{ route('admin.home-banners.create') }}"
               class="inline-flex bg-[#0f172a] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-bold transition-all shadow-md">
                + Add Banner
            </a>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto custom-scroll">
        <table class="min-w-full text-left text-xs">
            <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                <tr>
                    <th class="px-6 py-4">Preview</th>
                    <th class="px-6 py-4">Banner Info</th>
                    <th class="px-6 py-4">Link</th>
                    <th class="px-6 py-4 text-center">Sort</th>
                    <th class="px-6 py-4">Updated</th>
                    <th class="px-6 py-4 text-center">Display</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($banners as $banner)
                <tr>
                    <td class="px-6 py-4 align-middle">
                        <img
                            src="{{ asset('storage/' . ltrim((string) $banner->image_path, '/')) }}"
                            alt=""
                            class="h-14 w-24 rounded-lg border border-slate-200 object-cover"
                        >
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] font-semibold text-slate-900">{{ $banner->title ?: ($banner->caption_title ?: 'Untitled banner') }}</p>
                        @if($banner->eyebrow)
                            <p class="text-[10px] mt-1 font-bold uppercase tracking-wide text-indigo-600">{{ $banner->eyebrow }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="font-semibold text-[11px] text-slate-700">{{ $banner->link_url ?: '#' }}</p>
                    </td>
                    <td class="px-6 py-4 align-middle text-center font-bold text-indigo-600">
                        {{ $banner->sort_order }}
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] font-bold text-slate-500">{{ $banner->updated_at?->format('d M Y') }}</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $banner->creator->name ?? 'Admin' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        <form method="POST" action="{{ route('admin.home-banners.toggle-status', $banner->id) }}" class="inline-flex">
                            @csrf
                            <button title="Toggle Display Status" type="submit" class="inline-flex items-center cursor-pointer">
                                <span class="w-10 h-5 {{ $banner->is_active ? 'bg-emerald-400/60' : 'bg-slate-300/70' }} rounded-full relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform {{ $banner->is_active ? 'translate-x-5' : '' }}"></span>
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right align-middle">
                        <div class="inline-flex items-center justify-end gap-2">
                            <a href="{{ route('admin.home-banners.edit', $banner->id) }}"
                               title="Modify"
                               class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" />
                                </svg>
                            </a>
                            <form id="admin-delete-home-banner-{{ $banner->id }}" method="POST" action="{{ route('admin.home-banners.destroy', $banner->id) }}" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="button" title="Delete"
                                        data-delete-form="admin-delete-home-banner-{{ $banner->id }}"
                                        data-delete-title="Delete this banner?"
                                        data-delete-message="This will permanently remove the banner and image from storage."
                                        onclick="adminOpenDeleteModalFromEl(this)"
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
                    <td colspan="7" class="px-6 py-10 text-center text-slate-500 font-semibold">
                        No homepage banners found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $banners->links() }}
        </div>
    </div>
</div>
@endsection
