<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member | Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
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

    <div class="w-full max-w-[720px] light-glass-panel rounded-[48px] overflow-hidden">
        <div class="p-12 bg-white/40 text-center" x-data="{
            d1:'', d2:'', d3:'', d4:'',
            get code(){ return `${this.d1}${this.d2}${this.d3}${this.d4}`; },
            focusNext(e, nextId){ if((e.target.value || '').length === 1 && nextId){ document.getElementById(nextId)?.focus(); } }
        }">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">VERIFY YOUR IDENTITY</h1>
            <p class="text-slate-500 text-sm mb-10">Please enter the code sent to mobile no "{{ $maskedMobile }}"</p>

            @if($generatedOtp)
                <div class="mb-6 text-sm font-bold text-slate-700 bg-white/70 border border-slate-200 rounded-2xl px-5 py-4">
                    OTP (for now): <span class="text-indigo-700">{{ $generatedOtp }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-2xl px-5 py-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('member.otp.verify') }}" class="space-y-8">
                @csrf

                <div class="flex items-center justify-center gap-4">
                    <input id="otp1" maxlength="1" inputmode="numeric" pattern="[0-9]*" x-model="d1" @input="focusNext($event, 'otp2')"
                        class="w-14 h-14 text-center text-xl font-bold bg-white/80 border border-slate-200 rounded-2xl focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5">
                    <input id="otp2" maxlength="1" inputmode="numeric" pattern="[0-9]*" x-model="d2" @input="focusNext($event, 'otp3')"
                        class="w-14 h-14 text-center text-xl font-bold bg-white/80 border border-slate-200 rounded-2xl focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5">
                    <input id="otp3" maxlength="1" inputmode="numeric" pattern="[0-9]*" x-model="d3" @input="focusNext($event, 'otp4')"
                        class="w-14 h-14 text-center text-xl font-bold bg-white/80 border border-slate-200 rounded-2xl focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5">
                    <input id="otp4" maxlength="1" inputmode="numeric" pattern="[0-9]*" x-model="d4"
                        class="w-14 h-14 text-center text-xl font-bold bg-white/80 border border-slate-200 rounded-2xl focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5">
                </div>

                <input type="hidden" name="code" :value="code">
                @error('code')
                    <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror

                <div class="text-center">
                    <button form="resendForm" type="submit"
                        class="text-sm font-bold text-slate-600 hover:text-indigo-600 underline underline-offset-4">
                        Resend OTP?
                    </button>
                </div>

                <div class="flex gap-3 justify-center pt-2">
                    <a href="{{ route('member.login') }}"
                        class="w-44 py-4 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl font-bold transition-colors text-center">Cancels</a>
                    <button type="submit"
                        class="w-52 py-4 bg-slate-900 hover:bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-slate-200 hover:shadow-indigo-200 transition-all">Verify</button>
                </div>
            </form>

            <form id="resendForm" method="POST" action="{{ route('member.otp.resend') }}" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</body>
</html>

