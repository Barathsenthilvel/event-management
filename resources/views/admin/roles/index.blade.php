@extends('admin.layouts.app')

@section('content')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('rolesPage', () => ({
    destroyUrl: '',
    canManageMenuPermissions: false,
    init() {
      this.destroyUrl = this.$el.dataset.destroyUrl || '';
      this.canManageMenuPermissions = this.$el.dataset.canManageMenuPermissions === '1';
    },
    viewType: 'list',
    showAddPanel: false,
    showEditModal: false,
    showPermissionModal: false,
    showDeleteModal: false,
    deleteRoleId: null,
    deleteRoleName: '',
    selectedRole: '',
    selectedRoleId: null,
    selectedRoleData: null,
    rolePermissions: [],
    permissions: [],
    menus: [],
    menuPermissions: {},
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
    escapeAttr(s) {
      if (!s) return '';
      const div = document.createElement('div');
      div.textContent = s;
      return div.innerHTML.replace(/"/g, '&quot;');
    },
    buildTableRows(roles, destroyUrl, csrfToken) {
      if (!roles || roles.length === 0) {
        return '<tr><td colspan="4" class="px-6 py-8 text-center text-slate-400"><p>No roles found. <a href="/admin/roles/create" class="text-indigo-600 hover:underline">Create one</a></p></td></tr>';
      }
      return roles.map(r => {
        const nameAttr = this.escapeAttr(r.name);
        var permBtn = this.canManageMenuPermissions
          ? '<button type="button" data-action="permissions" data-role-id="' + r.id + '" data-role-name="' + nameAttr + '" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg" title="Menu Permissions"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg></button>'
          : '';
        return '<tr class="group transition-all">' +
          '<td class="px-6 py-4 bg-white border-y border-l border-slate-100 first:rounded-l-2xl"><div class="flex items-center gap-3">' +
          '<div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold">' + (r.initial || r.name.charAt(0).toUpperCase()) + '</div>' +
          '<div><p class="font-bold text-slate-800">' + this.escapeAttr(r.name) + '</p><div class="flex items-center gap-1 mt-0.5">' +
          '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="text-[9px] text-emerald-600 font-bold uppercase">Access Provided</span></div></div></td>' +
          '<td class="px-6 py-4 bg-white border-y border-slate-100 text-slate-400 font-medium">' + (r.created_at || '') + '</td>' +
          '<td class="px-6 py-4 bg-white border-y border-slate-100 text-center"><span class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 font-black text-[9px] uppercase border border-emerald-100">Active</span></td>' +
          '<td class="px-6 py-4 bg-white border-y border-r border-slate-100 last:rounded-r-2xl text-right">' +
          '<div class="flex items-center justify-end gap-1">' + permBtn +
          '<button type="button" data-action="edit" data-role-id="' + r.id + '" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg" title="Edit Role"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>' +
          '<button type="button" data-action="delete" data-role-id="' + r.id + '" data-role-name="' + nameAttr + '" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg" title="Delete Role"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>' +
          '</div></td></tr>';
      }).join('');
    },
    buildGridRows(roles) {
      if (!roles || roles.length === 0) return '';
      return roles.map(r => {
        const desc = (r.description || 'Manages access levels and permissions for this specific user group.').substring(0, 80);
        const nameAttr = this.escapeAttr(r.name);
        const permBtn = this.canManageMenuPermissions
          ? '<button type="button" data-action="permissions" data-role-id="' + r.id + '" data-role-name="' + nameAttr + '" class="flex-1 py-2 bg-slate-50 text-[10px] font-bold text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors">Menu Permissions</button>'
          : '';
        return '<div class="p-5 border border-slate-100 rounded-[20px] hover:shadow-lg hover:-translate-y-1 transition-all bg-white group relative">' +
          '<div class="flex justify-between items-start mb-4"><div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-indigo-500 font-bold group-hover:bg-indigo-500 group-hover:text-white transition-colors">' + (r.initial || r.name.charAt(0).toUpperCase()) + '</div>' +
          '<span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[9px] uppercase">Active</span></div>' +
          '<h4 class="font-bold text-slate-800 mb-1">' + this.escapeAttr(r.name) + '</h4>' +
          '<p class="text-xs text-slate-400 mb-4 line-clamp-2">' + this.escapeAttr(desc) + '</p>' +
          '<div class="mt-4 flex gap-2 pt-4 border-t border-slate-50">' + permBtn +
          '<button type="button" data-action="edit" data-role-id="' + r.id + '" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:text-indigo-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>' +
          '<button type="button" data-action="delete" data-role-id="' + r.id + '" data-role-name="' + nameAttr + '" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:text-rose-600" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>' +
          '</div></div>';
      }).join('');
    },
    async refreshRoles(resetToFirstPage = false) {
      const parentData = this.$root?.$data;
      if (parentData && typeof parentData.showLoader === 'function') {
        parentData.showLoader();
      } else if (parentData && parentData.refreshing !== undefined) {
        parentData.refreshing = true;
      }
      try {
        const url = resetToFirstPage
          ? (window.location.pathname + '?page=1')
          : window.location.href;
        const response = await fetch(url, {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        if (!response.ok) return;
        const data = await response.json();
        const roles = data.roles || [];
        const pagination = data.pagination || {};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const destroyUrl = this.destroyUrl || '';

        const tbody = document.querySelector('table.island-row tbody');
        if (tbody) {
          tbody.innerHTML = this.buildTableRows(roles, destroyUrl, csrfToken);
        }
        const contentArea = document.getElementById('roles-list-content');
        const gridEl = contentArea ? contentArea.querySelector('.grid') : null;
        if (gridEl) gridEl.innerHTML = this.buildGridRows(roles);
        const paginationWrap = document.getElementById('roles-pagination-wrap');
        if (paginationWrap) {
          const total = pagination.total != null ? pagination.total : roles.length;
          const firstItem = pagination.first_item != null ? pagination.first_item : (total ? 1 : 0);
          const lastItem = pagination.last_item != null ? pagination.last_item : roles.length;
          const info = total === 0 ? 'Showing 0 Roles' : ('Showing ' + firstItem + ' to ' + lastItem + ' of ' + total + ' Roles');
          paginationWrap.innerHTML = '<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">' + info + '</p>' +
            (pagination.has_pages && pagination.links_html ? '<div class="flex gap-1">' + pagination.links_html + '</div>' : '');
        }
      } catch (error) {
        console.error('Error refreshing roles:', error);
      } finally {
        if (parentData && typeof parentData.hideLoader === 'function') {
          parentData.hideLoader();
        } else if (parentData && parentData.refreshing !== undefined) {
          parentData.refreshing = false;
        }
      }
    },
    handleTableClick(e) {
      const delBtn = e.target.closest('[data-action="delete"]');
      if (delBtn) { e.preventDefault(); this.openDeleteConfirm(delBtn.dataset.roleId, delBtn.dataset.roleName || ''); return; }
      const editBtn = e.target.closest('[data-action="edit"]');
      if (editBtn) { e.preventDefault(); this.openEditModal(editBtn.dataset.roleId); return; }
      const permBtn = e.target.closest('[data-action="permissions"]');
      if (permBtn) { e.preventDefault(); this.openPermissionModal(permBtn.dataset.roleId, permBtn.dataset.roleName || ''); }
    },
    openDeleteConfirm(roleId, roleName) {
      this.deleteRoleId = roleId;
      this.deleteRoleName = roleName || '';
      this.showDeleteModal = true;
    },
    async confirmDelete() {
      if (!this.deleteRoleId) return;
      const url = (this.destroyUrl || '').replace(/\/$/, '') + '/' + this.deleteRoleId;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
      try {
        const formData = new URLSearchParams({ _method: 'DELETE', _token: csrf });
        const response = await fetch(url, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' }
        });
        const data = response.ok ? await response.json().catch(() => ({})) : {};
        this.showDeleteModal = false;
        this.deleteRoleId = null;
        this.deleteRoleName = '';
        this.addToast(data.message || 'Role deleted successfully.');
        await this.refreshRoles();
      } catch (err) {
        console.error(err);
        this.addToast('Error: Failed to delete role.');
      }
    },
    async openEditModal(roleId) {
      this.loading = true;
      this.showEditModal = true;
      this.selectedRoleData = null;
      try {
        const response = await fetch('/admin/roles/' + roleId + '/edit', {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
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
      this.menus = [];
      this.menuPermissions = {};
      try {
        const response = await fetch('/admin/roles/' + roleId + '/permissions-data', {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await response.json();
        this.menus = data.menus || [];
        this.menuPermissions = data.menuPermissions || {};
      } catch (error) {
        console.error('Error loading menu permissions:', error);
      } finally {
        this.loadingPermissions = false;
      }
    },
    getMenuPerm(menuId, key) {
      const p = this.menuPermissions[menuId];
      return p ? (p[key] || false) : false;
    },
    setMenuPerm(menuId, key, value) {
      if (!this.menuPermissions[menuId]) this.menuPermissions[menuId] = { can_view: false, can_create: false, can_edit: false, can_delete: false };
      this.menuPermissions[menuId][key] = value;
    },
    async submitCreateRole(form) {
      const formData = new FormData(form);
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });
        const contentType = response.headers.get('content-type');
        if (response.ok) {
          const data = await response.json();
          this.showAddPanel = false;
          form.reset();
          this.addToast(data.message || 'New Role Created Successfully');
          await this.refreshRoles(true);
        } else if (contentType && contentType.includes('application/json')) {
          const data = await response.json();
          let errorMsg = 'Failed to create role';
          if (data.errors) {
            const firstError = Object.values(data.errors)[0];
            errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
          } else if (data.message) errorMsg = data.message;
          this.addToast('Error: ' + errorMsg);
        } else {
          this.addToast('Error: Failed to create role');
        }
      } catch (error) {
        console.error(error);
        this.addToast('Error: Failed to create role');
      }
    },
    async submitEditRole(form) {
      const formData = new FormData(form);
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });
        if (response.ok) {
          const data = await response.json();
          this.showEditModal = false;
          this.addToast(data.message || 'Role Updated Successfully');
          await this.refreshRoles();
        } else {
          const data = await response.json();
          let errorMsg = (data.errors && Object.values(data.errors)[0]) ? Object.values(data.errors)[0][0] : (data.message || 'Failed to update role');
          this.addToast('Error: ' + errorMsg);
        }
      } catch (error) {
        console.error(error);
        this.addToast('Error: Failed to update role');
      }
    },
    async submitPermissions() {
      if (!this.selectedRoleId) return;
      const menu_permissions = this.menus.map(m => ({
        menu_id: m.id,
        can_view: this.getMenuPerm(m.id, 'can_view'),
        can_create: this.getMenuPerm(m.id, 'can_create'),
        can_edit: this.getMenuPerm(m.id, 'can_edit'),
        can_delete: this.getMenuPerm(m.id, 'can_delete')
      }));
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
      try {
        const response = await fetch('/admin/roles/' + this.selectedRoleId + '/permissions', {
          method: 'POST',
          body: JSON.stringify({ menu_permissions, _token: csrf }),
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' }
        });
        const data = response.ok ? await response.json().catch(() => ({})) : {};
        if (response.ok) {
          this.showPermissionModal = false;
          this.addToast(data.message || 'Menu permissions updated for ' + this.selectedRole);
          await this.refreshRoles();
        } else {
          this.addToast(data.message || 'Error: Failed to update menu permissions');
        }
      } catch (error) {
        console.error(error);
        this.addToast('Error: Failed to update menu permissions');
      }
    }
  }));
});
</script>
<div x-data="rolesPage()" data-destroy-url="{{ url('/admin/roles') }}" data-can-manage-menu-permissions="{{ ($canManageMenuPermissions ?? false) ? '1' : '0' }}" class="h-full flex gap-3 workspace-transition relative p-6" :class="showAddPanel ? '' : ''">
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
    <div id="roles-list-content" class="flex-1 overflow-y-auto custom-scroll pr-2 pb-4">

        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
            <p class="text-sm text-emerald-600 font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <!-- List View (Roles Table) -->
        <table x-show="viewType === 'list'" class="w-full text-left island-row" @click="handleTableClick($event)">
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
                        <div class="flex items-center justify-end gap-1">
                            @if($canManageMenuPermissions ?? false)
                            <button type="button" data-action="permissions" data-role-id="{{ $role->id }}" data-role-name="{{ e($role->name) }}"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                title="Menu Permissions">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </button>
                            @endif
                            <button type="button" data-action="edit" data-role-id="{{ $role->id }}"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                title="Edit Role">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button type="button" data-action="delete" data-role-id="{{ $role->id }}" data-role-name="{{ e($role->name) }}"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg"
                                title="Delete Role">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
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
        <div id="roles-pagination-wrap" class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between shrink-0">
        @if($roles->hasPages())
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} Roles</p>
            <div class="flex gap-1">
                {{ $roles->links('pagination.simple-tailwind') }}
            </div>
        @else
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $roles->count() }} Roles</p>
        @endif
        </div>

        <!-- Grid View (Roles) -->
        <div x-show="viewType === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" @click="handleTableClick($event)">
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
                    @if($canManageMenuPermissions ?? false)
                    <button type="button" data-action="permissions" data-role-id="{{ $role->id }}" data-role-name="{{ e($role->name) }}"
                        class="flex-1 py-2 bg-slate-50 text-[10px] font-bold text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors">Menu Permissions</button>
                    @endif
                    <button type="button" data-action="edit" data-role-id="{{ $role->id }}" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:text-indigo-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                    <button type="button" data-action="delete" data-role-id="{{ $role->id }}" data-role-name="{{ e($role->name) }}" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:text-rose-600" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
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

        <form method="POST" action="{{ route('admin.roles.store') }}" @submit.prevent="submitCreateRole($event.target)" class="p-8 space-y-6 flex-1 overflow-y-auto custom-scroll">
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
                    @submit.prevent="submitEditRole($event.target)" class="space-y-6">
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
                    <h3 class="font-bold text-slate-800">Menu Permissions Management</h3>
                </div>
                <button @click="showPermissionModal = false" class="p-2 text-slate-300 hover:text-slate-600"><svg
                        class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>

            <div class="flex-1 overflow-hidden flex">
                <div class="w-64 bg-slate-50/50 border-r border-slate-100 p-6 shrink-0 hidden md:block">
                    <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Active Focus</label>
                        <p class="font-bold text-indigo-600 text-sm" x-text="selectedRole"></p>
                    </div>
                    <p class="mt-6 text-[10px] text-slate-400 leading-relaxed italic">Configure granular access for this specific role. Only menus you created in Menu Management appear here. Changes affect all users with this role.</p>
                </div>

                <div class="flex-1 flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6 custom-scroll" x-show="!loadingPermissions">
                        <table class="w-full">
                            <thead class="sticky top-0 bg-white z-10">
                                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                    <th class="text-left pb-4">Menu Name</th>
                                    <th class="text-center pb-4">View</th>
                                    <th class="text-center pb-4">Create</th>
                                    <th class="text-center pb-4">Edit</th>
                                    <th class="text-center pb-4">Delete</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="menu in menus" :key="menu.id">
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 font-bold text-xs text-slate-700" x-text="menu.title"></td>
                                        <td class="py-4 text-center">
                                            <input type="checkbox"
                                                :checked="getMenuPerm(menu.id, 'can_view')"
                                                @change="setMenuPerm(menu.id, 'can_view', $event.target.checked)"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                        </td>
                                        <td class="py-4 text-center">
                                            <input type="checkbox"
                                                :checked="getMenuPerm(menu.id, 'can_create')"
                                                @change="setMenuPerm(menu.id, 'can_create', $event.target.checked)"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                        </td>
                                        <td class="py-4 text-center">
                                            <input type="checkbox"
                                                :checked="getMenuPerm(menu.id, 'can_edit')"
                                                @change="setMenuPerm(menu.id, 'can_edit', $event.target.checked)"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                        </td>
                                        <td class="py-4 text-center">
                                            <input type="checkbox"
                                                :checked="getMenuPerm(menu.id, 'can_delete')"
                                                @change="setMenuPerm(menu.id, 'can_delete', $event.target.checked)"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="menus.length === 0 && !loadingPermissions">
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">No menus yet. Create menus in Menu Management and they will appear here.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div x-show="loadingPermissions" class="flex-1 flex items-center justify-center p-12">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                            <p class="text-sm text-slate-500 font-bold">Loading menu permissions...</p>
                        </div>
                    </div>

                    <div class="p-6 border-t border-slate-50 bg-slate-50/20 flex justify-end gap-3">
                        <button @click="showPermissionModal = false"
                            class="px-6 py-2 text-xs font-bold text-slate-500 hover:text-slate-800">Cancel</button>
                        <button type="button" @click="submitPermissions()"
                            class="px-8 py-3 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Save Permissions</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Role Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showDeleteModal = false; deleteRoleId = null; deleteRoleName = ''"
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-rose-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Delete Role</h3>
                <p class="text-sm text-slate-500 mb-2">Are you sure you want to delete this role?</p>
                <p class="text-sm font-semibold text-slate-700 mb-6" x-text="deleteRoleName || 'This role'"></p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="showDeleteModal = false; deleteRoleId = null; deleteRoleName = ''"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="confirmDelete()"
                        class="px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm rounded-xl transition-colors">
                        Yes, delete
                    </button>
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

