@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manage Polling</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Home / Polling</p>
            </div>
            <a href="{{ route('admin.pollings.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">+ Add</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
        <div class="flex items-center justify-end mb-3">
            <form method="GET">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-56 outline-none focus:ring-2 focus:ring-indigo-200">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-rose-50">
                    <tr>
                        <th class="px-4 py-3">Polling Title</th>
                        <th class="px-4 py-3">Voting Counts</th>
                        <th class="px-4 py-3">Promote Front</th>
                        <th class="px-4 py-3">Open & Closes On</th>
                        <th class="px-4 py-3">Last Updated</th>
                        <th class="px-4 py-3">Publish Status</th>
                        <th class="px-4 py-3">Polling Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pollings as $polling)
                        <tr>
                            <td class="px-4 py-3">{{ $polling->title }}</td>
                            <td class="px-4 py-3">{{ $polling->votes_count }} Votes</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.pollings.toggle-promote', $polling->id) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $polling->promote_front ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $polling->promote_front ? 'ON' : 'OFF' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ optional($polling->polling_date)->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500">{{ $polling->polling_from }} - {{ $polling->polling_to }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $polling->updated_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500">{{ $polling->creator->name ?? 'Admin' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ strtoupper($polling->publish_status === 'na' ? 'N/A' : $polling->publish_status) }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.pollings.toggle-status', $polling->id) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $polling->polling_status === 'live' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $polling->polling_status === 'live' ? 'In Live' : 'Ends' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.pollings.edit', $polling->id) }}" title="Modify"
                                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" /></svg>
                                    </a>
                                    <a href="{{ route('admin.pollings.stats', $polling->id) }}" title="View Stats"
                                       class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.pollings.destroy', $polling->id) }}" onsubmit="return confirm('Delete this polling?')">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Delete List" class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500 font-bold">No pollings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pollings->links() }}</div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manage Polling</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Home / Polling</p>
            </div>
            <a href="{{ route('admin.pollings.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">+ Add</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
        <div class="flex items-center justify-end mb-3">
            <form method="GET">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-56 outline-none focus:ring-2 focus:ring-indigo-200">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-rose-50">
                    <tr>
                        <th class="px-4 py-3">Polling Title</th>
                        <th class="px-4 py-3">Voting Counts</th>
                        <th class="px-4 py-3">Promote Front</th>
                        <th class="px-4 py-3">Open & Closes On</th>
                        <th class="px-4 py-3">Last Updated</th>
                        <th class="px-4 py-3">Publish Status</th>
                        <th class="px-4 py-3">Polling Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pollings as $polling)
                        <tr>
                            <td class="px-4 py-3">{{ $polling->title }}</td>
                            <td class="px-4 py-3">{{ $polling->votes_count }} Votes</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.pollings.toggle-promote', $polling->id) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $polling->promote_front ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $polling->promote_front ? 'ON' : 'OFF' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ optional($polling->polling_date)->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500">{{ $polling->polling_from }} - {{ $polling->polling_to }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $polling->updated_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500">{{ $polling->creator->name ?? 'Admin' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ strtoupper($polling->publish_status === 'na' ? 'N/A' : $polling->publish_status) }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.pollings.toggle-status', $polling->id) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $polling->polling_status === 'live' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $polling->polling_status === 'live' ? 'In Live' : 'Ends' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.pollings.edit', $polling->id) }}" title="Modify"
                                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" /></svg>
                                    </a>
                                    <a href="{{ route('admin.pollings.stats', $polling->id) }}" title="View Stats"
                                       class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.pollings.destroy', $polling->id) }}" onsubmit="return confirm('Delete this polling?')">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Delete List" class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500 font-bold">No pollings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pollings->links() }}</div>
    </div>
</div>
@endsection

