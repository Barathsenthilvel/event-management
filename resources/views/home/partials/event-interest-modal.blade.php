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
                <h2 id="interest-modal-title" class="mt-1 text-lg font-extrabold text-[#351c42]">Register to attend</h2>
                <p id="interest-modal-subtitle" class="mt-1 text-xs text-[#351c42]/65">Confirm attendance — tell us how you’d like to continue.</p>
            </div>
            <button type="button" class="rounded-xl p-2 text-[#351c42]/50 hover:bg-[#351c42]/5 hover:text-[#351c42]" data-interest-modal-close aria-label="Close">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
        </div>

        <div id="interest-step-choose" class="hidden mt-5 space-y-4">
            <p class="text-sm font-semibold text-[#351c42] leading-snug">Please confirm: are you a <span class="text-[#965995]">member</span>, or an <span class="text-[#965995]">interested person</span> (guest — not a member yet)?</p>
            <div class="space-y-3">
                <button type="button" id="interest-opt-member" class="group w-full rounded-2xl border-2 border-[#351c42]/20 bg-white px-4 py-4 text-left transition-colors hover:border-[#965995]/40 hover:bg-[#f8f6fa]">
                    <span class="block text-sm font-extrabold text-[#351c42]">I am a member</span>
                    <span class="mt-1 block text-xs font-medium text-[#351c42]/65">You’ll sign in next, then return here to confirm your interest.</span>
                </button>
                <button type="button" id="interest-opt-guest" class="group w-full rounded-2xl border-2 border-transparent bg-[#351c42] px-4 py-4 text-left shadow-md shadow-[#351c42]/20 transition-colors hover:bg-[#4d2a5c]">
                    <span class="block text-sm font-extrabold text-[#fddc6a]">I am a guest / interested person</span>
                    <span class="mt-1 block text-xs font-medium text-[#fddc6a]/85">Share name, email, and phone — no account needed.</span>
                </button>
            </div>
        </div>

        <div id="interest-step-confirm" class="hidden mt-5 space-y-4">
            <button type="button" id="interest-confirm-proceed" class="w-full rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-md hover:bg-[#4d2a5c]">
                Continue
            </button>
            <button type="button" id="interest-back-confirm" class="w-full rounded-2xl border border-[#351c42]/15 py-2.5 text-sm font-extrabold text-[#351c42]/80 hover:bg-[#351c42]/5">
                Back
            </button>
        </div>

        <div id="interest-step-member" class="hidden mt-5 space-y-4">
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

        <div id="interest-step-member-loggedin" class="hidden mt-5 space-y-3">
            <form id="interest-member-confirm-form" method="POST" action="" class="space-y-3">
                @csrf
                <button type="submit" class="w-full rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-md hover:bg-[#4d2a5c] disabled:cursor-not-allowed disabled:opacity-60">
                    Confirm interest
                </button>
            </form>
            <button type="button" id="interest-back-member-loggedin" class="w-full rounded-2xl border border-[#351c42]/15 py-2.5 text-sm font-extrabold text-[#351c42]/80 hover:bg-[#351c42]/5">
                Cancel
            </button>
        </div>

        <div id="interest-step-form" class="hidden mt-5">
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
                <button type="button" id="interest-back-form" class="w-full rounded-2xl border border-[#351c42]/15 py-2.5 text-sm font-extrabold text-[#351c42]/80 hover:bg-[#351c42]/5">
                    Back
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
        const stepConfirm = document.getElementById("interest-step-confirm");
        const stepMember = document.getElementById("interest-step-member");
        const stepMemberLoggedIn = document.getElementById("interest-step-member-loggedin");
        const stepForm = document.getElementById("interest-step-form");
        const memberConfirmForm = document.getElementById("interest-member-confirm-form");
        const titleEl = document.getElementById("interest-modal-title");
        const subtitleEl = document.getElementById("interest-modal-subtitle");
        const emailHint = document.getElementById("interest-email-hint");
        if (!modal || !form || !stepChoose || !stepMember || !stepForm) return;

        let pendingMemberInterestUrl = "";
        let pendingConfirmTarget = null;

        const nameEl = document.getElementById("interest-name");
        const emailEl = document.getElementById("interest-email");
        const phoneEl = document.getElementById("interest-phone");

        function showSteps(which) {
            stepChoose.classList.toggle("hidden", which !== "choose");
            if (stepConfirm) {
                stepConfirm.classList.toggle("hidden", which !== "confirm");
            }
            stepMember.classList.toggle("hidden", which !== "member");
            if (stepMemberLoggedIn) {
                stepMemberLoggedIn.classList.toggle("hidden", which !== "member-loggedin");
            }
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
            pendingConfirmTarget = null;
            showSteps(null);
            if (emailEl) {
                emailEl.readOnly = false;
                emailEl.classList.remove("bg-[#f6f3e9]", "cursor-not-allowed");
            }
            if (emailHint) emailHint.classList.add("hidden");
        }

        function setTitles(title, subtitle) {
            if (titleEl) titleEl.textContent = title;
            if (subtitleEl) {
                const s = subtitle == null ? "" : String(subtitle);
                subtitleEl.textContent = s;
                subtitleEl.classList.toggle("hidden", s.trim() === "");
            }
        }

        function openInterestModalFromButton(btn) {
            if (!btn) return;
            pendingMemberInterestUrl = btn.getAttribute("data-member-interest-url") || "";
            const url = btn.getAttribute("data-interest-url");
            if (url) form.setAttribute("action", url);
            if (nameEl) nameEl.value = "";
            if (emailEl) {
                emailEl.value = "";
                emailEl.readOnly = false;
                emailEl.classList.remove("bg-[#f6f3e9]", "cursor-not-allowed");
            }
            if (emailHint) emailHint.classList.add("hidden");
            if (phoneEl) phoneEl.value = "";

            if (pendingMemberInterestUrl && memberConfirmForm) {
                memberConfirmForm.setAttribute("action", pendingMemberInterestUrl);
                setTitles("Confirm your interest", "");
                showSteps("member-loggedin");
            } else {
                pendingConfirmTarget = null;
                setTitles("Register your interest", "First, tell us if you’re a member or a guest.");
                showSteps("choose");
            }
            openModal();
        }

        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".interest-open-btn");
            if (!btn) return;
            e.preventDefault();
            openInterestModalFromButton(btn);
        });

        document.getElementById("interest-opt-member")?.addEventListener("click", () => {
            pendingConfirmTarget = "member";
            setTitles("Confirm next step", "Continue to member sign-in?");
            showSteps("confirm");
        });

        document.getElementById("interest-opt-guest")?.addEventListener("click", () => {
            pendingConfirmTarget = "guest";
            setTitles("Confirm next step", "Continue to share your details?");
            showSteps("confirm");
        });

        document.getElementById("interest-confirm-proceed")?.addEventListener("click", () => {
            if (pendingConfirmTarget === "member") {
                setTitles("Member login", "");
                showSteps("member");
                return;
            }
            if (pendingConfirmTarget === "guest") {
                setTitles("Your details", "");
                if (nameEl) nameEl.value = "";
                if (emailEl) {
                    emailEl.value = "";
                    emailEl.readOnly = false;
                    emailEl.classList.remove("bg-[#f6f3e9]", "cursor-not-allowed");
                }
                if (emailHint) emailHint.classList.add("hidden");
                if (phoneEl) phoneEl.value = "";
                showSteps("form");
            }
        });

        document.getElementById("interest-back-confirm")?.addEventListener("click", () => {
            pendingConfirmTarget = null;
            setTitles("Register your interest", "First, tell us if you’re a member or a guest.");
            showSteps("choose");
        });

        document.getElementById("interest-back-choose")?.addEventListener("click", () => {
            if (pendingConfirmTarget) {
                setTitles("Confirm next step", pendingConfirmTarget === "member" ? "Continue to member sign-in?" : "Continue to share your details?");
                showSteps("confirm");
                return;
            }
            setTitles("Register your interest", "First, tell us if you’re a member or a guest.");
            showSteps("choose");
        });

        document.getElementById("interest-back-member-loggedin")?.addEventListener("click", () => {
            closeModal();
        });

        document.getElementById("interest-back-form")?.addEventListener("click", () => {
            pendingConfirmTarget = "guest";
            setTitles("Confirm next step", "Continue to share your details?");
            showSteps("confirm");
        });

        function showInterestAjaxErrorModal(title, htmlBody) {
            const id = "home-event-interest-ajax-error-modal";
            document.getElementById(id)?.remove();
            const root = document.createElement("div");
            root.id = id;
            root.className =
                "fixed inset-0 z-[220] flex items-center justify-center bg-[#351c42]/45 px-4";
            root.setAttribute("role", "dialog");
            root.setAttribute("aria-modal", "true");
            root.setAttribute("aria-labelledby", "home-event-interest-ajax-error-title");
            const panel = document.createElement("div");
            panel.className =
                "w-full max-w-md rounded-2xl border border-rose-200 bg-white p-6 shadow-2xl";
            const iconWrap = document.createElement("div");
            iconWrap.className =
                "mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-700";
            iconWrap.innerHTML =
                '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            const h2 = document.createElement("h2");
            h2.id = "home-event-interest-ajax-error-title";
            h2.className = "text-center text-lg font-extrabold text-[#351c42]";
            h2.textContent = title || "Event registration unavailable";
            const body = document.createElement("div");
            body.className =
                "event-interest-error-markdown mt-3 text-left text-sm leading-relaxed text-[#351c42]/80 [&_p:not(:last-child)]:mb-3 [&_p:last-child]:mb-0 [&_strong]:font-extrabold [&_strong]:text-[#351c42]";
            body.innerHTML = htmlBody || "<p>Something went wrong.</p>";
            const ok = document.createElement("button");
            ok.type = "button";
            ok.className =
                "mt-6 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:brightness-105";
            ok.textContent = "OK";
            function tearDown() {
                root.remove();
                document.removeEventListener("keydown", onEsc);
            }
            function onEsc(e) {
                if (e.key === "Escape") tearDown();
            }
            ok.addEventListener("click", () => {
                tearDown();
                closeModal();
            });
            root.addEventListener("click", (e) => {
                if (e.target === root) {
                    tearDown();
                    closeModal();
                }
            });
            document.addEventListener("keydown", onEsc);
            panel.appendChild(iconWrap);
            panel.appendChild(h2);
            panel.appendChild(body);
            panel.appendChild(ok);
            root.appendChild(panel);
            document.body.appendChild(root);
        }

        function showInterestAjaxSuccessModal(message) {
            const id = "home-event-interest-ajax-success-modal";
            document.getElementById(id)?.remove();
            const root = document.createElement("div");
            root.id = id;
            root.className =
                "fixed inset-0 z-[220] flex items-center justify-center bg-[#351c42]/45 px-4";
            root.setAttribute("role", "dialog");
            root.setAttribute("aria-modal", "true");
            const panel = document.createElement("div");
            panel.className =
                "w-full max-w-md rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-2xl";
            const h2 = document.createElement("h2");
            h2.className = "text-center text-lg font-extrabold text-[#351c42]";
            h2.textContent = "Event registration confirmed";
            const p = document.createElement("p");
            p.className = "mt-2 text-center text-sm leading-relaxed text-[#351c42]/75";
            p.textContent = message || "Thank you. Your event interest has been recorded.";
            const ok = document.createElement("button");
            ok.type = "button";
            ok.className =
                "mt-6 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:brightness-105";
            ok.textContent = "OK";
            function tearDown() {
                root.remove();
                document.removeEventListener("keydown", onEsc);
            }
            function onEsc(e) {
                if (e.key === "Escape") tearDown();
            }
            ok.addEventListener("click", tearDown);
            root.addEventListener("click", (e) => {
                if (e.target === root) tearDown();
            });
            document.addEventListener("keydown", onEsc);
            panel.appendChild(h2);
            panel.appendChild(p);
            panel.appendChild(ok);
            root.appendChild(panel);
            document.body.appendChild(root);
        }

        memberConfirmForm?.addEventListener("submit", async function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const action = this.getAttribute("action");
            if (!action) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
            submitBtn?.setAttribute("disabled", "disabled");
            try {
                const fd = new FormData(this);
                const res = await fetch(action, {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        ...(token ? { "X-CSRF-TOKEN": token } : {}),
                    },
                    body: fd,
                });
                const raw = await res.text();
                let data = {};
                try {
                    data = raw ? JSON.parse(raw) : {};
                } catch {
                    data = {};
                }
                if (res.ok && data.ok) {
                    closeModal();
                    showInterestAjaxSuccessModal(data.message);
                    return;
                }
                submitBtn?.removeAttribute("disabled");
                const errTitle = data.event_interest_error_title || "Event registration unavailable";
                const errHtml =
                    data.event_interest_error_html ||
                    "<p>" + (data.message || "Unable to submit your interest.") + "</p>";
                showInterestAjaxErrorModal(errTitle, errHtml);
            } catch {
                submitBtn?.removeAttribute("disabled");
                showInterestAjaxErrorModal(
                    "Something went wrong",
                    "<p>Please check your connection and try again.</p>"
                );
            }
        });

        modal.querySelectorAll("[data-interest-modal-close], [data-interest-modal-backdrop]").forEach((el) => {
            el.addEventListener("click", closeModal);
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && modal.classList.contains("flex")) closeModal();
        });
    })();
</script>
