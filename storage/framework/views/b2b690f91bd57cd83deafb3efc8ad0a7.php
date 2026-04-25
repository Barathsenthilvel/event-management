<?php $__env->startSection('content'); ?>
<?php
    $menuItems = $menus->map(function ($menu) {
        return [
            'id' => $menu->id,
            'name' => $menu->title,
            'icon' => $menu->icon,
            'route' => $menu->route_name,
            'sort' => $menu->order,
            'parent' => $menu->parent?->title,
            'status' => (bool) $menu->is_active,
            'parent_id' => $menu->parent_id,
            'description' => $menu->description,
            'update_url' => route('admin.menus.update', $menu),
            'delete_url' => route('admin.menus.destroy', $menu),
        ];
    });
?>

<div class="h-full flex gap-3 workspace-transition relative p-6" x-data="menuPage()">
    <div class="flex flex-col gap-3 workspace-transition flex-1 min-w-0" :class="showCreateMenu ? 'w-2/3' : 'w-full'">
    <!-- Header text -->
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-lg font-bold text-slate-800">Menu Management</h1>
            <p class="text-xs text-slate-500 mt-1">Manage sidebar menus, order and visibility with a rich UI.</p>
        </div>
    </div>

    <!-- Main Content Board -->
    <div class="bg-white flex-1 rounded-[24px] shadow-sm flex flex-col p-6 overflow-hidden relative">
        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-800 shrink-0" role="alert">
                <ul class="list-disc list-inside space-y-0.5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <!-- Tools Bar: search left, actions right -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6 shrink-0">
            <div class="relative w-full sm:max-w-xs md:max-w-md min-w-0">
                <input type="search" x-model="searchQuery" placeholder="Search menus…" autocomplete="off"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-500/10">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex flex-wrap items-center gap-3 justify-end shrink-0">
                <div class="flex bg-slate-50 p-1 rounded-xl">
                    <button type="button" @click="viewType = 'list'"
                        :class="viewType === 'list' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                        class="p-2 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <button type="button" @click="viewType = 'grid'"
                        :class="viewType === 'grid' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                        class="p-2 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                </div>
                <button type="button" @click="openModal('create')"
                    class="bg-[#0f172a] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-bold transition-all shadow-lg">+ Create Menu</button>
            </div>
        </div>

        <!-- Content Area (Scrollable) -->
        <div class="flex-1 overflow-y-auto custom-scroll pr-2 pb-20">
            <!-- List View -->
            <table x-show="viewType === 'list'" class="w-full text-left island-row">
                <thead
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest sticky top-0 bg-white z-10">
                    <tr>
                        <th class="px-6 py-4">Menu Name</th>
                        <th class="px-6 py-4">Icon</th>
                        <th class="px-6 py-4">Route</th>
                        <th class="px-6 py-4 text-center">Sort Order</th>
                        <th class="px-6 py-4">Parent Menu</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    <template x-for="menu in filteredMenus" :key="menu.id">
                        <tr class="group transition-all">
                            <td
                                class="px-6 py-4 bg-white border-y border-l border-slate-100 first:rounded-l-2xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold"
                                        x-text="menu.name ? menu.name[0] : '?'">
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800" x-text="menu.name"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100">
                                <template x-if="menu.icon">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                            <i :class="menu.icon" class="text-indigo-500 text-sm"></i>
                                        </div>
                                        <code class="text-[9px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-500" x-text="menu.icon"></code>
                                    </div>
                                </template>
                                <template x-if="!menu.icon">
                                    <span class="text-slate-300 text-xs">—</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100 text-slate-500 font-medium">
                                <span x-text="menu.route || '-'"></span>
                            </td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100 text-center font-bold text-indigo-500"
                                x-text="menu.sort ?? '-'"></td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100 text-slate-500 font-medium"
                                x-text="menu.parent || '-'"></td>
                            <td class="px-6 py-4 bg-white border-y border-slate-100 text-center">
                                <span
                                    :class="menu.status ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-100 text-slate-400 border-slate-200'"
                                    class="px-2 py-1 rounded-md font-black text-[9px] uppercase border"
                                    x-text="menu.status ? 'Active' : 'Inactive'">
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 bg-white border-y border-r border-slate-100 last:rounded-r-2xl text-right">
                                <div
                                    class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button"
                                        @click="openModal('edit', menu)"
                                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                        title="Edit Menu">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button type="button"
                                        @click="menuToDelete = menu; showDeleteModal = true"
                                        class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg"
                                        title="Delete Menu">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Grid View -->
            <div x-show="viewType === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="menu in filteredMenus" :key="'grid-' + menu.id">
                    <div
                        class="p-5 border border-slate-100 rounded-[20px] hover:shadow-lg hover:-translate-y-1 transition-all bg-white group relative">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-indigo-500 font-bold group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                <template x-if="menu.icon">
                                    <i :class="menu.icon" class="text-lg"></i>
                                </template>
                                <template x-if="!menu.icon">
                                    <span x-text="menu.name ? menu.name[0] : '?'"></span>
                                </template>
                            </div>
                            <span
                                :class="menu.status ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                class="px-2 py-0.5 rounded font-bold text-[9px] uppercase"
                                x-text="menu.status ? 'Active' : 'Inactive'">
                            </span>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1" x-text="menu.name"></h4>
                        <p class="text-xs text-slate-400 mb-1">
                            Route:
                            <span x-text="menu.route || '-'"></span>
                        </p>
                        <p class="text-xs text-slate-400 mb-4">
                            Parent:
                            <span x-text="menu.parent || '-'"></span>
                        </p>

                        <div class="mt-4 flex gap-2 pt-4 border-t border-slate-50">
                            <button type="button"
                                @click="openModal('edit', menu)"
                                class="flex-1 py-2 bg-slate-50 text-[10px] font-bold text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors">
                                Edit Details
                            </button>
                            <button type="button"
                                @click="menuToDelete = menu; showDeleteModal = true"
                                class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:text-rose-600 hover:bg-rose-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    </div>

    <!-- Add / Edit Menu Side Panel (same UI as Admin panel) -->
    <div x-show="showCreateMenu" x-cloak
        class="w-1/3 bg-white rounded-[24px] shadow-2xl border border-slate-100 flex flex-col workspace-transition overflow-hidden flex-shrink-0"
        x-transition:enter="transition-all ease-out duration-500"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition-all ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0">

        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/20 shrink-0">
            <h3 class="font-bold text-slate-800" x-text="isEditing ? 'Edit Menu' : 'Create New Menu'"></h3>
            <button type="button" @click="closePanel()"
                class="p-2 text-slate-400 hover:bg-white rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form
            :action="isEditing ? menuForm.update_url : '<?php echo e(route('admin.menus.store')); ?>'"
            method="POST"
            class="flex-1 overflow-y-auto custom-scroll p-8">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" :value="isEditing ? 'PUT' : 'POST'">
                <input type="hidden" name="is_root_menu" :value="menuForm.is_parent ? 1 : 0">

                <div class="space-y-5">
                    <!-- Menu Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Menu Name <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                        <input type="text" x-model="menuForm.name" name="title" placeholder="e.g. Dashboard" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Sort Order <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                        <input type="number" x-model="menuForm.sort" name="order" placeholder="0" min="0" step="1" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>

                    <!-- Icon Picker -->
                    <div x-data="{ iconSearch: '' }">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Icon <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>

                        <!-- Selected icon preview + hidden input -->
                        <input type="hidden" name="icon" :value="menuForm.icon">
                        <div class="flex items-center gap-3 mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <template x-if="menuForm.icon">
                                    <i :class="menuForm.icon" class="text-indigo-600 text-base"></i>
                                </template>
                                <template x-if="!menuForm.icon">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-700" x-text="menuForm.icon || 'No icon selected'"></p>
                                <p class="text-[10px] text-slate-400">Click an icon below to select</p>
                            </div>
                            <button type="button" x-show="menuForm.icon" @click="menuForm.icon = ''"
                                class="text-slate-400 hover:text-rose-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Search -->
                        <input type="text" x-model="iconSearch" placeholder="Search icons..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 mb-2 transition-all">

                        <!-- Icon Grid -->
                        <div class="grid grid-cols-6 gap-1.5 max-h-52 overflow-y-auto custom-scroll p-1 bg-slate-50 rounded-xl border border-slate-200">
                            <template x-for="icon in iconList.filter(i => !iconSearch || i.label.toLowerCase().includes(iconSearch.toLowerCase()))" :key="icon.cls">
                                <button type="button"
                                    @click="menuForm.icon = icon.cls"
                                    :title="icon.label"
                                    :class="menuForm.icon === icon.cls ? 'bg-indigo-600 text-white ring-2 ring-indigo-400' : 'bg-white text-slate-500 hover:bg-indigo-50 hover:text-indigo-600'"
                                    class="w-full aspect-square rounded-lg flex items-center justify-center transition-all text-sm">
                                    <i :class="icon.cls"></i>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Route -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Route Name <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                        <input type="text" x-model="menuForm.route" name="route_name" placeholder="e.g. admin.menus.index" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Description</label>
                        <textarea x-model="menuForm.description" name="description" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                            placeholder="Short description for this menu"></textarea>
                    </div>

                    <!-- Parent / Submenu toggle and Parent Menu -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="is_parent_menu" x-model="menuForm.is_parent"
                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                            <label for="is_parent_menu" class="text-xs font-medium text-slate-600">
                                This is a parent menu
                            </label>
                        </div>

                        <div x-show="!menuForm.is_parent">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-2">Parent Menu <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                            <select x-model="menuForm.parent_id" name="parent_id" :required="!menuForm.is_parent"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                                <option value="">Select Parent Menu</option>
                                <template x-for="parent in parents" :key="parent.id">
                                    <option :value="parent.id" x-text="parent.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-sm font-bold text-slate-700 block">Active Status</span>
                            <p class="text-[10px] text-slate-400">Enable or disable this menu item</p>
                        </div>
                        <button type="button" @click="menuForm.status = !menuForm.status"
                            :class="menuForm.status ? 'bg-indigo-600' : 'bg-slate-300'"
                            class="w-11 h-6 rounded-full relative transition-all shadow-inner">
                            <span :class="menuForm.status ? 'translate-x-5' : 'translate-x-0.5'"
                                class="absolute top-0.5 w-5 h-5 bg-white rounded-full transition-all shadow-sm"></span>
                        </button>
                        <input type="hidden" name="is_active" :value="menuForm.status ? 1 : 0">
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" @click="closePanel()"
                        class="flex-1 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all hover:scale-[1.02]"
                        x-text="isEditing ? 'Update Menu' : 'Save Menu'">
                    </button>
                </div>
            </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[150] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-[40px] p-10 max-w-sm w-full text-center shadow-2xl"
            @click.away="showDeleteModal = false"
            x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
            x-transition:enter-start="scale-50 opacity-0 translate-y-10"
            x-transition:enter-end="scale-100 opacity-100 translate-y-0">

            <div
                class="w-16 h-16 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-rose-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Delete Menu?</h3>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                Are you sure you want to delete
                <span class="font-bold text-slate-800"
                    x-text="menuToDelete ? menuToDelete.name : 'this item'"></span>?
                This action cannot be undone.
            </p>
            <div class="flex gap-3">
                <button @click="showDeleteModal = false"
                    class="flex-1 py-4 bg-slate-50 hover:bg-slate-100 rounded-2xl font-bold text-slate-600 transition-colors">
                    Cancel
                </button>

                <form x-show="menuToDelete" method="POST"
                    :action="menuToDelete ? menuToDelete.delete_url : '#'"
                    class="flex-1">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit"
                        class="w-full py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl font-bold shadow-lg shadow-rose-200 transition-all hover:shadow-xl hover:scale-[1.02]">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script>
    function menuPage() {
        return {
            viewType: 'list',
            showCreateMenu: false,
            showDeleteModal: false,
            isEditing: false,
            searchQuery: '',

            get filteredMenus() {
                const q = (this.searchQuery || '').toLowerCase().trim();
                if (!q) return this.menus;
                return this.menus.filter((m) => {
                    const name = (m.name || '').toLowerCase();
                    const route = (m.route || '').toLowerCase();
                    const parent = (m.parent || '').toLowerCase();
                    return name.includes(q) || route.includes(q) || parent.includes(q);
                });
            },

            iconList: [
                { cls: 'fas fa-home',            label: 'Home' },
                { cls: 'fas fa-tachometer-alt',  label: 'Dashboard' },
                { cls: 'fas fa-users',            label: 'Users' },
                { cls: 'fas fa-user',             label: 'User' },
                { cls: 'fas fa-user-tie',         label: 'Admin' },
                { cls: 'fas fa-user-shield',      label: 'User Shield' },
                { cls: 'fas fa-user-cog',         label: 'User Settings' },
                { cls: 'fas fa-id-card',          label: 'ID Card' },
                { cls: 'fas fa-lock',             label: 'Lock' },
                { cls: 'fas fa-key',              label: 'Key' },
                { cls: 'fas fa-shield-alt',       label: 'Shield' },
                { cls: 'fas fa-cog',              label: 'Settings' },
                { cls: 'fas fa-cogs',             label: 'Cogs' },
                { cls: 'fas fa-sliders-h',        label: 'Sliders' },
                { cls: 'fas fa-chart-bar',        label: 'Chart Bar' },
                { cls: 'fas fa-chart-line',       label: 'Chart Line' },
                { cls: 'fas fa-chart-pie',        label: 'Chart Pie' },
                { cls: 'fas fa-table',            label: 'Table' },
                { cls: 'fas fa-list',             label: 'List' },
                { cls: 'fas fa-th',               label: 'Grid' },
                { cls: 'fas fa-layer-group',      label: 'Layers' },
                { cls: 'fas fa-sitemap',          label: 'Sitemap' },
                { cls: 'fas fa-project-diagram',  label: 'Diagram' },
                { cls: 'fas fa-folder',           label: 'Folder' },
                { cls: 'fas fa-folder-open',      label: 'Folder Open' },
                { cls: 'fas fa-file',             label: 'File' },
                { cls: 'fas fa-file-alt',         label: 'File Alt' },
                { cls: 'fas fa-clipboard',        label: 'Clipboard' },
                { cls: 'fas fa-tasks',            label: 'Tasks' },
                { cls: 'fas fa-bell',             label: 'Bell' },
                { cls: 'fas fa-envelope',         label: 'Envelope' },
                { cls: 'fas fa-calendar',         label: 'Calendar' },
                { cls: 'fas fa-calendar-alt',     label: 'Calendar Alt' },
                { cls: 'fas fa-clock',            label: 'Clock' },
                { cls: 'fas fa-search',           label: 'Search' },
                { cls: 'fas fa-filter',           label: 'Filter' },
                { cls: 'fas fa-tag',              label: 'Tag' },
                { cls: 'fas fa-tags',             label: 'Tags' },
                { cls: 'fas fa-star',             label: 'Star' },
                { cls: 'fas fa-bookmark',         label: 'Bookmark' },
                { cls: 'fas fa-heart',            label: 'Heart' },
                { cls: 'fas fa-shopping-cart',    label: 'Cart' },
                { cls: 'fas fa-credit-card',      label: 'Credit Card' },
                { cls: 'fas fa-wallet',           label: 'Wallet' },
                { cls: 'fas fa-money-bill',       label: 'Money' },
                { cls: 'fas fa-database',         label: 'Database' },
                { cls: 'fas fa-server',           label: 'Server' },
                { cls: 'fas fa-globe',            label: 'Globe' },
                { cls: 'fas fa-link',             label: 'Link' },
                { cls: 'fas fa-map-marker-alt',   label: 'Location' },
                { cls: 'fas fa-building',         label: 'Building' },
                { cls: 'fas fa-university',       label: 'University' },
                { cls: 'fas fa-briefcase',        label: 'Briefcase' },
                { cls: 'fas fa-book',             label: 'Book' },
                { cls: 'fas fa-book-open',        label: 'Book Open' },
                { cls: 'fas fa-graduation-cap',   label: 'Education' },
                { cls: 'fas fa-image',            label: 'Image' },
                { cls: 'fas fa-video',            label: 'Video' },
                { cls: 'fas fa-music',            label: 'Music' },
                { cls: 'fas fa-print',            label: 'Print' },
                { cls: 'fas fa-download',         label: 'Download' },
                { cls: 'fas fa-upload',           label: 'Upload' },
                { cls: 'fas fa-cloud',            label: 'Cloud' },
                { cls: 'fas fa-wifi',             label: 'WiFi' },
                { cls: 'fas fa-plug',             label: 'Plug' },
                { cls: 'fas fa-boxes',            label: 'Boxes' },
                { cls: 'fas fa-box',              label: 'Box' },
                { cls: 'fas fa-cubes',            label: 'Cubes' },
                { cls: 'fas fa-medal',            label: 'Medal' },
                { cls: 'fas fa-trophy',           label: 'Trophy' },
                { cls: 'fas fa-percent',          label: 'Percent' },
                { cls: 'fas fa-receipt',          label: 'Receipt' },
                { cls: 'fas fa-hand-holding-usd', label: 'Payment' },
                { cls: 'fas fa-id-badge',         label: 'Badge' },
                { cls: 'fas fa-qrcode',           label: 'QR Code' },
                { cls: 'fas fa-barcode',          label: 'Barcode' },
            ],

            menuForm: {
                id: null,
                name: '',
                icon: '',
                route: '',
                sort: '0',
                parent_id: '',
                is_parent: true,
                description: '',
                status: true,
                update_url: ''
            },
            menuToDelete: null,
            menus: <?php echo json_encode($menuItems, 15, 512) ?>,
            parents: <?php echo json_encode($parents->map(fn($p) => ['id' => $p->id, 'name' => $p->title]), 512) ?>,
            openModal(mode, menu = null) {
                this.isEditing = mode === 'edit';
                if (this.isEditing && menu) {
                    this.menuForm = {
                        id: menu.id,
                        name: menu.name,
                        icon: menu.icon || '',
                        route: menu.route || '',
                        sort: menu.sort !== null && menu.sort !== undefined ? String(menu.sort) : '0',
                        parent_id: menu.parent_id || '',
                        is_parent: !menu.parent_id,
                        description: menu.description || '',
                        status: !!menu.status,
                        update_url: menu.update_url
                    };
                } else {
                    this.menuForm = {
                        id: null,
                        name: '',
                        icon: '',
                        route: '',
                        sort: '0',
                        parent_id: '',
                        is_parent: true,
                        description: '',
                        status: true,
                        update_url: ''
                    };
                }
                this.showCreateMenu = true;
            },
            closePanel() {
                this.showCreateMenu = false;
                this.isEditing = false;
                this.menuForm = {
                    id: null,
                    name: '',
                    icon: '',
                    route: '',
                    sort: '0',
                    parent_id: '',
                    is_parent: true,
                    description: '',
                    status: true,
                    update_url: ''
                };
            }
        }
    }
</script>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/admin/menus/index.blade.php ENDPATH**/ ?>