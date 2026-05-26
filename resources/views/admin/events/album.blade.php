@extends('admin.layouts.app')

@section('content')
<div class="flex-1 min-h-0 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Event Album</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Event: {{ $event->title }} • Status: {{ ucfirst($event->status) }}</p>
                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Cover and banner images from the event form are added here automatically. Upload more photos below.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.events.edit', $event->id) }}" class="px-4 py-2 rounded-xl border border-indigo-200 bg-indigo-50 text-xs font-extrabold text-indigo-700">Edit Event</a>
                <a href="{{ route('admin.events.show', $event->id) }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Event Details</a>
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <form method="POST" action="{{ route('admin.events.album.store', $event->id) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500">Upload Event Photos</label>
                <input id="event_album_photos_input" type="file" name="photos[]" multiple accept="image/*"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">
                @error('photos')<p class="text-[11px] text-red-600">{{ $message }}</p>@enderror
                @error('photos.*')<p class="text-[11px] text-red-600">{{ $message }}</p>@enderror
                <div id="event_album_upload_preview" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold hover:bg-indigo-700">Upload Photos</button>
            </form>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-900 mb-1">Gallery ({{ $event->photos->count() }})</h2>
            <p class="text-xs text-slate-500 mb-4">All images for this event — including cover, banner, and album uploads.</p>
            @if($event->photos->isEmpty())
                <p class="text-sm text-slate-500">No photos yet. Upload images above or add a cover/banner on the event edit page.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($event->photos as $photo)
                        @php
                            $photoPath = ltrim((string) $photo->photo_path, '/');
                            $imageUrl = asset('storage/' . $photoPath);
                            $isCover = $photoPath !== '' && $photoPath === ltrim((string) $event->cover_image_path, '/');
                            $isBanner = $photoPath !== '' && $photoPath === ltrim((string) $event->banner_image_path, '/');
                        @endphp
                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50">
                            <a href="{{ $imageUrl }}" target="_blank" rel="noopener noreferrer" class="block relative">
                                <img src="{{ $imageUrl }}" alt="Event photo" class="w-full h-40 object-cover">
                                @if($isCover || $isBanner)
                                    <span class="absolute left-2 top-2 rounded-full bg-[#0f172a]/85 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white">
                                        {{ $isCover ? 'Cover' : 'Banner' }}
                                    </span>
                                @endif
                            </a>
                            <div class="p-2.5 space-y-2">
                                <a href="{{ $imageUrl }}" target="_blank" rel="noopener noreferrer"
                                   class="block w-full rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-extrabold py-2 text-center hover:bg-slate-50">
                                    View full size
                                </a>
                                <form id="admin-delete-event-photo-{{ $photo->id }}" method="POST" action="{{ route('admin.events.album.destroy', [$event->id, $photo->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="w-full rounded-lg bg-rose-600 text-white text-xs font-extrabold py-2 hover:bg-rose-700"
                                        data-delete-form="admin-delete-event-photo-{{ $photo->id }}"
                                        data-delete-title="Remove this photo?"
                                        data-delete-message="{{ ($isCover || $isBanner) ? 'This removes the image from the album list. The event cover/banner file stays on the event until you change it on Edit event.' : 'This image will be deleted from the event album permanently.' }}"
                                        onclick="adminOpenDeleteModalFromEl(this)">Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('event_album_photos_input');
        const preview = document.getElementById('event_album_upload_preview');
        if (!input || !preview) return;

        let objectUrls = [];

        input.addEventListener('change', function () {
            objectUrls.forEach((url) => URL.revokeObjectURL(url));
            objectUrls = [];
            preview.innerHTML = '';

            const files = Array.from(input.files || []);
            if (files.length === 0) {
                preview.classList.add('hidden');
                return;
            }

            preview.classList.remove('hidden');
            files.forEach((file) => {
                const url = URL.createObjectURL(file);
                objectUrls.push(url);
                const wrap = document.createElement('div');
                wrap.className = 'rounded-lg border border-slate-200 overflow-hidden bg-white';
                wrap.innerHTML = '<img src="' + url + '" alt="" class="w-full h-24 object-cover"><p class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 truncate">' + file.name + '</p>';
                preview.appendChild(wrap);
            });
        });
    })();
</script>
@endpush
