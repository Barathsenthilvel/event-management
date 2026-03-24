@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ $event->title }}</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">{{ ucfirst($event->status) }} • {{ $event->is_active ? 'Active' : 'Inactive' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.events.edit', $event->id) }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Edit</a>
                <a href="{{ route('admin.events.invite', $event->id) }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-xs font-extrabold text-white">Invite Members</a>
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                <h2 class="text-sm font-extrabold text-slate-900">Event Info</h2>
                <p class="text-sm text-slate-700">{{ $event->description ?: 'No description provided.' }}</p>
                <p class="text-xs font-bold text-slate-500">Venue: <span class="text-slate-700">{{ $event->venue ?: 'N/A' }}</span></p>
                <p class="text-xs font-bold text-slate-500">Seats: <span class="text-slate-700">{{ ucfirst($event->seat_mode) }} @if($event->seat_mode === 'limited') ({{ $event->seat_limit }}) @endif</span></p>
                <p class="text-xs font-bold text-slate-500">Interested: <span class="text-slate-700">{{ $event->interested_count }}</span></p>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                <h2 class="text-sm font-extrabold text-slate-900">Event Dates</h2>
                @forelse($event->dates as $d)
                    <div class="p-3 rounded-xl border border-slate-100">
                        <p class="text-sm font-bold text-slate-800">{{ $d->event_date?->format('d M Y') }}</p>
                        <p class="text-xs font-bold text-slate-500">
                            {{ $d->start_time ? \Illuminate\Support\Carbon::parse($d->start_time)->format('h:i A') : '--' }}
                            to
                            {{ $d->end_time ? \Illuminate\Support\Carbon::parse($d->end_time)->format('h:i A') : '--' }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No dates added.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-900 mb-3">Invited Members</h2>
            @if($event->invites->count() === 0)
                <p class="text-sm text-slate-500">No invites sent yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                            <tr>
                                <th class="px-4 py-3">Member</th>
                                <th class="px-4 py-3">Contact</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Invited At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($event->invites as $invite)
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $invite->user->name ?? 'User' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $invite->user->email ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ str_replace('_', ' ', $invite->participation_status) }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $invite->invited_at?->format('d M Y h:i A') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
