@php
    $isEdit = isset($meeting);
    $action = $isEdit ? route('admin.meetings.update', $meeting->id) : route('admin.meetings.store');
    $schedule = old('schedule_date')
        ? [
            'meeting_date' => old('schedule_date'),
            'from_time' => old('schedule_from'),
            'to_time' => old('schedule_to'),
        ]
        : ($isEdit && $meeting->schedules->first()
            ? [
                'meeting_date' => optional($meeting->schedules->first()->meeting_date)->format('Y-m-d'),
                'from_time' => $meeting->schedules->first()->from_time,
                'to_time' => $meeting->schedules->first()->to_time,
            ]
            : ['meeting_date' => '', 'from_time' => '', 'to_time' => '']);
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting Title *</label>
                <input type="text" name="title" value="{{ old('title', $meeting->title ?? '') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                @error('title')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting Link *</label>
                <input type="text" name="meeting_link" value="{{ old('meeting_link', $meeting->meeting_link ?? '') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                @error('meeting_link')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">{{ old('description', $meeting->description ?? '') }}</textarea>
                @error('description')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting Mode *</label>
                @php $mode = old('meeting_mode', $meeting->meeting_mode ?? 'direct'); @endphp
                <select name="meeting_mode"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="whatsapp" {{ $mode === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="teams" {{ $mode === 'teams' ? 'selected' : '' }}>Meet / Teams / Others</option>
                    <option value="others" {{ $mode === 'others' ? 'selected' : '' }}>Others</option>
                    <option value="direct" {{ $mode === 'direct' ? 'selected' : '' }}>Direct</option>
                    <option value="phone_call" {{ $mode === 'phone_call' ? 'selected' : '' }}>Phone Call</option>
                </select>
                @error('meeting_mode')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-3">Meeting Date & Time *</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <input type="date" name="schedule_date" value="{{ $schedule['meeting_date'] }}"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div>
                        <input type="time" name="schedule_from" value="{{ $schedule['from_time'] }}"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div>
                        <input type="time" name="schedule_to" value="{{ $schedule['to_time'] }}"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                </div>
                @error('schedule_date')<p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>@enderror
                @error('schedule_from')<p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>@enderror
                @error('schedule_to')<p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-3">Images (optional)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-5 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <span class="text-xs font-semibold text-slate-700">Cover Image</span>
                        <input type="file" name="cover_image" class="hidden" accept="image/*">
                    </label>
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-5 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <span class="text-xs font-semibold text-slate-700">Banner Image</span>
                        <input type="file" name="banner_image" class="hidden" accept="image/*">
                    </label>
                </div>
                @error('cover_image')<p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>@enderror
                @error('banner_image')<p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-2">Status *</label>
                    @php $status = old('status', $meeting->status ?? 'upcoming'); @endphp
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        <option value="upcoming" {{ $status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="live" {{ $status === 'live' ? 'selected' : '' }}>Live</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $meeting->is_active ?? true) ? 'checked' : '' }}>
                        Display Active
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.meetings.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold">
            {{ $isEdit ? 'Update Meeting' : 'Create Meeting' }}
        </button>
    </div>
</form>

