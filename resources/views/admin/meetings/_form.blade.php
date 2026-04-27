@php
    $isEdit = isset($meeting);
    $action = $isEdit ? route('admin.meetings.update', $meeting->id) : route('admin.meetings.store');
    $defaults = $duplicateSource ?? [];
    $titleValue = old('title', $meeting->title ?? ($defaults['title'] ?? ''));
    $linkValue = old('meeting_link', $meeting->meeting_link ?? ($defaults['meeting_link'] ?? ''));
    $descriptionValue = old('description', $meeting->description ?? ($defaults['description'] ?? ''));
    $modeValue = old('meeting_mode', $meeting->meeting_mode ?? ($defaults['meeting_mode'] ?? 'direct'));
    $statusValue = old('status', $meeting->status ?? ($defaults['status'] ?? 'upcoming'));
    $isActiveValue = old('is_active', $meeting->is_active ?? ($defaults['is_active'] ?? true));
    $schedule = old('schedule_date')
        ? [
            'meeting_date' => old('schedule_date'),
            'from_time' => old('schedule_from'),
            'to_time' => old('schedule_to'),
        ]
        : ($isEdit && $meeting->schedules->first()
            ? [
                'meeting_date' => optional($meeting->schedules->first()->meeting_date)->format('Y-m-d'),
                'from_time' => $meeting->schedules->first()->from_time ? \Carbon\Carbon::parse($meeting->schedules->first()->from_time)->format('h:i A') : '',
                'to_time' => $meeting->schedules->first()->to_time ? \Carbon\Carbon::parse($meeting->schedules->first()->to_time)->format('h:i A') : '',
            ]
            : [
                'meeting_date' => $defaults['schedule_date'] ?? '',
                'from_time' => !empty($defaults['schedule_from']) ? \Carbon\Carbon::parse($defaults['schedule_from'])->format('h:i A') : '',
                'to_time' => !empty($defaults['schedule_to']) ? \Carbon\Carbon::parse($defaults['schedule_to'])->format('h:i A') : '',
            ]);
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting Title @include('admin.partials.required-mark')</label>
                <input type="text" name="title" value="{{ $titleValue }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                @error('title')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting link <span class="text-slate-400 font-normal">(optional)</span></label>
                <p class="text-[11px] text-slate-500 mb-1">Add a Zoom / Meet / WhatsApp link when ready. Members still see the meeting on their dashboard from the schedule.</p>
                <input type="url" name="meeting_link" value="{{ $linkValue }}" placeholder="https://…"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                @error('meeting_link')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">{{ $descriptionValue }}</textarea>
                @error('description')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Meeting Mode @include('admin.partials.required-mark')</label>
                <select name="meeting_mode"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="whatsapp" {{ $modeValue === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="teams" {{ $modeValue === 'teams' ? 'selected' : '' }}>Meet / Teams / Others</option>
                    <option value="others" {{ $modeValue === 'others' ? 'selected' : '' }}>Others</option>
                    <option value="direct" {{ $modeValue === 'direct' ? 'selected' : '' }}>Direct</option>
                    <option value="phone_call" {{ $modeValue === 'phone_call' ? 'selected' : '' }}>Phone Call</option>
                </select>
                @error('meeting_mode')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-3">Meeting Date & Time @include('admin.partials.required-mark')</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Meeting date</label>
                        <input type="date" name="schedule_date" value="{{ $schedule['meeting_date'] }}"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">From time</label>
                        <input type="time" name="schedule_from" step="60"
                            value="{{ $schedule['from_time'] ? \Carbon\Carbon::parse($schedule['from_time'])->format('H:i') : '' }}"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">To time</label>
                        <input type="time" name="schedule_to" step="60"
                            value="{{ $schedule['to_time'] ? \Carbon\Carbon::parse($schedule['to_time'])->format('H:i') : '' }}"
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
                    <label class="block text-xs font-black text-slate-600 mb-2">Status @include('admin.partials.required-mark')</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        <option value="upcoming" {{ $statusValue === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="live" {{ $statusValue === 'live' ? 'selected' : '' }}>Live</option>
                        <option value="completed" {{ $statusValue === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $statusValue === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $isActiveValue ? 'checked' : '' }}>
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

