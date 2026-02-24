<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Universal Dashboard | Admin Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    notifications: [
        { title: 'Security Alert', desc: 'New login from Chrome/MacOs', time: 'Just now', type: 'alert' },
        { title: 'New User', desc: 'Sarah registered an account', time: '2 min ago', type: 'info' }
    ],
    toasts: [],
    addToast(msg) {
        const id = Date.now();
        this.toasts.push({ id, msg });
        setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 3000);
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
                x-transition>VANGUARD</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scroll">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-xl transition-all group">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }} transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Dashboard</span>
            </a>

            <a href="{{ route('admin.admins.index') }}"
                class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.admins.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-xl transition-all group">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.admins.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }} transition-colors" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Users</span>
            </a>

            <a href="{{ route('admin.roles.index') }}"
                class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.roles.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-xl transition-all group">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.roles.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }} transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Roles & Permissions</span>
            </a>
        </nav>

        <!-- User Profile Bottom -->
        <div class="p-4 border-t border-white/5 bg-slate-800/50 rounded-b-[24px] shrink-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-xs font-bold text-indigo-300">
                    {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 2)) }}</div>
                <div x-show="sidebarOpen">
                    <p class="text-xs font-bold text-white">{{ Auth::guard('admin')->user()->name }}</p>
                    <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-wider">{{ Auth::guard('admin')->user()->is_super_admin ? 'Super Admin' : 'Admin' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex gap-3 workspace-transition relative overflow-hidden">
        <div class="flex flex-col gap-3 workspace-transition w-full">
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
                        <p class="text-[11px] font-bold text-slate-600">{{ Auth::guard('admin')->user()->updated_at->format('H:i A') }} Today</p>
                    </div>

                    <div class="relative">
                        <button @click="showNotifications = !showNotifications"
                            class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-slate-500 relative">
                            <span
                                class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white animate-pulse"></span>
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
                                    <div
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
                                    </div>
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
            <div class="bg-white flex-1 rounded-[24px] shadow-sm flex flex-col overflow-hidden relative">
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
                @yield('content')
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
                <form method="POST" action="{{ route('admin.logout') }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold shadow-lg shadow-red-200 transition-all hover:shadow-xl hover:scale-[1.02]">Logout</button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('toast', { msg: '{{ session('success') }}' });
        });
    </script>
    @endif

</body>
</html>

