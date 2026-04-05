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
                        @foreach ($errors->all() as $e)
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
                        <form class="space-y-5" method="POST" action="{{ route('member.login.store') }}">
                            @csrf
                            <div>
                                <label class="ml-label" for="signin-identifier">Email or mobile</label>
                                <input id="signin-identifier" name="identifier" type="text" autocomplete="username" required class="ml-inp" placeholder="name@email.com or mobile" value="{{ old('identifier') }}" />
                            </div>
                            <div>
                                <label class="ml-label" for="signin-password">Password</label>
                                <input id="signin-password" name="password" type="password" autocomplete="current-password" required class="ml-inp" placeholder="••••••••" />
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-[#351c42]/75">
                                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded-md border-[#351c42]/20 text-[#965995] focus:ring-[#965995]" />
                                    Remember me
                                </label>
                                <a href="{{ route('member.password.request') }}" class="text-sm font-semibold text-[#965995] transition hover:text-[#351c42]">Forgot password?</a>
                            </div>
                            <button type="submit" class="ml-btn-primary mt-2 w-full">Sign in</button>
                        </form>
                    </div>

                    <div id="panel-signup" role="tabpanel" aria-labelledby="tab-signup" data-auth-panel="signup" hidden>
                        <h2 class="text-center text-lg font-bold tracking-tight text-[#351c42] sm:text-xl">New member registration</h2>
                        <p class="mx-auto mt-2 max-w-sm text-center text-sm text-[#351c42]/55">We’ll verify your mobile number in the next step.</p>
                        <form class="mt-8 space-y-6" method="POST" action="{{ route('member.register.store') }}" id="member-signup-form" novalidate>
                            @csrf
                            <input type="hidden" name="country_code" value="+91" />
                            <div class="grid gap-6 sm:grid-cols-2 sm:gap-x-8">
                                <div>
                                    <label class="ml-label" for="su-first">First name <span class="text-red-500">*</span></label>
                                    <input id="su-first" name="first_name" type="text" autocomplete="given-name" required class="ml-inp" placeholder="Given name" value="{{ old('first_name') }}" />
                                </div>
                                <div>
                                    <label class="ml-label" for="su-email">Email <span class="text-red-500">*</span></label>
                                    <input id="su-email" name="email" type="email" autocomplete="email" required class="ml-inp" placeholder="name@email.com" value="{{ old('email') }}" />
                                </div>
                                <div>
                                    <label class="ml-label" for="su-last">Last name <span class="text-red-500">*</span></label>
                                    <input id="su-last" name="last_name" type="text" autocomplete="family-name" required class="ml-inp" placeholder="Family name" value="{{ old('last_name') }}" />
                                </div>
                                <div>
                                    <label class="ml-label" for="su-pass">Password <span class="text-red-500">*</span></label>
                                    <input id="su-pass" name="password" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="Min. 6 characters" />
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
                                </div>
                                <div>
                                    <label class="ml-label" for="su-pass2">Confirm password <span class="text-red-500">*</span></label>
                                    <input id="su-pass2" name="password_confirmation" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="Repeat password" />
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

            const signupForm = document.getElementById("member-signup-form");
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

            signupForm?.addEventListener("submit", (event) => {
                setPasswordMismatchError();
                if (!signupForm.checkValidity()) {
                    event.preventDefault();
                    signupForm.reportValidity();
                }
            });

            @if($errors->any() && (old('first_name') || old('password_confirmation')))
                setTab("signup");
            @elseif($errors->any())
                setTab("signin");
            @endif
        })();
    </script>
@endsection