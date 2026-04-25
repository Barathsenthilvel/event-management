<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>GNAT| Admin Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #F1F5F9;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .nav-item-active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        [x-cloak] {
            display: none !important;
        }

        .search-expand {
            transition: width 0.3s ease;
        }

        .transition-all-300 {
            transition: all 0.3s ease-in-out;
        }

        .workspace-transition {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .island-row {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        /* Smooth page transitions */
        body {
            transition: opacity 0.3s ease-in-out;
        }

        body.page-reloading {
            opacity: 0.7;
        }
    </style>
</head>
<body class="h-screen overflow-hidden flex p-3 gap-3" x-data="{
    sidebarOpen: true,
    viewType: 'list',
    showLogout: false,
    showNotifications: false,
    refreshing: false,
    pendingApprovalsCount: <?php echo e((int) (\App\Models\User::query()->where('profile_completed', true)->where('is_approved', false)->count())); ?>,
    notifications: [
        <?php
            $pendingApprovalsCountHeader = \App\Models\User::query()
                ->where('profile_completed', true)
                ->where('is_approved', false)
                ->count();
        ?>
        <?php if($pendingApprovalsCountHeader > 0): ?>
        { title: 'Member Approvals', desc: '<?php echo e((int) $pendingApprovalsCountHeader); ?> member(s) waiting for approval', time: 'Now', type: 'alert', url: '<?php echo e(route('admin.members.pending-approvals.index')); ?>' },
        <?php endif; ?>
        { title: 'Security Alert', desc: 'New login activity detected', time: 'Just now', type: 'info' }
    ],
    toasts: [],
    addToast(msg, type = 'success') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, msg, type: type === 'error' ? 'error' : 'success' });
        setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4500);
    },
    showLoader() {
        this.refreshing = true;
    },
    hideLoader() {
        this.refreshing = false;
    },
    async loadPage(url) {
        this.showLoader();
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });
            if (response.ok) {
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.flex-1.overflow-y-auto.custom-scroll');
                const currentContent = document.querySelector('.flex-1.overflow-y-auto.custom-scroll');
                if (newContent && currentContent) {
                    currentContent.innerHTML = newContent.innerHTML;
                    window.history.pushState({}, '', url);
                }
            }
        } catch (error) {
            console.error('Error loading page:', error);
        } finally {
            this.hideLoader();
        }
    },
    async refreshContent() {
        this.showLoader();
        try {
            await new Promise(resolve => setTimeout(resolve, 300));
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
                const newContent = doc.querySelector('.bg-white.flex-1.rounded-\\[24px\\]');
                if (newContent) {
                    const currentContent = document.querySelector('.bg-white.flex-1.rounded-\\[24px\\]');
                    if (currentContent) {
                        currentContent.innerHTML = newContent.innerHTML;
                    }
                }
            }
        } catch (error) {
            console.error('Error refreshing content:', error);
            window.location.reload();
        } finally {
            this.hideLoader();
        }
    },
    deleteModalOpen: false,
    deleteModalTitle: 'Delete this item?',
    deleteModalMessage: '',
    pendingDeleteFormId: null,
    openDeleteModal(formId, message, title) {
        this.pendingDeleteFormId = formId;
        this.deleteModalMessage = message || 'This action cannot be undone.';
        this.deleteModalTitle = title || 'Delete this item?';
        this.deleteModalOpen = true;
    },
    closeDeleteModal() {
        this.deleteModalOpen = false;
        this.pendingDeleteFormId = null;
    },
    confirmPendingDelete() {
        if (!this.pendingDeleteFormId) return;
        const form = document.getElementById(this.pendingDeleteFormId);
        if (form) form.submit();
        this.closeDeleteModal();
    }
}">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="bg-[#0f172a] rounded-[24px] flex flex-col transition-all-300 shadow-2xl z-20">
        <!-- Logo Area -->
        <div class="p-6 flex items-center gap-3 shrink-0">
            <div
                class="w-8 h-8 bg-indigo-500 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            </div>
            <span x-show="sidebarOpen"
                class="font-bold text-lg tracking-tight text-white transition-opacity duration-300"
                x-transition>GNAT</span>
        </div>

        <!-- Navigation: fixed links + dynamic menus from Menu Management -->
        <?php
            $admin = Auth::guard('admin')->user();
            $pendingApprovalsCount = \App\Models\User::query()
                ->where('profile_completed', true)
                ->where('is_approved', false)
                ->count();
        ?>
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scroll">
            
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.dashboard') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.dashboard') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Dashboard</span>
            </a>

            
            <?php $__currentLoopData = $sidebarMenus ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $url = $menu->route_name && \Illuminate\Support\Facades\Route::has($menu->route_name)
                        ? route($menu->route_name)
                        : '#';
                    $isActive = $menu->route_name && request()->routeIs(preg_replace('/\.(index|show|create|edit)$/', '.*', $menu->route_name));
                ?>
                <?php if($menu->children->isEmpty()): ?>
                    <a href="<?php echo e($url); ?>"
                        class="flex items-center gap-3 p-3 <?php echo e($isActive ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                        <?php if($menu->icon && (str_starts_with($menu->icon, 'fa') || str_contains($menu->icon, 'icon-'))): ?>
                            <i class="w-5 h-5 flex items-center justify-center <?php echo e($menu->icon); ?> <?php echo e($isActive ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?>"></i>
                        <?php else: ?>
                            <svg class="w-5 h-5 <?php echo e($isActive ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        <?php endif; ?>
                        <span x-show="sidebarOpen" class="text-sm font-medium"><?php echo e($menu->title); ?></span>
                    </a>
                <?php else: ?>
                    <?php
                        $childActive = $menu->children->contains(function ($c) {
                            if (!$c->route_name) return false;
                            $pattern = \Illuminate\Support\Str::beforeLast($c->route_name, '.') . '.*';
                            return request()->routeIs($pattern) || request()->routeIs($c->route_name);
                        });
                    ?>
                    <div x-data="{ open: true }" class="space-y-1">
                        <div class="flex items-center gap-3 p-3 <?php echo e($isActive ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                            <a href="<?php echo e($url); ?>" class="flex items-center gap-3 flex-1 min-w-0">
                                <?php if($menu->icon && (str_starts_with($menu->icon, 'fa') || str_contains($menu->icon, 'icon-'))): ?>
                                    <i class="w-5 h-5 flex items-center justify-center flex-shrink-0 <?php echo e($menu->icon); ?> <?php echo e($isActive ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?>"></i>
                                <?php else: ?>
                                    <svg class="w-5 h-5 flex-shrink-0 <?php echo e($isActive ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                <?php endif; ?>
                                <span x-show="sidebarOpen" class="text-sm font-medium truncate"><?php echo e($menu->title); ?></span>
                            </a>
                            <button type="button" x-show="sidebarOpen" @click.prevent="open = !open" class="flex-shrink-0 p-1 rounded hover:bg-white/10 transition-colors"
                                :aria-expanded="open">
                                <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        <div x-show="open && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="ml-10 space-y-1 mt-1 text-xs font-medium text-slate-500 border-l border-slate-700 pl-3">
                            <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $childUrl = $child->route_name && \Illuminate\Support\Facades\Route::has($child->route_name)
                                        ? route($child->route_name)
                                        : '#';
                                    $childActive = $child->route_name && (request()->routeIs($child->route_name) || request()->routeIs(\Illuminate\Support\Str::beforeLast($child->route_name, '.') . '.*'));
                                ?>
                                <a href="<?php echo e($childUrl); ?>"
                                    class="block py-2 <?php echo e($childActive ? 'text-indigo-400 font-bold' : 'hover:text-white'); ?> transition-colors">
                                    <?php echo e($child->title); ?>

                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($admin && $admin->is_super_admin): ?>
            <?php if($admin && $admin->hasPermission('user.view')): ?>
            <a href="<?php echo e(route('admin.admins.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.admins.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.admins.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Users</span>
            </a>
            <?php endif; ?>

            <a href="<?php echo e(route('admin.events.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.events.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.events.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Events</span>
            </a>

            <a href="<?php echo e(route('admin.home-banners.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.home-banners.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.home-banners.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Home Banners</span>
            </a>

            <a href="<?php echo e(route('admin.home-blogs.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.home-blogs.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.home-blogs.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Home Blogs</span>
            </a>

            <a href="<?php echo e(route('admin.home-galleries.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.home-galleries.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.home-galleries.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Home Galleries</span>
            </a>

            <a href="<?php echo e(route('admin.members.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.members.index') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.members.index') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Members</span>
            </a>

            <a href="<?php echo e(route('admin.members.pending-approvals.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.members.pending-approvals.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.members.pending-approvals.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8zm9 10v-2a4 4 0 00-3-3.87" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Member Approvals</span>
                <?php if($pendingApprovalsCount > 0): ?>
                    <span x-show="sidebarOpen"
                        class="ml-auto inline-flex items-center justify-center min-w-6 h-6 px-2 rounded-xl bg-rose-500 text-white text-[10px] font-black">
                        <?php echo e((int) $pendingApprovalsCount); ?>

                    </span>
                <?php endif; ?>
            </a>

            <a href="<?php echo e(route('admin.designations.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.designations.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.designations.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Designations</span>
            </a>

            <?php if($admin && $admin->hasPermission('role.view')): ?>
            <a href="<?php echo e(route('admin.roles.index')); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.roles.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.roles.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Roles & Permissions</span>
            </a>
            <?php endif; ?>

            <?php if($admin && $admin->hasPermission('menu.view') && \Illuminate\Support\Facades\Route::has('admin.menus.index')): ?>
            <a href="<?php echo e(\Illuminate\Support\Facades\Route::has('admin.menus.index') ? route('admin.menus.index') : '#'); ?>"
                class="flex items-center gap-3 p-3 <?php echo e(request()->routeIs('admin.menus.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                <svg class="w-5 h-5 <?php echo e(request()->routeIs('admin.menus.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h10" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Menu Management</span>
            </a>
            <?php endif; ?>

            <?php if(\Illuminate\Support\Facades\Route::has('admin.settings.default-settings')): ?>
            <?php
                $settingsActive = request()->routeIs('admin.settings.*');
            ?>
            <div x-data="{ open: <?php echo e($settingsActive ? 'true' : 'false'); ?> }" class="space-y-1">
                <div class="flex items-center gap-3 p-3 <?php echo e($settingsActive ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5'); ?> rounded-xl transition-all group">
                    <a href="#" class="flex items-center gap-3 flex-1 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 <?php echo e($settingsActive ? 'text-indigo-400' : 'group-hover:text-indigo-400'); ?> transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium truncate">Settings</span>
                    </a>
                    <button type="button" x-show="sidebarOpen" @click.prevent="open = !open" class="flex-shrink-0 p-1 rounded hover:bg-white/10 transition-colors"
                        :aria-expanded="open">
                        <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
                <div x-show="open && sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="ml-10 space-y-1 mt-1 text-xs font-medium text-slate-500 border-l border-slate-700 pl-3">
                    <a href="<?php echo e(route('admin.settings.default-settings')); ?>"
                        class="block py-2 <?php echo e(request()->routeIs('admin.settings.default-settings*') ? 'text-indigo-400 font-bold' : 'hover:text-white'); ?> transition-colors">
                        Default Settings
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </nav>

        <!-- User Profile Bottom -->
        <div class="p-4 border-t border-white/5 bg-slate-800/50 rounded-b-[24px] shrink-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-xs font-bold text-indigo-300">
                    <?php echo e(strtoupper(substr(Auth::guard('admin')->user()->name, 0, 2))); ?></div>
                <div x-show="sidebarOpen">
                    <p class="text-xs font-bold text-white"><?php echo e(Auth::guard('admin')->user()->name); ?></p>
                    <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-wider"><?php echo e(Auth::guard('admin')->user()->is_super_admin ? 'Super Admin' : 'Admin'); ?></p>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex gap-3 workspace-transition relative overflow-hidden">
        <div class="flex flex-col gap-3 workspace-transition w-full min-h-0 flex-1">
            <!-- Header -->
            <header
                class="bg-white h-16 rounded-[24px] flex items-center justify-between px-6 shadow-sm border border-white shrink-0 z-10 relative">

                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 bg-slate-50 rounded-xl hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </button>

                    <div class="hidden md:block">
                        <h2 class="text-sm font-bold text-slate-800 tracking-tight">System Overview</h2>
                        <div
                            class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>Home</span>
                            <span class="text-indigo-300">/</span>
                            <span class="text-indigo-600">Dashboard</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative hidden sm:block group">
                        <input type="text" placeholder="Search..."
                            class="search-expand pl-9 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 w-48 focus:w-64 transition-all">
                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400 group-focus-within:text-indigo-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="hidden lg:block text-right pr-4 border-r border-slate-100 mr-2">
                        <p class="text-[9px] font-bold text-slate-400 uppercase">Last Login</p>
                        <p class="text-[11px] font-bold text-slate-600"><?php echo e(Auth::guard('admin')->user()->updated_at->format('H:i A')); ?> Today</p>
                    </div>

                    <div class="relative">
                        <button @click="showNotifications = !showNotifications"
                            class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-slate-500 relative">
                            <template x-if="pendingApprovalsCount > 0">
                                <span class="absolute -top-1 -right-1 min-w-6 h-6 px-2 rounded-xl bg-rose-500 text-white text-[10px] font-black flex items-center justify-center border-2 border-white">
                                    <span x-text="pendingApprovalsCount"></span>
                                </span>
                            </template>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>

                        <div x-show="showNotifications" @click.away="showNotifications = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 p-2">
                            <div class="flex items-center justify-between p-2 mb-2 border-b border-slate-50">
                                <h3 class="font-bold text-xs text-slate-700">Notifications</h3>
                                <button class="text-[10px] text-indigo-500 font-bold hover:underline">Mark all
                                    read</button>
                            </div>
                            <div class="space-y-1 max-h-64 overflow-y-auto custom-scroll">
                                <template x-for="note in notifications">
                                    <a :href="note.url || '#'"
                                        class="p-2 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer flex gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                            :class="note.type === 'alert' ? 'bg-rose-50 text-rose-500' : 'bg-blue-50 text-blue-500'">
                                            <svg x-show="note.type === 'alert'" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <svg x-show="note.type === 'info'" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-700" x-text="note.title"></p>
                                            <p class="text-[10px] text-slate-500" x-text="note.desc"></p>
                                            <p class="text-[9px] text-slate-300 mt-1" x-text="note.time"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <button @click="showLogout = true"
                        class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-red-50 transition-all text-slate-500 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <div class="bg-white flex-1 min-h-0 rounded-[24px] shadow-sm flex flex-col overflow-hidden relative">
                <!-- Global Loader Overlay -->
                <div x-show="refreshing" x-cloak
                    class="absolute inset-0 bg-white/90 backdrop-blur-sm z-50 flex items-center justify-center rounded-[24px]"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    <div class="text-center">
                        <div class="w-16 h-16 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-sm text-slate-600 font-bold">Loading...</p>
                    </div>
                </div>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </main>

    <!-- Global Script for Pagination -->
    <script>
        // Global function for pagination links to work with loader
        window.loadPageWithLoader = function(url) {
            const body = document.querySelector('body');
            if (body && body.__x && body.__x.$data) {
                const alpineData = body.__x.$data;
                if (alpineData && typeof alpineData.loadPage === 'function') {
                    alpineData.loadPage(url);
                } else {
                    window.location.href = url;
                }
            } else {
                window.location.href = url;
            }
        };
    </script>

    <!-- Toast Notifications (success + error) -->
    <div class="fixed bottom-10 right-10 z-[300] flex flex-col gap-3 pointer-events-none max-w-md">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-5" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="flex items-center gap-4 bg-[#111827] text-white pl-4 pr-6 py-4 rounded-full shadow-2xl border border-white/10 pointer-events-auto">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                    :class="toast.type === 'error' ? 'bg-red-500' : 'bg-emerald-500'">
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <svg x-show="toast.type !== 'error'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-bold leading-snug" x-text="toast.msg"></p>
            </div>
        </template>
    </div>

    <!-- Logout Modal -->
    <div x-show="showLogout" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-[40px] p-10 max-w-sm w-full text-center shadow-2xl scale-100"
            @click.away="showLogout = false"
            x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
            x-transition:enter-start="scale-50 opacity-0 translate-y-10"
            x-transition:enter-end="scale-100 opacity-100 translate-y-0">

            <div
                class="w-16 h-16 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-red-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">End Session?</h3>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed">You are about to securely log out of your admin
                dashboard.</p>
            <div class="flex gap-3">
                <button @click="showLogout = false"
                    class="flex-1 py-4 bg-slate-50 hover:bg-slate-100 rounded-2xl font-bold text-slate-600 transition-colors">Cancel</button>
                <form method="POST" action="<?php echo e(route('admin.logout')); ?>" class="flex-1">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                        class="w-full py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold shadow-lg shadow-red-200 transition-all hover:shadow-xl hover:scale-[1.02]">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Global delete confirmation modal -->
    <div x-show="deleteModalOpen" x-cloak
        class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm z-[110] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeDeleteModal()">
        <div class="bg-white rounded-[28px] p-8 max-w-md w-full shadow-2xl border border-slate-100"
            @click.away="closeDeleteModal()"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-5 shadow-inner shadow-rose-100/80 ring-1 ring-rose-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight" x-text="deleteModalTitle"></h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed" x-text="deleteModalMessage"></p>
                <p class="mt-2 text-xs font-semibold text-rose-600/90">This cannot be undone.</p>
            </div>
            <div class="flex gap-3 mt-8">
                <button type="button" @click="closeDeleteModal()"
                    class="flex-1 py-3.5 rounded-2xl font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Cancel
                </button>
                <button type="button" @click="confirmPendingDelete()"
                    class="flex-1 py-3.5 rounded-2xl font-bold text-sm text-white bg-rose-600 hover:bg-rose-700 shadow-lg shadow-rose-200/60 transition-all hover:shadow-xl">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        window.adminOpenDeleteModal = function (formId, message, title) {
            if (!window.Alpine || typeof Alpine.$data !== 'function') return;
            var root = Alpine.$data(document.querySelector('body'));
            if (root && typeof root.openDeleteModal === 'function') {
                root.openDeleteModal(formId, message, title);
            }
        };
        window.adminOpenDeleteModalFromEl = function (el) {
            if (!el) return;
            var formId = el.getAttribute('data-delete-form');
            var message = el.getAttribute('data-delete-message') || '';
            var title = el.getAttribute('data-delete-title') || '';
            adminOpenDeleteModal(formId, message, title || undefined);
        };
    </script>

    <script>
        (function () {
            function flushAdminToasts() {
                if (window.__adminFlashConsumed) return true;
                if (!window.Alpine || typeof Alpine.$data !== 'function') return false;
                var root = Alpine.$data(document.querySelector('body'));
                if (!root || typeof root.addToast !== 'function') return false;
                window.__adminFlashConsumed = true;
                <?php if(session('success')): ?>
                root.addToast(<?php echo json_encode(session('success'), 15, 512) ?>, 'success');
                <?php elseif(session('error')): ?>
                root.addToast(<?php echo json_encode(session('error'), 15, 512) ?>, 'error');
                <?php elseif($errors->any()): ?>
                root.addToast(<?php echo json_encode($errors->first(), 15, 512) ?>, 'error');
                <?php endif; ?>
                return true;
            }
            document.addEventListener('alpine:initialized', flushAdminToasts);
            document.addEventListener('DOMContentLoaded', function () {
                var n = 0;
                var t = setInterval(function () {
                    if (flushAdminToasts() || ++n > 100) clearInterval(t);
                }, 25);
            });
        })();
    </script>

</body>
</html>

<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>