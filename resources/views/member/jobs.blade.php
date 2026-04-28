@extends('member.layouts.portal')

@section('title', 'Career Jobs — GNAT Association')

@section('portal_main_id', 'member-jobs-main')

@section('content')
    <header class="scroll-mt-28 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Careers</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Member Jobs</h1>
            <p class="mt-1 max-w-2xl text-sm text-[#351c42]/65">Search, apply, save jobs and submit your Need Job profile.</p>
        </div>
        <a href="{{ route('member.dashboard') }}" class="shrink-0 text-sm font-semibold text-[#965995] hover:text-[#351c42]">← Back to dashboard</a>
    </header>

    <section class="space-y-4">
        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-900">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-[#351c42]/10 bg-white p-2">
            <a href="{{ route('member.jobs.index', ['tab' => 'search']) }}"
               class="rounded-xl px-4 py-2 text-xs font-extrabold {{ $tab === 'search' ? 'bg-[#351c42] text-white' : 'text-[#351c42]/70 hover:bg-[#351c42]/5' }}">
                Search Jobs
            </a>
            <a href="{{ route('member.jobs.index', ['tab' => 'applied']) }}"
               class="rounded-xl px-4 py-2 text-xs font-extrabold {{ $tab === 'applied' ? 'bg-[#351c42] text-white' : 'text-[#351c42]/70 hover:bg-[#351c42]/5' }}">
                Applied <span class="ml-1 rounded-md bg-[#f8f6fb] px-1.5 py-0.5 text-[10px] text-[#351c42]">{{ $appliedCount ?? 0 }}</span>
            </a>
            <a href="{{ route('member.jobs.index', ['tab' => 'saved']) }}"
               class="rounded-xl px-4 py-2 text-xs font-extrabold {{ $tab === 'saved' ? 'bg-[#351c42] text-white' : 'text-[#351c42]/70 hover:bg-[#351c42]/5' }}">
                Saved <span class="ml-1 rounded-md bg-[#f8f6fb] px-1.5 py-0.5 text-[10px] text-[#351c42]">{{ $savedCount ?? 0 }}</span>
            </a>
            <a href="{{ route('member.jobs.index', ['tab' => 'need-job']) }}"
               class="rounded-xl px-4 py-2 text-xs font-extrabold {{ $tab === 'need-job' ? 'bg-[#351c42] text-white' : 'text-[#351c42]/70 hover:bg-[#351c42]/5' }}">
                Need Job
            </a>
        </div>

        @if($tab !== 'need-job')
            <form method="GET" class="rounded-2xl border border-[#351c42]/10 bg-white p-3 shadow-sm">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <div class="relative flex-1">
                        <input
                            type="search"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Search by title, hospital, skill, or code..."
                            class="w-full rounded-xl border border-[#351c42]/15 bg-white pl-9 pr-4 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:border-[#965995]/60 focus:ring-2 focus:ring-[#965995]/20"
                        />
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#351c42]/35" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M20 20l-3.5-3.5" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <select name="sort" class="rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-bold text-[#351c42]/70 outline-none focus:ring-2 focus:ring-[#965995]/20">
                        <option value="recent" {{ ($sort ?? 'recent') === 'recent' ? 'selected' : '' }}>Recently Added</option>
                        <option value="a-z" {{ ($sort ?? 'recent') === 'a-z' ? 'selected' : '' }}>A to Z</option>
                        <option value="z-a" {{ ($sort ?? 'recent') === 'z-a' ? 'selected' : '' }}>Z to A</option>
                    </select>
                    <button type="submit" class="rounded-xl bg-[#351c42] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#291331]">Apply</button>
                    <a href="{{ route('member.jobs.index', ['tab' => $tab]) }}" class="rounded-xl border border-[#351c42]/15 bg-white px-4 py-2.5 text-sm font-bold text-[#351c42]/80 hover:border-[#351c42]/30">Reset</a>
                </div>
            </form>

            @if($jobs->isEmpty())
                <div class="rounded-2xl border border-[#351c42]/10 bg-white/85 p-8 text-center shadow-sm">
                    <p class="text-sm font-semibold text-[#351c42]/70">No jobs found in this tab.</p>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2">
                    @foreach($jobs as $job)
                        @php
                            $alreadyApplied = in_array((int) $job->id, $appliedJobIds ?? [], true);
                            $isSaved = in_array((int) $job->id, $savedJobIds ?? [], true);
                            $jobType = $job->vacancy_any ? 'Any' : trim(collect([
                                $job->vacancy_permanent ? 'Permanent' : null,
                                $job->vacancy_temporary ? 'Temporary' : null,
                            ])->filter()->implode(' / '));
                            $prefType = $job->preference_any ? 'Any' : trim(collect([
                                $job->preference_wfh ? 'WFH' : null,
                                $job->preference_onsite ? 'Onsite' : null,
                            ])->filter()->implode(' / '));
                        @endphp
                        <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="h-1 w-full bg-gradient-to-r from-[#351c42] via-[#6c3f79] to-[#965995]"></div>
                            <div class="flex h-full flex-col p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-[#965995]">{{ $job->hospital ?: 'Hospital' }}</p>
                                        <h2 class="mt-1 line-clamp-2 text-xl font-extrabold tracking-tight text-[#351c42]">{{ $job->title }}</h2>
                                        <p class="mt-1 text-[11px] font-bold text-[#351c42]/65">Job code: {{ $job->code ?: 'N/A' }}</p>
                                    </div>
                                    @if($job->promote_front)
                                        <span class="rounded-full border border-[#965995]/30 bg-[#965995]/10 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-[#965995]">Featured</span>
                                    @endif
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span class="rounded-full border border-[#351c42]/15 bg-[#f8f6fb] px-3 py-1 text-[11px] font-bold text-[#351c42]/80">Openings: {{ (int) ($job->no_of_openings ?? 0) }}</span>
                                    <span class="rounded-full border border-[#351c42]/15 bg-[#f8f6fb] px-3 py-1 text-[11px] font-bold text-[#351c42]/80">Job Type: {{ $jobType !== '' ? $jobType : 'Any' }}</span>
                                    <span class="rounded-full border border-[#351c42]/15 bg-[#f8f6fb] px-3 py-1 text-[11px] font-bold text-[#351c42]/80">Preference: {{ $prefType !== '' ? $prefType : 'Any' }}</span>
                                </div>

                                @if($job->description)
                                    <p class="mt-4 text-sm leading-6 text-[#351c42]/80 line-clamp-3">{{ $job->description }}</p>
                                @endif

                                <div class="mt-5 flex items-center gap-2 border-t border-[#351c42]/10 pt-4">
                                    <form method="POST" action="{{ route('member.jobs.save-toggle', $job->id) }}" class="shrink-0">
                                        @csrf
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border {{ $isSaved ? 'border-amber-300 bg-amber-50 text-amber-700' : 'border-[#351c42]/15 bg-white text-[#351c42]/60' }} hover:border-[#351c42]/30">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="M6 3h12a1 1 0 011 1v17l-7-4-7 4V4a1 1 0 011-1z" />
                                            </svg>
                                        </button>
                                    </form>
                                    @if($alreadyApplied)
                                        <button type="button" disabled class="inline-flex flex-1 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-extrabold text-emerald-800">Applied</button>
                                    @else
                                        <form method="POST" action="{{ route('member.jobs.apply', $job->id) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[#351c42] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#291331]">Apply now</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pt-2">
                    {{ $jobs->links() }}
                </div>
            @endif
        @else
            @php
                $member = auth()->user();
            @endphp
            <div class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-5 shadow-sm">
                <h2 class="text-lg font-extrabold text-[#351c42]">Need Job Form</h2>
                <p class="mt-1 text-sm text-[#351c42]/65">Fill this and submit. Admin can review and contact you.</p>
                @if($latestNeedJobRequest)
                    <p class="mt-2 text-xs font-bold text-emerald-700">
                        Last submitted: {{ $latestNeedJobRequest->created_at?->format('d M Y h:i A') }} · Status: {{ ucfirst($latestNeedJobRequest->status) }}
                    </p>
                @endif
            </div>

            <form method="POST" action="{{ route('member.jobs.need-job.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-[#351c42]/10 bg-white p-5 shadow-sm">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Name *</label>
                        <input name="name" value="{{ old('name', $member->name) }}" required class="w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:ring-2 focus:ring-[#965995]/20" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Mobile</label>
                        <input name="mobile" value="{{ old('mobile', $member->mobile) }}" class="w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:ring-2 focus:ring-[#965995]/20" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}" required class="w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:ring-2 focus:ring-[#965995]/20" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Qualification</label>
                        <input name="qualification" value="{{ old('qualification') }}" class="w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:ring-2 focus:ring-[#965995]/20" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Position Looking For</label>
                        <input name="position_looking_for" value="{{ old('position_looking_for') }}" class="w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:ring-2 focus:ring-[#965995]/20" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Experience</label>
                        <input name="experience" value="{{ old('experience') }}" class="w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:ring-2 focus:ring-[#965995]/20" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Explain in detail</label>
                        <textarea name="details" rows="4" class="w-full rounded-xl border border-[#351c42]/15 px-3 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:ring-2 focus:ring-[#965995]/20">{{ old('details') }}</textarea>
                    </div>
                    <div class="md:col-span-2 rounded-xl border border-[#351c42]/10 bg-[#faf8fc] p-4">
                        <label class="block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Upload Resume (PDF)</label>
                        <input type="file" name="resume" accept=".pdf" class="mt-2 block w-full text-xs font-semibold text-[#351c42]/70" />
                        <label class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-[#351c42]/70">
                            <input type="checkbox" name="use_profile_resume" value="1" class="rounded border-[#351c42]/30 text-[#351c42] focus:ring-[#965995]/25">
                            Use profile certificate as resume
                        </label>
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <a href="{{ route('member.jobs.index', ['tab' => 'need-job']) }}" class="rounded-xl bg-slate-200 px-4 py-2 text-xs font-extrabold text-slate-700">Cancel</a>
                    <button type="submit" class="rounded-xl bg-[#351c42] px-5 py-2 text-xs font-extrabold text-white hover:bg-[#291331]">Submit</button>
                </div>
            </form>
        @endif
    </section>

    @php
        $jobApplyModalMessage = session('job_apply_success') ?: session('job_apply_error');
        $jobApplyModalType = session('job_apply_success') ? 'success' : (session('job_apply_error') ? 'error' : null);
    @endphp
    @if($jobApplyModalMessage)
        <div id="job-apply-modal" class="fixed inset-0 z-[140] flex items-center justify-center bg-black/45 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full {{ $jobApplyModalType === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    @if($jobApplyModalType === 'success')
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    @endif
                </div>
                <h3 class="mt-4 text-center text-lg font-extrabold text-[#351c42]">
                    {{ $jobApplyModalType === 'success' ? 'Application submitted' : 'Application failed' }}
                </h3>
                <p class="mt-2 text-center text-sm font-semibold text-[#351c42]/80">
                    {{ $jobApplyModalMessage }}
                </p>
                <div class="mt-5 flex justify-center">
                    <button type="button" id="job-apply-modal-ok" class="rounded-xl bg-[#351c42] px-6 py-2.5 text-sm font-bold text-white hover:bg-[#291331]">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <script>
            (function () {
                var modal = document.getElementById('job-apply-modal');
                var okBtn = document.getElementById('job-apply-modal-ok');
                if (!modal || !okBtn) return;
                var closeModal = function () {
                    modal.remove();
                };
                okBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) closeModal();
                });
            })();
        </script>
    @endif
@endsection
