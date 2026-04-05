@extends('member.layouts.auth')

@section('title', 'Choose new password — GNAT Association')

@section('content')
    <div id="auth-main">
        <div class="mx-auto mb-10 max-w-lg text-center">
            <a href="{{ route('member.login') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#965995] transition hover:text-[#351c42]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to sign in
            </a>
            <span class="mt-6 inline-flex items-center gap-2 rounded-full bg-[#965995]/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-[#965995]">Members</span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-[#351c42] sm:text-4xl">Choose a new password</h1>
            <p class="mx-auto mt-3 max-w-md text-base leading-relaxed text-[#351c42]/65">
                Pick a strong password you haven’t used here before.
            </p>
        </div>

        @if ($errors->any())
            <div class="mx-auto mb-6 max-w-xl rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mx-auto max-w-xl overflow-hidden rounded-3xl border border-white/60 bg-white/70 p-2 shadow-2xl shadow-[#351c42]/10 backdrop-blur-sm ml-card-elevated">
            <div class="px-5 pb-8 pt-6 sm:px-8 sm:pb-10 sm:pt-8">
                <form class="space-y-5" method="POST" action="{{ route('member.password.reset.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}" />
                    <div>
                        <label class="ml-label" for="rp-email">Email</label>
                        <input id="rp-email" name="email" type="email" autocomplete="email" required class="ml-inp" value="{{ old('email', $email) }}" />
                    </div>
                    <div>
                        <label class="ml-label" for="rp-password">New password</label>
                        <input id="rp-password" name="password" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="At least 6 characters" />
                    </div>
                    <div>
                        <label class="ml-label" for="rp-password-confirmation">Confirm password</label>
                        <input id="rp-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="Repeat password" />
                    </div>
                    <button type="submit" class="ml-btn-primary mt-2 w-full">Update password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
