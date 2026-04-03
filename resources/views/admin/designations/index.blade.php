@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[24px] border border-white bg-linear-to-br from-white via-white to-indigo-50/40 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Member designations</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">Create titles (e.g. Secretary, Treasurer) and assign them to members from the Members list.</p>
            </div>
            <a href="{{ route('admin.members.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                Members
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <p class="text-xs font-extrabold text-slate-900">Add designation</p>
            <p class="text-[11px] font-bold text-slate-500 mt-1">Each name must be unique.</p>
        </div>
        <form method="POST" action="{{ route('admin.designations.store') }}" class="p-6 pt-2 flex flex-col sm:flex-row flex-wrap items-end gap-4">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label for="designation_name" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Name</label>
                <input id="designation_name" name="name" value="{{ old('name') }}" required maxlength="255"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20 @error('name') border-rose-300 @enderror"
                    placeholder="e.g. General Secretary">
                @error('name')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="w-full sm:w-28">
                <label for="designation_sort" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Sort</label>
                <input id="designation_sort" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', 0) }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <button type="submit"
                class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all">
                Create
            </button>
        </form>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-900">All designations</p>
                <p class="text-[11px] font-bold text-slate-500 mt-1">Total: {{ $designations->count() }}</p>
            </div>
        </div>

        @if($designations->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No designations yet</p>
                <p class="mt-1 text-xs font-bold text-slate-500">Add one above, then assign members under Members.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4 text-center">Sort</th>
                            <th class="px-6 py-4 text-center">Members</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($designations as $d)
                            <tr>
                                <td class="px-6 py-4 font-extrabold text-slate-900">{{ $d->name }}</td>
                                <td class="px-6 py-4 text-center text-[11px] font-bold text-slate-600">{{ $d->sort_order }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex min-w-8 justify-center rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-black text-slate-700">{{ $d->users_count }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.designations.edit', $d) }}"
                                           class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-extrabold shadow-sm transition-all">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.designations.destroy', $d) }}" class="inline"
                                              onsubmit="return confirm('Remove this designation? Members using it will have no designation.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-4 py-2 rounded-xl border border-rose-100 bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-extrabold transition-all">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
