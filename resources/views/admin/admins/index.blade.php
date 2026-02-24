@extends('admin.layouts.app')

@section('content')
<div x-data="{
    viewType: 'list',
    showAddPanel: false,
    panelMode: 'create',
    selectedAdmin: null,
    roles: @js($roles),
    adminRoles: [],
    loading: false,
    searchQuery: '',
    addToast(msg) {
        const id = Date.now();
        this.toasts = this.toasts || [];
        this.toasts.push({ id, msg });
        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 3000);
    },
    toasts: [],
    async refreshAdmins() {
        // Access parent Alpine component (layout) to show global loader
        const parentData = this.$root?.$data;
        if (parentData && typeof parentData.showLoader === 'function') {
            parentData.showLoader();
        } else if (parentData && parentData.refreshing !== undefined) {
            parentData.refreshing = true;
        }

        try {
            // Fetch updated content
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });
            if (response.ok) {
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update table body content
                const newTableBody = doc.querySelector('table.island-row tbody');
                const newGridContent = doc.querySelector('.grid.grid-cols-1');
                const newPagination = doc.querySelector('.mt-4.pt-4.border-t');

                if (newTableBody) {
                    const currentTableBody = document.querySelector('table.island-row tbody');
                    if (currentTableBody) {
                        currentTableBody.innerHTML = newTableBody.innerHTML;
                    }
                }

                if (newGridContent) {
                    const currentGrid = document.querySelector('.grid.grid-cols-1');
                    if (currentGrid) {
                        currentGrid.innerHTML = newGridContent.innerHTML;
                    }
                }

                if (newPagination) {
                    const currentPagination = document.querySelector('.mt-4.pt-4.border-t');
                    if (currentPagination && currentPagination.parentElement) {
                        currentPagination.outerHTML = newPagination.outerHTML;
                    }
                }
            }
        } catch (error) {
            console.error('Error refreshing admins:', error);
            // Don't reload on error, just hide loader
        } finally {
            // Hide global loader
            if (parentData && typeof parentData.hideLoader === 'function') {
                parentData.hideLoader();
            } else if (parentData && parentData.refreshing !== undefined) {
                parentData.refreshing = false;
            }
        }
    },
    openCreatePanel() {
        this.panelMode = 'create';
        this.selectedAdmin = null;
        this.adminRoles = [];
        this.showAddPanel = true;
    },
    async openEditPanel(adminId) {
        this.loading = true;
        this.panelMode = 'edit';
        this.showAddPanel = true;
        try {
            const response = await fetch(`/admin/admins/${adminId}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            this.selectedAdmin = data.admin;
            this.adminRoles = data.adminRoles || [];
        } catch (error) {
            console.error('Error loading admin data:', error);
            this.addToast('Error loading admin');
        } finally {
            this.loading = false;
        }
    },
    closePanel() {
        this.showAddPanel = false;
        this.panelMode = 'create';
        this.selectedAdmin = null;
        this.adminRoles = [];
    }
}" class="h-full flex gap-3 workspace-transition relative p-6">
<div class="flex flex-col gap-3 workspace-transition flex-1" :class="showAddPanel ? 'w-2/3' : 'w-full'">
    <!-- Tools Bar -->
    <div class="flex items-center justify-between mb-6 shrink-0">
        <div class="flex items-center gap-2">
            <input type="text" x-model="searchQuery" placeholder="Search users..."
                class="pl-4 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs w-48 outline-none focus:ring-2 focus:ring-indigo-500/10">
            <button class="p-2 text-slate-400 bg-slate-50 rounded-xl hover:text-indigo-600"><svg
                    class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg></button>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex bg-slate-50 p-1 rounded-xl">
                <button @click="viewType = 'list'"
                    :class="viewType === 'list' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                    class="p-2 rounded-lg transition-all"><svg class="w-4 h-4" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg></button>
                <button @click="viewType = 'grid'"
                    :class="viewType === 'grid' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                    class="p-2 rounded-lg transition-all"><svg class="w-4 h-4" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg></button>
            </div>
            <button @click="openCreatePanel()"
                class="bg-[#0f172a] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-bold transition-all shadow-lg">+
                Add New Admin</button>
        </div>
    </div>

    <!-- Content Area (Scrollable) -->
    <div class="flex-1 overflow-y-auto custom-scroll pr-2 pb-4">
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
            <p class="text-sm text-emerald-600 font-bold">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm text-red-600 font-bold">{{ session('error') }}</p>
        </div>
        @endif

        <!-- List View (Table – one row per user) -->
        <table x-show="viewType === 'list'" class="w-full text-left island-row">
            <thead
                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest sticky top-0 bg-white z-10">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Roles</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                @forelse($admins as $admin)
                <tr class="group transition-all hover:bg-slate-50/50">
                    <td class="px-4 py-3 bg-white border-y border-l border-slate-100 first:rounded-l-2xl align-middle">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                            <span class="font-bold text-slate-800">{{ $admin->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 bg-white border-y border-slate-100 text-slate-600 align-middle">
                        {{ $admin->email }}
                    </td>
                    <td class="px-4 py-3 bg-white border-y border-slate-100 align-middle">
                        @forelse($admin->roles as $role)
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-medium rounded">{{ $role->name }}</span>@if(!$loop->last), @endif
                        @empty
                            <span class="text-slate-400">No roles</span>
                        @endforelse
                    </td>
                    <td class="px-4 py-3 bg-white border-y border-slate-100 text-center align-middle">
                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-600 font-bold text-[9px] uppercase border border-emerald-100">Active</span>
                    </td>
                    <td class="px-4 py-3 bg-white border-y border-r border-slate-100 last:rounded-r-2xl text-right align-middle">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openEditPanel({{ $admin->id }})"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                title="Edit Admin">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            @if(!$admin->is_super_admin)
                            <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg"
                                    title="Delete Admin">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                        <p>No admins found. <button @click="openCreatePanel()" class="text-indigo-600 hover:underline">Create one</button></p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($admins->hasPages())
        <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between shrink-0">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ $admins->total() }} Users</p>
            <div class="flex gap-1">
                {{ $admins->links('pagination.simple-tailwind') }}
            </div>
        </div>
        @else
        <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between shrink-0">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $admins->count() }} Users</p>
        </div>
        @endif

        <!-- Grid View (Users) -->
        <div x-show="viewType === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($admins as $admin)
            <div
                class="p-5 border border-slate-100 rounded-[20px] hover:shadow-lg hover:-translate-y-1 transition-all bg-white group relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-indigo-500 font-bold group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                        {{ strtoupper(substr($admin->name, 0, 2)) }}</div>
                    <span
                        class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[9px] uppercase">Active</span>
                </div>
                <h4 class="font-bold text-slate-800 mb-1">{{ $admin->name }}</h4>
                <p class="text-xs text-slate-400 mb-2">{{ $admin->email }}</p>
                <div class="flex flex-wrap gap-1 mb-4">
                    @forelse($admin->roles as $role)
                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[9px] font-bold rounded">{{ $role->name }}</span>
                    @empty
                    <span class="text-[9px] text-slate-400">No roles</span>
                    @endforelse
                </div>

                <div class="mt-4 flex gap-2 pt-4 border-t border-slate-50">
                    <button @click="openEditPanel({{ $admin->id }})"
                        class="flex-1 py-2 bg-slate-50 text-[10px] font-bold text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors">Edit</button>
                    @if(!$admin->is_super_admin)
                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:text-rose-600"><svg
                                class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg></button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Add / Edit Admin Side Panel (same UI for create and edit) -->
<div x-show="showAddPanel" x-cloak
    class="w-1/3 bg-white rounded-[24px] shadow-2xl border border-white flex flex-col workspace-transition overflow-hidden"
    x-transition:enter="transition-all ease-out duration-500"
    x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100">

    <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/20">
        <h3 class="font-bold text-slate-800" x-text="panelMode === 'edit' ? 'Edit Admin' : 'Create New Admin'"></h3>
        <button type="button" @click="closePanel()"
            class="p-2 text-slate-400 hover:bg-white rounded-xl shadow-sm"><svg class="w-4 h-4" fill="none"
                stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" />
            </svg></button>
    </div>

    <!-- Create form -->
    <form x-show="panelMode === 'create'" method="POST" action="{{ route('admin.admins.store') }}" @submit.prevent="
        const form = $event.target;
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.content || '{{ csrf_token() }}'
            }
        })
        .then(async response => {
            if (response.ok) {
                closePanel();
                form.reset();
                addToast('New Admin Created Successfully');
                await refreshAdmins();
            } else {
                return response.json().then(data => {
                    console.error('Validation errors:', data);
                    addToast('Error: ' + (data.message || 'Failed to create admin'));
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addToast('Error: Failed to create admin');
        });
    " class="p-8 space-y-6 flex-1 overflow-y-auto custom-scroll">
        @csrf
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Full Name</label>
            <input type="text" name="name" placeholder="e.g. John Doe" required
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Email</label>
            <input type="email" name="email" placeholder="e.g. john@example.com" required
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Phone (Optional)</label>
            <input type="tel" name="phone" placeholder="+1234567890"
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Password</label>
            <input type="password" name="password" placeholder="••••••••" required
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Confirm Password</label>
            <input type="password" name="password_confirmation" placeholder="••••••••" required
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Assign Roles</label>
            <div class="space-y-2 max-h-48 overflow-y-auto custom-scroll">
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
        </div>
        <button type="submit"
            class="w-full bg-[#0f172a] text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-indigo-600 transition-all">Submit
            Admin</button>
    </form>

    <!-- Edit form (same panel UI) -->
    <div x-show="panelMode === 'edit' && loading" class="flex-1 flex items-center justify-center p-12">
        <div class="text-center">
            <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-sm text-slate-500 font-bold">Loading admin data...</p>
        </div>
    </div>
    <form x-show="panelMode === 'edit' && !loading && selectedAdmin" method="POST" :action="selectedAdmin ? `/admin/admins/${selectedAdmin.id}` : '#'"
        @submit.prevent="
            const form = $event.target;
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.ok) {
                    const data = await response.json();
                    closePanel();
                    addToast(data.message || 'Admin Updated Successfully');
                    await refreshAdmins();
                } else {
                    const data = await response.json();
                    let errorMsg = 'Failed to update admin';
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
                    } else if (data.message) errorMsg = data.message;
                    addToast('Error: ' + errorMsg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                addToast('Error: Failed to update admin');
            });
        "
        class="p-8 space-y-6 flex-1 overflow-y-auto custom-scroll">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Full Name</label>
            <input type="text" name="name" required x-model="selectedAdmin?.name"
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5"
                placeholder="e.g. John Doe">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Email</label>
            <input type="email" name="email" required x-model="selectedAdmin?.email"
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5"
                placeholder="e.g. john@example.com">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Phone (Optional)</label>
            <input type="tel" name="phone" x-model="selectedAdmin?.phone"
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5"
                placeholder="+1234567890">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">New Password (leave blank to keep)</label>
            <input type="password" name="password"
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5"
                placeholder="••••••••">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Confirm New Password</label>
            <input type="password" name="password_confirmation"
                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5"
                placeholder="••••••••">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Assign Roles</label>
            <div class="space-y-2 max-h-48 overflow-y-auto custom-scroll">
                <template x-for="role in roles" :key="role.id">
                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="roles[]" :value="role.id"
                            :checked="adminRoles.includes(role.id)"
                            @change="
                                if ($event.target.checked) {
                                    if (!adminRoles.includes(role.id)) adminRoles.push(role.id);
                                } else {
                                    adminRoles = adminRoles.filter(id => id !== role.id);
                                }
                            "
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                        <div>
                            <span class="text-sm font-bold text-slate-700" x-text="role.name"></span>
                            <p x-show="role.description" class="text-xs text-slate-500" x-text="role.description"></p>
                        </div>
                    </label>
                </template>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="button" @click="closePanel()"
                class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition-colors">
                Cancel
            </button>
            <button type="submit"
                class="flex-1 px-6 py-3.5 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-xs rounded-xl shadow-lg transition-all">
                Update Admin
            </button>
        </div>
    </form>
</div>

<!-- Toast Notifications -->
<div class="fixed bottom-10 right-10 z-[300] space-y-3 pointer-events-none">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-5" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center gap-4 bg-[#0f172a] text-white px-6 py-4 rounded-2xl shadow-2xl border border-white/10 pointer-events-auto">
            <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shrink-0"><svg
                    class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="4"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg></div>
            <p class="text-xs font-bold" x-text="toast.msg"></p>
        </div>
    </template>
</div>
</div>
@endsection
