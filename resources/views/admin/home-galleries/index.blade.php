@extends('admin.layouts.app')

@section('content')
<div class="flex-1 min-h-0 overflow-y-auto custom-scroll p-6 space-y-4">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Homepage Galleries</h1>
        <p class="text-xs text-slate-500 mt-1">Manage gallery section content and gallery cards.</p>
    </div>

    <form method="POST" action="{{ route('admin.home-galleries.section.update') }}" class="bg-white rounded-2xl border border-slate-100 px-6 py-5">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-extrabold text-slate-800">Gallery Section Fields (3)</h2>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Save Section</button>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Section badge</label>
                <input type="text" name="section_badge" value="{{ old('section_badge', $section?->section_badge ?? 'Impact in pictures') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                @error('section_badge')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Section title</label>
                <input type="text" name="section_title" value="{{ old('section_title', $section?->section_title ?? 'Our gallery') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                @error('section_title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-700 mb-2">Section description</label>
                <textarea name="section_description" rows="3"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('section_description', $section?->section_description ?? 'Field moments from Aminjikarai and across our programs—outreach, learning spaces, and celebrations with the communities we serve.') }}</textarea>
                @error('section_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex items-center gap-2 w-full sm:max-w-md min-w-0">
            <div class="relative flex-1 min-w-0">
                <input
                    name="q"
                    type="search"
                    placeholder="Search gallery items"
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
            <a href="{{ route('admin.home-galleries.create') }}"
               class="inline-flex bg-[#0f172a] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-bold transition-all shadow-md">
                + Add Gallery Item
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
        <table class="min-w-full table-fixed text-left text-xs">
            <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                <tr>
                    <th class="px-6 py-4 w-[7rem]">Preview</th>
                    <th class="px-6 py-4 w-[32%] min-w-[10rem]">Title</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4 text-center">Sort</th>
                    <th class="px-6 py-4">Updated</th>
                    <th class="px-6 py-4 text-center">Display</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($items as $group)
                @php
                    $primary = $group->primary;
                    $groupItems = $group->items;
                @endphp
                <tr>
                    <td class="px-6 py-4 align-middle">
                        @if($group->count > 1)
                            <div class="flex flex-wrap gap-1.5 max-w-[10rem]">
                                @foreach($groupItems->take(4) as $thumb)
                                    <img src="{{ asset('storage/' . ltrim((string) $thumb->image_path, '/')) }}"
                                         alt="" class="h-12 w-12 rounded-lg border border-slate-200 object-cover">
                                @endforeach
                                @if($group->count > 4)
                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[10px] font-bold text-slate-500">+{{ $group->count - 4 }}</span>
                                @endif
                            </div>
                        @else
                            <img src="{{ asset('storage/' . ltrim((string) $primary->image_path, '/')) }}"
                                 alt="" class="h-14 w-24 rounded-lg border border-slate-200 object-cover">
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle max-w-0">
                        <p class="text-[11px] font-semibold text-slate-900 line-clamp-2 break-words">{{ $primary->title }}</p>
                        @if($group->count > 1)
                            <p class="mt-1 inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700">{{ $group->count }} images in one upload</p>
                        @endif
                        @if(filled($primary->description_text))
                            <p class="admin-gallery-desc text-[10px] text-slate-500 mt-1 leading-snug">{{ $primary->description_text }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] font-semibold text-slate-700">Category: {{ ucfirst($primary->category_key) }}</p>
                        <p class="text-[11px] font-semibold text-slate-700">Layout: {{ ucfirst($primary->layout_type) }}</p>
                        @if($primary->is_category_primary)
                            <span class="mt-1 inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Category main (homepage)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle text-center font-bold text-indigo-600">{{ $primary->sort_order }}</td>
                    <td class="px-6 py-4 align-middle">
                        <p class="text-[11px] font-bold text-slate-500">{{ $primary->updated_at?->format('d M Y') }}</p>
                        <p class="font-semibold text-[11px] text-slate-700">{{ $primary->creator->name ?? 'Admin' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        <form method="POST" action="{{ route('admin.home-galleries.toggle-status', $primary->id) }}" class="inline-flex">
                            @csrf
                            <button type="submit" class="inline-flex items-center cursor-pointer" title="Toggle Display Status">
                                <span class="w-10 h-5 {{ $primary->is_active ? 'bg-emerald-400/60' : 'bg-slate-300/70' }} rounded-full relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform {{ $primary->is_active ? 'translate-x-5' : '' }}"></span>
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right align-middle">
                        <x-admin.row-actions>
                            <x-slot:primary>
                                <a href="{{ route('admin.home-galleries.edit', $primary->id) }}" title="Edit"
                                    class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" />
                                    </svg>
                                </a>
                                <form id="admin-delete-home-gallery-{{ $primary->id }}" method="POST" action="{{ route('admin.home-galleries.destroy', $primary->id) }}" class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        data-delete-form="admin-delete-home-gallery-{{ $primary->id }}"
                                        data-delete-title="{{ $group->count > 1 ? 'Delete this upload (' . $group->count . ' images)?' : 'Delete this gallery item?' }}"
                                        data-delete-message="{{ $group->count > 1 ? 'All ' . $group->count . ' images from this upload will be removed from the website and storage.' : 'This will permanently remove the gallery card and image from storage.' }}"
                                        onclick="adminOpenDeleteModalFromEl(this)"
                                        class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </x-slot:primary>
                        </x-admin.row-actions>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-slate-500 font-semibold">
                        No home gallery items found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $items->links() }}
        </div>
    </div>
</div>

<style>
    .admin-gallery-desc {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        overflow: hidden;
        word-break: break-all;
        overflow-wrap: anywhere;
    }
</style>
@endsection
