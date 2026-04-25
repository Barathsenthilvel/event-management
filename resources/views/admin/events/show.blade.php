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
                @if($event->status === 'completed')
                    <a href="{{ route('admin.events.album', $event->id) }}" class="px-4 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-xs font-extrabold text-emerald-700">Album</a>
                @endif
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                <h2 class="text-sm font-extrabold text-slate-900">Event Info</h2>
                <p class="text-sm text-slate-700">{{ $event->description ?: 'No description provided.' }}</p>
                <p class="text-xs font-bold text-slate-500">Venue: <span class="text-slate-700">{{ $event->venue ?: 'N/A' }}</span></p>
                <p class="text-xs font-bold text-slate-500">Seats: <span class="text-slate-700">{{ ucfirst($event->seat_mode) }} @if($event->seat_mode === 'limited') ({{ $event->seat_limit }}) @endif</span></p>
                <p class="text-xs font-bold text-slate-500">Interested (public): <span class="text-slate-700">{{ $event->interests->count() }}</span></p>
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
            <h2 class="text-sm font-extrabold text-slate-900 mb-1">Interested (public)</h2>
            <p class="text-xs font-bold text-slate-500 mb-3">People who registered interest from the events page ({{ $allInterests->count() }}).</p>
            <div class="mb-4 inline-flex items-center gap-1 rounded-full bg-slate-100 p-1">
                <a href="{{ route('admin.events.show', ['event' => $event->id, 'interest_type' => 'all']) }}"
                   class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[11px] font-black uppercase tracking-wider transition {{ $interestType === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>All</span>
                    <span class="text-[10px]">{{ $allInterests->count() }}</span>
                </a>
                <a href="{{ route('admin.events.show', ['event' => $event->id, 'interest_type' => 'members']) }}"
                   class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[11px] font-black uppercase tracking-wider transition {{ $interestType === 'members' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Members</span>
                    <span class="text-[10px]">{{ $memberInterests->count() }}</span>
                </a>
                <a href="{{ route('admin.events.show', ['event' => $event->id, 'interest_type' => 'non_members']) }}"
                   class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[11px] font-black uppercase tracking-wider transition {{ $interestType === 'non_members' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Guest</span>
                    <span class="text-[10px]">{{ $nonMemberInterests->count() }}</span>
                </a>
            </div>
            @if($filteredInterests->count() === 0)
                <p class="text-sm text-slate-500">No interest registrations yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                            <tr>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Member ID</th>
                                <th class="px-4 py-3">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($filteredInterests as $row)
                                <tr>
                                    <td class="px-4 py-3">
                                        @if($row->user_id)
                                            <span class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-black uppercase text-indigo-700">Member</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-black uppercase text-amber-800">Guest</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $row->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row->email }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row->phone }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row->user_id ? '#' . $row->user_id : '—' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row->created_at?->format('d M Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @php
            $canMarkAttendance = in_array($event->status, ['live', 'completed'], true);
            $guestAttendanceEnabled = \Illuminate\Support\Facades\Schema::hasColumn('event_interests', 'participation_status');
        @endphp

        @if($guestAttendanceEnabled && $event->interests->count() > 0)
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-slate-900 mb-1">Public registrations — attendance</h2>
                <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                    Non-member interest from the website. Mark <strong>Attended</strong> for certificate eligibility (same template PDF as members).
                </p>
                @if(!$canMarkAttendance && $event->status !== 'cancelled')
                    <p class="text-xs font-bold text-amber-800 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 mb-3">
                        Attendance unlocks when the event is <strong>Live</strong> or <strong>Completed</strong>.
                    </p>
                @endif
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email / Phone</th>
                                <th class="px-4 py-3">Attendance</th>
                                <th class="px-4 py-3 text-right">Certificate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($event->interests as $row)
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $row->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">
                                        <p>{{ $row->email }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $row->phone ?? '—' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.events.interests.attendance', [$event->id, $row->id]) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <select name="participation_status"
                                                class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                @disabled(!$canMarkAttendance)>
                                                <option value="interested" {{ ($row->participation_status ?? 'interested') === 'interested' ? 'selected' : '' }}>Interested</option>
                                                <option value="participated" {{ ($row->participation_status ?? '') === 'participated' ? 'selected' : '' }}>Attended</option>
                                                <option value="not_participated" {{ ($row->participation_status ?? '') === 'not_participated' ? 'selected' : '' }}>Did not attend</option>
                                            </select>
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white text-[11px] font-extrabold disabled:opacity-50" @disabled(!$canMarkAttendance)>Save</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if(($row->participation_status ?? '') === 'participated')
                                            <a href="{{ route('admin.events.interests.certificate', [$event->id, $row->id]) }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-extrabold text-white hover:bg-emerald-700">Certificate PDF</a>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div id="event-member-attendance" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm scroll-mt-6">
            <h2 class="text-sm font-extrabold text-slate-900 mb-1">Invited &amp; interested members</h2>
            <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                When the event is <strong class="text-slate-800">Live</strong> or <strong class="text-slate-800">Completed</strong>, set <strong>Attended</strong> or <strong>Did not attend</strong> for each member.
                Members can <strong>download the certificate</strong> after they are marked <strong>Attended</strong> and you upload the <strong>template PDF</strong> on <a href="{{ route('admin.events.edit', $event) }}" class="font-bold text-indigo-600 hover:underline">Edit event</a>.
            </p>
            @if(!$canMarkAttendance && $event->status !== 'cancelled')
                <p class="text-xs font-bold text-amber-800 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 mb-3">
                    Attendance fields unlock when you set this event to <strong>Live</strong> (or after it is <strong>Completed</strong>).
                </p>
            @endif
            @if($event->invites->count() === 0)
                <p class="text-sm text-slate-500">No invites sent yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                            <tr>
                                <th class="px-4 py-3">Member</th>
                                <th class="px-4 py-3">Contact</th>
                                <th class="px-4 py-3">Attendance</th>
                                <th class="px-4 py-3">Invited At</th>
                                <th class="px-4 py-3 text-right">Certificate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($event->invites as $invite)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-bold text-slate-800">{{ $invite->user->name ?? 'User' }}</p>
                                        <p class="text-[11px] text-slate-500">Member Code: {{ $invite->user_id }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        <p>{{ $invite->user->email ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $invite->user->mobile ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        <form method="POST" action="{{ route('admin.events.invites.status', [$event->id, $invite->id]) }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="participation_status"
                                                class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                @disabled(!$canMarkAttendance)>
                                                <option value="interested" {{ $invite->participation_status === 'interested' ? 'selected' : '' }}>Interested (registered)</option>
                                                <option value="participated" {{ $invite->participation_status === 'participated' ? 'selected' : '' }}>Attended (certificate eligible)</option>
                                                <option value="not_participated" {{ $invite->participation_status === 'not_participated' ? 'selected' : '' }}>Did not attend</option>
                                            </select>
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white text-[11px] font-extrabold disabled:opacity-50"
                                                @disabled(!$canMarkAttendance)>
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $invite->invited_at?->format('d M Y h:i A') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if($invite->participation_status === 'participated')
                                            <a href="{{ route('admin.events.invites.certificate', [$event->id, $invite->id]) }}"
                                               class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-extrabold text-white hover:bg-emerald-700">
                                                Certificate PDF
                                            </a>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">N/A</span>
                                        @endif
                                    </td>
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
