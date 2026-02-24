@extends('admin.layouts.app')

@section('content')
<div x-data="{
    viewType: 'list',
    showAddPanel: false,
    showEditModal: false,
    showPermissionModal: false,
    selectedRole: '',
    selectedRoleId: null,
    selectedRoleData: null,
    rolePermissions: [],
    permissions: [],
    loading: false,
    loadingPermissions: false,
    searchQuery: '',
    addToast(msg) {
        const id = Date.now();
        this.toasts = this.toasts || [];
        this.toasts.push({ id, msg });
        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 3000);
    },
    toasts: [],
    async refreshRoles() {
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

                // Find the content area container
                const contentArea = document.querySelector('.flex-1.overflow-y-auto.custom-scroll');

                // Get new content from the fetched HTML
                const newContentArea = doc.querySelector('.flex-1.overflow-y-auto.custom-scroll');

                if (contentArea && newContentArea) {
                    // Update entire content area (table, grid, pagination) - no page reload
                    contentArea.innerHTML = newContentArea.innerHTML;
                } else {
                    // Fallback: update individual elements
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
            }
        } catch (error) {
            console.error('Error refreshing roles:', error);
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
    async openEditModal(roleId) {
        this.loading = true;
        this.showEditModal = true;
        this.selectedRoleData = null; // Reset first
        try {
            const response = await fetch(`/admin/roles/${roleId}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            this.selectedRoleData = data.role || {};
        } catch (error) {
            console.error('Error loading role data:', error);
            this.selectedRoleData = {};
        } finally {
            this.loading = false;
        }
    },
    async openPermissionModal(roleId, roleName) {
        this.selectedRole = roleName;
        this.selectedRoleId = roleId;
        this.loadingPermissions = true;
        this.showPermissionModal = true;
        try {
            const response = await fetch(`/admin/roles/${roleId}/permissions-data`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            this.rolePermissions = data.permissions || [];
        } catch (error) {
            console.error('Error loading permissions:', error);
        } finally {
            this.loadingPermissions = false;
        }
    }
}" class="h-full flex gap-3 workspace-transition relative p-6" :class="showAddPanel ? '' : ''">
<div class="flex flex-col gap-3 workspace-transition flex-1" :class="showAddPanel ? 'w-2/3' : 'w-full'">
    <!-- Tools Bar -->
    <div class="flex items-center justify-between mb-6 shrink-0">
        <div class="flex items-center gap-2">
            <input type="text" x-model="searchQuery" placeholder="Search roles..."
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
            <button @click="showAddPanel = true"
                class="bg-[#0f172a] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-bold transition-all shadow-lg">+
                Add New Role</button>
        </div>
    </div>

    <!-- Content Area (Scrollable) -->
    <div class="flex-1 overflow-y-auto custom-scroll pr-2 pb-4">

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
            <p class="text-sm text-emerald-600 font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <!-- List View (Roles Table) -->
        <table x-show="viewType === 'list'" class="w-full text-left island-row">
            <thead
                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest sticky top-0 bg-white z-10">
                <tr>
                    <th class="px-6 py-4">Role Name</th>
                    <th class="px-6 py-4">Created On</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                @forelse($roles as $role)
                <tr class="group transition-all">
                    <td
                        class="px-6 py-4 bg-white border-y border-l border-slate-100 first:rounded-l-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold">
                                {{ strtoupper(substr($role->name, 0, 1)) }}</div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $role->name }}</p>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-[9px] text-emerald-600 font-bold uppercase">Access Provided</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 bg-white border-y border-slate-100 text-slate-400 font-medium">
                        {{ $role->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 bg-white border-y border-slate-100 text-center">
                        <span
                            class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 font-black text-[9px] uppercase border border-emerald-100">Active</span>
                    </td>
                    <td
                        class="px-6 py-4 bg-white border-y border-r border-slate-100 last:rounded-r-2xl text-right">
                        <div
                            class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openPermissionModal({{ $role->id }}, '{{ $role->name }}')"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                title="Set Permissions">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </button>
                            <button @click="openEditModal({{ $role->id }})"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                title="Edit Role">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this role?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg"
                                    title="Delete Role">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                        <p>No roles found. <a href="{{ route('admin.roles.create') }}" class="text-indigo-600 hover:underline">Create one</a></p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($roles->hasPages())
        <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between shrink-0">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} Roles</p>
            <div class="flex gap-1">
                {{ $roles->links('pagination.simple-tailwind') }}
            </div>
        </div>
        @else
        <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between shrink-0">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $roles->count() }} Roles</p>
        </div>
        @endif

        <!-- Grid View (Roles) -->
        <div x-show="viewType === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($roles as $role)
            <div
                class="p-5 border border-slate-100 rounded-[20px] hover:shadow-lg hover:-translate-y-1 transition-all bg-white group relative">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-indigo-500 font-bold group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                        {{ strtoupper(substr($role->name, 0, 1)) }}</div>
                    <span
                        class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[9px] uppercase">Active</span>
                </div>
                <h4 class="font-bold text-slate-800 mb-1">{{ $role->name }}</h4>
                <p class="text-xs text-slate-400 mb-4 line-clamp-2">{{ $role->description ?? 'Manages access levels and permissions for this specific user group.' }}</p>

                <div class="mt-4 flex gap-2 pt-4 border-t border-slate-50">
                    <button @click="openPermissionModal({{ $role->id }}, '{{ $role->name }}')"
                        class="flex-1 py-2 bg-slate-50 text-[10px] font-bold text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors">Permissions</button>
                    <button @click="openEditModal({{ $role->id }})" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:text-indigo-600"><svg
                            class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg></button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

    <!-- Add Role Side Panel -->
    <div x-show="showAddPanel" x-cloak
        class="w-1/3 bg-white rounded-[24px] shadow-2xl border border-white flex flex-col workspace-transition overflow-hidden"
        x-transition:enter="transition-all ease-out duration-500"
        x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100">

        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/20">
            <h3 class="font-bold text-slate-800">Create New Role</h3>
            <button @click="showAddPanel = false"
                class="p-2 text-slate-400 hover:bg-white rounded-xl shadow-sm"><svg class="w-4 h-4" fill="none"
                    stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg></button>
        </div>

        <form method="POST" action="{{ route('admin.roles.store') }}" @submit.prevent="
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
                const contentType = response.headers.get('content-type');
                if (response.ok) {
                    const data = await response.json();
                    showAddPanel = false;
                    form.reset(); // Clear form
                    addToast(data.message || 'New Role Created Successfully');
                    // Refresh with loader - no page shake
                    await refreshRoles();
                } else if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    console.error('Validation errors:', data);
                    let errorMsg = 'Failed to create role';
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
                    } else if (data.message) {
                        errorMsg = data.message;
                    }
                    addToast('Error: ' + errorMsg);
                } else {
                    addToast('Error: Failed to create role');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                addToast('Error: Failed to create role');
            });
        " class="p-8 space-y-6 flex-1 overflow-y-auto custom-scroll">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Role Name</label>
                <input type="text" name="name" value="" placeholder="e.g. Content Auditor" required
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3.5 text-xs outline-none focus:ring-4 focus:ring-indigo-500/5">
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-700 block">Status</span>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Role Activation</p>
                </div>
                <label x-data="{ roleStat: false }" class="relative inline-block w-10 h-5">
                    <input type="checkbox" name="is_active" value="1" :checked="roleStat" @change="roleStat = $event.target.checked" class="sr-only">
                    <span :class="roleStat ? 'bg-indigo-600' : 'bg-slate-300'"
                        class="absolute inset-0 rounded-full transition-all"></span>
                    <span :class="roleStat ? 'translate-x-5' : 'translate-x-0.5'"
                        class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-all shadow-sm"></span>
                </label>
            </div>
            <button type="submit"
                class="w-full bg-[#0f172a] text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-indigo-600 transition-all">Submit
                Role</button>
        </form>
    </div>

    <!-- Edit Role Modal -->
    <div x-show="showEditModal" x-cloak
        class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showEditModal = false"
            class="bg-white w-full max-w-2xl rounded-[32px] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
            x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
            x-transition:enter-start="scale-95 opacity-0 translate-y-10"
            x-transition:enter-end="scale-100 opacity-100 translate-y-0">

            <div class="p-6 border-b border-slate-50 flex justify-between items-center shrink-0 bg-slate-50/20">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Edit Role</h3>
                    <p class="text-xs text-slate-500 mt-1">Update role details</p>
                </div>
                <button @click="showEditModal = false" class="p-2 text-slate-300 hover:text-slate-600"><svg
                        class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scroll" x-show="!loading">
                <form method="POST" :action="selectedRoleData ? `/admin/roles/${selectedRoleData.id}` : '#'"
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
                                showEditModal = false;
                                addToast(data.message || 'Role Updated Successfully');
                                // Refresh with loader - no page shake
                                await refreshRoles();
                            } else {
                                const data = await response.json();
                                let errorMsg = 'Failed to update role';
                                if (data.errors) {
                                    const firstError = Object.values(data.errors)[0];
                                    errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
                                } else if (data.message) {
                                    errorMsg = data.message;
                                }
                                addToast('Error: ' + errorMsg);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            addToast('Error: Failed to update role');
                        });
                    "
                    class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Role Name</label>
                        <input type="text" name="name" required
                            :value="selectedRoleData ? selectedRoleData.name : ''"
                            @input="if (selectedRoleData) selectedRoleData.name = $event.target.value"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                            placeholder="e.g. Content Auditor">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Slug</label>
                        <input type="text" name="slug" required
                            :value="selectedRoleData ? selectedRoleData.slug : ''"
                            @input="if (selectedRoleData) selectedRoleData.slug = $event.target.value"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5"
                            placeholder="e.g. content-auditor">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                        <textarea name="description"
                            :value="selectedRoleData ? selectedRoleData.description : ''"
                            @input="if (selectedRoleData) selectedRoleData.description = $event.target.value"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-indigo-500/5 h-24 resize-none"
                            placeholder="Role description..."></textarea>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-700 block">Status</span>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">Role Activation</p>
                        </div>
                        <label class="relative inline-block w-10 h-5">
                            <input type="checkbox" name="is_active" value="1"
                                :checked="selectedRoleData && (selectedRoleData.is_active == 1 || selectedRoleData.is_active === true)"
                                @change="if (selectedRoleData) selectedRoleData.is_active = $event.target.checked ? 1 : 0"
                                class="sr-only">
                            <span :class="(selectedRoleData && (selectedRoleData.is_active == 1 || selectedRoleData.is_active === true)) ? 'bg-indigo-600' : 'bg-slate-300'"
                                class="absolute inset-0 rounded-full transition-all"></span>
                            <span :class="(selectedRoleData && (selectedRoleData.is_active == 1 || selectedRoleData.is_active === true)) ? 'translate-x-5' : 'translate-x-0.5'"
                                class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-all shadow-sm"></span>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-50">
                        <button type="button" @click="showEditModal = false"
                            class="flex-1 px-6 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-[#0f172a] hover:bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
                            Update Role
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="loading" class="flex-1 flex items-center justify-center p-12">
                <div class="text-center">
                    <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-sm text-slate-500 font-bold">Loading role data...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Permission Modal -->
    <div x-show="showPermissionModal" x-cloak
        class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showPermissionModal = false"
            class="bg-white w-full max-w-4xl rounded-[32px] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
            x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
            x-transition:enter-start="scale-95 opacity-0 translate-y-10"
            x-transition:enter-end="scale-100 opacity-100 translate-y-0">

            <div class="p-6 border-b border-slate-50 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-4">
                    <div class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold text-xs">
                        Role: <span x-text="selectedRole"></span>
                    </div>
                    <h3 class="font-bold text-slate-800">Module Permissions Management</h3>
                </div>
                <button @click="showPermissionModal = false" class="p-2 text-slate-300 hover:text-slate-600"><svg
                        class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>

            <div class="flex-1 overflow-hidden flex">
                <div class="w-64 bg-slate-50/50 border-r border-slate-100 p-6 shrink-0 hidden md:block">
                    <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Active
                            Focus</label>
                        <p class="font-bold text-indigo-600 text-sm" x-text="selectedRole"></p>
                    </div>
                    <p class="mt-6 text-[10px] text-slate-400 leading-relaxed italic">Configure granular access for this
                        specific role. Changes will affect all users assigned to this role immediately.</p>
                </div>

                <div class="flex-1 flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6 custom-scroll" x-show="!loadingPermissions">
                        <form id="permissionForm" method="POST" :action="selectedRoleId ? `/admin/roles/${selectedRoleId}/permissions` : '#'">
                            @csrf
                            <table class="w-full">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                        <th class="text-left pb-4">Module Name</th>
                                        <th class="text-center pb-4">Create</th>
                                        <th class="text-center pb-4">Edit</th>
                                        <th class="text-center pb-4">Delete</th>
                                        <th class="text-center pb-4">View</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @php
                                        $permissions = \App\Models\Permission::all()->groupBy('module');
                                    @endphp
                                    @foreach($permissions as $moduleName => $modulePerms)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 font-bold text-xs text-slate-700">{{ $moduleName }}</td>
                                        @php
                                            $createPerm = $modulePerms->where('slug', 'like', '%.create')->first();
                                            $editPerm = $modulePerms->where('slug', 'like', '%.edit')->first();
                                            $deletePerm = $modulePerms->where('slug', 'like', '%.delete')->first();
                                            $viewPerm = $modulePerms->where('slug', 'like', '%.view')->first();
                                        @endphp
                                        <td class="py-4 text-center">
                                            @if($createPerm)
                                            <input type="checkbox" name="permissions[]" value="{{ $createPerm->slug }}"
                                                x-bind:checked="rolePermissions.includes('{{ $createPerm->slug }}')"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                            @else
                                            <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 text-center">
                                            @if($editPerm)
                                            <input type="checkbox" name="permissions[]" value="{{ $editPerm->slug }}"
                                                x-bind:checked="rolePermissions.includes('{{ $editPerm->slug }}')"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                            @else
                                            <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 text-center">
                                            @if($deletePerm)
                                            <input type="checkbox" name="permissions[]" value="{{ $deletePerm->slug }}"
                                                x-bind:checked="rolePermissions.includes('{{ $deletePerm->slug }}')"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                            @else
                                            <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 text-center">
                                            @if($viewPerm)
                                            <input type="checkbox" name="permissions[]" value="{{ $viewPerm->slug }}"
                                                x-bind:checked="rolePermissions.includes('{{ $viewPerm->slug }}')"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                            @else
                                            <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <div x-show="loadingPermissions" class="flex-1 flex items-center justify-center p-12">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                            <p class="text-sm text-slate-500 font-bold">Loading permissions...</p>
                        </div>
                    </div>

                    <div class="p-6 border-t border-slate-50 bg-slate-50/20 flex justify-end gap-3">
                        <button @click="showPermissionModal = false"
                            class="px-6 py-2 text-xs font-bold text-slate-500 hover:text-slate-800">Cancel</button>
                        <button
                            @click="
                                const form = document.getElementById('permissionForm');
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
                                        showPermissionModal = false;
                                        addToast('Success: Permissions Updated for ' + selectedRole);
                                        // Refresh with loader - no page shake
                                        await refreshRoles();
                                    } else {
                                        addToast('Error: Failed to update permissions');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    addToast('Error: Failed to update permissions');
                                });
                            "
                            class="px-8 py-3 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Save
                            Permissions</button>
                    </div>
                </div>
            </div>
        </div>
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

