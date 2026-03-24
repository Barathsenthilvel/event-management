{{--
    Legacy “Vanguard” demo shell — not used for logged-in member pages.
    Member area uses member.layouts.gnat; admin area uses admin.layouts.app.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Universal Dashboard | Member</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F1F5F9; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .nav-item-active { background-color: rgba(255, 255, 255, 0.1); color: white; }
        [x-cloak] { display: none !important; }
        .transition-all-300 { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="h-screen overflow-hidden flex p-3 gap-3" x-data="{ sidebarOpen: true, showLogout: false }">

    <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="bg-[#0f172a] rounded-[24px] flex flex-col transition-all-300 shadow-2xl z-20">
        <div class="p-6 flex items-center gap-3 shrink-0">
            <div class="w-8 h-8 bg-indigo-500 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            </div>
            <span x-show="sidebarOpen" class="font-bold text-lg tracking-tight text-white transition-opacity duration-300" x-transition>VANGUARD</span>
        </div>

        @php
            $member = Auth::user();
            $hasActiveSubscription = $member?->activeSubscription()->exists();
            $canSeeMembership = $member && $member->profile_completed && $member->is_approved;
        @endphp

        <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scroll">
            {{-- Always visible --}}
            <a href="{{ route('member.dashboard') }}"
                class="flex items-center gap-3 p-3 {{ request()->routeIs('member.dashboard') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-xl transition-all group">
                <svg class="w-5 h-5 {{ request()->routeIs('member.dashboard') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Dashboard</span>
            </a>

            {{-- Menus visible only after profile completed + admin approved --}}
            @if($canSeeMembership && $hasActiveSubscription)
                <a href="{{ route('member.subscription.index') }}"
                    class="flex items-center gap-3 p-3 {{ request()->routeIs('member.subscription.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-xl transition-all group">
                    <svg class="w-5 h-5 {{ request()->routeIs('member.subscription.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m4 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Membership</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                    <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c1.657 0 3-.895 3-2s-1.343-2-3-2-3 .895-3 2 1.343 2 3 2zm0 0v10m-7-4a7 7 0 0114 0" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Events</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                    <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3M5 11h14M5 19h14M5 11a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 19a2 2 0 01-2-2v-2a2 2 0 012-2" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Meetings</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                    <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 5a2 2 0 012-2h8.5a1.5 1.5 0 011.06.44l2.5 2.5A1.5 1.5 0 0119.5 7H6a2 2 0 01-2-2zm0 4h16M4 13h16M4 17h10" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Find E-Books</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                    <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 9a3 3 0 116 0 3 3 0 01-6 0zM4 20l4-4 2 2 6-6 4 4" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Search Jobs</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                    <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2a2 2 0 012-2h2m4 4a9 9 0 11-18 0 9 9 0 0118 0zm-9-7h.01" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Polling</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                    <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .843-3 2.5 0 1.757 2.25 3.25 3 5.5.75-2.25 3-3.743 3-5.5C15 8.843 13.657 8 12 8z" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Donations</span>
                </a>
            @endif

            {{-- Always available profile / password --}}
            <a href="{{ route('member.profile.edit') }}"
                class="flex items-center gap-3 p-3 {{ request()->routeIs('member.profile.*') ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }} rounded-xl transition-all group">
                <svg class="w-5 h-5 {{ request()->routeIs('member.profile.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Profile</span>
            </a>

            <a href="#"
                class="flex items-center gap-3 p-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 2c-2.21 0-4 1.567-4 3.5V19h8v-2.5C16 14.567 14.21 13 12 13z" />
                </svg>
                <span x-show="sidebarOpen" class="text-sm font-medium">Change Password</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/5 bg-slate-800/50 rounded-b-[24px] shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-xs font-bold text-indigo-300">
                    {{ strtoupper(substr(Auth::user()->name ?? 'ME', 0, 2)) }}
                </div>
                <div x-show="sidebarOpen">
                    <p class="text-xs font-bold text-white">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-wider">Member</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex gap-3 relative overflow-hidden">
        <div class="flex flex-col gap-3 w-full">
            <header class="bg-white h-16 rounded-[24px] flex items-center justify-between px-6 shadow-sm border border-white shrink-0 z-10 relative">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 bg-slate-50 rounded-xl hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </button>
                    <div class="hidden md:block">
                        <h2 class="text-sm font-bold text-slate-800 tracking-tight">Member Area</h2>
                        <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>Home</span>
                            <span class="text-indigo-300">/</span>
                            <span class="text-indigo-600">{{ request()->routeIs('member.profile.*') ? 'Profile' : 'Dashboard' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="showLogout = true"
                        class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-red-50 transition-all text-slate-500 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </header>

            <div class="bg-white flex-1 rounded-[24px] shadow-sm flex flex-col overflow-hidden relative">
                @yield('content')
            </div>
        </div>
    </main>

    <div x-show="showLogout" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-[40px] p-10 max-w-sm w-full text-center shadow-2xl"
            @click.away="showLogout = false">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-red-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">End Session?</h3>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed">You are about to securely log out of your member dashboard.</p>
            <div class="flex gap-3">
                <button @click="showLogout = false"
                    class="flex-1 py-4 bg-slate-50 hover:bg-slate-100 rounded-2xl font-bold text-slate-600 transition-colors">Cancel</button>
                <form method="POST" action="{{ route('member.logout') }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold shadow-lg shadow-red-200 transition-all hover:shadow-xl hover:scale-[1.02]">Logout</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>

