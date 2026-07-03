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

        <div id="event-attendance-insecure-warning" class="hidden bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900 leading-relaxed">
            <p class="font-extrabold mb-1">Camera scanning requires HTTPS.</p>
            <p>
                This site is being served over an insecure connection, so browsers block camera access.
                Use the <span class="font-bold">Upload QR image</span> or <span class="font-bold">Paste QR link</span> options below,
                or open this page over <span class="font-mono">https://</span> (or via <span class="font-mono">localhost</span>) to use the camera.
            </p>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Scan member/public attendance QR. Valid scans automatically update status from
                <span class="font-bold">Interested</span> to <span class="font-bold">Attended</span>.
            </p>

            <div id="event-attendance-camera-select-wrap" class="hidden">
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wide mb-1">Camera</label>
                <select id="event-attendance-camera-select" class="w-full max-w-lg rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700"></select>
            </div>

            <div id="event-attendance-qr-reader" class="w-full max-w-lg rounded-xl border border-slate-200 bg-slate-50 p-3"></div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="event-attendance-start" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold">Start Scanner</button>
                <button type="button" id="event-attendance-stop" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700">Stop</button>

                <label for="event-attendance-file" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-extrabold text-slate-700 cursor-pointer">
                    Upload QR image
                </label>
                <input type="file" id="event-attendance-file" accept="image/*" class="hidden" />
            </div>

            <div class="border-t border-slate-100 pt-3">
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wide mb-1">Or paste QR link</label>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="text" id="event-attendance-manual" placeholder="Paste the QR URL here" class="flex-1 min-w-[240px] rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700" />
                    <button type="button" id="event-attendance-manual-submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Submit</button>
                </div>
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
        const startBtn = document.getElementById("event-attendance-start");
        const stopBtn = document.getElementById("event-attendance-stop");
        const fileInput = document.getElementById("event-attendance-file");
        const manualInput = document.getElementById("event-attendance-manual");
        const manualBtn = document.getElementById("event-attendance-manual-submit");
        const cameraSelect = document.getElementById("event-attendance-camera-select");
        const cameraSelectWrap = document.getElementById("event-attendance-camera-select-wrap");
        const insecureWarning = document.getElementById("event-attendance-insecure-warning");
        const resultBox = document.getElementById("event-attendance-result");

        if (typeof Html5Qrcode === "undefined") {
            resultBox.textContent = "QR scanner library failed to load. Check your network connection.";
            return;
        }

        const html5QrCode = new Html5Qrcode(readerId);
        let scanning = false;
        let lock = false;

        // Camera + getUserMedia only work on secure origins (https or localhost).
        // Detect and warn early so the user has an actionable message.
        const isSecure = window.isSecureContext
            || location.protocol === "https:"
            || location.hostname === "localhost"
            || location.hostname === "127.0.0.1";
        const hasMediaDevices = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);

        if (!isSecure || !hasMediaDevices) {
            insecureWarning.classList.remove("hidden");
            startBtn.disabled = true;
            startBtn.classList.add("opacity-50", "cursor-not-allowed");
        }

        function setResult(message, ok = true) {
            resultBox.textContent = message;
            resultBox.classList.toggle("border-emerald-200", ok);
            resultBox.classList.toggle("bg-emerald-50", ok);
            resultBox.classList.toggle("text-emerald-900", ok);
            resultBox.classList.toggle("border-rose-200", !ok);
            resultBox.classList.toggle("bg-rose-50", !ok);
            resultBox.classList.toggle("text-rose-900", !ok);
        }

        function describeError(err) {
            if (!err) return "Unknown error.";
            if (typeof err === "string") return err;
            const name = err.name || "";
            const msg = err.message || String(err);
            if (name === "NotAllowedError" || /permission/i.test(msg)) {
                return "Camera permission was denied. Allow camera access in the browser and try again.";
            }
            if (name === "NotFoundError" || /no camera/i.test(msg)) {
                return "No camera was found on this device.";
            }
            if (name === "NotReadableError" || /in use/i.test(msg)) {
                return "Camera is already in use by another application.";
            }
            if (name === "SecurityError" || /secure/i.test(msg) || /https/i.test(msg)) {
                return "Camera access requires HTTPS. Open this page over https:// or use Upload QR image below.";
            }
            return msg;
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
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.ok) {
                    setResult(data.message || ("Could not update attendance (HTTP " + response.status + ")."), false);
                    return;
                }
                setResult((data.message || "Attendance updated.") + " " + (data.who ? "(" + data.who + ")" : ""), true);
            } catch (e) {
                setResult("Invalid QR code: " + describeError(e), false);
            } finally {
                setTimeout(() => { lock = false; }, 1000);
            }
        }

        async function populateCameras() {
            try {
                const cameras = await Html5Qrcode.getCameras();
                if (!cameras || cameras.length === 0) return null;
                cameraSelect.innerHTML = "";
                cameras.forEach((cam, idx) => {
                    const opt = document.createElement("option");
                    opt.value = cam.id;
                    opt.textContent = cam.label || ("Camera " + (idx + 1));
                    cameraSelect.appendChild(opt);
                });
                // Prefer a back-facing camera if present.
                const back = cameras.find(c => /back|rear|environment/i.test(c.label || ""));
                if (back) cameraSelect.value = back.id;
                cameraSelectWrap.classList.remove("hidden");
                return cameraSelect.value;
            } catch (e) {
                return null;
            }
        }

        async function start() {
            if (scanning) return;
            if (!isSecure || !hasMediaDevices) {
                setResult("Camera scanning requires HTTPS. Use Upload QR image or Paste QR link instead.", false);
                return;
            }

            const cameraId = await populateCameras();
            const source = cameraId ? cameraId : { facingMode: "environment" };

            try {
                await html5QrCode.start(
                    source,
                    { fps: 10, qrbox: { width: 240, height: 240 } },
                    async (decodedText) => { await markAttendance(decodedText); }
                );
                scanning = true;
                setResult("Scanner started. Point camera at attendee QR.");
            } catch (err) {
                // If a specific camera id failed, retry once with facingMode as a fallback.
                if (cameraId) {
                    try {
                        await html5QrCode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 240, height: 240 } },
                            async (decodedText) => { await markAttendance(decodedText); }
                        );
                        scanning = true;
                        setResult("Scanner started. Point camera at attendee QR.");
                        return;
                    } catch (err2) {
                        setResult("Unable to start camera scanner: " + describeError(err2), false);
                        return;
                    }
                }
                setResult("Unable to start camera scanner: " + describeError(err), false);
            }
        }

        async function stop() {
            if (!scanning) return;
            try {
                await html5QrCode.stop();
                await html5QrCode.clear();
            } catch (e) {
                setResult("Unable to stop scanner cleanly: " + describeError(e), false);
                return;
            }
            scanning = false;
            setResult("Scanner stopped.");
        }

        async function scanFromFile(file) {
            if (!file) return;
            if (scanning) {
                try { await stop(); } catch (_) {}
            }
            try {
                const decodedText = await html5QrCode.scanFile(file, true);
                await markAttendance(decodedText);
            } catch (err) {
                setResult("Could not read QR from image: " + describeError(err), false);
            }
        }

        startBtn?.addEventListener("click", () => { start(); });
        stopBtn?.addEventListener("click", () => { stop(); });
        fileInput?.addEventListener("change", (e) => {
            const file = e.target.files && e.target.files[0];
            scanFromFile(file);
            e.target.value = "";
        });
        manualBtn?.addEventListener("click", () => {
            const val = (manualInput.value || "").trim();
            if (!val) {
                setResult("Paste a QR link first.", false);
                return;
            }
            markAttendance(val);
        });
        manualInput?.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                manualBtn.click();
            }
        });
    })();
</script>
@endpush
