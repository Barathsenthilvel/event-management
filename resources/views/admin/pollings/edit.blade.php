@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-6xl mx-auto space-y-5">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Edit Polling</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Polling / Edit</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            @include('admin.pollings._form', ['polling' => $polling])
        </div>
    </div>
</div>
@endsection

