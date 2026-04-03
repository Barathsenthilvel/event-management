@extends('admin.layouts.app')

@section('content')
@php
    $m = $member;
    $doc = fn ($path) => $path ? asset('storage/' . ltrim($path, '/')) : null;
@endphp
<div class="flex-1 overflow-y-auto custom-scroll p-4 sm:p-6">
    {{-- Modal-style frame: unique GNAT-inspired panel --}}
    <div class="mx-auto max-w-4xl">
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.members.pending-approvals.index', request()->only('q')) }}"
               class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-[#351c42]/20">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to pending list
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
                            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[#fddc6a]/90">Pending approval</p>
                            <h1 class="mt-1 text-xl font-extrabold tracking-tight sm:text-2xl">{{ $m->name }}</h1>
                            <p class="mt-1 text-xs font-semibold text-white/80">{{ $m->email }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.members.pending-approvals.approve', $m->id) }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-emerald-500 px-5 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-emerald-400">
                                Approve member
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.members.pending-approvals.reject', $m->id) }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-white/30 bg-white/10 px-5 py-2.5 text-xs font-extrabold text-white backdrop-blur transition hover:bg-white/20">
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2">
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
                            'Qualification' => $m->qualification,
                            'RNRM number & date' => $m->rnrm_number_with_date,
                            'College' => $m->college_name,
                            'Door no.' => $m->door_no,
                            'Locality / area' => $m->locality_area,
                            'State' => $m->state,
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
@endsection
