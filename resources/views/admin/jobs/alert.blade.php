@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">GNAT Job Posting Notification</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Job: {{ $job->title }}</p>
            </div>
            <form method="GET" action="{{ route('admin.jobs.alert', $job) }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Role</label>
                    <select name="designation_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold min-w-[8rem]">
                        <option value="0">All roles</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}" {{ (int) $designationId === (int) $designation->id ? 'selected' : '' }}>
                                {{ $designation->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <label class="inline-flex items-center gap-2 pb-1.5 text-[11px] font-bold text-slate-600">
                    <input type="checkbox" name="leaders_only" value="1" {{ $leadersOnly ? 'checked' : '' }}>
                    Leaders only (filter)
                </label>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Search</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Name, email, mobile"
                        class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200 min-w-[10rem]">
                </div>
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-extrabold">Apply filter</button>
                <a href="{{ route('admin.jobs.index') }}" class="px-4 py-1.5 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </form>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.jobs.alert.store', $job->id) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-black text-slate-700 mb-2">To participant *</p>
                        <div class="flex flex-wrap items-center gap-4">
                            @php $target = old('target', 'all'); @endphp
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="all" {{ $target === 'all' ? 'checked' : '' }}> All members
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="specific" {{ $target === 'specific' ? 'checked' : '' }}> Specific
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="leaders_only" {{ $target === 'leaders_only' ? 'checked' : '' }}> Leaders only
                            </label>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-700 mb-2">Notify members via</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2"><input type="checkbox" name="notify_all" value="1" {{ old('notify_all') ? 'checked' : '' }}> All channels</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200"><input type="checkbox" name="notify_whatsapp" value="1" {{ old('notify_whatsapp') ? 'checked' : '' }}> WhatsApp</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200"><input type="checkbox" name="notify_sms" value="1" {{ old('notify_sms') ? 'checked' : '' }}> SMS</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2"><input type="checkbox" name="notify_email" value="1" {{ old('notify_email', true) ? 'checked' : '' }}> Email</label>
                        </div>
                        @error('notify_channel')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-3">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Members (this page)</p>
                        <p class="text-[11px] font-bold text-slate-400">When &quot;Specific&quot; is on, pick members below (or use select all).</p>
                    </div>
                    <div class="mb-2 flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-extrabold text-slate-800">
                            <input type="checkbox" id="job-alert-select-all-page" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
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
                                    <p class="text-[11px] text-slate-400">{{ $m->designation->name ?? 'No role' }}</p>
                                </div>
                                <input type="checkbox" name="member_ids[]" value="{{ $m->id }}" class="js-job-alert-member-cb h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $checked ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                    @error('member_ids')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-[11px] font-bold text-slate-500">Showing {{ $members->count() }} of {{ $members->total() }} members on this page.</p>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.jobs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Send</button>
                </div>
            </div>
        </form>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            {{ $members->links() }}
        </div>
    </div>
</div>
<script>
(function () {
    function memberCbs() {
        return Array.prototype.slice.call(document.querySelectorAll('.js-job-alert-member-cb'));
    }
    function syncSelectAllMaster() {
        var master = document.getElementById('job-alert-select-all-page');
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
        var master = document.getElementById('job-alert-select-all-page');
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
