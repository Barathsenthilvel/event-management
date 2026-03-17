@extends('member.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Dashboard</h1>
        <p class="text-sm text-slate-500">Hello, {{ Auth::user()->first_name ?? 'Member' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-sm font-bold text-slate-600 mb-2">Profile</h3>
            <p class="text-2xl font-bold text-slate-900">{{ $profileIncomplete ? 'Incomplete' : 'Completed' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-sm font-bold text-slate-600 mb-2">Email</h3>
            <p class="text-sm font-bold text-slate-900 break-all">{{ Auth::user()->email }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-sm font-bold text-slate-600 mb-2">Mobile</h3>
            <p class="text-sm font-bold text-slate-900">{{ Auth::user()->mobile ?? '-' }}</p>
        </div>
    </div>

    @if($profileIncomplete)
        <div x-data="{ open: true }">
            <div x-show="open" x-cloak
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div class="bg-white border border-slate-100 rounded-[28px] shadow-2xl w-full max-w-md p-10 text-center">
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Hello, Member</h2>
                    <p class="text-slate-600 mb-8">Your Profile is incomplete, Please Complete your Profile to be a part of GNAT Member</p>
                    <a href="{{ route('member.profile.edit') }}"
                        class="inline-flex items-center justify-center px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all">
                        Update
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

