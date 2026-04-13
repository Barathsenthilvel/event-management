@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Job Alert</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Meeting / Create</p>
        </div>

        <form method="POST" action="{{ route('admin.jobs.alert.store', $job->id) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-black text-slate-700 mb-2">To Participant *</p>
                        <div class="flex items-center gap-4">
                            @php $target = old('target', 'all'); @endphp
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="all" {{ $target === 'all' ? 'checked' : '' }}> All Members
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="specific" {{ $target === 'specific' ? 'checked' : '' }}> Specific
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                <input type="radio" name="target" value="leaders_only" {{ $target === 'leaders_only' ? 'checked' : '' }}> Leaders Only
                            </label>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-700 mb-2">Notify Members Via</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2"><input type="checkbox" name="notify_all" value="1" {{ old('notify_all') ? 'checked' : '' }}> All</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200"><input type="checkbox" name="notify_whatsapp" value="1" {{ old('notify_whatsapp') ? 'checked' : '' }}> WhatsApp</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200"><input type="checkbox" name="notify_sms" value="1" {{ old('notify_sms') ? 'checked' : '' }}> SMS</label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2"><input type="checkbox" name="notify_email" value="1" {{ old('notify_email') ? 'checked' : '' }}> Email</label>
                        </div>
                        @error('notify_channel')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-3 gap-2">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Members</p>
                        <div class="flex items-center gap-2">
                            <select name="designation_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold">
                                <option value="0">Select Role</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}" {{ (int) $designationId === (int) $designation->id ? 'selected' : '' }}>
                                        {{ $designation->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600">
                                <input type="checkbox" name="leaders_only" value="1" {{ $leadersOnly ? 'checked' : '' }}>
                                Leaders Only
                            </label>
                            <input type="text" name="q" value="{{ $q }}" placeholder="Search" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold">
                            <button type="submit" formmethod="GET" formaction="{{ route('admin.jobs.alert', $job->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-extrabold">Search</button>
                        </div>
                    </div>
                    <div class="max-h-80 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100">
                        @foreach($members as $m)
                            @php $checked = in_array($m->id, old('member_ids', $alertedIds)); @endphp
                            <label class="flex items-start justify-between gap-3 p-3 hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $m->name }}</p>
                                    <p class="text-[11px] font-bold text-slate-500">{{ $m->email }} @if($m->mobile) • {{ $m->mobile }} @endif</p>
                                    <p class="text-[11px] text-slate-400">{{ $m->designation->name ?? 'No role' }}</p>
                                </div>
                                <input type="checkbox" name="member_ids[]" value="{{ $m->id }}" {{ $checked ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.jobs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold">Send</button>
            </div>
        </form>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            {{ $members->links() }}
        </div>
    </div>
</div>
@endsection

