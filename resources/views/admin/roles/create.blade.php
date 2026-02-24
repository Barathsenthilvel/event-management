@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Create New Role</h1>
        <p class="text-sm text-slate-500">Create a new role and assign permissions</p>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Role Name</label>
                <input type="text" name="name" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="e.g. Content Auditor" value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                    placeholder="Role description...">{{ old('description') }}</textarea>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-slate-700 block">Status</span>
                    <p class="text-xs text-slate-400 font-bold uppercase">Role Activation</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                    <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-4">Permissions</label>
                <div class="space-y-4">
                    @php
                        $permissions = \App\Models\Permission::all()->groupBy('module');
                    @endphp
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="border border-slate-100 rounded-xl p-4">
                        <h4 class="font-bold text-sm text-slate-800 mb-3">{{ $module }}</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($modulePermissions as $permission)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                <span class="text-xs text-slate-600">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <a href="{{ route('admin.roles.index') }}"
                    class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors text-center">
                    Cancel
                </a>
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                    Create Role
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

