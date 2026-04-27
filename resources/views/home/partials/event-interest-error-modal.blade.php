@php
    $eventError = session('event_interest_error');
    $showEventErrorModal = (bool) session('event_interest_error_modal', false) && filled($eventError);
@endphp

@if($showEventErrorModal)
    <div
        id="home-event-interest-error-modal"
        class="fixed inset-0 z-[120] flex items-center justify-center bg-[#351c42]/45 px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="home-event-interest-error-title"
    >
        <div class="w-full max-w-md rounded-2xl border border-rose-200 bg-white p-6 shadow-2xl">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 id="home-event-interest-error-title" class="text-center text-lg font-extrabold text-[#351c42]">Event registration unavailable</h2>
            <p class="mt-2 text-center text-sm leading-relaxed text-[#351c42]/75">{{ $eventError }}</p>
            <button
                type="button"
                class="mt-6 w-full rounded-2xl bg-[#351c42] py-3 text-sm font-extrabold text-[#fddc6a] transition hover:brightness-105"
                data-close-home-event-interest-error-modal
            >
                OK
            </button>
        </div>
    </div>
    <script>
        (function () {
            var root = document.getElementById('home-event-interest-error-modal');
            if (!root) return;
            function closeModal() {
                root.remove();
            }
            root.querySelector('[data-close-home-event-interest-error-modal]')?.addEventListener('click', closeModal);
            root.addEventListener('click', function (e) {
                if (e.target === root) closeModal();
            });
            document.addEventListener('keydown', function esc(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', esc);
                }
            });
        })();
    </script>
@endif
