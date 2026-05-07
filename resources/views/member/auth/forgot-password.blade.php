@extends('member.layouts.auth')

@section('title', 'Forgot password — GNAT Association')

@section('content')
    @php
        $hpLogo = config('homepage.logo', ['src' => 'images/logo.png', 'alt' => 'GNAT Association']);
    @endphp
    <div id="auth-main" class="mx-auto max-w-xl">
        <section class="relative overflow-hidden rounded-[28px] border border-[#351c42]/10 bg-white/85 backdrop-blur px-6 py-8 shadow-sm sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-[#fddc6a]/25 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-[#965995]/15 blur-2xl"></div>

            <div class="relative text-center">
                <a href="{{ route('home') }}" class="inline-block" aria-label="GNAT Association home">
                    <img
                        src="{{ asset($hpLogo['src']) }}"
                        alt="{{ $hpLogo['alt'] }}"
                        class="mx-auto h-14 w-auto max-h-16 object-contain sm:h-16"
                        width="200"
                        height="64"
                        loading="eager"
                    />
                </a>
                <p class="mt-5 text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">Members</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-[#351c42] sm:text-3xl">Forgot your password?</h1>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-[#351c42]/65">
                    Enter the email on your member account. We’ll send a secure link to set a new password.
                </p>
            </div>
        </section>

        <div class="mt-6 text-center">
            <a href="{{ route('member.login') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#965995] transition hover:text-[#351c42]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to sign in
            </a>
        </div>

        @if (session('status'))
            <div class="mx-auto mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900">
                <p class="font-extrabold">Check your inbox</p>
                <p class="mt-1 text-emerald-900/85">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            @php
                $errText = strtolower(implode(' ', $errors->all()));
                $isThrottle = str_contains($errText, 'wait') || str_contains($errText, 'minute') || str_contains($errText, 'recently requested');
            @endphp
            <div class="mx-auto mt-6 rounded-2xl border px-5 py-4 text-sm {{ $isThrottle ? 'border-amber-200 bg-amber-50 text-amber-950' : 'border-rose-200 bg-rose-50 text-rose-900' }}">
                <p class="font-extrabold">{{ $isThrottle ? 'Please wait' : 'Something went wrong' }}</p>
                <ul class="mt-2 list-inside list-disc space-y-1 {{ $isThrottle ? 'text-amber-950/85' : 'text-rose-900/85' }}">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mx-auto mt-6 overflow-hidden rounded-[28px] border border-[#351c42]/10 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-sm font-extrabold tracking-tight text-[#351c42]">Send reset link</h2>
            <p class="mt-1 text-xs text-[#351c42]/55">Use the same email you registered with.</p>

            <form id="member-forgot-password-form" class="mt-6 space-y-5" method="POST" action="{{ route('member.password.email') }}" autocomplete="on">
                @csrf
                <div>
                    <label class="block text-xs font-black uppercase tracking-[0.18em] text-[#965995]" for="fp-email">Email address</label>
                    <input
                        id="fp-email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        class="mt-2 w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm text-[#351c42] outline-none transition focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25 @error('email') border-rose-400 @enderror"
                        placeholder="name@email.com"
                        value="{{ old('email') }}"
                    />
                </div>
                <button type="submit" id="fp-submit-btn" class="mt-2 inline-flex w-full items-center justify-center rounded-2xl bg-[#351c42] px-6 py-3.5 text-sm font-extrabold text-[#fddc6a] shadow-lg shadow-[#351c42]/15 transition hover:bg-[#4d2a5c] disabled:cursor-not-allowed disabled:opacity-80">
                    <span class="fp-btn-idle inline-flex items-center justify-center gap-2">
                        Email me a reset link
                    </span>
                    <span class="fp-btn-loading hidden items-center justify-center gap-2">
                        <svg class="h-5 w-5 animate-spin text-[#fddc6a]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending…
                    </span>
                </button>
            </form>
        </section>

        <p class="mx-auto mt-8 max-w-lg text-center text-xs leading-relaxed text-[#351c42]/45">
            Didn’t receive anything? Check your spam folder. If it still doesn’t arrive, confirm your SMTP settings and that this address matches your member account.
        </p>
    </div>

    {{-- Email not registered --}}
    <div id="fp-email-not-found-modal" class="fixed inset-0 z-[220] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="fp-modal-title" aria-hidden="true">
        <div class="absolute inset-0 bg-[#351c42]/60 backdrop-blur-sm" data-fp-modal-close></div>
        <div class="relative w-full max-w-md rounded-[28px] border border-[#351c42]/10 bg-white p-8 shadow-2xl">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 id="fp-modal-title" class="mt-5 text-center text-xl font-extrabold tracking-tight text-[#351c42]">This email isn’t registered</h3>
            <p class="mt-3 text-center text-sm leading-relaxed text-[#351c42]/70">
                We don’t have a member account with that email address. Please check the spelling, try the email you used when you signed up, or create a new member account.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <button type="button" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-[#351c42]/15 bg-white px-5 py-3 text-sm font-bold text-[#351c42] transition hover:bg-[#351c42]/5 sm:flex-none" data-fp-modal-close>
                    OK
                </button>
                <a href="{{ route('member.register') }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-lg shadow-[#351c42]/15 transition hover:bg-[#4d2a5c] sm:flex-none">
                    Register
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById("member-forgot-password-form");
    const btn = document.getElementById("fp-submit-btn");
    if (form && btn) {
        form.addEventListener("submit", function () {
            if (btn.disabled) return;
            btn.disabled = true;
            const idle = btn.querySelector(".fp-btn-idle");
            const load = btn.querySelector(".fp-btn-loading");
            if (idle) idle.classList.add("hidden");
            if (load) {
                load.classList.remove("hidden");
                load.classList.add("inline-flex");
            }
        });
    }

    const modal = document.getElementById("fp-email-not-found-modal");
    function closeFpModal() {
        if (!modal) return;
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        modal.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }
    function openFpModal() {
        if (!modal) return;
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        modal.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }
    modal?.querySelectorAll("[data-fp-modal-close]").forEach(function (el) {
        el.addEventListener("click", closeFpModal);
    });
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && modal && !modal.classList.contains("hidden")) closeFpModal();
    });

    @if(session('fp_unknown_email'))
    openFpModal();
    @endif
})();
</script>
@endpush
