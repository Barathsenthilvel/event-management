@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Invite / Remove Members</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Meeting: {{ $meeting->title }}</p>
            </div>
            <a href="{{ route('admin.meetings.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
        </div>

        {{-- Search must not be nested inside the POST form (invalid HTML breaks submit). --}}
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Find members</p>
            <form method="GET" action="{{ route('admin.meetings.invite', $meeting) }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search by name, email, mobile"
                    class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200 min-w-[12rem]">
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-extrabold">Search</button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.meetings.invite.store', $meeting) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Meeting Participant *</p>
                        <div class="flex items-center gap-4">
                            @php $target = old('target', 'all'); @endphp
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="all" {{ $target === 'all' ? 'checked' : '' }}>
                                All Members
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="specific" {{ $target === 'specific' ? 'checked' : '' }}>
                                Specific
                            </label>
                        </div>
                        @error('target')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Notify Members Via</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200">
                                <input type="checkbox" name="notify_whatsapp" value="1" {{ old('notify_whatsapp', true) ? 'checked' : '' }}>
                                WhatsApp
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200">
                                <input type="checkbox" name="notify_sms" value="1" {{ old('notify_sms') ? 'checked' : '' }}>
                                SMS
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2">
                                <input type="checkbox" name="notify_email" value="1" {{ old('notify_email', true) ? 'checked' : '' }}>
                                Email
                            </label>
                        </div>
                        @error('notify_channel')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-3">Approved Members</p>
                    <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100">
                        @foreach($members as $m)
                            @php $checked = in_array($m->id, old('member_ids', $invitedUserIds)); @endphp
                            <label class="flex items-start justify-between gap-3 p-3 hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $m->name }}</p>
                                    <p class="text-[11px] font-bold text-slate-500">{{ $m->email }} @if($m->mobile) • {{ $m->mobile }} @endif</p>
                                </div>
                                <input type="checkbox" name="member_ids[]" value="{{ $m->id }}" {{ $checked ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                    @error('member_ids')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500">Total members: {{ $members->total() }}</p>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Send Invites</button>
            </div>
        </form>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-3">Invited Members</h3>
            @php
                $canMarkAttendance = in_array($meeting->status, ['live', 'completed'], true);
                $attendedCount = $invites->where('participation_status', 'participated')->count();
                $notAttendedCount = $invites->where('participation_status', 'not_participated')->count();
                $interestedCount = $invites->where('participation_status', 'interested')->count();
            @endphp
            <div class="mb-3 flex flex-wrap items-center gap-2 text-[11px] font-black uppercase tracking-wide">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">Interested: {{ $interestedCount }}</span>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">Attended: {{ $attendedCount }}</span>
                <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-700">Not attended: {{ $notAttendedCount }}</span>
            </div>
            @if(!$canMarkAttendance && $meeting->status !== 'cancelled')
                <p class="mb-3 rounded-xl border border-amber-100 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-900">
                    Attendance updates are enabled when meeting status is Live or Completed.
                </p>
            @endif
            @if($invites->count() === 0)
                <p class="text-xs font-bold text-slate-500">No members invited yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($invites as $invite)
                        <div class="flex items-center justify-between border border-slate-100 rounded-xl p-3">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $invite->user->name ?? 'Member' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $invite->user->email ?? '-' }}</p>
                                <p class="text-[10px] text-slate-500 mt-1">
                                    Last attendance update:
                                    {{ $invite->attended_at?->format('d M Y h:i A') ?? '—' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.meetings.invite.attendance', [$meeting->id, $invite->id]) }}" class="inline-flex items-center gap-2">
                                    @csrf
                                    <select name="participation_status"
                                        class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                        @disabled(!$canMarkAttendance)>
                                        <option value="interested" {{ ($invite->participation_status ?? 'interested') === 'interested' ? 'selected' : '' }}>Interested</option>
                                        <option value="participated" {{ ($invite->participation_status ?? '') === 'participated' ? 'selected' : '' }}>Attended</option>
                                        <option value="not_participated" {{ ($invite->participation_status ?? '') === 'not_participated' ? 'selected' : '' }}>Not attended</option>
                                    </select>
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white text-[11px] font-extrabold disabled:opacity-50" @disabled(!$canMarkAttendance)>Save</button>
                                </form>
                                <form id="admin-delete-meeting-invite-{{ $invite->id }}" method="POST" action="{{ route('admin.meetings.invite.remove', [$meeting->id, $invite->id]) }}" class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center" title="Remove Member"
                                        data-delete-form="admin-delete-meeting-invite-{{ $invite->id }}"
                                        data-delete-title="Remove this invite?"
                                        data-delete-message="This member will be removed from the meeting invite list."
                                        onclick="adminOpenDeleteModalFromEl(this)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            {{ $members->links() }}
        </div>
    </div>
</div>
@endsection
