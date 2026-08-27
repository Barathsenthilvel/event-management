@extends('member.layouts.gnat')

@section('title', 'Member profile — GNAT Association')

@push('styles')
<style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
        .ml-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(53, 28, 66, 0.78);
            margin-bottom: 0.5rem;
        }
        .ml-inp {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(255, 255, 255, 0.9);
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            color: #351c42;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            text-align: left;
        }
        input[type="date"].ml-inp,
        input[type="date"] {
            text-align: left !important;
            -webkit-appearance: none;
            appearance: none;
            min-height: 2.75rem;
            box-sizing: border-box;
        }
        input[type="date"]::-webkit-date-and-time-value {
            text-align: left !important;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            margin-left: auto;
            cursor: pointer;
        }
        .ml-inp:focus {
            border-color: rgba(150, 89, 149, 0.55);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
        }
        .ml-inp.is-invalid {
            border-color: rgba(220, 38, 38, 0.6) !important;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.14) !important;
            background: #fff !important;
        }
        .ml-help {
            margin-top: 0.4rem;
            min-height: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #dc2626;
        }
        .ml-card-soft {
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.08);
            background: linear-gradient(180deg, rgba(150, 89, 149, 0.03), rgba(255, 255, 255, 0.9));
            padding: 1rem;
        }
        .ml-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, #351c42 0%, #4d2a5c 100%);
            color: #fddc6a;
            box-shadow: 0 8px 24px rgba(53, 28, 66, 0.28);
        }
        .ml-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: #fff;
            color: #351c42;
            border: 1px solid rgba(53, 28, 66, 0.14);
            text-decoration: none;
        }
        .ml-upload-zone {
            border-radius: 1rem;
            border: 2px dashed rgba(150, 89, 149, 0.28);
            background: linear-gradient(180deg, rgba(150, 89, 149, 0.04) 0%, rgba(255, 255, 255, 0.6) 100%);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .ml-upload-zone.is-invalid {
            border-color: rgba(220, 38, 38, 0.6) !important;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.14) !important;
            background: linear-gradient(180deg, rgba(254, 242, 242, 0.8) 0%, rgba(255, 255, 255, 0.9) 100%) !important;
        }
        .ml-upload-zone input[type="file"]::file-selector-button {
            margin-right: 0.75rem;
            border: 0;
            border-radius: 9999px;
            background: linear-gradient(135deg, #351c42, #5c3570);
            color: #fddc6a;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
        }
    </style>
@include('member.partials.profile-searchable-select')
@endpush

@section('content')
    @php
        $isApproved = (bool) $user->is_approved;
        $profileLocked = (bool) $user->profile_completed || $isApproved;
        $pendingProfileDocs = $pendingProfileDocs ?? [];
        $profileUploadMaxBytes = $profileUploadMaxBytes ?? \App\Http\Controllers\MemberProfileController::maxFileSizeBytes();
        $profileUploadMaxLabel = $profileUploadMaxLabel ?? \App\Http\Controllers\MemberProfileController::maxFileSizeLabel();
        $documentAccept = '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.rtf,.txt,.csv,.jpg,.jpeg,.png,.webp,.gif,.bmp,.tif,.tiff,.heic,.heif';
        $stateOptions = [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat',
            'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh',
            'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
            'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand',
            'West Bengal', 'Andaman and Nicobar Islands', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry',
        ];
    @endphp
    <div class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-6 shadow-md sm:p-8">
                <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Account</p>
                        <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">My profile</h1>
                        <p class="mt-2 text-sm text-[#351c42]/65">{{ $profileLocked ? 'Your submitted details are shown below (read-only).' : 'Complete your details to activate your membership.' }}</p>
                    </div>
                    <span class="rounded-full bg-[#351c42] px-4 py-2 text-xs font-bold text-[#fddc6a]">Required fields marked *</span>
                </div>

                @if(session('success'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($isApproved)
                    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
                        Your profile is already approved and cannot be updated now. Please contact admin for any correction.
                    </div>
                @elseif($user->profile_completed)
                    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
                        Please wait for admin approval. You can review your submitted profile details below.
                    </div>
                @endif

                @include('member.partials.profile-upload-error-modal')

                <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" class="space-y-8" id="member-profile-form" novalidate data-upload-max-bytes="{{ $profileUploadMaxBytes }}" data-upload-max-label="{{ $profileUploadMaxLabel }}" @if($profileLocked) data-profile-locked @endif>
                    @csrf

                    <div id="form-validation-summary" class="hidden rounded-2xl border border-red-300 bg-red-50 p-4 sm:p-5 shadow-sm text-red-800" role="alert">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 mt-0.5 text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-red-900">Please fix the missing or invalid details highlighted in red below:</h4>
                                <ul id="form-validation-summary-list" class="mt-2 list-inside list-disc text-xs font-semibold space-y-1 text-red-700">
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <section class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Personal
                            </h3>
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <div>
                                    <label class="ml-label">First name <span class="text-red-500">*</span></label>
                                    <input name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="ml-inp" data-validate="required" data-label="First name" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="first_name"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Last name <span class="text-red-500">*</span></label>
                                    <input name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="ml-inp" data-validate="required" data-label="Last name" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="last_name"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Email ID</label>
                                    <input value="{{ $user->email }}" disabled class="ml-inp bg-slate-100 text-slate-500" />
                                </div>
                                <div>
                                    <label class="ml-label">Mobile <span class="text-red-500">*</span></label>
                                    <input name="mobile" value="{{ old('mobile', $user->mobile) }}" required class="ml-inp" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" data-validate="required|digits:10" data-label="Mobile" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="mobile"></p>
                                </div>
                                <div>
                                    <label class="ml-label">DOB <span class="text-red-500">*</span></label>
                                    <input type="date" name="dob" value="{{ old('dob', optional($user->dob)->format('Y-m-d')) }}" required class="ml-inp" max="{{ now()->format('Y-m-d') }}" data-validate="required|past_date" data-label="Date of birth" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="dob"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Gender <span class="text-red-500">*</span></label>
                                    @php($gender = old('gender', $user->gender))
                                    <x-member.profile-select-field name="gender" :required="true" :disabled="$profileLocked" data-validate="required" data-label="Gender">
                                        <option value="">Select</option>
                                        <option value="Male" @selected($gender === 'Male')>Male</option>
                                        <option value="Female" @selected($gender === 'Female')>Female</option>
                                        <option value="Other" @selected($gender === 'Other')>Other</option>
                                    </x-member.profile-select-field>
                                    <p class="ml-help" data-error-for="gender"></p>
                                </div>
                            </div>
                        </section>

                        <section class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Professional &amp; address
                            </h3>
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <div>
                                    <label class="ml-label">Type of profile <span class="text-red-500">*</span></label>
                                    @php($profileType = old('profile_type', $user->profile_type))
                                    <x-member.profile-select-field name="profile_type" :required="true" :disabled="$profileLocked" data-profile-type-select data-validate="required" data-label="Type of profile">
                                        <option value="">Select</option>
                                        <option value="registered_nurse" @selected($profileType === 'registered_nurse')>Registered Nurse</option>
                                        <option value="student_nurse" @selected($profileType === 'student_nurse')>Student Nurse</option>
                                        <option value="volunteer" @selected($profileType === 'volunteer')>Volunteer</option>
                                    </x-member.profile-select-field>
                                    <p class="ml-help" data-error-for="profile_type"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Referred by</label>
                                    @php($refBy = old('referred_by_user_id', $user->referred_by_user_id))
                                    <x-member.profile-select-field name="referred_by_user_id" :disabled="$profileLocked">
                                        <option value="">Select</option>
                                        @foreach(($referrers ?? []) as $r)
                                            <option value="{{ $r->id }}" @selected((string)$refBy === (string)$r->id)>{{ $r->name }}</option>
                                        @endforeach
                                    </x-member.profile-select-field>
                                </div>
                                <div>
                                    <label class="ml-label">Qualification <span class="text-red-500">*</span></label>
                                    @php($qualification = old('qualification', $user->qualification))
                                    <x-member.profile-select-field name="qualification" :required="true" :disabled="$profileLocked" data-validate="required" data-label="Qualification">
                                        <option value="">Select</option>
                                        <option value="Diploma" @selected($qualification === 'Diploma')>Diploma</option>
                                        <option value="B.Sc" @selected($qualification === 'B.Sc')>B.Sc</option>
                                        <option value="B.Tech" @selected($qualification === 'B.Tech')>B.Tech</option>
                                        <option value="M.Sc" @selected($qualification === 'M.Sc')>M.Sc</option>
                                        <option value="M.Tech" @selected($qualification === 'M.Tech')>M.Tech</option>
                                        <option value="PhD" @selected($qualification === 'PhD')>PhD</option>
                                        <option value="Other" @selected($qualification === 'Other')>Other</option>
                                    </x-member.profile-select-field>
                                    <p class="ml-help" data-error-for="qualification"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Blood group <span class="text-red-500">*</span></label>
                                    @php($blood = old('blood_group', $user->blood_group))
                                    <x-member.profile-select-field name="blood_group" :required="true" :disabled="$profileLocked" data-validate="required" data-label="Blood group">
                                        <option value="">Select</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}" @selected($blood === $bg)>{{ $bg }}</option>
                                        @endforeach
                                    </x-member.profile-select-field>
                                    <p class="ml-help" data-error-for="blood_group"></p>
                                </div>
                                <div data-profile-show="registered_nurse">
                                    <label class="ml-label">RNRM No <span class="text-red-500" data-profile-required="registered_nurse">*</span></label>
                                    <input name="rnrm_number_with_date" value="{{ old('rnrm_number_with_date', $user->rnrm_number_with_date) }}" class="ml-inp" data-profile-show="registered_nurse" data-validate="required_if:registered_nurse" data-label="RNRM No" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="rnrm_number_with_date"></p>
                                </div>
                                <div data-profile-show="student_nurse">
                                    <label class="ml-label">Student ID <span class="text-red-500" data-profile-required="student_nurse">*</span></label>
                                    <input name="student_id" value="{{ old('student_id', $user->student_id) }}" class="ml-inp" data-profile-show="student_nurse" data-validate="required_if:student_nurse" data-label="Student ID" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="student_id"></p>
                                </div>
                                <div>
                                    <label class="ml-label">College name <span class="text-red-500">*</span></label>
                                    <input name="college_name" value="{{ old('college_name', $user->college_name) }}" required class="ml-inp" data-validate="required" data-label="College name" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="college_name"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Door no <span class="text-red-500">*</span></label>
                                    <input name="door_no" value="{{ old('door_no', $user->door_no) }}" required class="ml-inp" data-validate="required" data-label="Door no" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="door_no"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Locality / area <span class="text-red-500">*</span></label>
                                    <input name="locality_area" value="{{ old('locality_area', $user->locality_area) }}" required class="ml-inp" data-validate="required|min:3" data-label="Locality / area" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="locality_area"></p>
                                </div>
                                <div>
                                    <label class="ml-label">State <span class="text-red-500">*</span></label>
                                    @php($selectedState = old('state', $user->state))
                                    <x-member.profile-select-field name="state" :required="true" :disabled="$profileLocked" data-validate="required" data-label="State">
                                        <option value="">Select</option>
                                        @foreach($stateOptions as $state)
                                            <option value="{{ $state }}" @selected($selectedState === $state)>{{ $state }}</option>
                                        @endforeach
                                    </x-member.profile-select-field>
                                    <p class="ml-help" data-error-for="state"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Country <span class="text-red-500">*</span></label>
                                    <input type="hidden" name="country" value="India" />
                                    <input value="India" disabled class="ml-inp bg-slate-100 text-slate-500" />
                                </div>
                                <div>
                                    <label class="ml-label">Pin code <span class="text-red-500">*</span></label>
                                    <input name="pin_code" value="{{ old('pin_code', $user->pin_code) }}" required maxlength="6" inputmode="numeric" pattern="[0-9]*" class="ml-inp" data-validate="required|digits:6" data-label="Pin code" @disabled($profileLocked) />
                                    <p class="ml-help" data-error-for="pin_code"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Council state <span class="text-red-500">*</span></label>
                                    @php($councilState = old('council_state', $user->council_state))
                                    <x-member.profile-select-field name="council_state" :required="true" :disabled="$profileLocked" data-validate="required" data-label="Council state">
                                        <option value="">Select</option>
                                        @if($councilState && !in_array($councilState, $stateOptions, true))
                                            <option value="{{ $councilState }}" selected>{{ $councilState }}</option>
                                        @endif
                                        @foreach($stateOptions as $state)
                                            <option value="{{ $state }}" @selected($councilState === $state)>{{ $state }}</option>
                                        @endforeach
                                    </x-member.profile-select-field>
                                    <p class="ml-help" data-error-for="council_state"></p>
                                </div>
                                <div class="md:col-span-2 xl:col-span-3">
                                    <label class="ml-label">Currently working</label>
                                    <textarea name="currently_working" rows="3" class="ml-inp" placeholder="Role, hospital/clinic, and experience (optional)" @disabled($profileLocked)>{{ old('currently_working', $user->currently_working) }}</textarea>
                                </div>
                            </div>
                        </section>

                        <section class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Documents
                            </h3>
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <div class="ml-upload-zone p-4" data-doc-zone="educational_certificate" data-has-file="{{ ($user->educational_certificate_path || !empty($pendingProfileDocs['educational_certificate'])) ? 'true' : 'false' }}">
                                    <label class="ml-label">
                                        Educational certificate
                                        <span class="text-red-500" data-profile-required="student_nurse,volunteer">*</span>
                                    </label>
                                    @if($user->educational_certificate_path)
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->educational_certificate_path) }}">View current</a>
                                    @endif
                                    @if(!empty($pendingProfileDocs['educational_certificate']))
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="{{ asset('storage/' . $pendingProfileDocs['educational_certificate']) }}">Preview</a>
                                        </p>
                                    @endif
                                    <input type="file" name="educational_certificate" accept="{{ $documentAccept }}" class="w-full text-sm" @disabled($profileLocked) />
                                    <p class="mt-2 text-[11px] font-medium text-[#351c42]/55">PDF, Office documents, or images. Maximum size: {{ $profileUploadMaxLabel }}.</p>
                                    <p class="ml-help mt-1" data-error-for="educational_certificate"></p>
                                    @error('educational_certificate')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ml-upload-zone p-4" data-profile-show="registered_nurse" data-doc-zone="rnrm_certificate" data-has-file="{{ ($user->rnrm_certificate_path || !empty($pendingProfileDocs['rnrm_certificate'])) ? 'true' : 'false' }}">
                                    <label class="ml-label">RNRM certificate copy <span class="text-red-500" data-profile-required="registered_nurse">*</span></label>
                                    @if($user->rnrm_certificate_path)
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->rnrm_certificate_path) }}">View current</a>
                                    @endif
                                    @if(!empty($pendingProfileDocs['rnrm_certificate']))
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="{{ asset('storage/' . $pendingProfileDocs['rnrm_certificate']) }}">Preview</a>
                                        </p>
                                    @endif
                                    <input type="file" name="rnrm_certificate" accept="{{ $documentAccept }}" class="w-full text-sm" @disabled($profileLocked) />
                                    <p class="mt-2 text-[11px] font-medium text-[#351c42]/55">PDF, Office documents, or images. Maximum size: {{ $profileUploadMaxLabel }}.</p>
                                    <p class="ml-help mt-1" data-error-for="rnrm_certificate"></p>
                                    @error('rnrm_certificate')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ml-upload-zone p-4" data-profile-show="student_nurse" data-doc-zone="student_id_card" data-has-file="{{ ($user->student_id_card_path || !empty($pendingProfileDocs['student_id_card'])) ? 'true' : 'false' }}">
                                    <label class="ml-label">Student ID (card) <span class="text-red-500" data-profile-required="student_nurse">*</span></label>
                                    @if($user->student_id_card_path)
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->student_id_card_path) }}">View current</a>
                                    @endif
                                    @if(!empty($pendingProfileDocs['student_id_card']))
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="{{ asset('storage/' . $pendingProfileDocs['student_id_card']) }}">Preview</a>
                                        </p>
                                    @endif
                                    <input type="file" name="student_id_card" accept="{{ $documentAccept }}" class="w-full text-sm" @disabled($profileLocked) />
                                    <p class="mt-2 text-[11px] font-medium text-[#351c42]/55">PDF, Office documents, or images. Maximum size: {{ $profileUploadMaxLabel }}.</p>
                                    <p class="ml-help mt-1" data-error-for="student_id_card"></p>
                                    @error('student_id_card')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ml-upload-zone p-4" data-doc-zone="aadhar_card" data-has-file="{{ ($user->aadhar_card_path || !empty($pendingProfileDocs['aadhar_card'])) ? 'true' : 'false' }}">
                                    <label class="ml-label">Aadhar card <span class="text-red-500">*</span></label>
                                    @if($user->aadhar_card_path)
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->aadhar_card_path) }}">View current</a>
                                    @endif
                                    @if(!empty($pendingProfileDocs['aadhar_card']))
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="{{ asset('storage/' . $pendingProfileDocs['aadhar_card']) }}">Preview</a>
                                        </p>
                                    @endif
                                    <input type="file" name="aadhar_card" accept="{{ $documentAccept }}" class="w-full text-sm" @disabled($profileLocked) />
                                    <p class="mt-2 text-[11px] font-medium text-[#351c42]/55">PDF, Office documents, or images. Maximum size: {{ $profileUploadMaxLabel }}.</p>
                                    <p class="ml-help mt-1" data-error-for="aadhar_card"></p>
                                    @error('aadhar_card')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ml-upload-zone p-4" data-doc-zone="passport_photo" data-has-file="{{ ($user->passport_photo_path || !empty($pendingProfileDocs['passport_photo'])) ? 'true' : 'false' }}">
                                    <label class="ml-label">Passport size photo <span class="text-red-500">*</span></label>
                                    @if($user->passport_photo_path)
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->passport_photo_path) }}">View current</a>
                                    @endif
                                    @if(!empty($pendingProfileDocs['passport_photo']))
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="{{ asset('storage/' . $pendingProfileDocs['passport_photo']) }}">Preview</a>
                                        </p>
                                    @endif
                                    <input type="file" name="passport_photo" accept="image/*,.heic,.heif" class="w-full text-sm" @disabled($profileLocked) />
                                    <p class="mt-2 text-[11px] font-medium text-[#351c42]/55">Image files only. Maximum size: {{ $profileUploadMaxLabel }}.</p>
                                    <p class="ml-help mt-1" data-error-for="passport_photo"></p>
                                    @error('passport_photo')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                        </section>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-[#351c42]/10 pt-6 sm:flex-row sm:justify-end sm:gap-4">
                        @if($profileLocked)
                            <button type="button" disabled class="ml-btn-primary w-full sm:w-auto cursor-not-allowed opacity-55" aria-disabled="true">Save &amp; continue</button>
                        @else
                            <button type="submit" class="ml-btn-primary w-full sm:w-auto">Save &amp; continue</button>
                        @endif
                    </div>
                </form>
            </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
        (() => {
            const form = document.getElementById("member-profile-form");
            if (!form) return;
            const isProfileLocked = form.hasAttribute("data-profile-locked");

            // Searchable dropdowns for all profile selects.
            const resolveChoicesRoot = (selectEl, instance) => {
                const outer = instance?.containerOuter;
                if (outer?.element instanceof Element) {
                    return outer.element;
                }
                if (outer instanceof Element) {
                    return outer;
                }
                return selectEl.closest(".choices");
            };

            const wrapSelectSearchInput = (choicesRoot) => {
                const rootEl = choicesRoot instanceof Element
                    ? choicesRoot
                    : choicesRoot?.element instanceof Element
                        ? choicesRoot.element
                        : null;
                if (!rootEl) return;
                const dropdown = rootEl.querySelector(".choices__list--dropdown");
                if (!dropdown) return;
                const searchInput = dropdown.querySelector(":scope > .choices__input, :scope > .choices__input--cloned");
                if (!searchInput || searchInput.closest(".mp-select-search")) return;
                const wrap = document.createElement("div");
                wrap.className = "mp-select-search";
                dropdown.insertBefore(wrap, searchInput);
                wrap.appendChild(searchInput);
            };

            const getChoicesRoot = (selectEl) => selectEl.closest(".choices");

            const syncChoicesValidation = (field, hasError) => {
                const choicesRoot = getChoicesRoot(field);
                if (choicesRoot) {
                    choicesRoot.classList.toggle("mp-is-invalid", hasError);
                }
            };

            form.querySelectorAll("select.mp-searchable-select").forEach((selectEl) => {
                if (selectEl.dataset.searchableInit === "1") return;
                if ((selectEl.options?.length || 0) <= 1) return;
                selectEl.dataset.searchableInit = "1";
                const instance = new Choices(selectEl, {
                    searchEnabled: true,
                    searchFloor: 0,
                    searchPlaceholderValue: "Search options…",
                    searchResultLimit: 50,
                    shouldSort: false,
                    itemSelectText: "",
                    noResultsText: "No matching options",
                    noChoicesText: "No options",
                    position: "auto",
                    allowHTML: false,
                });
                const choicesRoot = resolveChoicesRoot(selectEl, instance);
                wrapSelectSearchInput(choicesRoot);
                selectEl.addEventListener("showDropdown", () => wrapSelectSearchInput(resolveChoicesRoot(selectEl, instance)));
            });

            const typeSelect = form.querySelector("[data-profile-type-select]");
            const showEls = Array.from(form.querySelectorAll("[data-profile-show]"));
            const reqEls = Array.from(form.querySelectorAll("[data-profile-required]"));

            function setVisibility(type) {
                showEls.forEach((el) => {
                    const allowed = (el.getAttribute("data-profile-show") || "").split(",").map(s => s.trim()).filter(Boolean);
                    const shouldShow = allowed.length === 0 || allowed.includes(type);
                    el.classList.toggle("hidden", !shouldShow);
                });

                // Mark only visible fields as required (client-side UX)
                reqEls.forEach((mark) => {
                    const allowed = (mark.getAttribute("data-profile-required") || "").split(",").map(s => s.trim()).filter(Boolean);
                    const active = allowed.includes(type);
                    mark.classList.toggle("hidden", !active);
                });
            }

            setVisibility(typeSelect?.value || "");
            typeSelect?.addEventListener("change", () => {
                setVisibility(typeSelect.value);
                if (!isProfileLocked) {
                    checkDocumentRules();
                }
            });

            // Keep type-based visibility active even in read-only mode.
            if (isProfileLocked) return;

            const fields = Array.from(form.querySelectorAll("[data-validate]"));
            const getErrorEl = (name) => form.querySelector(`[data-error-for="${name}"]`);

            const checkRules = (field) => {
                const rules = (field.dataset.validate || "").split("|");
                const label = field.dataset.label || "This field";
                const value = (field.value || "").trim();
                const currentProfileType = typeSelect?.value || "";

                for (const rule of rules) {
                    if (!rule) continue;

                    if (rule === "required" && !value) {
                        return `${label} is required.`;
                    }

                    if (rule.startsWith("required_if:")) {
                        const targetType = rule.split(":")[1];
                        if (currentProfileType === targetType && !value) {
                            return `${label} is required.`;
                        }
                    }

                    if (rule === "past_date" && value) {
                        const selectedDate = new Date(value);
                        const today = new Date();
                        today.setHours(23, 59, 59, 999);
                        if (selectedDate > today) {
                            return `${label} cannot be in the future.`;
                        }
                    }

                    if (rule.startsWith("min:")) {
                        const min = Number(rule.split(":")[1] || 0);
                        if (value && value.length < min) {
                            return `${label} must be at least ${min} characters.`;
                        }
                    }

                    if (rule.startsWith("digits:")) {
                        const count = Number(rule.split(":")[1] || 0);
                        if (value && !new RegExp(`^\\d{${count}}$`).test(value)) {
                            return `${label} must be ${count} digits.`;
                        }
                    }
                }
                return "";
            };

            const paintValidation = (field, message) => {
                const errorEl = getErrorEl(field.name);
                const hasError = Boolean(message);
                field.classList.toggle("is-invalid", hasError);
                field.setAttribute("aria-invalid", hasError ? "true" : "false");
                syncChoicesValidation(field, hasError);
                if (errorEl) errorEl.textContent = message;
            };

            fields.forEach((field) => {
                ["input", "change", "blur"].forEach((eventName) => {
                    field.addEventListener(eventName, () => {
                        if (field.name === "pin_code") field.value = field.value.replace(/\D/g, "").slice(0, 6);
                        if (field.name === "mobile") field.value = field.value.replace(/\D/g, "").slice(0, 10);
                        paintValidation(field, checkRules(field));
                    });
                });
            });

            const isDocUploaded = (fieldName) => {
                const zone = form.querySelector(`[data-doc-zone="${fieldName}"]`);
                const hasExisting = zone?.getAttribute("data-has-file") === "true";
                const input = form.querySelector(`input[name="${fieldName}"]`);
                const hasSelected = Boolean(input?.files && input.files.length > 0);
                return hasExisting || hasSelected;
            };

            const checkDocumentRules = () => {
                const currentProfileType = typeSelect?.value || "";
                const docErrors = [];

                const setDocError = (fieldName, message) => {
                    const zone = form.querySelector(`[data-doc-zone="${fieldName}"]`);
                    const errorEl = getErrorEl(fieldName);
                    const hasError = Boolean(message);
                    if (zone) zone.classList.toggle("is-invalid", hasError);
                    if (errorEl) errorEl.textContent = message;
                    if (message) docErrors.push({ fieldName, message });
                };

                ["educational_certificate", "rnrm_certificate", "student_id_card", "aadhar_card", "passport_photo"].forEach(f => setDocError(f, ""));

                if (!isDocUploaded("aadhar_card")) {
                    setDocError("aadhar_card", "Aadhar card document is required.");
                }

                if (!isDocUploaded("passport_photo")) {
                    setDocError("passport_photo", "Passport size photo is required.");
                }

                if (currentProfileType === "student_nurse") {
                    if (!isDocUploaded("educational_certificate")) {
                        setDocError("educational_certificate", "Educational certificate is required for Student Nurse.");
                    }
                    if (!isDocUploaded("student_id_card")) {
                        setDocError("student_id_card", "Student ID (card) is required for Student Nurse.");
                    }
                } else if (currentProfileType === "volunteer") {
                    if (!isDocUploaded("educational_certificate")) {
                        setDocError("educational_certificate", "Educational certificate is required for Volunteer.");
                    }
                } else if (currentProfileType === "registered_nurse") {
                    const hasRnrm = isDocUploaded("rnrm_certificate");
                    const hasEdu = isDocUploaded("educational_certificate");
                    if (!hasRnrm && !hasEdu) {
                        setDocError("rnrm_certificate", "RNRM certificate OR Educational certificate is required.");
                        setDocError("educational_certificate", "RNRM certificate OR Educational certificate is required.");
                    }
                }

                return docErrors;
            };

            const profileFileInputs = Array.from(form.querySelectorAll('input[type="file"]'));
            profileFileInputs.forEach((input) => {
                input.addEventListener("change", () => {
                    const maxBytes = Number(form.dataset.uploadMaxBytes || 0);
                    const maxLabel = form.dataset.uploadMaxLabel || "5 MB";
                    const file = input.files?.[0];
                    if (file && maxBytes && file.size > maxBytes) {
                        const label = input.closest(".ml-upload-zone")?.querySelector(".ml-label")?.textContent?.replace("*", "").trim() || "This file";
                        showProfileUploadClientError(`${label} is too large. Each document must not be larger than ${maxLabel}.`);
                        input.value = "";
                    }
                    checkDocumentRules();
                });
            });

            form.addEventListener("submit", (e) => {
                const errorList = [];
                let firstInvalidEl = null;

                fields.forEach((field) => {
                    if (field.name === "pin_code") field.value = field.value.replace(/\D/g, "").slice(0, 6);
                    if (field.name === "mobile") field.value = field.value.replace(/\D/g, "").slice(0, 10);

                    const parentSection = field.closest("[data-profile-show]");
                    if (parentSection && parentSection.classList.contains("hidden")) {
                        paintValidation(field, "");
                        return;
                    }

                    const message = checkRules(field);
                    paintValidation(field, message);
                    if (message) {
                        errorList.push(message);
                        if (!firstInvalidEl) {
                            firstInvalidEl = getChoicesRoot(field) || field;
                        }
                    }
                });

                const docErrors = checkDocumentRules();
                docErrors.forEach(err => {
                    errorList.push(err.message);
                    if (!firstInvalidEl) {
                        firstInvalidEl = form.querySelector(`[data-doc-zone="${err.fieldName}"]`);
                    }
                });

                const maxBytes = Number(form.dataset.uploadMaxBytes || 0);
                const maxLabel = form.dataset.uploadMaxLabel || "5 MB";
                for (const input of profileFileInputs) {
                    const file = input.files?.[0];
                    if (!file || !maxBytes) continue;
                    if (file.size > maxBytes) {
                        e.preventDefault();
                        showProfileUploadClientError(
                            `${input.closest(".ml-upload-zone")?.querySelector(".ml-label")?.textContent?.replace("*", "").trim() || "This file"} is too large. Each document must not be larger than ${maxLabel}.`
                        );
                        input.value = "";
                        input.focus();
                        return;
                    }
                }

                const summaryBox = document.getElementById("form-validation-summary");
                const summaryList = document.getElementById("form-validation-summary-list");

                if (errorList.length > 0) {
                    e.preventDefault();

                    if (summaryBox && summaryList) {
                        summaryList.innerHTML = errorList.map(err => `<li>${err}</li>`).join("");
                        summaryBox.classList.remove("hidden");
                    }

                    if (firstInvalidEl) {
                        firstInvalidEl.scrollIntoView({ behavior: "smooth", block: "center" });
                        if (typeof firstInvalidEl.focus === "function") {
                            firstInvalidEl.focus();
                        }
                    }
                } else {
                    if (summaryBox) summaryBox.classList.add("hidden");
                }
            });

            const bindModal = (root, closeSelector) => {
                if (!root) return;
                const close = () => {
                    root.classList.remove("is-open");
                    root.setAttribute("aria-hidden", "true");
                };
                root.querySelector(closeSelector)?.addEventListener("click", close);
                root.addEventListener("click", (event) => {
                    if (event.target === root) close();
                });
                document.addEventListener("keydown", (event) => {
                    if (event.key === "Escape" && root.classList.contains("is-open")) close();
                });
                return close;
            };

            const clientModal = document.getElementById("profile-upload-client-error-modal");
            const clientMessage = document.getElementById("profile-upload-client-error-message");
            const showProfileUploadClientError = (message) => {
                if (!clientModal || !clientMessage) return;
                clientMessage.textContent = message;
                clientModal.classList.add("is-open");
                clientModal.setAttribute("aria-hidden", "false");
            };
            window.showProfileUploadClientError = showProfileUploadClientError;

            bindModal(document.getElementById("profile-upload-error-modal"), "[data-close-profile-upload-error-modal]");
            bindModal(clientModal, "[data-close-profile-upload-client-error-modal]");

            if (new URLSearchParams(window.location.search).has("upload_error")) {
                const cleanUrl = new URL(window.location.href);
                cleanUrl.searchParams.delete("upload_error");
                window.history.replaceState({}, "", cleanUrl.pathname + cleanUrl.search + cleanUrl.hash);
            }
        })();
    </script>
@endpush

