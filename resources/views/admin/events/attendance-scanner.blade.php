@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6">
    <div class="max-w-4xl mx-auto space-y-5">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">QR Attendance Scanner</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Event: {{ $event->title }}</p>
            </div>
            <a href="{{ route('admin.events.show', $event->id) }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Back to event</a>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Scan member/public attendance QR. Valid scans automatically update status from
                <span class="font-bold">Interested</span> to <span class="font-bold">Attended</span>.
            </p>
            <div id="event-attendance-qr-reader" class="w-full max-w-lg rounded-xl border border-slate-200 bg-slate-50 p-3"></div>
            <div class="flex items-center gap-2">
                <button type="button" id="event-attendance-start" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">Start Scanner</button>
                <button type="button" id="event-attendance-stop" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Stop</button>
            </div>
            <div id="event-attendance-result" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                Waiting to scan...
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    (() => {
        const readerId = "event-attendance-qr-reader";
        const html5QrCode = new Html5Qrcode(readerId);
        const startBtn = document.getElementById("event-attendance-start");
        const stopBtn = document.getElementById("event-attendance-stop");
        const resultBox = document.getElementById("event-attendance-result");
        let scanning = false;
        let lock = false;

        function setResult(message, ok = true) {
            resultBox.textContent = message;
            resultBox.classList.toggle("border-emerald-200", ok);
            resultBox.classList.toggle("bg-emerald-50", ok);
            resultBox.classList.toggle("text-emerald-900", ok);
            resultBox.classList.toggle("border-rose-200", !ok);
            resultBox.classList.toggle("bg-rose-50", !ok);
            resultBox.classList.toggle("text-rose-900", !ok);
        }

        async function markAttendance(scannedText) {
            if (lock) return;
            lock = true;
            try {
                const parsed = new URL(scannedText, window.location.origin);
                const expectedPathPrefix = "/admin/events/{{ $event->id }}/attendance/consume/";
                if (!parsed.pathname.startsWith(expectedPathPrefix)) {
                    setResult("Scanned QR is not for this event attendance flow.", false);
                    return;
                }

                const response = await fetch(parsed.href, {
                    method: "GET",
                    headers: { "Accept": "application/json" },
                    credentials: "same-origin",
                });
                const data = await response.json();
                if (!response.ok || !data.ok) {
                    setResult(data.message || "Could not update attendance.", false);
                    return;
                }
                setResult((data.message || "Attendance updated.") + " " + (data.who ? "(" + data.who + ")" : ""), true);
            } catch (e) {
                setResult("Invalid QR code.", false);
            } finally {
                setTimeout(() => { lock = false; }, 1000);
            }
        }

        async function start() {
            if (scanning) return;
            await html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                async (decodedText) => {
                    await markAttendance(decodedText);
                }
            );
            scanning = true;
            setResult("Scanner started. Point camera at attendee QR.");
        }

        async function stop() {
            if (!scanning) return;
            await html5QrCode.stop();
            await html5QrCode.clear();
            scanning = false;
            setResult("Scanner stopped.");
        }

        startBtn?.addEventListener("click", () => start().catch(() => setResult("Unable to start camera scanner.", false)));
        stopBtn?.addEventListener("click", () => stop().catch(() => setResult("Unable to stop scanner cleanly.", false)));
    })();
</script>
@endpush

