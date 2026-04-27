@extends('member.layouts.portal')

@section('title', 'Events — GNAT Association')

@section('portal_main_id', 'member-events-main')

@section('content')
    <header class="scroll-mt-28 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Events</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Your events</h1>
            <p class="mt-1 max-w-2xl text-sm text-[#351c42]/65">Events you’ve shown interest in — attendance and certificates appear here.</p>
        </div>
        <a href="{{ route('member.dashboard') }}" class="shrink-0 text-sm font-semibold text-[#965995] hover:text-[#351c42]">← Back to dashboard</a>
    </header>

    @include('member.partials.member-events-panel', [
        'myEventInvites' => $myEventInvites,
    ])

    <div id="member-attendance-qr-modal" class="fixed inset-0 z-[190] hidden items-center justify-center bg-[#351c42]/70 p-4 backdrop-blur-[2px]" aria-hidden="true">
        <div class="absolute inset-0" data-close-attendance-qr></div>
        <div class="relative w-full max-w-sm rounded-2xl border border-white/25 bg-white p-5 shadow-2xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="member-attendance-qr-title" class="text-sm font-extrabold text-[#351c42]">Event entry QR</h3>
                <button type="button" data-close-attendance-qr class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">✕</button>
            </div>
            <p class="mb-3 text-xs font-semibold text-[#351c42]/65">Show this QR at event desk for attendance scan.</p>
            <div id="member-attendance-qr-code" class="mx-auto flex min-h-[240px] w-[240px] items-center justify-center rounded-xl border border-slate-200 bg-slate-50"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    (() => {
        const modal = document.getElementById("member-attendance-qr-modal");
        const qrBox = document.getElementById("member-attendance-qr-code");
        const titleEl = document.getElementById("member-attendance-qr-title");
        if (!modal || !qrBox || !titleEl || typeof QRCode === "undefined") return;

        const setOpen = (open) => {
            modal.classList.toggle("hidden", !open);
            modal.classList.toggle("flex", open);
            modal.setAttribute("aria-hidden", open ? "false" : "true");
            document.body.style.overflow = open ? "hidden" : "";
        };

        document.querySelectorAll("[data-open-attendance-qr]").forEach((btn) => {
            btn.addEventListener("click", () => {
                const value = btn.getAttribute("data-qr-value") || "";
                const title = btn.getAttribute("data-qr-title") || "Event entry QR";
                if (!value) return;

                titleEl.textContent = title + " — Entry QR";
                qrBox.innerHTML = "";
                new QRCode(qrBox, {
                    text: value,
                    width: 220,
                    height: 220,
                    correctLevel: QRCode.CorrectLevel.M,
                });
                setOpen(true);
            });
        });

        modal.querySelectorAll("[data-close-attendance-qr]").forEach((el) => {
            el.addEventListener("click", () => setOpen(false));
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && !modal.classList.contains("hidden")) {
                setOpen(false);
            }
        });
    })();
</script>
@endpush
