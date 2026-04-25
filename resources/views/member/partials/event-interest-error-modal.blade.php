@php
    $eventError = session('event_interest_error');
    $showEventErrorModal = (bool) session('event_interest_error_modal', false) && filled($eventError);
@endphp

@if ($showEventErrorModal)
    <div
        id="event-interest-error-modal"
        class="md-modal-overlay is-open"
        role="dialog"
        aria-modal="true"
        aria-labelledby="event-interest-error-title"
        data-event-interest-error-modal
    >
        <div class="relative w-full max-w-md rounded-2xl border border-rose-200 bg-white p-6 shadow-2xl shadow-[#351c42]/20">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 id="event-interest-error-title" class="text-center text-lg font-extrabold text-[#351c42]">Event registration unavailable</h2>
            <p class="mt-2 text-center text-sm leading-relaxed text-[#351c42]/75">{{ $eventError }}</p>
            <button
                type="button"
                class="mt-6 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995] focus-visible:ring-offset-2"
                data-close-event-interest-error-modal
            >
                OK
            </button>
        </div>
    </div>
    <script>
        (function () {
            var root = document.getElementById("event-interest-error-modal");
            if (!root) return;
            function close() {
                root.classList.remove("is-open");
                root.setAttribute("aria-hidden", "true");
            }
            root.querySelector("[data-close-event-interest-error-modal]")?.addEventListener("click", close);
            root.addEventListener("click", function (e) {
                if (e.target === root) close();
            });
            document.addEventListener("keydown", function esc(e) {
                if (e.key === "Escape" && root.classList.contains("is-open")) {
                    close();
                    document.removeEventListener("keydown", esc);
                }
            });
        })();
    </script>
@endif
