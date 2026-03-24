@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-5xl mx-auto space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Create Event</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Create a new event and configure dates, seats, files, and status.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            @include('admin.events._form')
        </div>
    </div>
</div>
@endsection
