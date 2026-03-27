@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Events</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Manage events and quick actions including Invite Members.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search events..."
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-56 outline-none focus:ring-2 focus:ring-indigo-200">
                    <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
                </form>
                <a href="{{ route('admin.events.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">+ Create Event</a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($events->count() === 0)
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No events found</p>
                <p class="text-xs font-bold text-slate-500 mt-1">Start by creating your first event.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                        <tr>
                            <th class="px-5 py-4">Event</th>
                            <th class="px-5 py-4">Seat Counts</th>
                            <th class="px-5 py-4">Promote Front</th>
                            <th class="px-5 py-4">Created</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Display</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($events as $event)
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 text-sm font-black flex items-center justify-center">
                                            {{ strtoupper(substr($event->title, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-extrabold text-slate-900 truncate">{{ $event->title }}</p>
                                            <p class="text-[11px] font-bold text-slate-500">
                                                {{ optional($event->dates->first())->event_date?->format('d M Y') ?? 'No date' }}
                                                @if($event->venue)
                                                    • {{ $event->venue }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700">
                                        {{ ucfirst($event->seat_mode) }}
                                        @if($event->seat_mode === 'limited')
                                            ({{ $event->interested_count }} / {{ $event->seat_limit }})
                                        @else
                                            ({{ $event->interested_count }} interested)
                                        @endif
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <form method="POST" action="{{ route('admin.events.toggle-promote', $event->id) }}">
                                        @csrf
                                        <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $event->promote_front ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $event->promote_front ? 'ON' : 'OFF' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700">{{ $event->created_at->format('d M Y') }}</p>
                                    <p class="text-[10px] font-bold text-slate-500">By {{ $event->creator->name ?? 'Admin' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $event->status === 'live' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $event->status === 'upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $event->status === 'completed' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $event->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : '' }}">
                                        {{ $event->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <form method="POST" action="{{ route('admin.events.toggle-display', $event->id) }}">
                                        @csrf
                                        <button class="px-3 py-1 rounded-full text-[10px] font-black {{ $event->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $event->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        <a href="{{ route('admin.events.show', $event->id) }}"
                                            title="More Details"
                                            class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event->id) }}"
                                            title="Modify Event"
                                            class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 3h12M6 9l1 10h10l1-10M9 9V7a3 3 0 016 0v2" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.events.invite', $event->id) }}"
                                            title="Invite Members"
                                            class="w-8 h-8 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-6 0v5m6 0H9" />
                                            </svg>
                                        </a>
                                        @if($event->status === 'completed')
                                            <a href="{{ route('admin.events.album', $event->id) }}"
                                                title="Add Event Album"
                                                class="w-8 h-8 rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-50 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.events.send-reminder', $event->id) }}">
                                            @csrf
                                            <button title="Send Reminder" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" />
                                                </svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.events.cancel', $event->id) }}">
                                            @csrf
                                            <button title="Cancel Event" class="w-8 h-8 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('Delete this event?')">
                                            @csrf
                                            @method('DELETE')
                                            <button title="Delete Event" class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12H6L5 7m3 0V5a1 1 0 011-1h6a1 1 0 011 1v2M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-100">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
