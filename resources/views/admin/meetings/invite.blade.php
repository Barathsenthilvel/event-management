@extends('admin.layouts.app')

@section('content')
@php
    $invNav = ['meeting' => $meeting->id, 'q' => $q];
    if (! empty($reminderMode)) {
        $invNav['reminder'] = 1;
    }
@endphp
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ ! empty($reminderMode) ? 'Send meeting reminders' : 'Invite / Remove Members' }}</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Meeting: {{ $meeting->title }}</p>
                @if(! empty($reminderMode))
                    <p class="text-[11px] font-bold text-slate-500 mt-2 max-w-xl">
                        Same layout as invites: pick who gets this reminder and which channels to use. Each member still only receives a channel they agreed to on their original invite.
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(! empty($reminderMode))
                    <a href="{{ route('admin.meetings.invite', $meeting) }}" class="px-4 py-2 rounded-xl border border-indigo-200 text-xs font-extrabold text-indigo-700 hover:bg-indigo-50">Invite members</a>
                @elseif($meeting->is_active && in_array($meeting->status, ['upcoming', 'live'], true))
                    <a href="{{ route('admin.meetings.invite', ['meeting' => $meeting, 'reminder' => 1]) }}" class="px-4 py-2 rounded-xl border border-amber-200 bg-amber-50 text-xs font-extrabold text-amber-900 hover:bg-amber-100">Send reminders</a>
                @endif
                <a href="{{ route('admin.meetings.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-900">
                {{ session('warning') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Search must not be nested inside the POST form (invalid HTML breaks submit). --}}
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Find members</p>
            <form method="GET" action="{{ route('admin.meetings.invite', $meeting) }}" class="flex items-center gap-2">
                @if(! empty($reminderMode))
                    <input type="hidden" name="reminder" value="1">
                @endif
                <input type="hidden" name="status_tab" value="{{ $statusTab }}">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search by name, email, mobile"
                    class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200 min-w-[12rem]">
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-extrabold">Search</button>
            </form>
        </div>

        @if(! empty($reminderMode))
            <form method="POST" action="{{ route('admin.meetings.send-reminder', $meeting->id) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-5">
                        <div>
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Recipients *</p>
                            <div class="flex items-center gap-4 flex-wrap">
                                @php $rtarget = old('target', 'all'); @endphp
                                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                    <input type="radio" name="target" value="all" {{ $rtarget === 'all' ? 'checked' : '' }}>
                                    All invited members
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                    <input type="radio" name="target" value="specific" {{ $rtarget === 'specific' ? 'checked' : '' }}>
                                    Selected only
                                </label>
                            </div>
                            @error('target')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Notify via *</p>
                            <p class="text-[11px] text-slate-500 mb-2">Email uses GNAT templates. SMS uses MSG91. WhatsApp uses the same SMS template until a WhatsApp API is configured.</p>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200">
                                    <input type="checkbox" name="notify_whatsapp" value="1" {{ old('notify_whatsapp') ? 'checked' : '' }}>
                                    WhatsApp (SMS)
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
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-3">
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Approved members (this page)</p>
                            <p class="text-[11px] font-bold text-slate-400">When &quot;Selected only&quot; is on, pick members below (or use select all).</p>
                        </div>
                        <div class="mb-2 flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                            <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-extrabold text-slate-800">
                                <input type="checkbox" id="meeting-reminder-page-select-all" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Select all on this page
                            </label>
                            <span class="text-[10px] font-bold text-slate-400">Reload clears selections (except after a validation error).</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100">
                            @foreach($members as $m)
                                @php
                                    $checked = in_array((string) $m->id, array_map('strval', old('member_ids', [])), true);
                                @endphp
                                <label class="flex items-start justify-between gap-3 p-3 hover:bg-slate-50">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">{{ $m->name }}</p>
                                        <p class="text-[11px] font-bold text-slate-500">{{ $m->email }} @if($m->mobile) • {{ $m->mobile }} @endif</p>
                                    </div>
                                    <input type="checkbox" name="member_ids[]" value="{{ $m->id }}" class="js-meeting-reminder-page-cb h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $checked ? 'checked' : '' }}>
                                </label>
                            @endforeach
                        </div>
                        @error('member_ids')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                        @error('member_ids.*')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-[11px] font-bold text-slate-500">Showing {{ $members->count() }} of {{ $members->total() }} members on this page.</p>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Queue reminders</button>
                </div>
            </form>
        @else
        <form id="meeting-invite-bulk-form" method="POST" action="{{ route('admin.meetings.invite.store', $meeting) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Meeting Participant *</p>
                        <div class="flex items-center gap-4">
                            @php $target = old('target', 'approved'); @endphp
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="approved" {{ $target === 'approved' ? 'checked' : '' }}>
                                Approved Members
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
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Approved Members</p>
                        <label class="inline-flex items-center gap-2 text-[11px] font-bold text-slate-700">
                            <input type="checkbox" id="select-all-approved" name="select_all_approved" value="1" {{ old('select_all_approved') ? 'checked' : '' }}>
                            Select all
                        </label>
                    </div>
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
        @endif

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-3">Invited Members</h3>
            @php
                $canMarkAttendance = in_array($meeting->status, ['live', 'completed'], true);
                $invitedCount = (int) ($statusCounts->invited_count ?? 0);
                $attendedCount = (int) ($statusCounts->attended_count ?? 0);
                $notAttendedCount = (int) ($statusCounts->not_attended_count ?? 0);
                $interestedCount = (int) ($statusCounts->interested_count ?? 0);
                $allCount = (int) ($statusCounts->total ?? 0);
            @endphp
            <div class="mb-3 flex flex-wrap items-center gap-2 text-[11px] font-black uppercase tracking-wide">
                <a href="{{ route('admin.meetings.invite', array_merge($invNav, ['status_tab' => 'all'])) }}"
                   class="rounded-full px-3 py-1 {{ $statusTab === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">
                    All: {{ $allCount }}
                </a>
                <a href="{{ route('admin.meetings.invite', array_merge($invNav, ['status_tab' => 'invited'])) }}"
                   class="rounded-full px-3 py-1 {{ $statusTab === 'invited' ? 'bg-indigo-700 text-white' : 'bg-indigo-100 text-indigo-700' }}">
                    Invited: {{ $invitedCount }}
                </a>
                <a href="{{ route('admin.meetings.invite', array_merge($invNav, ['status_tab' => 'interested'])) }}"
                   class="rounded-full px-3 py-1 {{ $statusTab === 'interested' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">
                    Interested: {{ $interestedCount }}
                </a>
                <a href="{{ route('admin.meetings.invite', array_merge($invNav, ['status_tab' => 'participated'])) }}"
                   class="rounded-full px-3 py-1 {{ $statusTab === 'participated' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-700' }}">
                    Attended: {{ $attendedCount }}
                </a>
                <a href="{{ route('admin.meetings.invite', array_merge($invNav, ['status_tab' => 'not_participated'])) }}"
                   class="rounded-full px-3 py-1 {{ $statusTab === 'not_participated' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-700' }}">
                    Not attended: {{ $notAttendedCount }}
                </a>
            </div>
            @if(!$canMarkAttendance && $meeting->status !== 'cancelled')
                <p class="mb-3 rounded-xl border border-amber-100 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-900">
                    Attendance updates are enabled when meeting status is Live or Completed.
                </p>
            @endif
            @if($invites->count() === 0)
                <p class="text-xs font-bold text-slate-500">
                    @if($statusTab === 'all')
                        No members invited yet.
                    @else
                        No members found for this tab.
                    @endif
                </p>
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
                                        <option value="invited" {{ ($invite->participation_status ?? 'invited') === 'invited' ? 'selected' : '' }}>Invited</option>
                                        <option value="interested" {{ ($invite->participation_status ?? '') === 'interested' ? 'selected' : '' }}>Interested</option>
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

@push('scripts')
<script>
    (function () {
        const selectAll = document.getElementById('select-all-approved');
        if (!selectAll) return;

        const memberCheckboxes = Array.from(document.querySelectorAll('#meeting-invite-bulk-form input[name="member_ids[]"]'));
        const targetApproved = document.querySelector('input[name="target"][value="approved"]');
        const targetSpecific = document.querySelector('input[name="target"][value="specific"]');

        const syncSelectAllFromMembers = () => {
            if (!memberCheckboxes.length) return;
            selectAll.checked = memberCheckboxes.every((cb) => cb.checked);
        };

        const applySelectAllState = (checked) => {
            memberCheckboxes.forEach((cb) => {
                cb.checked = checked;
            });
        };

        selectAll.addEventListener('change', function () {
            applySelectAllState(this.checked);
            if (this.checked && targetApproved) {
                targetApproved.checked = true;
            } else if (!this.checked && targetSpecific) {
                targetSpecific.checked = true;
            }
        });

        memberCheckboxes.forEach((cb) => {
            cb.addEventListener('change', () => {
                syncSelectAllFromMembers();
                if (targetSpecific) {
                    targetSpecific.checked = true;
                }
            });
        });

        syncSelectAllFromMembers();
    })();
</script>
<script>
(function () {
    function reminderCbs() {
        return Array.prototype.slice.call(document.querySelectorAll('.js-meeting-reminder-page-cb'));
    }
    function syncReminderPageSelectAll() {
        var master = document.getElementById('meeting-reminder-page-select-all');
        if (!master) return;
        var cbs = reminderCbs();
        if (!cbs.length) {
            master.checked = false;
            master.indeterminate = false;
            return;
        }
        var n = cbs.filter(function (c) { return c.checked; }).length;
        master.checked = n === cbs.length;
        master.indeterminate = n > 0 && n < cbs.length;
    }
    document.addEventListener('DOMContentLoaded', function () {
        var master = document.getElementById('meeting-reminder-page-select-all');
        if (master) {
            master.addEventListener('change', function () {
                reminderCbs().forEach(function (cb) { cb.checked = master.checked; });
                master.indeterminate = false;
            });
        }
        reminderCbs().forEach(function (cb) {
            cb.addEventListener('change', syncReminderPageSelectAll);
        });
        syncReminderPageSelectAll();
    });
})();
</script>
@endpush
