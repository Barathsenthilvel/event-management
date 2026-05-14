@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ !empty($reminderMode) ? 'Send event reminders' : 'Invite Members' }}</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Event: {{ $event->title }}</p>
                @if(!empty($reminderMode))
                    <p class="text-[11px] font-bold text-slate-500 mt-2 max-w-xl">
                        Same layout as invites: pick who gets this reminder and which channels to use. Each member still only receives a channel they agreed to on their original invite.
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(!empty($reminderMode))
                    <form method="GET" action="{{ route('admin.events.invite', $event) }}" class="flex items-center gap-2">
                        <input type="hidden" name="reminder" value="1">
                        <input type="text" name="q" value="{{ $q }}" placeholder="Search members"
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200 min-w-[10rem]">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-extrabold text-slate-700 border border-slate-200 hover:bg-slate-200">Search</button>
                    </form>
                    <a href="{{ route('admin.events.invite', $event) }}" class="px-4 py-2 rounded-xl border border-indigo-200 text-xs font-extrabold text-indigo-700 hover:bg-indigo-50">Invite members</a>
                @else
                    <form method="GET" action="{{ route('admin.events.invite', $event) }}" class="flex items-center gap-2">
                        <input type="text" name="q" value="{{ $q }}" placeholder="Search members"
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200 min-w-[10rem]">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-extrabold text-slate-700 border border-slate-200 hover:bg-slate-200">Search</button>
                    </form>
                @endif
                @if($event->is_active && in_array($event->status, ['upcoming', 'live'], true) && empty($reminderMode))
                    <a href="{{ route('admin.events.invite', ['event' => $event, 'reminder' => 1]) }}" class="px-4 py-2 rounded-xl border border-amber-200 bg-amber-50 text-xs font-extrabold text-amber-900 hover:bg-amber-100">Send reminders</a>
                @endif
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
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

        @if(!empty($reminderMode))
            <form method="POST" action="{{ route('admin.events.send-reminder', $event->id) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
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
                                <input type="checkbox" id="event-invite-select-all-page" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Select all on this page
                            </label>
                            <span class="text-[10px] font-bold text-slate-400">Reload clears selections (except after a validation error).</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100" id="event-invite-member-list">
                            @foreach($members as $m)
                                @php
                                    $checked = in_array((string) $m->id, array_map('strval', old('member_ids', [])), true);
                                @endphp
                                <label class="flex items-start justify-between gap-3 p-3 hover:bg-slate-50">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">{{ $m->name }}</p>
                                        <p class="text-[11px] font-bold text-slate-500">{{ $m->email }} @if($m->mobile) • {{ $m->mobile }} @endif</p>
                                    </div>
                                    <input type="checkbox" name="member_ids[]" value="{{ $m->id }}" class="js-event-invite-member-cb h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $checked ? 'checked' : '' }}>
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
        <form method="POST" action="{{ route('admin.events.invite.store', $event->id) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Recipients *</p>
                        <div class="flex items-center gap-4 flex-wrap">
                            @php $target = old('target', 'all'); @endphp
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="all" {{ $target === 'all' ? 'checked' : '' }}>
                                All approved members
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="specific" {{ $target === 'specific' ? 'checked' : '' }}>
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
                            <input type="checkbox" id="event-invite-select-all-page" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            Select all on this page
                        </label>
                        <span class="text-[10px] font-bold text-slate-400">Reload clears selections (except after a validation error).</span>
                    </div>
                    <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100" id="event-invite-member-list">
                        @foreach($members as $m)
                            @php
                                /** Only repopulate from old() after validation errors — not from prior invites, so reload starts clean. */
                                $checked = in_array((string) $m->id, array_map('strval', old('member_ids', [])), true);
                            @endphp
                            <label class="flex items-start justify-between gap-3 p-3 hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $m->name }}</p>
                                    <p class="text-[11px] font-bold text-slate-500">{{ $m->email }} @if($m->mobile) • {{ $m->mobile }} @endif</p>
                                </div>
                                <input type="checkbox" name="member_ids[]" value="{{ $m->id }}" class="js-event-invite-member-cb h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $checked ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                    @error('member_ids')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    @error('member_ids.*')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-[11px] font-bold text-slate-500">Showing {{ $members->count() }} of {{ $members->total() }} members on this page.</p>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Send invites</button>
            </div>
        </form>
        @endif

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            {{ $members->links() }}
        </div>
    </div>
</div>
<script>
(function () {
    function memberCbs() {
        return Array.prototype.slice.call(document.querySelectorAll('.js-event-invite-member-cb'));
    }
    function syncSelectAllMaster() {
        var master = document.getElementById('event-invite-select-all-page');
        if (!master) return;
        var cbs = memberCbs();
        if (cbs.length === 0) {
            master.checked = false;
            master.indeterminate = false;
            return;
        }
        var n = cbs.filter(function (c) { return c.checked; }).length;
        master.checked = n === cbs.length;
        master.indeterminate = n > 0 && n < cbs.length;
    }
    document.addEventListener('DOMContentLoaded', function () {
        var master = document.getElementById('event-invite-select-all-page');
        if (master) {
            master.addEventListener('change', function () {
                memberCbs().forEach(function (cb) { cb.checked = master.checked; });
                master.indeterminate = false;
            });
        }
        memberCbs().forEach(function (cb) {
            cb.addEventListener('change', syncSelectAllMaster);
        });
        syncSelectAllMaster();
    });
})();
</script>
@endsection
