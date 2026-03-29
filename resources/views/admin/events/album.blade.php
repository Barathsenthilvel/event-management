@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Event Album</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Event: {{ $event->title }} • Status: {{ ucfirst($event->status) }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.events.show', $event->id) }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Event Details</a>
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back</a>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            @if($event->status !== 'completed')
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
                    Album upload is available only when event status is Completed.
                </div>
            @else
                <form method="POST" action="{{ route('admin.events.album.store', $event->id) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-500">Upload Event Photos</label>
                    <input type="file" name="photos[]" multiple accept="image/*"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">
                    @error('photos')<p class="text-[11px] text-red-600">{{ $message }}</p>@enderror
                    @error('photos.*')<p class="text-[11px] text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-extrabold">Upload Photos</button>
                </form>
            @endif
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-900 mb-4">Album Photos ({{ $event->photos->count() }})</h2>
            @if($event->photos->isEmpty())
                <p class="text-sm text-slate-500">No album photos yet.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($event->photos as $photo)
                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Event photo" class="w-full h-40 object-cover">
                            <div class="p-2.5">
                                <form id="admin-delete-event-photo-{{ $photo->id }}" method="POST" action="{{ route('admin.events.album.destroy', [$event->id, $photo->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="w-full rounded-lg bg-rose-600 text-white text-xs font-extrabold py-2 hover:bg-rose-700"
                                        data-delete-form="admin-delete-event-photo-{{ $photo->id }}"
                                        data-delete-title="Remove this photo?"
                                        data-delete-message="This image will be deleted from the event album permanently."
                                        onclick="adminOpenDeleteModalFromEl(this)">Delete</button>
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
