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
                <a href="{{ route('admin.events.attendance.scanner', $event->id) }}" class="px-4 py-2 rounded-xl border border-indigo-300 bg-indigo-50 text-xs font-extrabold text-indigo-700">QR Scanner</a>
                @if($event->status !== 'cancelled')
                    <a href="{{ route('admin.events.album', $event->id) }}" class="px-4 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-xs font-extrabold text-emerald-700">Album</a>
                @endif
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="min-w-0 bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                <h2 class="text-sm font-extrabold text-slate-900">Event Info</h2>
                <p class="text-sm text-slate-700 break-words [overflow-wrap:anywhere] max-w-full">{{ $event->description ?: 'No description provided.' }}</p>
                <p class="text-xs font-bold text-slate-500">Venue: <span class="text-slate-700 break-words [overflow-wrap:anywhere]">{{ $event->venue ?: 'N/A' }}</span></p>
                <p class="text-xs font-bold text-slate-500">Seats: <span class="text-slate-700">{{ ucfirst($event->seat_mode) }} @if($event->seat_mode === 'limited') ({{ $event->seat_limit }}) @endif</span></p>
                @php
                    $memberInterestedCount = (int) $event->invites
                        ->where('has_confirmed_interest', true)
                        ->whereIn('participation_status', ['interested', 'participated'])
                        ->count();
                    $memberParticipatedCount = (int) $event->invites->where('participation_status', 'participated')->count();
                    $publicInterestedCount = (int) $event->interests->whereNull('user_id')->count();
                    $publicParticipatedCount = (int) $event->interests->whereNull('user_id')->where('participation_status', 'participated')->count();
                @endphp
                <p class="text-xs font-bold text-slate-500">
                    Interested count:
                    <span class="text-slate-700">Members {{ $memberInterestedCount }}</span>,
                    <span class="text-slate-700">Public {{ $publicInterestedCount }}</span>
                </p>
                <p class="text-xs font-bold text-slate-500">
                    Participated count:
                    <span class="text-slate-700">Members {{ $memberParticipatedCount }}</span>,
                    <span class="text-slate-700">Public {{ $publicParticipatedCount }}</span>
                </p>
            </div>

            <div class="min-w-0 bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
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
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Event Gallery Upload</h2>
                    <p class="text-xs font-bold text-slate-500 mt-1">Upload event photos directly here; uploaded files will appear in the event gallery.</p>
                </div>
                <a href="{{ route('admin.events.album', $event->id) }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Open Gallery</a>
            </div>
            <form method="POST" action="{{ route('admin.events.album.store', $event->id) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="photos[]" multiple accept="image/*"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">
                @error('photos')<p class="text-[11px] text-red-600">{{ $message }}</p>@enderror
                @error('photos.*')<p class="text-[11px] text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Upload to Gallery</button>
            </form>
            @if($event->photos->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 mb-3">Gallery preview ({{ $event->photos->count() }})</p>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                        @foreach($event->photos->take(10) as $photo)
                            <a href="{{ asset('storage/' . ltrim((string) $photo->photo_path, '/')) }}" target="_blank" rel="noopener noreferrer" class="block rounded-lg overflow-hidden border border-slate-200">
                                <img src="{{ asset('storage/' . ltrim((string) $photo->photo_path, '/')) }}" alt="" class="h-16 w-full object-cover">
                            </a>
                        @endforeach
                    </div>
                    @if($event->photos->count() > 10)
                        <p class="mt-2 text-xs text-slate-500">+ {{ $event->photos->count() - 10 }} more in the full album.</p>
                    @endif
                </div>
            @endif
        </div>

        @php
            $canMarkAttendance = in_array($event->status, ['live', 'completed'], true);
            $guestAttendanceEnabled = \Illuminate\Support\Facades\Schema::hasColumn('event_interests', 'participation_status');
        @endphp

        <div id="event-member-attendance" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm scroll-mt-6">
            <h2 class="text-sm font-extrabold text-slate-900 mb-1">Attendees &amp; registrations</h2>
            <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                <strong class="text-slate-800">Invited members</strong> and <strong class="text-slate-800">guests</strong> who registered on the public events page appear in one list. When the event is <strong class="text-slate-800">Live</strong> or <strong class="text-slate-800">Completed</strong>, set attendance here. After <strong>Attended</strong>, members and eligible guests can download the certificate once the template PDF is set on <a href="{{ route('admin.events.edit', $event) }}" class="font-bold text-indigo-600 hover:underline">Edit event</a>.
            </p>
            <div class="mb-4 inline-flex flex-wrap items-center gap-1 rounded-full bg-slate-100 p-1">
                <a href="{{ route('admin.events.show', ['event' => $event->id, 'interest_type' => 'all']) }}"
                   class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[11px] font-black uppercase tracking-wider transition {{ $interestType === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>All</span>
                    <span class="text-[10px]">{{ $unifiedCountAll }}</span>
                </a>
                <a href="{{ route('admin.events.show', ['event' => $event->id, 'interest_type' => 'members']) }}"
                   class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[11px] font-black uppercase tracking-wider transition {{ $interestType === 'members' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Members</span>
                    <span class="text-[10px]">{{ $unifiedCountMembers }}</span>
                </a>
                <a href="{{ route('admin.events.show', ['event' => $event->id, 'interest_type' => 'non_members']) }}"
                   class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[11px] font-black uppercase tracking-wider transition {{ $interestType === 'non_members' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Guests</span>
                    <span class="text-[10px]">{{ $unifiedCountGuests }}</span>
                </a>
            </div>
            @if(!$canMarkAttendance && $event->status !== 'cancelled')
                <p class="text-xs font-bold text-amber-800 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 mb-3">
                    Attendance fields unlock when you set this event to <strong>Live</strong> (or after it is <strong>Completed</strong>).
                </p>
            @endif
            @if($filteredUnifiedRows->count() === 0)
                <p class="text-sm text-slate-500">No invited members or public registrations yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 w-[7rem]">Type</th>
                                <th class="px-4 py-3 min-w-[8rem]">Name</th>
                                <th class="px-4 py-3 min-w-[10rem] max-w-[16rem]">Contact</th>
                                <th class="px-4 py-3 min-w-[12rem]">Attendance</th>
                                <th class="px-4 py-3 whitespace-nowrap">Recorded</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Certificate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($filteredUnifiedRows as $uRow)
                                @if($uRow['kind'] === 'invite')
                                    @php $invite = $uRow['invite']; @endphp
                                    <tr>
                                        <td class="px-4 py-3 align-top">
                                            <span class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-black uppercase text-indigo-700">Member</span>
                                            <p class="mt-1 text-[10px] font-bold text-slate-400">Invited</p>
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <p class="font-bold text-slate-800 break-words">{{ $invite->user->name ?? 'User' }}</p>
                                            <p class="text-[11px] text-slate-500">Member #{{ $invite->user_id }}</p>
                                        </td>
                                        <td class="px-4 py-3 align-top text-slate-600 max-w-[16rem]">
                                            <p class="break-all sm:break-words [overflow-wrap:anywhere]">{{ $invite->user->email ?? '—' }}</p>
                                            <p class="text-[11px] text-slate-500 break-all sm:break-words">{{ $invite->user->mobile ?? '—' }}</p>
                                        </td>
                                        <td class="px-4 py-3 align-top text-slate-600">
                                            @if(! ($invite->has_confirmed_interest ?? true))
                                                <p class="mb-2 text-[11px] font-bold text-amber-800">Invite sent — awaiting member confirmation on the portal.</p>
                                            @endif
                                            <form method="POST" action="{{ route('admin.events.invites.status', [$event->id, $invite->id]) }}" class="flex flex-wrap items-center gap-2">
                                                @csrf
                                                <select name="participation_status"
                                                    class="max-w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                    @disabled(!$canMarkAttendance)>
                                                    <option value="interested" {{ $invite->participation_status === 'interested' ? 'selected' : '' }}>Interested (registered)</option>
                                                    <option value="participated" {{ $invite->participation_status === 'participated' ? 'selected' : '' }}>Attended (certificate eligible)</option>
                                                    <option value="not_participated" {{ $invite->participation_status === 'not_participated' ? 'selected' : '' }}>Did not attend</option>
                                                </select>
                                                <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white text-[11px] font-extrabold disabled:opacity-50 shrink-0"
                                                    @disabled(!$canMarkAttendance)>Save</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 align-top text-slate-600 whitespace-nowrap">{{ $invite->invited_at?->format('d M Y h:i A') ?? '—' }}</td>
                                        <td class="px-4 py-3 align-top text-right">
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
                                @else
                                    @php $row = $uRow['interest']; @endphp
                                    <tr>
                                        <td class="px-4 py-3 align-top">
                                            @if($row->user_id)
                                                <span class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-black uppercase text-indigo-700">Member</span>
                                                <p class="mt-1 text-[10px] font-bold text-slate-400">Public form</p>
                                            @else
                                                <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-black uppercase text-amber-800">Guest</span>
                                                <p class="mt-1 text-[10px] font-bold text-slate-400">Public form</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top font-bold text-slate-800 break-words">{{ $row->name }}</td>
                                        <td class="px-4 py-3 align-top text-slate-600 max-w-[16rem]">
                                            <p class="break-all sm:break-words [overflow-wrap:anywhere]">{{ $row->email }}</p>
                                            <p class="text-[11px] text-slate-500 break-all sm:break-words">{{ $row->phone ?? '—' }}</p>
                                            @if($row->user_id)
                                                <p class="text-[10px] font-bold text-slate-400 mt-1">Member #{{ $row->user_id }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top text-slate-600">
                                            @if($guestAttendanceEnabled)
                                                <form method="POST" action="{{ route('admin.events.interests.attendance', [$event->id, $row->id]) }}" class="flex flex-wrap items-center gap-2">
                                                    @csrf
                                                    <select name="participation_status"
                                                        class="max-w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                        @disabled(!$canMarkAttendance)>
                                                        <option value="interested" {{ ($row->participation_status ?? 'interested') === 'interested' ? 'selected' : '' }}>Interested</option>
                                                        <option value="participated" {{ ($row->participation_status ?? '') === 'participated' ? 'selected' : '' }}>Attended</option>
                                                        <option value="not_participated" {{ ($row->participation_status ?? '') === 'not_participated' ? 'selected' : '' }}>Did not attend</option>
                                                    </select>
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white text-[11px] font-extrabold disabled:opacity-50 shrink-0" @disabled(!$canMarkAttendance)>Save</button>
                                                </form>
                                            @else
                                                <span class="text-xs font-bold text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top text-slate-600 whitespace-nowrap">{{ $row->created_at?->format('d M Y h:i A') ?? '—' }}</td>
                                        <td class="px-4 py-3 align-top text-right">
                                            @if($guestAttendanceEnabled && ($row->participation_status ?? '') === 'participated')
                                                <a href="{{ route('admin.events.interests.certificate', [$event->id, $row->id]) }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-extrabold text-white hover:bg-emerald-700">Certificate PDF</a>
                                            @else
                                                <span class="text-xs font-bold text-slate-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
