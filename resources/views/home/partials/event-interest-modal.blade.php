@php
    $eventsReturnUrl = request()->fullUrl();
    $memberLoginReturn = route('member.login', ['return' => $eventsReturnUrl]);
    $memberRegisterReturn = route('member.register', ['return' => $eventsReturnUrl]);
@endphp
<div id="event-interest-modal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4" aria-hidden="true">
    <div class="absolute inset-0 bg-[#351c42]/55" data-interest-modal-backdrop></div>
    <div class="relative w-full max-w-md rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-2xl shadow-[#351c42]/20 max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-[#965995]">Events</p>
                <h2 id="interest-modal-title" class="mt-1 text-lg font-extrabold text-[#351c42]">Register your interest</h2>
                <p id="interest-modal-subtitle" class="mt-1 text-xs text-[#351c42]/65">Tell us how you’d like to continue.</p>
            </div>
            <button type="button" class="rounded-xl p-2 text-[#351c42]/50 hover:bg-[#351c42]/5 hover:text-[#351c42]" data-interest-modal-close aria-label="Close">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
        </div>

        <div id="interest-step-choose" class="hidden mt-5 space-y-4">
            <p class="text-sm font-semibold text-[#351c42] leading-snug">Are you a <span class="text-[#965995]">member</span>, or an <span class="text-[#965995]">interested person</span> (not a member yet)?</p>
            <div class="space-y-3">
                <button type="button" id="interest-opt-member" class="group w-full rounded-2xl border-2 border-[#351c42]/20 bg-white px-4 py-4 text-left transition-colors hover:border-[#965995]/40 hover:bg-[#f8f6fa]">
                    <span class="block text-sm font-extrabold text-[#351c42]">I am a member</span>
                    <span class="mt-1 block text-xs font-medium text-[#351c42]/65">Log in first, then submit your interest. We’ll bring you back here after sign-in.</span>
                </button>
                <button type="button" id="interest-opt-guest" class="group w-full rounded-2xl border-2 border-transparent bg-[#351c42] px-4 py-4 text-left shadow-md shadow-[#351c42]/20 transition-colors hover:bg-[#4d2a5c]">
                    <span class="block text-sm font-extrabold text-[#fddc6a]">I am an interested person (not a member)</span>
                    <span class="mt-1 block text-xs font-medium text-[#fddc6a]/85">Share a few details — name, email, and phone. No account needed.</span>
                </button>
            </div>
        </div>

        <div id="interest-step-member" class="hidden mt-5 space-y-4">
            <p class="text-sm text-[#351c42]/85 leading-relaxed">Please <strong>log in</strong> with your member account (email/mobile + password, then OTP). After verification you’ll <strong>return to this page automatically</strong> — open the event and tap <strong>Interested</strong> again to confirm.</p>
            <a href="{{ $memberLoginReturn }}" class="flex w-full items-center justify-center rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-md hover:bg-[#4d2a5c]">
                Member login
            </a>
            <p class="text-center text-xs text-[#351c42]/55">New member?</p>
            <a href="{{ $memberRegisterReturn }}" class="flex w-full items-center justify-center rounded-2xl border border-[#351c42]/20 bg-white px-5 py-3 text-sm font-extrabold text-[#351c42] hover:bg-[#351c42]/5">
                Create an account
            </a>
            <button type="button" id="interest-back-choose" class="w-full rounded-2xl border border-[#351c42]/15 py-2.5 text-sm font-extrabold text-[#351c42]/80 hover:bg-[#351c42]/5">
                Back
            </button>
        </div>

        <div id="interest-step-form" class="hidden mt-5">
            <p id="interest-form-intro" class="mb-4 text-sm text-[#351c42]/80 leading-relaxed hidden"></p>
            <form id="event-interest-form" method="post" action="" class="space-y-4">
                @csrf
                <div>
                    <label for="interest-name" class="block text-xs font-bold text-[#351c42]/70">Full name</label>
                    <input id="interest-name" name="name" type="text" required autocomplete="name"
                           class="mt-1 w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm text-[#351c42] outline-none focus:border-[#965995]/50 focus:ring-2 focus:ring-[#965995]/20" />
                </div>
                <div>
                    <label for="interest-email" class="block text-xs font-bold text-[#351c42]/70">Email</label>
                    <input id="interest-email" name="email" type="email" required autocomplete="email"
                           class="mt-1 w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm text-[#351c42] outline-none focus:border-[#965995]/50 focus:ring-2 focus:ring-[#965995]/20" />
                    <p id="interest-email-hint" class="mt-1 hidden text-[11px] text-[#351c42]/55">Email is taken from your member account.</p>
                </div>
                <div>
                    <label for="interest-phone" class="block text-xs font-bold text-[#351c42]/70">Phone number</label>
                    <input id="interest-phone" name="phone" type="tel" required autocomplete="tel"
                           class="mt-1 w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm text-[#351c42] outline-none focus:border-[#965995]/50 focus:ring-2 focus:ring-[#965995]/20" />
                </div>
                <button type="submit" class="w-full rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-md hover:bg-[#4d2a5c]">
                    Submit interest
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById("event-interest-modal");
        const form = document.getElementById("event-interest-form");
        const stepChoose = document.getElementById("interest-step-choose");
        const stepMember = document.getElementById("interest-step-member");
        const stepForm = document.getElementById("interest-step-form");
        const titleEl = document.getElementById("interest-modal-title");
        const subtitleEl = document.getElementById("interest-modal-subtitle");
        const emailHint = document.getElementById("interest-email-hint");
        if (!modal || !form || !stepChoose || !stepMember || !stepForm) return;

        const nameEl = document.getElementById("interest-name");
        const emailEl = document.getElementById("interest-email");
        const phoneEl = document.getElementById("interest-phone");
        const formIntro = document.getElementById("interest-form-intro");

        function showSteps(which) {
            stepChoose.classList.toggle("hidden", which !== "choose");
            stepMember.classList.toggle("hidden", which !== "member");
            stepForm.classList.toggle("hidden", which !== "form");
        }

        function openModal() {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            modal.setAttribute("aria-hidden", "false");
            document.body.classList.add("overflow-hidden");
        }

        function closeModal() {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            modal.setAttribute("aria-hidden", "true");
            document.body.classList.remove("overflow-hidden");
            showSteps(null);
            const intro = document.getElementById("interest-form-intro");
            if (intro) {
                intro.textContent = "";
                intro.classList.add("hidden");
            }
            if (emailEl) {
                emailEl.readOnly = false;
                emailEl.classList.remove("bg-[#f6f3e9]", "cursor-not-allowed");
            }
            if (emailHint) emailHint.classList.add("hidden");
        }

        function setTitles(title, subtitle) {
            if (titleEl) titleEl.textContent = title;
            if (subtitleEl) subtitleEl.textContent = subtitle;
        }

        document.querySelectorAll(".interest-open-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                const url = btn.getAttribute("data-interest-url");
                if (url) form.setAttribute("action", url);
                const loggedIn = btn.getAttribute("data-is-logged-in") === "1";

                if (loggedIn) {
                    setTitles("Member — confirm your interest", "Signed in as a member. Review your details and submit.");
                    if (formIntro) {
                        formIntro.textContent = "";
                        formIntro.classList.add("hidden");
                    }
                    if (nameEl) nameEl.value = btn.getAttribute("data-prefill-name") || "";
                    if (emailEl) {
                        emailEl.value = btn.getAttribute("data-prefill-email") || "";
                        emailEl.readOnly = true;
                        emailEl.classList.add("bg-[#f6f3e9]", "cursor-not-allowed");
                    }
                    if (emailHint) emailHint.classList.remove("hidden");
                    if (phoneEl) phoneEl.value = btn.getAttribute("data-prefill-phone") || "";
                    showSteps("form");
                } else {
                    setTitles("Register your interest", "First, tell us whether you’re a member or an interested visitor.");
                    if (formIntro) {
                        formIntro.textContent = "";
                        formIntro.classList.add("hidden");
                    }
                    if (nameEl) nameEl.value = "";
                    if (emailEl) {
                        emailEl.value = "";
                        emailEl.readOnly = false;
                        emailEl.classList.remove("bg-[#f6f3e9]", "cursor-not-allowed");
                    }
                    if (emailHint) emailHint.classList.add("hidden");
                    if (phoneEl) phoneEl.value = "";
                    showSteps("choose");
                }
                openModal();
            });
        });

        document.getElementById("interest-opt-member")?.addEventListener("click", () => {
            setTitles("Member login", "Sign in, then return here to complete your interest.");
            showSteps("member");
        });

        document.getElementById("interest-opt-guest")?.addEventListener("click", () => {
            setTitles("Your details", "Not a member — we only need a few details for this event.");
            if (formIntro) {
                formIntro.textContent = "Please share your name, email, and phone number. We’ll use these only to follow up about this event.";
                formIntro.classList.remove("hidden");
            }
            if (nameEl) nameEl.value = "";
            if (emailEl) {
                emailEl.value = "";
                emailEl.readOnly = false;
                emailEl.classList.remove("bg-[#f6f3e9]", "cursor-not-allowed");
            }
            if (emailHint) emailHint.classList.add("hidden");
            if (phoneEl) phoneEl.value = "";
            showSteps("form");
        });

        document.getElementById("interest-back-choose")?.addEventListener("click", () => {
            setTitles("Register your interest", "First, tell us whether you’re a member or an interested visitor.");
            if (formIntro) {
                formIntro.textContent = "";
                formIntro.classList.add("hidden");
            }
            showSteps("choose");
        });

        modal.querySelectorAll("[data-interest-modal-close], [data-interest-modal-backdrop]").forEach((el) => {
            el.addEventListener("click", closeModal);
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && modal.classList.contains("flex")) closeModal();
        });
    })();
</script>
