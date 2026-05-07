@extends('member.layouts.auth')

@section('title', 'Member sign in — GNAT Association')

@section('content')
        <div id="auth-main">
            <div class="mx-auto mb-10 max-w-lg text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-[#965995]/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-[#965995]">Members</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-[#351c42] sm:text-4xl" data-auth-page-title>Welcome back</h1>
                <p class="mx-auto mt-3 max-w-md text-base leading-relaxed text-[#351c42]/65" data-auth-page-sub>Sign in or register to track donations and volunteer activity.</p>
            </div>

            @if (session('status'))
                <div class="mx-auto mb-6 max-w-xl rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-auto mb-6 max-w-xl rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach (collect($errors->all())->unique()->values() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mx-auto max-w-xl overflow-hidden rounded-3xl border border-white/60 bg-white/70 p-2 shadow-2xl shadow-[#351c42]/10 backdrop-blur-sm ml-card-elevated">
                <div class="ml-tabs-track" role="tablist" aria-label="Sign in or sign up">
                    <button type="button" role="tab" id="tab-signin" aria-selected="true" aria-controls="panel-signin" data-auth-tab="signin">Sign in</button>
                    <button type="button" role="tab" id="tab-signup" aria-selected="false" aria-controls="panel-signup" data-auth-tab="signup">Sign up</button>
                </div>

                <div class="px-5 pb-8 pt-6 sm:px-8 sm:pb-10 sm:pt-8">
                    <div id="panel-signin" role="tabpanel" aria-labelledby="tab-signin" data-auth-panel="signin">
                        <form class="space-y-5" method="POST" action="{{ route('member.login.store') }}" id="member-signin-form" novalidate>
                            @csrf
                            <p class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert" data-form-error></p>
                            <div>
                                <label class="ml-label" for="signin-identifier">Email or mobile</label>
                                <input id="signin-identifier" name="identifier" type="text" autocomplete="username" required class="ml-inp" placeholder="name@email.com or mobile" value="{{ old('identifier') }}" />
                                <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="identifier"></p>
                            </div>
                            <div>
                                <label class="ml-label" for="signin-password">Password</label>
                                <div class="ml-password-wrap">
                                    <input id="signin-password" name="password" type="password" autocomplete="current-password" required class="ml-inp" placeholder="••••••••" data-password-input />
                                    <button type="button" class="ml-password-toggle" data-password-toggle aria-label="Show password" aria-pressed="false">
                                        <svg class="h-5 w-5" data-icon-show viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg class="hidden h-5 w-5" data-icon-hide viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                                <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="password"></p>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-[#351c42]/75">
                                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded-md border-[#351c42]/20 text-[#965995] focus:ring-[#965995]" />
                                    Remember me
                                </label>
                                <a href="{{ route('member.password.request') }}" class="text-sm font-semibold text-[#965995] transition hover:text-[#351c42]">Forgot password?</a>
                            </div>
                            <button type="submit" class="ml-btn-primary mt-2 w-full" id="signin-submit-btn">
                                <span class="signin-btn-idle inline-flex w-full items-center justify-center gap-2">Sign in</span>
                                <span class="signin-btn-loading hidden w-full items-center justify-center gap-2">
                                    <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Signing in…
                                </span>
                            </button>
                        </form>
                    </div>

                    <div id="panel-signup" role="tabpanel" aria-labelledby="tab-signup" data-auth-panel="signup" hidden>
                        <h2 class="text-center text-lg font-bold tracking-tight text-[#351c42] sm:text-xl">New member registration</h2>
                        <p class="mx-auto mt-2 max-w-sm text-center text-sm text-[#351c42]/55">We’ll verify your mobile number in the next step.</p>
                        <form class="mt-8 space-y-6" method="POST" action="{{ route('member.register.store') }}" id="member-signup-form" novalidate>
                            @csrf
                            <input type="hidden" name="country_code" value="+91" />
                            <p class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert" data-form-error></p>
                            <div class="grid gap-6 sm:grid-cols-2 sm:gap-x-8">
                                {{-- Row 1: First name, Last name --}}
                                <div>
                                    <label class="ml-label" for="su-first">First name <span class="text-red-500">*</span></label>
                                    <input id="su-first" name="first_name" type="text" autocomplete="given-name" required class="ml-inp" placeholder="Given name" value="{{ old('first_name') }}" />
                                    @error('first_name')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                    <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="first_name"></p>
                                </div>
                                <div>
                                    <label class="ml-label" for="su-last">Last name <span class="text-red-500">*</span></label>
                                    <input id="su-last" name="last_name" type="text" autocomplete="family-name" required class="ml-inp" placeholder="Family name" value="{{ old('last_name') }}" />
                                    @error('last_name')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                    <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="last_name"></p>
                                </div>
                                {{-- Row 2: Email, Mobile --}}
                                <div>
                                    <label class="ml-label" for="su-email">Email <span class="text-red-500">*</span></label>
                                    <input id="su-email" name="email" type="email" autocomplete="email" required class="ml-inp" placeholder="name@email.com" value="{{ old('email') }}" />
                                    @error('email')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                    <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="email"></p>
                                </div>
                                <div>
                                    <label class="ml-label" for="su-mobile">Mobile <span class="text-red-500">*</span></label>
                                    <div class="ml-inp-phone-wrap">
                                        <span class="ml-inp-phone-prefix" aria-hidden="true">+91</span>
                                        <input id="su-mobile" name="mobile" type="tel" inputmode="numeric" autocomplete="tel-national" required
                                            class="ml-inp-phone-field" placeholder="10-digit mobile number" pattern="[0-9]{10}" minlength="10" maxlength="10"
                                            title="Please enter exactly 10 digits" value="{{ old('mobile') }}" aria-describedby="su-mobile-hint" />
                                    </div>
                                    <p id="su-mobile-hint" class="sr-only">Enter 10 digits; country code +91 is already included.</p>
                                    @error('mobile')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                    <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="mobile"></p>
                                </div>
                                {{-- Row 3: Password, Confirm password --}}
                                <div>
                                    <label class="ml-label" for="su-pass">Password <span class="text-red-500">*</span></label>
                                    <div class="ml-password-wrap">
                                        <input id="su-pass" name="password" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="Min. 6 characters" data-password-input />
                                        <button type="button" class="ml-password-toggle" data-password-toggle aria-label="Show password" aria-pressed="false">
                                            <svg class="h-5 w-5" data-icon-show viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg class="hidden h-5 w-5" data-icon-hide viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                    @error('password')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                    <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="password"></p>
                                </div>
                                <div>
                                    <label class="ml-label" for="su-pass2">Confirm password <span class="text-red-500">*</span></label>
                                    <div class="ml-password-wrap">
                                        <input id="su-pass2" name="password_confirmation" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="Repeat password" data-password-input />
                                        <button type="button" class="ml-password-toggle" data-password-toggle aria-label="Show password" aria-pressed="false">
                                            <svg class="h-5 w-5" data-icon-show viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg class="hidden h-5 w-5" data-icon-hide viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                    <p class="mt-1.5 text-xs font-semibold text-red-600" data-error-for="password_confirmation"></p>
                                </div>
                                <div class="sm:col-span-2 flex justify-end pt-1">
                                    <button type="button" class="text-sm font-semibold text-[#965995] transition hover:text-[#351c42]" data-back-to-login>Back to sign in</button>
                                </div>
                            </div>
                            <p id="su-password-error" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert"></p>
                            <div class="flex flex-col-reverse gap-3 border-t border-[#351c42]/8 pt-8 sm:flex-row sm:justify-end sm:gap-4">
                                <button type="button" class="ml-btn-secondary w-full sm:w-auto" data-signup-cancel>Cancel</button>
                                <button type="submit" class="ml-btn-primary w-full sm:w-auto">Create account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <p class="mx-auto mt-10 max-w-xl text-center text-xs text-[#351c42]/45">After sign in or registration, you’ll be asked to verify a one-time code sent to your mobile.</p>

            <div id="member-account-not-found-modal" class="fixed inset-0 z-[220] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="member-not-found-title" aria-hidden="true">
                <div class="absolute inset-0 bg-[#351c42]/60 backdrop-blur-sm" data-member-not-found-close></div>
                <div class="relative w-full max-w-md rounded-[28px] border border-[#351c42]/10 bg-white p-8 shadow-2xl">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h3 id="member-not-found-title" class="mt-5 text-center text-xl font-extrabold tracking-tight text-[#351c42]">No member account found</h3>
                    <p id="member-not-found-body" class="mt-3 text-center text-sm leading-relaxed text-[#351c42]/70">
                        We couldn’t find a member account for the email or mobile you entered. Check for typos, try again, or register as a new member.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                        <button type="button" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-[#351c42]/15 bg-white px-5 py-3 text-sm font-bold text-[#351c42] transition hover:bg-[#351c42]/5 sm:flex-none" data-member-not-found-close>
                            Close
                        </button>
                        <button type="button" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-lg shadow-[#351c42]/15 transition hover:bg-[#4d2a5c] sm:flex-none" data-open-signup-from-modal>
                            Create account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <script>
        (() => {
            const menuBtn = document.querySelector("[data-ml-menu]");
            const mobileNav = document.getElementById("ml-mobile-nav");
            if (menuBtn && mobileNav) {
                menuBtn.addEventListener("click", () => {
                    const open = menuBtn.getAttribute("aria-expanded") === "true";
                    menuBtn.setAttribute("aria-expanded", open ? "false" : "true");
                    mobileNav.hidden = open;
                    mobileNav.classList.toggle("is-open", !open);
                    document.body.classList.toggle("ml-menu-open", !open);
                });
            }

            const tabs = Array.from(document.querySelectorAll("[data-auth-tab]"));
            const panels = Array.from(document.querySelectorAll("[data-auth-panel]"));
            const pageTitle = document.querySelector("[data-auth-page-title]");
            const pageSub = document.querySelector("[data-auth-page-sub]");

            function setTab(name) {
                const isSignin = name === "signin";
                tabs.forEach((btn) => {
                    btn.setAttribute("aria-selected", btn.getAttribute("data-auth-tab") === name ? "true" : "false");
                });
                panels.forEach((panel) => {
                    panel.hidden = panel.getAttribute("data-auth-panel") !== name;
                });
                if (pageTitle) pageTitle.textContent = isSignin ? "Welcome back" : "Create your account";
                if (pageSub) {
                    pageSub.textContent = isSignin
                        ? "Sign in to your member account."
                        : "Fill in your details — we’ll verify your mobile with a code next.";
                }
                const h = name === "signup" ? "#signup" : "#signin";
                if (history.replaceState) history.replaceState(null, "", h);
            }

            tabs.forEach((btn) => {
                btn.addEventListener("click", () => setTab(btn.getAttribute("data-auth-tab") || "signin"));
            });

            const defaultTab = document.body.getAttribute("data-auth-default") || "signin";
            function fromHash() {
                if (location.hash === "#signup") setTab("signup");
                else setTab("signin");
            }
            if (defaultTab === "signup") setTab("signup");
            else fromHash();
            window.addEventListener("hashchange", fromHash);

            document.querySelector("[data-back-to-login]")?.addEventListener("click", () => {
                setTab("signin");
                if (history.replaceState) history.replaceState(null, "", "#signin");
            });
            document.querySelector("[data-signup-cancel]")?.addEventListener("click", () => {
                setTab("signin");
                if (history.replaceState) history.replaceState(null, "", "#signin");
            });

            const signupMobileInput = document.getElementById("su-mobile");
            if (signupMobileInput) {
                signupMobileInput.addEventListener("input", () => {
                    signupMobileInput.value = signupMobileInput.value.replace(/\D/g, "").slice(0, 10);
                });
            }

            const signinForm = document.getElementById("member-signin-form");
            const signupForm = document.getElementById("member-signup-form");
            const accountNotFoundModal = document.getElementById("member-account-not-found-modal");
            const memberNotFoundBody = document.getElementById("member-not-found-body");

            function openAccountNotFoundModal(message) {
                if (memberNotFoundBody) {
                    memberNotFoundBody.textContent =
                        message ||
                        "We couldn’t find a member account for the email or mobile you entered. Check for typos, try again, or register as a new member.";
                }
                if (!accountNotFoundModal) return;
                accountNotFoundModal.classList.remove("hidden");
                accountNotFoundModal.classList.add("flex");
                accountNotFoundModal.setAttribute("aria-hidden", "false");
                document.body.style.overflow = "hidden";
            }

            function closeAccountNotFoundModal() {
                if (!accountNotFoundModal) return;
                accountNotFoundModal.classList.add("hidden");
                accountNotFoundModal.classList.remove("flex");
                accountNotFoundModal.setAttribute("aria-hidden", "true");
                document.body.style.overflow = "";
            }

            accountNotFoundModal?.querySelectorAll("[data-member-not-found-close]").forEach((el) => {
                el.addEventListener("click", closeAccountNotFoundModal);
            });
            document.querySelector("[data-open-signup-from-modal]")?.addEventListener("click", () => {
                closeAccountNotFoundModal();
                setTab("signup");
                if (history.replaceState) history.replaceState(null, "", "#signup");
            });
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && accountNotFoundModal && !accountNotFoundModal.classList.contains("hidden")) {
                    closeAccountNotFoundModal();
                }
            });
            const passwordInput = document.getElementById("su-pass");
            const confirmPasswordInput = document.getElementById("su-pass2");
            const passwordError = document.getElementById("su-password-error");

            function setPasswordMismatchError() {
                if (!passwordInput || !confirmPasswordInput || !passwordError) return;
                const hasMismatch = Boolean(confirmPasswordInput.value) && passwordInput.value !== confirmPasswordInput.value;
                if (hasMismatch) {
                    confirmPasswordInput.setCustomValidity("Passwords do not match");
                    passwordError.textContent = "Password and confirm password do not match.";
                    passwordError.classList.remove("hidden");
                } else {
                    confirmPasswordInput.setCustomValidity("");
                    passwordError.textContent = "";
                    passwordError.classList.add("hidden");
                }
            }

            passwordInput?.addEventListener("input", setPasswordMismatchError);
            confirmPasswordInput?.addEventListener("input", setPasswordMismatchError);

            function clearFormErrors(form) {
                form.querySelectorAll("[data-error-for]").forEach((el) => {
                    el.textContent = "";
                });
                form.querySelectorAll("input, select, textarea").forEach((field) => {
                    field.classList.remove("is-invalid");
                    field.removeAttribute("aria-invalid");
                    const phoneWrap = field.closest(".ml-inp-phone-wrap");
                    if (phoneWrap) phoneWrap.classList.remove("is-invalid");
                });
                const formError = form.querySelector("[data-form-error]");
                if (formError) {
                    formError.textContent = "";
                    formError.classList.add("hidden");
                }
            }

            function paintFieldError(form, fieldName, message) {
                const field = form.querySelector(`[name="${fieldName}"]`);
                const errorEl = form.querySelector(`[data-error-for="${fieldName}"]`);
                if (field) {
                    field.classList.add("is-invalid");
                    field.setAttribute("aria-invalid", "true");
                    const phoneWrap = field.closest(".ml-inp-phone-wrap");
                    if (phoneWrap) phoneWrap.classList.add("is-invalid");
                }
                if (errorEl) {
                    errorEl.textContent = message;
                }
            }

            function paintFormError(form, message) {
                const formError = form.querySelector("[data-form-error]");
                if (!formError || !message) return;
                formError.textContent = message;
                formError.classList.remove("hidden");
            }

            function setSigninButtonLoading(isLoading) {
                const submitBtn = document.getElementById("signin-submit-btn");
                if (!submitBtn) return;
                const idle = submitBtn.querySelector(".signin-btn-idle");
                const loading = submitBtn.querySelector(".signin-btn-loading");
                submitBtn.disabled = isLoading;
                if (idle && loading) {
                    idle.classList.toggle("hidden", isLoading);
                    loading.classList.toggle("hidden", !isLoading);
                    loading.classList.toggle("inline-flex", isLoading);
                }
            }

            async function submitAuthFormAjax(form) {
                clearFormErrors(form);

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.textContent : "";
                const isSignin = form.id === "member-signin-form";
                if (submitBtn) {
                    submitBtn.disabled = true;
                    if (isSignin) {
                        setSigninButtonLoading(true);
                    } else {
                        submitBtn.textContent = "Please wait...";
                    }
                }

                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        headers: {
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: new FormData(form),
                        credentials: "same-origin",
                    });

                    let payload = {};
                    try {
                        payload = await response.json();
                    } catch (_) {
                        payload = {};
                    }

                    if (response.ok && payload.redirect) {
                        window.location.href = payload.redirect;
                        return;
                    }

                    if (response.status === 422) {
                        const errors = payload.errors || {};
                        const idMsg = errors.identifier?.[0] || "";
                        if (
                            idMsg &&
                            form.id === "member-signin-form" &&
                            /not\s*found/i.test(idMsg)
                        ) {
                            openAccountNotFoundModal(idMsg);
                            return;
                        }

                        let hasFieldErrors = false;
                        Object.entries(errors).forEach(([fieldName, messages]) => {
                            if (!messages || !messages.length) return;
                            hasFieldErrors = true;
                            paintFieldError(form, fieldName, messages[0]);
                        });

                        // Avoid duplicate messages: show top error only for non-field/general errors.
                        if (!hasFieldErrors && payload.message) {
                            paintFormError(form, payload.message);
                        }

                        form.querySelector(".is-invalid")?.focus();
                        return;
                    }

                    paintFormError(form, payload.message || "Something went wrong. Please try again.");
                } catch (_) {
                    paintFormError(form, "Network error. Please check your connection and try again.");
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        if (form.id === "member-signin-form") {
                            setSigninButtonLoading(false);
                        } else {
                            submitBtn.textContent = originalBtnText;
                        }
                    }
                }
            }

            signinForm?.addEventListener("submit", (event) => {
                event.preventDefault();
                submitAuthFormAjax(signinForm);
            });

            signupForm?.addEventListener("submit", (event) => {
                setPasswordMismatchError();
                if (!signupForm.checkValidity()) {
                    event.preventDefault();
                    signupForm.reportValidity();
                    return;
                }
                event.preventDefault();
                submitAuthFormAjax(signupForm);
            });

            @if($errors->any() && (old('first_name') || old('password_confirmation')))
                setTab("signup");
            @elseif($errors->any())
                setTab("signin");
            @endif

            document.querySelectorAll("[data-password-toggle]").forEach((btn) => {
                const wrap = btn.closest(".ml-password-wrap");
                const input = wrap?.querySelector("[data-password-input]");
                const iconShow = btn.querySelector("[data-icon-show]");
                const iconHide = btn.querySelector("[data-icon-hide]");
                if (!input) return;
                btn.addEventListener("click", () => {
                    const isHidden = input.type === "password";
                    input.type = isHidden ? "text" : "password";
                    btn.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
                    btn.setAttribute("aria-pressed", isHidden ? "true" : "false");
                    iconShow?.classList.toggle("hidden", isHidden);
                    iconHide?.classList.toggle("hidden", !isHidden);
                });
            });
        })();
    </script>
@endsection