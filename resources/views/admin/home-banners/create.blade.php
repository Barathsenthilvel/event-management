@extends('admin.layouts.app')

@section('content')
<div class="flex h-full min-h-0 flex-col">
    <div class="shrink-0 px-6 pt-6 pb-4">
        <h1 class="text-xl font-bold text-slate-900">Homepage Banners</h1>
        <p class="text-xs text-slate-500">Homepage Banners / <span class="font-semibold text-indigo-600">Create</span></p>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto custom-scroll px-6 pb-6">
        @include('admin.home-banners._form', ['banner' => null])
    </div>
</div>
@endsection
