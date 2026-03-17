@extends('member.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">My Profile</h1>
        <p class="text-sm text-slate-500">Update your details.</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">First Name</label>
                        <input name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                        @error('first_name')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Last Name</label>
                        <input name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                        @error('last_name')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Mobile</label>
                        <input name="mobile" value="{{ old('mobile', $user->mobile) }}" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                        @error('mobile')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex gap-3 justify-end">
                    <a href="{{ route('member.dashboard') }}"
                        class="px-8 py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl font-bold text-slate-700 transition-colors">Cancel</a>
                    <button type="submit"
                        class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

