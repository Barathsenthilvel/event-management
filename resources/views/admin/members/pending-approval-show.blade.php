@extends('admin.layouts.app')

@section('content')
@php
    $m = $member;
    $doc = fn ($path) => $path ? asset('storage/' . ltrim($path, '/')) : null;
    $showApprovalActions = $showApprovalActions ?? true;
    $backUrl = $backUrl ?? route('admin.members.pending-approvals.index', request()->only('q'));
    $backLabel = $backLabel ?? 'Back to pending list';
    $statusTitle = $m->is_approved ? 'Approved member' : 'Pending approval';

    $idCardService = app(\App\Services\MemberIdCardService::class);
    $memberCode = $idCardService->memberCode($m);
    $verifyUrl = route('member.verify', ['code' => $memberCode]);
@endphp
<div class="flex-1 overflow-y-auto custom-scroll p-4 sm:p-6">
    {{-- Modal-style frame: unique GNAT-inspired panel --}}
    <div class="mx-auto max-w-4xl">
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <a href="{{ $backUrl }}"
               class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-[#351c42]/20">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ $backLabel }}
            </a>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-[#351c42]/12 bg-white shadow-[0_24px_80px_-12px_rgba(53,28,66,0.18)] ring-1 ring-black/5">
            {{-- Header strip --}}
            <div class="relative bg-gradient-to-r from-[#351c42] via-[#4a2660] to-[#965995] px-6 py-8 text-white">
                <div class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-[#fddc6a]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-10 left-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border-2 border-[#fddc6a]/40 bg-white/10 text-xl font-black tracking-tight text-[#fddc6a] shadow-inner">
                            {{ strtoupper(substr($m->name ?? 'ME', 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[#fddc6a]/90">{{ $statusTitle }}</p>
                            <h1 class="mt-1 text-xl font-extrabold tracking-tight sm:text-2xl flex flex-wrap items-center gap-2.5">
                                <span>{{ $m->name }}</span>
                                <span class="rounded-lg bg-[#fddc6a]/20 border border-[#fddc6a]/40 px-2.5 py-0.5 text-xs font-black tracking-wider text-[#fddc6a] font-mono">{{ $memberCode }}</span>
                            </h1>
                            <p class="mt-1 text-xs font-semibold text-white/80">{{ $m->email }}</p>
                        </div>
                    </div>
                    @if($showApprovalActions)
                    <div class="flex flex-wrap gap-2">
                        <form id="approve-member-form" method="POST" action="{{ route('admin.members.pending-approvals.approve', $m->id) }}">
                            @csrf
                            <button type="button" data-open-approve-modal class="rounded-xl bg-emerald-500 px-5 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-emerald-400">
                                Approve member
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.members.pending-approvals.reject', $m->id) }}" onsubmit="const b=this.querySelector('button'); b.disabled=true; b.querySelector('[data-reject-spinner]')?.classList.remove('hidden'); b.querySelector('[data-reject-label]').textContent='Rejecting...';">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-5 py-2.5 text-xs font-extrabold text-white backdrop-blur transition hover:bg-white/20 disabled:opacity-75 disabled:cursor-not-allowed">
                                <svg class="hidden h-4 w-4 animate-spin text-white" data-reject-spinner viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span data-reject-label>Reject</span>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2">
                {{-- Member Verification QR Code Section --}}
                <section class="rounded-2xl border border-[#351c42]/15 bg-gradient-to-br from-[#faf8fc] via-white to-slate-50 p-6 shadow-sm lg:col-span-2">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
                            <div class="relative p-3 bg-white rounded-2xl border border-slate-200/90 shadow-md shrink-0">
                                <div id="admin-member-qr-code" data-qr-value="{{ $verifyUrl }}" class="flex items-center justify-center min-w-[130px] min-h-[130px]"></div>
                                <div class="mt-2 text-center">
                                    <span class="inline-block text-[10px] font-black uppercase tracking-wider text-slate-600 font-mono">{{ $memberCode }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full bg-[#965995]/10 px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest text-[#965995]">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    Member QR Verification
                                </div>
                                <h3 class="mt-2 text-lg font-extrabold text-[#351c42] tracking-tight">Official Member QR Code</h3>
                                <p class="mt-1 text-xs leading-relaxed text-slate-600 max-w-xl">
                                    Scan this QR code using any smartphone camera or reader to verify authentic membership credentials and view live verification status.
                                </p>
                                <div class="mt-4 flex flex-wrap items-center gap-3 justify-center sm:justify-start">
                                    <a href="{{ $verifyUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 rounded-xl bg-[#351c42] px-4 py-2.5 text-xs font-extrabold text-[#fddc6a] shadow-sm hover:bg-[#4a2660] transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Open Verification Page
                                    </a>
                                    <button type="button" data-open-qr-modal
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 shadow-sm hover:bg-slate-50 hover:border-[#965995]/40 transition">
                                        <svg class="h-4 w-4 text-[#965995]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                        Expand QR Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50/80 to-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#965995]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span>
                        Personal
                    </h2>
                    <dl class="mt-4 space-y-3 text-xs">
                        @foreach([
                            'First name' => $m->first_name,
                            'Last name' => $m->last_name,
                            'Mobile' => $m->mobile,
                            'Date of birth' => $m->dob?->format('d M Y'),
                            'Gender' => $m->gender,
                            'Blood group' => $m->blood_group,
                        ] as $label => $val)
                            <div class="flex justify-between gap-4 border-b border-slate-100/80 pb-2 last:border-0 last:pb-0">
                                <dt class="font-bold text-slate-500">{{ $label }}</dt>
                                <dd class="max-w-[60%] text-right font-semibold text-slate-900">{{ $val ?: '—' }}</dd>
                            </div>
                        @endforeach
                        <div class="flex justify-between gap-4 border-t border-slate-100/80 pt-3">
                            <dt class="font-bold text-slate-500">Designation</dt>
                            <dd class="max-w-[60%] text-right font-semibold text-slate-900">{{ $m->designation?->name ?? '—' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50/80 to-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#965995]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span>
                        Professional &amp; address
                    </h2>
                    <dl class="mt-4 space-y-3 text-xs">
                        @foreach([
                            'Profile type' => $m->profile_type ? ucwords(str_replace('_', ' ', (string) $m->profile_type)) : null,
                            'Referred by' => $m->referrer?->name
                                ? $m->referrer->name . ($m->referrer->mobile ? ' (' . $m->referrer->mobile . ')' : '')
                                : null,
                            'Qualification' => $m->qualification,
                            'RNRM number & date' => $m->rnrm_number_with_date,
                            'Student ID' => $m->student_id,
                            'College' => $m->college_name,
                            'Door no.' => $m->door_no,
                            'Locality / area' => $m->locality_area,
                            'State' => $m->state,
                            'Country' => 'India',
                            'PIN code' => $m->pin_code,
                            'Council state' => $m->council_state,
                            'Currently working' => $m->currently_working,
                        ] as $label => $val)
                            <div class="flex justify-between gap-4 border-b border-slate-100/80 pb-2 last:border-0 last:pb-0">
                                <dt class="shrink-0 font-bold text-slate-500">{{ $label }}</dt>
                                <dd class="text-right font-semibold text-slate-900">{{ $val ?: '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section class="rounded-2xl border border-dashed border-[#351c42]/20 bg-[#faf8fc] p-5 lg:col-span-2">
                    <h2 class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#351c42]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#351c42]"></span>
                        Documents
                    </h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        @foreach([
                            'Educational certificate' => $doc($m->educational_certificate_path),
                            'RNRM certificate' => $doc($m->rnrm_certificate_path),
                            'Student ID card' => $doc($m->student_id_card_path),
                            'Aadhar card' => $doc($m->aadhar_card_path),
                            'Passport photo' => $doc($m->passport_photo_path),
                        ] as $label => $url)
                            <div class="rounded-xl border border-white bg-white p-4 shadow-sm">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">{{ $label }}</p>
                                @if($url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center gap-1.5 text-xs font-extrabold text-[#965995] hover:text-[#351c42]">
                                        View file
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @else
                                    <p class="mt-2 text-[11px] font-bold text-slate-400">Not uploaded</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

@if($showApprovalActions)
<div id="approve-member-modal" class="fixed inset-0 z-[160] hidden items-center justify-center bg-[#111827]/60 p-4 backdrop-blur-[2px]" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="approve-member-modal-title">
    <div data-approve-member-backdrop class="absolute inset-0" aria-hidden="true"></div>
    <div class="relative w-full max-w-md overflow-hidden rounded-3xl border border-white/20 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-[#faf9fc] px-5 py-4">
            <h3 id="approve-member-modal-title" class="text-base font-extrabold text-[#351c42]">Approve this member?</h3>
            <button type="button" data-close-approve-modal class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Close">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6l-12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-5 py-5 text-sm text-slate-700">
            They will be able to sign in and purchase membership plans.
        </div>
        <div class="flex flex-col gap-2 border-t border-slate-100 bg-white px-5 py-4 sm:flex-row sm:justify-end">
            <button type="button" data-close-approve-modal class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">Cancel</button>
            <button type="button" data-confirm-approve-member class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-xs font-extrabold text-white transition hover:bg-emerald-400 disabled:opacity-75 disabled:cursor-not-allowed">
                <svg class="hidden h-4 w-4 animate-spin text-white" data-approve-spinner viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span data-approve-label>Approve member</span>
            </button>
        </div>
    </div>
</div>
@endif
{{-- Member QR Modal --}}
<div id="member-qr-modal" class="fixed inset-0 z-[170] hidden items-center justify-center bg-[#111827]/60 p-4 backdrop-blur-[2px]" aria-hidden="true" role="dialog" aria-modal="true">
    <div data-close-qr-modal class="absolute inset-0" aria-hidden="true"></div>
    <div class="relative w-full max-w-sm overflow-hidden rounded-3xl border border-white/20 bg-white shadow-2xl p-6 text-center">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <h3 class="text-sm font-extrabold text-[#351c42]">Official Member QR Code</h3>
            <button type="button" data-close-qr-modal class="inline-flex h-8 w-8 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">✕</button>
        </div>
        <p class="text-sm font-extrabold text-slate-900">{{ $m->name }}</p>
        <p class="text-xs font-bold text-[#965995] font-mono mt-0.5">{{ $memberCode }}</p>
        <div class="my-5 p-4 bg-white rounded-2xl border border-slate-200 shadow-inner flex items-center justify-center">
            <div id="admin-member-modal-qr" data-qr-value="{{ $verifyUrl }}" class="mx-auto flex items-center justify-center"></div>
        </div>
        <a href="{{ $verifyUrl }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#351c42] py-2.5 text-xs font-extrabold text-[#fddc6a] hover:bg-[#4a2660] transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Verify Member Online
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    (() => {
        const qrEl = document.getElementById("admin-member-qr-code");
        const modalQrEl = document.getElementById("admin-member-modal-qr");
        const modal = document.getElementById("member-qr-modal");
        const openModalBtn = document.querySelector("[data-open-qr-modal]");
        const closeModalEls = document.querySelectorAll("[data-close-qr-modal]");

        if (qrEl && typeof QRCode !== "undefined") {
            const val = qrEl.getAttribute("data-qr-value");
            if (val) {
                qrEl.innerHTML = "";
                new QRCode(qrEl, {
                    text: val,
                    width: 130,
                    height: 130,
                    colorDark: "#351c42",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        }

        if (openModalBtn && modal) {
            openModalBtn.addEventListener("click", () => {
                modal.classList.remove("hidden");
                modal.classList.add("flex");
                if (modalQrEl && typeof QRCode !== "undefined") {
                    const val = modalQrEl.getAttribute("data-qr-value");
                    if (val && !modalQrEl.hasChildNodes()) {
                        new QRCode(modalQrEl, {
                            text: val,
                            width: 220,
                            height: 220,
                            colorDark: "#351c42",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    }
                }
            });

            closeModalEls.forEach((el) => {
                el.addEventListener("click", () => {
                    modal.classList.add("hidden");
                    modal.classList.remove("flex");
                });
            });
        }
    })();
</script>
@if($showApprovalActions)
<script>
    (() => {
        const modal = document.getElementById("approve-member-modal");
        const openBtn = document.querySelector("[data-open-approve-modal]");
        const form = document.getElementById("approve-member-form");
        if (!modal || !openBtn || !form) return;

        const backdrop = modal.querySelector("[data-approve-member-backdrop]");
        const closeEls = modal.querySelectorAll("[data-close-approve-modal]");
        const confirmBtn = modal.querySelector("[data-confirm-approve-member]");
        const spinner = confirmBtn?.querySelector("[data-approve-spinner]");
        const label = confirmBtn?.querySelector("[data-approve-label]");
        let lastActive = null;

        function setOpen(open) {
            modal.classList.toggle("hidden", !open);
            modal.classList.toggle("flex", open);
            modal.setAttribute("aria-hidden", open ? "false" : "true");
            document.body.style.overflow = open ? "hidden" : "";

            if (confirmBtn) {
                confirmBtn.disabled = false;
                if (spinner) spinner.classList.add("hidden");
                if (label) label.textContent = "Approve member";
            }

            if (!open && lastActive && typeof lastActive.focus === "function") {
                lastActive.focus();
            }
        }

        openBtn.addEventListener("click", () => {
            lastActive = openBtn;
            setOpen(true);
            confirmBtn?.focus({ preventScroll: true });
        });

        closeEls.forEach((el) => el.addEventListener("click", () => setOpen(false)));
        backdrop?.addEventListener("click", () => setOpen(false));
        confirmBtn?.addEventListener("click", () => {
            if (confirmBtn) confirmBtn.disabled = true;
            if (spinner) spinner.classList.remove("hidden");
            if (label) label.textContent = "Approving...";
            form.submit();
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && !modal.classList.contains("hidden")) {
                setOpen(false);
            }
        });
    })();
</script>
@endif
@endpush
