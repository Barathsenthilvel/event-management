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

        <form method="POST" action="{{ route('admin.meetings.invite.store', $meeting->id) }}" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-5">
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
                                <input type="checkbox" name="notify_whatsapp" value="1" {{ old('notify_whatsapp') ? 'checked' : '' }}>
                                WhatsApp
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200">
                                <input type="checkbox" name="notify_sms" value="1" {{ old('notify_sms') ? 'checked' : '' }}>
                                SMS
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 p-2 rounded-lg border border-slate-200 col-span-2">
                                <input type="checkbox" name="notify_email" value="1" {{ old('notify_email') ? 'checked' : '' }}>
                                Email
                            </label>
                        </div>
                        @error('notify_channel')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Approved Members</p>
                        <form method="GET">
                            <input type="text" name="q" value="{{ $q }}" placeholder="Search"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                        </form>
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

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-3">Invited Members</h3>
            @if($invites->count() === 0)
                <p class="text-xs font-bold text-slate-500">No members invited yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($invites as $invite)
                        <div class="flex items-center justify-between border border-slate-100 rounded-xl p-3">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $invite->user->name ?? 'Member' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $invite->user->email ?? '-' }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.meetings.invite.remove', [$meeting->id, $invite->id]) }}" onsubmit="return confirm('Remove this member from meeting invite list?')">
                                @csrf
                                @method('DELETE')
                                <button class="w-8 h-8 rounded-lg bg-rose-600 text-white hover:bg-rose-700 inline-flex items-center justify-center" title="Remove Member">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
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

