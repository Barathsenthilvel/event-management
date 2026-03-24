@php
    $isEdit = isset($event);
    $action = $isEdit ? route('admin.events.update', $event->id) : route('admin.events.store');
    $dates = old('event_dates', $isEdit ? $event->dates->map(fn($d) => [
        'date' => optional($d->event_date)->format('Y-m-d'),
        'start_time' => $d->start_time ? \Illuminate\Support\Carbon::parse($d->start_time)->format('H:i') : '',
        'end_time' => $d->end_time ? \Illuminate\Support\Carbon::parse($d->end_time)->format('H:i') : '',
    ])->values()->all() : [['date' => '', 'start_time' => '', 'end_time' => '']]);
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Event Title *</label>
            <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none" required>
            @error('title')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Venue (optional)</label>
            <input type="text" name="venue" value="{{ old('venue', $event->venue ?? '') }}"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
            @error('venue')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-2">Event Description</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">{{ old('description', $event->description ?? '') }}</textarea>
            @error('description')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Seat Mode *</label>
            @php $seatMode = old('seat_mode', $event->seat_mode ?? 'unlimited'); @endphp
            <select name="seat_mode" id="seat_mode"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
                <option value="unlimited" {{ $seatMode === 'unlimited' ? 'selected' : '' }}>Unlimited</option>
                <option value="limited" {{ $seatMode === 'limited' ? 'selected' : '' }}>Limited</option>
            </select>
            @error('seat_mode')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Seat Limit</label>
            <input type="number" min="1" name="seat_limit" id="seat_limit" value="{{ old('seat_limit', $event->seat_limit ?? '') }}"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
            @error('seat_limit')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Event Status *</label>
            @php $status = old('status', $event->status ?? 'upcoming'); @endphp
            <select name="status"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-indigo-200 outline-none">
                <option value="upcoming" {{ $status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="live" {{ $status === 'live' ? 'selected' : '' }}>Live</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-6">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="promote_front" value="0">
            <input type="checkbox" name="promote_front" value="1" {{ old('promote_front', $event->promote_front ?? false) ? 'checked' : '' }} class="rounded border-slate-300">
            Promote on front
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $event->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300">
            Display active
        </label>
    </div>

    <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-800">Event Dates *</h3>
            <button type="button" id="add-date-row" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold">+ Add Date</button>
        </div>
        <div id="event-dates-wrapper" class="space-y-3">
            @foreach($dates as $i => $d)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 date-row">
                    <input type="date" name="event_dates[{{ $i }}][date]" value="{{ $d['date'] ?? '' }}" class="px-3 py-2 rounded-lg border border-slate-200" required>
                    <input type="time" name="event_dates[{{ $i }}][start_time]" value="{{ $d['start_time'] ?? '' }}" class="px-3 py-2 rounded-lg border border-slate-200">
                    <input type="time" name="event_dates[{{ $i }}][end_time]" value="{{ $d['end_time'] ?? '' }}" class="px-3 py-2 rounded-lg border border-slate-200">
                    <button type="button" class="remove-date-row px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold">Remove</button>
                </div>
            @endforeach
        </div>
        @error('event_dates')<p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>@enderror
        @error('event_dates.*.date')<p class="text-[11px] text-red-600 mt-2">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Cover Image</label>
            <input type="file" name="cover_image" accept="image/*" class="w-full text-sm">
            @error('cover_image')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Banner Image</label>
            <input type="file" name="banner_image" accept="image/*" class="w-full text-sm">
            @error('banner_image')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Template PDF</label>
            <input type="file" name="template_pdf" accept="application/pdf" class="w-full text-sm">
            @error('template_pdf')<p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.events.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold">
            {{ $isEdit ? 'Update Event' : 'Create Event' }}
        </button>
    </div>
</form>

<script>
    (function () {
        const seatMode = document.getElementById('seat_mode');
        const seatLimit = document.getElementById('seat_limit');
        const wrapper = document.getElementById('event-dates-wrapper');
        const addBtn = document.getElementById('add-date-row');

        function toggleSeatLimit() {
            if (!seatMode || !seatLimit) return;
            seatLimit.disabled = seatMode.value !== 'limited';
            if (seatMode.value !== 'limited') seatLimit.value = '';
        }

        function reindexRows() {
            const rows = wrapper.querySelectorAll('.date-row');
            rows.forEach((row, i) => {
                const date = row.querySelector('input[type="date"]');
                const start = row.querySelector('input[type="time"][name*="[start_time]"]');
                const end = row.querySelector('input[type="time"][name*="[end_time]"]');
                date.name = `event_dates[${i}][date]`;
                start.name = `event_dates[${i}][start_time]`;
                end.name = `event_dates[${i}][end_time]`;
            });
        }

        addBtn?.addEventListener('click', function () {
            const idx = wrapper.querySelectorAll('.date-row').length;
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 md:grid-cols-4 gap-3 date-row';
            row.innerHTML = `
                <input type="date" name="event_dates[${idx}][date]" class="px-3 py-2 rounded-lg border border-slate-200" required>
                <input type="time" name="event_dates[${idx}][start_time]" class="px-3 py-2 rounded-lg border border-slate-200">
                <input type="time" name="event_dates[${idx}][end_time]" class="px-3 py-2 rounded-lg border border-slate-200">
                <button type="button" class="remove-date-row px-3 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold">Remove</button>
            `;
            wrapper.appendChild(row);
        });

        wrapper?.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-date-row')) return;
            const rows = wrapper.querySelectorAll('.date-row');
            if (rows.length <= 1) return;
            e.target.closest('.date-row').remove();
            reindexRows();
        });

        seatMode?.addEventListener('change', toggleSeatLimit);
        toggleSeatLimit();
    })();
</script>
