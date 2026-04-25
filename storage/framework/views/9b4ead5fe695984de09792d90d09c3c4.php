<?php if(session('event_interest_success') && session('event_interest_success_modal', true)): ?>
    <div
        id="event-interest-success-modal"
        class="md-modal-overlay is-open"
        role="dialog"
        aria-modal="true"
        aria-labelledby="event-interest-success-title"
        data-event-interest-success-modal
    >
        <div class="relative w-full max-w-md rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-2xl shadow-[#351c42]/20">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 id="event-interest-success-title" class="text-center text-lg font-extrabold text-[#351c42]">Event registration confirmed</h2>
            <p class="mt-2 text-center text-sm leading-relaxed text-[#351c42]/75"><?php echo e(session('event_interest_success')); ?></p>
            <button
                type="button"
                class="mt-6 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995] focus-visible:ring-offset-2"
                data-close-event-interest-success-modal
            >
                OK
            </button>
        </div>
    </div>
    <script>
        (function () {
            var root = document.getElementById("event-interest-success-modal");
            if (!root) return;
            function close() {
                root.classList.remove("is-open");
                root.setAttribute("aria-hidden", "true");
            }
            root.querySelector("[data-close-event-interest-success-modal]")?.addEventListener("click", close);
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
<?php endif; ?>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\partials\event-interest-success-modal.blade.php ENDPATH**/ ?>