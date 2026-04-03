<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GNAT Admin Login</title>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet"></noscript>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; }
        .fluid-bg {
            background: linear-gradient(120deg, #F0F4F8 0%, #E2E8F0 50%, #F8FAFC 100%);
            position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1;
        }
        .light-glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
        }
        @media (prefers-reduced-motion: reduce) {
            * { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 overflow-hidden">
    <div class="fluid-bg" style="background: radial-gradient(circle at top left, #fddc6a33, transparent 55%), radial-gradient(circle at bottom right, #351c4233, transparent 55%), #f8fafc;"></div>

    <div class="absolute top-1/4 left-1/4 w-[480px] h-[480px] bg-[#fddc6a]/40 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[480px] h-[480px] bg-[#351c42]/35 rounded-full blur-[120px]"></div>

    <div class="w-full max-w-[1000px] flex flex-col md:flex-row light-glass-panel rounded-[48px] overflow-hidden" x-data="{ loginType: 'email', showOtpModal: false }">

        <div class="w-full md:w-1/2 p-12 flex flex-col justify-between border-b md:border-b-0 md:border-r border-slate-100">
            <div>
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-10 h-10 bg-[#351c42] rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}" alt="GNAT" class="h-7 w-auto object-contain">
                    </div>
                    <span class="text-[#351c42] font-bold text-xl tracking-tighter">GNAT<span class="text-[#965995] font-medium"> Admin</span></span>
                </div>

                <h1 class="text-4xl md:text-5xl font-bold text-[#351c42] tracking-tight leading-[1.1]">
                    Manage your <br> <span class="text-[#fbbf24]">GNAT platform.</span>
                </h1>
                <p class="mt-6 text-slate-600 text-lg leading-relaxed max-w-xs">
                    Secure console for GNAT Association to manage members, events, donations, and more.
                </p>
            </div>

            <div class="space-y-6">
                <div class="flex items-center gap-4 group cursor-pointer">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 group-hover:border-indigo-500/50 transition-all">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-slate-900 text-sm font-semibold">GNAT Admin Access</p>
                        <p class="text-slate-500 text-xs">Only authorised administrators can sign in here.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-12 bg-white/50">
            <div class="mb-10 flex justify-between items-end">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Login</h2>
                <div class="flex gap-2 bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button @click="loginType = 'email'" :class="loginType === 'email' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-tighter">Email</button>
                    <button @click="loginType = 'mobile'" :class="loginType === 'mobile' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-tighter">Mobile</button>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                @csrf
                <div class="relative group">
                    <input x-bind:type="loginType === 'email' ? 'email' : 'tel'" name="identifier" required
                        class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-6 py-5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                        x-bind:placeholder="loginType === 'email' ? 'Identification (Email)' : 'Identification (Phone)'"
                        value="{{ old('identifier') }}">
                    @error('identifier')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="relative group">
                    <input type="password" name="password" required
                        class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-6 py-5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                        placeholder="Secure Password">
                    <div class="absolute right-6 top-5">
                        <a href="#" class="text-xs font-bold text-indigo-600/80 hover:text-indigo-600">RESET?</a>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-bold py-5 rounded-2xl shadow-xl shadow-slate-200 hover:shadow-indigo-200 transform active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    Continue to Dashboard
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </form>

            <div class="mt-12 text-center">
                <p class="text-slate-500 text-sm">Need help? <a href="#" class="text-indigo-600 font-bold hover:underline underline-offset-4">Security Support</a></p>
            </div>
        </div>
    </div>
</body>
</html>

