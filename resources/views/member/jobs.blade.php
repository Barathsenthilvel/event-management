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
                @php
                    $profileResumeAvailable = $profileResumeAvailable ?? false;
                    $hospitalLogos = $hospitalLogos ?? collect();
                @endphp
                <div class="grid w-full gap-5">
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
                            $dirLogoPath = $job->hospital ? $hospitalLogos->get($job->hospital) : null;
                            $hospitalLogoUrl = $job->hospital_logo_path
                                ? asset('storage/' . ltrim($job->hospital_logo_path, '/'))
                                : ($dirLogoPath ? asset('storage/' . ltrim($dirLogoPath, '/')) : null);
                            $hospitalLabel = $job->hospital ?: 'Hospital';
                            $hospitalCompact = preg_replace('/\s+/u', '', $hospitalLabel) ?: 'H';
                            $hospitalInitials = mb_strtoupper(mb_substr($hospitalCompact, 0, 2));
                            $hospitalMeta = ['label' => 'Hospital', 'value' => $hospitalLabel];
                            if ($hospitalLogoUrl) {
                                $hospitalMeta['logo'] = $hospitalLogoUrl;
                            }
                            $jobReadMoreMeta = array_values(array_filter([
                                $hospitalMeta,
                                ['label' => 'Job code', 'value' => $job->code ? (string) $job->code : null],
                                ['label' => 'Vacancy type', 'value' => $jobType !== '' ? $jobType : 'Any'],
                                ['label' => 'Preference', 'value' => $prefType !== '' ? $prefType : 'Any'],
                                ['label' => 'No. of openings', 'value' => (string) (int) ($job->no_of_openings ?? 0)],
                            ], fn ($row) => ! empty($row['value'])));
                            $jobReadMoreParts = [];
                            if (trim((string) ($job->description ?? '')) !== '') {
                                $jobReadMoreParts[] = "Job description\n\n".trim((string) $job->description);
                            }
                            if (trim((string) ($job->key_skills ?? '')) !== '') {
                                $jobReadMoreParts[] = "Key skills or roles & responsibility\n\n".trim((string) $job->key_skills);
                            }
                            $jobReadMoreBody = $jobReadMoreParts === []
                                ? 'No job description or key skills have been added for this listing.'
                                : implode("\n\n—\n\n", $jobReadMoreParts);
                        @endphp
                        <article class="group flex h-full w-full flex-col overflow-hidden rounded-3xl border border-[#351c42]/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="h-1 w-full bg-gradient-to-r from-[#351c42] via-[#6c3f79] to-[#965995]"></div>
                            <div class="flex h-full flex-col p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 flex-1 items-start gap-3">
                                        @if($hospitalLogoUrl)
                                            <img src="{{ $hospitalLogoUrl }}" alt="" class="h-12 w-12 shrink-0 rounded-xl border border-[#351c42]/10 object-cover shadow-sm" width="48" height="48" loading="lazy">
                                        @else
                                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-[#351c42]/10 bg-[#965995]/12 text-[11px] font-extrabold text-[#965995]" aria-hidden="true">{{ $hospitalInitials }}</span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-[#965995]">{{ $hospitalLabel }}</p>
                                            <h2 class="mt-1 line-clamp-2 text-xl font-extrabold tracking-tight text-[#351c42]">{{ $job->title }}</h2>
                                            <p class="mt-1 text-[11px] font-bold text-[#351c42]/65">Job code: {{ $job->code ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @if($job->promote_front)
                                        <span class="shrink-0 rounded-full border border-[#965995]/30 bg-[#965995]/10 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-[#965995]">Featured</span>
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

                                <button
                                    type="button"
                                    class="mt-3 inline-flex items-center gap-1 text-sm font-extrabold text-[#965995] underline decoration-[#965995]/35 underline-offset-2 transition hover:text-[#351c42] hover:decoration-[#351c42]/40"
                                    data-read-more
                                    data-read-more-title="{{ e($job->title) }}"
                                    data-read-more-meta='@json($jobReadMoreMeta)'
                                    data-read-more-content='@json($jobReadMoreBody)'
                                    data-read-more-job-id="{{ $job->id }}"
                                    data-read-more-job-save-url="{{ route('member.jobs.save-toggle', $job->id) }}"
                                    data-read-more-job-apply-url="{{ route('member.jobs.apply', $job->id) }}"
                                    data-read-more-job-saved="{{ $isSaved ? '1' : '0' }}"
                                    data-read-more-job-applied="{{ $alreadyApplied ? '1' : '0' }}"
                                    data-read-more-job-profile-resume="{{ $profileResumeAvailable ? '1' : '0' }}"
                                >
                                    Read more
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

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
                                        <button type="button" disabled class="inline-flex flex-1 cursor-not-allowed items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-extrabold text-emerald-800">Applied</button>
                                    @else
                                        <button
                                            type="button"
                                            class="job-apply-open-btn inline-flex flex-1 cursor-pointer items-center justify-center rounded-xl bg-[#351c42] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#291331]"
                                            data-apply-url="{{ route('member.jobs.apply', $job->id) }}"
                                            data-job-title="{{ e($job->title) }}"
                                            data-profile-resume="{{ $profileResumeAvailable ? '1' : '0' }}"
                                        >Apply now</button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pt-2">
                    {{ $jobs->links() }}
                </div>

                <div
                    id="job-apply-choice-modal"
                    class="fixed inset-0 z-[150] hidden items-center justify-center bg-black/45 p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="job-apply-choice-title"
                >
                    <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
                        <h3 id="job-apply-choice-title" class="text-lg font-extrabold text-[#351c42]">Submit application</h3>
                        <p id="job-apply-choice-subtitle" class="mt-1 text-sm font-semibold text-[#351c42]/70"></p>
                        <p class="mt-2 text-sm text-[#351c42]/65">Choose whether to use the resume already on your profile (educational certificate) or upload a different PDF for this application.</p>
                        <form id="job-apply-choice-form" method="POST" action="#" class="mt-5 space-y-4" enctype="multipart/form-data">
                            @csrf
                            <fieldset class="space-y-3">
                                <legend class="sr-only">Resume source</legend>
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#351c42]/15 bg-[#faf8fc] p-3">
                                    <input type="radio" name="resume_choice" value="profile" class="mt-1 text-[#351c42]" data-job-apply-profile-radio>
                                    <span class="text-sm font-semibold text-[#351c42]">Use resume from my profile <span class="block text-xs font-normal text-[#351c42]/60">Same file as your educational certificate on file.</span></span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#351c42]/15 bg-white p-3">
                                    <input type="radio" name="resume_choice" value="upload" class="mt-1 text-[#351c42]" checked data-job-apply-upload-radio>
                                    <span class="text-sm font-semibold text-[#351c42]">Upload a different resume <span class="block text-xs font-normal text-[#351c42]/60">PDF, max 5&nbsp;MB.</span></span>
                                </label>
                            </fieldset>
                            <div id="job-apply-upload-field" class="rounded-xl border border-dashed border-[#351c42]/20 bg-[#faf8fc] p-4">
                                <label class="block text-xs font-extrabold uppercase tracking-wide text-[#351c42]/70">Resume (PDF)</label>
                                <input type="file" name="resume" accept=".pdf,application/pdf" class="mt-2 block w-full text-xs font-semibold text-[#351c42]/80 file:mr-2 file:rounded-lg file:border-0 file:bg-[#351c42] file:px-3 file:py-2 file:text-xs file:font-bold file:text-[#fddc6a]">
                            </div>
                            <p id="job-apply-profile-hint" class="hidden text-xs font-semibold text-amber-800"></p>
                            <div class="flex flex-wrap gap-2 pt-2">
                                <button type="button" id="job-apply-choice-cancel" class="flex-1 rounded-xl border border-[#351c42]/20 bg-white px-4 py-2.5 text-sm font-extrabold text-[#351c42] transition hover:bg-[#351c42]/5">Cancel</button>
                                <button type="submit" class="flex-1 rounded-xl bg-[#351c42] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#291331]">Submit application</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    (function () {
                        var modal = document.getElementById('job-apply-choice-modal');
                        var form = document.getElementById('job-apply-choice-form');
                        var subtitle = document.getElementById('job-apply-choice-subtitle');
                        var cancel = document.getElementById('job-apply-choice-cancel');
                        var uploadWrap = document.getElementById('job-apply-upload-field');
                        var profileRadio = form && form.querySelector('[data-job-apply-profile-radio]');
                        var uploadRadio = form && form.querySelector('[data-job-apply-upload-radio]');
                        var profileHint = document.getElementById('job-apply-profile-hint');
                        if (!modal || !form) return;

                        function toggleUploadField() {
                            var useUpload = uploadRadio && uploadRadio.checked;
                            if (uploadWrap) uploadWrap.classList.toggle('hidden', !useUpload);
                            var fileInput = form.querySelector('input[name="resume"]');
                            if (fileInput) {
                                fileInput.required = !!useUpload;
                                if (!useUpload) fileInput.value = '';
                            }
                        }

                        function openModal(url, jobTitle, profileOk) {
                            var readMoreModal = document.getElementById('read-more-modal');
                            if (readMoreModal && readMoreModal.classList.contains('flex')) {
                                readMoreModal.classList.add('hidden');
                                readMoreModal.classList.remove('flex');
                                readMoreModal.setAttribute('aria-hidden', 'true');
                                document.body.style.overflow = '';
                            }
                            form.action = url;
                            if (subtitle) subtitle.textContent = jobTitle ? ('Role: ' + jobTitle) : '';
                            var hasProfile = profileOk === '1' || profileOk === true;
                            if (profileRadio) {
                                profileRadio.disabled = !hasProfile;
                                if (hasProfile) {
                                    profileRadio.checked = true;
                                    if (uploadRadio) uploadRadio.checked = false;
                                } else {
                                    if (uploadRadio) uploadRadio.checked = true;
                                }
                            }
                            if (profileHint) {
                                if (!hasProfile) {
                                    profileHint.textContent = 'No profile resume on file — upload a PDF below or add your certificate in your profile.';
                                    profileHint.classList.remove('hidden');
                                } else {
                                    profileHint.classList.add('hidden');
                                    profileHint.textContent = '';
                                }
                            }
                            toggleUploadField();
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                            document.body.classList.add('overflow-hidden');
                        }

                        function closeModal() {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                            document.body.classList.remove('overflow-hidden');
                            form.action = '#';
                            var fileInput = form.querySelector('input[name="resume"]');
                            if (fileInput) fileInput.value = '';
                        }

                        document.addEventListener('click', function (e) {
                            var btn = e.target.closest('.job-apply-open-btn');
                            if (!btn || btn.disabled) return;
                            openModal(btn.getAttribute('data-apply-url'), btn.getAttribute('data-job-title'), btn.getAttribute('data-profile-resume'));
                        });

                        if (profileRadio) profileRadio.addEventListener('change', toggleUploadField);
                        if (uploadRadio) uploadRadio.addEventListener('change', toggleUploadField);

                        if (cancel) cancel.addEventListener('click', closeModal);
                        modal.addEventListener('click', function (e) {
                            if (e.target === modal) closeModal();
                        });
                        document.addEventListener('keydown', function (e) {
                            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
                        });
                    })();
                </script>
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
                @if($jobApplyModalType !== 'success')
                    <h3 class="mt-4 text-center text-lg font-extrabold text-[#351c42]">Application failed</h3>
                @endif
                <p class="{{ $jobApplyModalType === 'success' ? 'mt-4' : 'mt-2' }} text-center text-sm font-semibold text-[#351c42]/80">
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
