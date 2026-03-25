@extends('admin.layouts.app')

@section('content')
<div class="h-full flex flex-col p-6 gap-4">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">Donations</h1>
        <p class="text-xs text-slate-500">Donations / <span class="font-semibold text-indigo-600">Create</span></p>
    </div>

    @include('admin.donations._form', ['donation' => null])
</div>
@endsection

