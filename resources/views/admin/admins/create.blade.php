@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Create New Admin</h1>
        <p class="text-sm text-slate-500">Add a new admin user to the system</p>
    </div>

    <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Full Name @include('admin.partials.required-mark')</label>
                <input type="text" name="name" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="John Doe" value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email @include('admin.partials.required-mark')</label>
                <input type="email" name="email" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="john@example.com" value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Phone (Optional)</label>
                <input type="tel" name="phone"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="+1234567890" value="{{ old('phone') }}">
                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Password @include('admin.partials.required-mark')</label>
                <input type="password" name="password" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Confirm Password @include('admin.partials.required-mark')</label>
                <input type="password" name="password_confirmation" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="••••••••">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-4">Assign Roles</label>
                <div class="space-y-2">
                    @foreach($roles as $role)
                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                        <div>
                            <span class="text-sm font-bold text-slate-700">{{ $role->name }}</span>
                            @if($role->description)
                            <p class="text-xs text-slate-500">{{ $role->description }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <a href="{{ route('admin.admins.index') }}"
                    class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center">
                    Cancel
                </a>
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                    Create Admin
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

