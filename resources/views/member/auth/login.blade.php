<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Member sign in — GNAT Donation</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: "DM Sans", system-ui, sans-serif; }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
        .ml-page-bg {
            background-color: #f8f6fc;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(150, 89, 149, 0.18), transparent),
                radial-gradient(ellipse 60% 40% at 100% 0%, rgba(253, 220, 106, 0.15), transparent),
                radial-gradient(ellipse 50% 30% at 0% 100%, rgba(53, 28, 66, 0.08), transparent);
            min-height: 100vh;
        }
        .ml-glass-header {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(53, 28, 66, 0.08);
        }
        .ml-nav-link {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #5c5a6b;
            transition: color 0.2s ease;
        }
        .ml-nav-link:hover { color: #351c42; }
        body.ml-menu-open { overflow: hidden; }
        .ml-mobile-panel { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .ml-mobile-panel.is-open { max-height: 28rem; }
        .ml-tabs-track {
            display: flex;
            gap: 0.25rem;
            padding: 0.35rem;
            border-radius: 9999px;
            background: rgba(53, 28, 66, 0.06);
        }
        [data-auth-tab] {
            flex: 1;
            border-radius: 9999px;
            padding: 0.65rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: background 0.25s ease, color 0.25s ease, box-shadow 0.25s ease, transform 0.2s ease;
        }
        [data-auth-tab][aria-selected="false"] { color: #351c42; background: transparent; }
        [data-auth-tab][aria-selected="false"]:hover { background: rgba(255, 255, 255, 0.7); }
        [data-auth-tab][aria-selected="true"] {
            background: linear-gradient(135deg, #351c42 0%, #4a2660 100%);
            color: #fff;
            box-shadow: 0 4px 20px rgba(53, 28, 66, 0.35);
        }
        .ml-label { display: block; font-size: 0.8125rem; font-weight: 600; color: rgba(53, 28, 66, 0.78); margin-bottom: 0.5rem; }
        .ml-inp {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(255, 255, 255, 0.85);
            padding: 0.8125rem 1.125rem;
            font-size: 0.9375rem;
            color: #351c42;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .ml-inp::placeholder { color: rgba(53, 28, 66, 0.32); }
        .ml-inp:hover { border-color: rgba(150, 89, 149, 0.25); background: #fff; }
        .ml-inp:focus {
            border-color: rgba(150, 89, 149, 0.55);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
        }
        .ml-card-elevated {
            box-shadow: 0 4px 6px -1px rgba(53, 28, 66, 0.06), 0 24px 48px -12px rgba(53, 28, 66, 0.14);
        }
        .ml-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: 9999px;
            padding: 0.875rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, #351c42 0%, #4d2a5c 100%);
            color: #fddc6a;
            box-shadow: 0 8px 24px rgba(53, 28, 66, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .ml-btn-primary:hover { filter: brightness(1.06); box-shadow: 0 12px 28px rgba(53, 28, 66, 0.32); transform: translateY(-1px); }
        .ml-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.875rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            background: #fff;
            color: #351c42;
            border: 1px solid rgba(53, 28, 66, 0.14);
            cursor: pointer;
        }
        .ml-btn-secondary:hover { background: rgba(53, 28, 66, 0.04); border-color: rgba(53, 28, 66, 0.22); }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    </style>
</head>
<body class="ml-page-bg text-[#351c42] antialiased" data-auth-default="{{ $defaultTab ?? 'signin' }}">
    <header class="sticky top-0 z-40 ml-glass-header">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3.5 lg:gap-6">
            <button type="button" class="flex shrink-0 rounded-xl p-2.5 transition-colors hover:bg-[#351c42]/5 lg:hidden -ml-2" data-ml-menu aria-expanded="false" aria-controls="ml-mobile-nav" aria-label="Open menu">
                <span class="flex w-[22px] flex-col gap-1.5" aria-hidden="true">
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                    <span class="h-0.5 w-full rounded-full bg-[#351c42]"></span>
                </span>
            </button>
            <a href="{{ route('home') }}" class="flex min-w-0 max-w-[min(48vw,200px)] shrink-0 items-center sm:max-w-[220px]" aria-label="GNAT Donation home">
                <img src="{{ asset('clogo.png') }}" alt="GNAT Donation" class="h-8 w-auto max-h-11 max-w-full object-contain object-left sm:h-11" width="200" height="48" />
            </a>
            <nav class="hidden flex-1 items-center justify-center gap-6 lg:flex xl:gap-10" aria-label="Primary">
                <a href="{{ route('home') }}#home" class="ml-nav-link">Home</a>
                <a href="{{ route('home') }}#about2" class="ml-nav-link">About us</a>
                <a href="{{ route('home') }}#events" class="ml-nav-link">Events</a>
                <a href="{{ route('home') }}#gallery" class="ml-nav-link">Gallery</a>
                <a href="{{ route('home') }}#contact" class="ml-nav-link">Contact us</a>
            </nav>
            <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3">
                <a href="{{ route('member.login') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-[#351c42] shadow-md shadow-[#351c42]/8 ring-1 ring-[#351c42]/10 transition hover:bg-[#351c42] hover:text-white hover:ring-[#351c42] sm:h-11 sm:w-11" aria-label="Member login">
                    <svg class="h-[21px] w-[21px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill="currentColor" d="M12 11.5a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z"/>
                        <path fill="currentColor" d="M6 20.25v-.75c0-2.9 2.35-5.25 5.25-5.25h1.5c2.9 0 5.25 2.35 5.25 5.25v.75H6Z" opacity="0.92"/>
                        <circle cx="17.75" cy="16.75" r="3.15" stroke="currentColor" stroke-width="1.25" fill="none"/>
                        <path stroke="currentColor" stroke-width="1.35" stroke-linecap="round" d="M17.75 15.15v3.2M16.15 16.75h3.2"/>
                    </svg>
                </a>
                <a href="{{ route('home') }}#donate" class="hidden rounded-full bg-gradient-to-r from-[#fddc6a] to-[#f5d56a] px-5 py-2.5 text-xs font-bold text-[#311742] shadow-lg shadow-amber-200/50 ring-1 ring-amber-200/60 transition hover:brightness-105 min-[400px]:inline-flex sm:text-sm">Donate</a>
            </div>
        </div>
        <div id="ml-mobile-nav" class="ml-mobile-panel border-t border-[#351c42]/10 bg-white/95 backdrop-blur-md lg:hidden" hidden>
            <nav class="flex flex-col px-4 py-4 pb-6" aria-label="Mobile">
                <a href="{{ route('home') }}#home" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Home</a>
                <a href="{{ route('home') }}#about2" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">About us</a>
                <a href="{{ route('home') }}#events" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Events</a>
                <a href="{{ route('home') }}#gallery" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Gallery</a>
                <a href="{{ route('home') }}#contact" class="rounded-xl px-3 py-3 text-sm font-bold uppercase tracking-wide text-[#351c42] hover:bg-[#351c42]/5">Contact us</a>
                <a href="{{ route('home') }}#donate" class="mt-4 rounded-full bg-[#351c42] py-3.5 text-center text-sm font-bold text-[#fddc6a] shadow-lg">Donate</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        <div id="auth-main">
            <div class="mx-auto mb-10 max-w-lg text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-[#965995]/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-[#965995]">Members</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-[#351c42] sm:text-4xl" data-auth-page-title>Welcome back</h1>
                <p class="mx-auto mt-3 max-w-md text-base leading-relaxed text-[#351c42]/65" data-auth-page-sub>Sign in or register to track donations and volunteer activity.</p>
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
                                <span class="text-sm font-semibold text-[#351c42]/40">Forgot password?</span>
                            </div>
                            <button type="submit" class="ml-btn-primary mt-2 w-full">Sign in</button>
                        </form>
                    </div>

                    <div id="panel-signup" role="tabpanel" aria-labelledby="tab-signup" data-auth-panel="signup" hidden>
                        <h2 class="text-center text-lg font-bold tracking-tight text-[#351c42] sm:text-xl">New member registration</h2>
                        <p class="mx-auto mt-2 max-w-sm text-center text-sm text-[#351c42]/55">We’ll verify your mobile number in the next step.</p>
                        <form class="mt-8 space-y-6" method="POST" action="{{ route('member.register.store') }}">
                            @csrf
                            <div class="grid gap-6 sm:grid-cols-2 sm:gap-x-8">
                                <div class="space-y-5">
                                    <div>
                                        <label class="ml-label" for="su-first">First name <span class="text-red-500">*</span></label>
                                        <input id="su-first" name="first_name" type="text" autocomplete="given-name" required class="ml-inp" placeholder="Given name" value="{{ old('first_name') }}" />
                                    </div>
                                    <div>
                                        <label class="ml-label" for="su-last">Last name <span class="text-red-500">*</span></label>
                                        <input id="su-last" name="last_name" type="text" autocomplete="family-name" required class="ml-inp" placeholder="Family name" value="{{ old('last_name') }}" />
                                    </div>
                                    <div>
                                        <label class="ml-label" for="su-mobile">Mobile <span class="text-red-500">*</span></label>
                                        <input id="su-mobile" name="mobile" type="tel" inputmode="tel" autocomplete="tel" required class="ml-inp" placeholder="+91 98765 43210" value="{{ old('mobile') }}" />
                                    </div>
                                </div>
                                <div class="space-y-5">
                                    <div>
                                        <label class="ml-label" for="su-email">Email <span class="text-red-500">*</span></label>
                                        <input id="su-email" name="email" type="email" autocomplete="email" required class="ml-inp" placeholder="name@email.com" value="{{ old('email') }}" />
                                    </div>
                                    <div>
                                        <label class="ml-label" for="su-pass">Password <span class="text-red-500">*</span></label>
                                        <input id="su-pass" name="password" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="Min. 6 characters" />
                                    </div>
                                    <div>
                                        <label class="ml-label" for="su-pass2">Confirm password <span class="text-red-500">*</span></label>
                                        <input id="su-pass2" name="password_confirmation" type="password" autocomplete="new-password" required minlength="6" class="ml-inp" placeholder="Repeat password" />
                                    </div>
                                    <div class="flex justify-end pt-1">
                                        <button type="button" class="text-sm font-semibold text-[#965995] transition hover:text-[#351c42]" data-back-to-login>Back to sign in</button>
                                    </div>
                                </div>
                            </div>
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
    </main>

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

            @if($errors->any() && (old('first_name') || old('password_confirmation')))
                setTab("signup");
            @elseif($errors->any())
                setTab("signin");
            @endif
        })();
    </script>
</body>
</html>
