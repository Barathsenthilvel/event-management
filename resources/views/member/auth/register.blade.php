<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member | Create Account</title>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; }
        .fluid-bg { background: linear-gradient(120deg, #F0F4F8 0%, #E2E8F0 50%, #F8FAFC 100%); position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; }
        .light-glass-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(25px); border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 20px 50px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 overflow-hidden">
    <div class="fluid-bg"></div>
    <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-indigo-200/50 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-blue-200/50 rounded-full blur-[120px]"></div>

    <div class="w-full max-w-[1100px] light-glass-panel rounded-[48px] overflow-hidden">
        <div class="p-10 md:p-14 bg-white/40">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
                <span class="text-slate-900 font-bold text-xl tracking-tighter">MEMBER<span class="text-slate-500 font-light italic">.PORTAL</span></span>
            </div>

            <div class="flex items-end justify-between mb-8">
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Create Account</h1>
                <a href="{{ route('member.login') }}" class="text-sm font-bold text-slate-500 hover:text-indigo-600 underline underline-offset-4">Go back to Login?</a>
            </div>

            <form method="POST" action="{{ route('member.register.store') }}" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">First Name <span class="text-rose-500">*</span></label>
                        <input name="first_name" value="{{ old('first_name') }}" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                        @error('first_name')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                        @error('email')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Last Name <span class="text-rose-500">*</span></label>
                        <input name="last_name" value="{{ old('last_name') }}" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                        @error('last_name')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                        @error('password')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Mobile No <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <div class="w-20 bg-white/80 border border-slate-200 rounded-2xl px-4 py-4 text-slate-700 font-bold text-sm flex items-center justify-center">+91</div>
                            <input name="mobile" value="{{ old('mobile', '+91') }}" required
                                class="flex-1 bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                placeholder="Enter">
                        </div>
                        @error('mobile')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Confirm <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-white/80 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                            placeholder="Enter">
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 pt-4">
                    <a href="{{ route('member.login') }}"
                        class="w-full md:w-52 text-center py-4 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl font-bold transition-colors">Cancels</a>
                    <button type="submit"
                        class="w-full md:w-60 py-4 bg-slate-900 hover:bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-slate-200 hover:shadow-indigo-200 transition-all">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

