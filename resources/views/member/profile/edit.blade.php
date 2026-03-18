@extends('member.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[28px] border border-white bg-gradient-to-br from-white via-white to-indigo-50/40 shadow-sm p-6 md:p-7">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">My Profile</h1>
                <p class="mt-1 text-sm text-slate-500">Complete your details to activate your membership.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-indigo-600 text-white text-xs font-extrabold shadow-lg shadow-indigo-200">
                    Required fields marked *
                </span>
            </div>
        </div>
    </div>

    @if(isset($activeSubscription) && $activeSubscription)
    <div class="w-full bg-white p-6 md:p-8 rounded-[28px] border border-slate-100 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Active Subscription</h2>
                <p class="text-xs text-slate-500">Your current membership plan details.</p>
            </div>
            <a href="{{ route('member.subscription.invoice', $activeSubscription->id) }}" target="_blank" 
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-extrabold text-white shadow-sm hover:bg-indigo-600 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Download Invoice
            </a>
        </div>
        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-[20px] border border-slate-100 bg-slate-50/50">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Plan</p>
                <p class="mt-1 font-bold text-slate-900">{{ $activeSubscription->subscriptionPlan->subscription_type }}</p>
            </div>
            <div class="p-4 rounded-[20px] border border-slate-100 bg-slate-50/50">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Cycle</p>
                <p class="mt-1 font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $activeSubscription->subscriptionPlan->payment_type)) }}</p>
            </div>
            <div class="p-4 rounded-[20px] border border-slate-100 bg-slate-50/50">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Amount Paid</p>
                <p class="mt-1 font-extrabold text-emerald-600">₹ {{ number_format($activeSubscription->amount, 0) }}</p>
            </div>
            <div class="p-4 rounded-[20px] border border-slate-100 bg-slate-50/50">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status</p>
                <p class="mt-1 font-bold text-indigo-600 uppercase">{{ $activeSubscription->status }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="w-full">
        <div class="bg-white p-6 md:p-8 rounded-[28px] border border-slate-100 shadow-sm">
            <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">
                    <div class="xl:col-span-8 space-y-6">
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/40 p-5 md:p-6 h-full">
                            <div class="flex items-center justify-between gap-3 mb-5">
                                <div>
                                    <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Personal & Membership</h2>
                                    <p class="text-xs text-slate-500">Make sure your details match your documents.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">First Name <span class="text-rose-500">*</span></label>
                                    <input name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                                    @error('first_name')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Last Name <span class="text-rose-500">*</span></label>
                                    <input name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                                    @error('last_name')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Email ID</label>
                                    <input value="{{ $user->email }}" disabled
                                        class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-5 py-4 text-slate-600 placeholder-slate-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Mobile <span class="text-rose-500">*</span></label>
                                    <input name="mobile" value="{{ old('mobile', $user->mobile) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                                    @error('mobile')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">DOB <span class="text-rose-500">*</span></label>
                                    <input type="date" name="dob" value="{{ old('dob', optional($user->dob)->format('Y-m-d')) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                                    @error('dob')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Gender <span class="text-rose-500">*</span></label>
                                    <select name="gender" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                                        <option value="">Select</option>
                                        @php($gender = old('gender', $user->gender))
                                        <option value="Male" @selected($gender === 'Male')>Male</option>
                                        <option value="Female" @selected($gender === 'Female')>Female</option>
                                        <option value="Other" @selected($gender === 'Other')>Other</option>
                                    </select>
                                    @error('gender')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Select Qualification <span class="text-rose-500">*</span></label>
                                    <select name="qualification" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                                        <option value="">Select</option>
                                        @php($qualification = old('qualification', $user->qualification))
                                        <option value="Diploma" @selected($qualification === 'Diploma')>Diploma</option>
                                        <option value="B.Sc" @selected($qualification === 'B.Sc')>B.Sc</option>
                                        <option value="B.Tech" @selected($qualification === 'B.Tech')>B.Tech</option>
                                        <option value="M.Sc" @selected($qualification === 'M.Sc')>M.Sc</option>
                                        <option value="M.Tech" @selected($qualification === 'M.Tech')>M.Tech</option>
                                        <option value="PhD" @selected($qualification === 'PhD')>PhD</option>
                                        <option value="Other" @selected($qualification === 'Other')>Other</option>
                                    </select>
                                    @error('qualification')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Blood Group <span class="text-rose-500">*</span></label>
                                    <select name="blood_group" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all">
                                        <option value="">Select</option>
                                        @php($blood = old('blood_group', $user->blood_group))
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}" @selected($blood === $bg)>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                    @error('blood_group')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">RNRM Number with Date <span class="text-rose-500">*</span></label>
                                    <input name="rnrm_number_with_date" value="{{ old('rnrm_number_with_date', $user->rnrm_number_with_date) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('rnrm_number_with_date')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">College Name <span class="text-rose-500">*</span></label>
                                    <input name="college_name" value="{{ old('college_name', $user->college_name) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('college_name')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div class="md:col-span-2">
                                    <div class="mt-2 mb-1 flex items-center gap-3">
                                        <div class="h-px flex-1 bg-slate-200/80"></div>
                                        <div class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500">Address</div>
                                        <div class="h-px flex-1 bg-slate-200/80"></div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Door No <span class="text-rose-500">*</span></label>
                                    <input name="door_no" value="{{ old('door_no', $user->door_no) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('door_no')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Locality / Area <span class="text-rose-500">*</span></label>
                                    <input name="locality_area" value="{{ old('locality_area', $user->locality_area) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('locality_area')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">State <span class="text-rose-500">*</span></label>
                                    <input name="state" value="{{ old('state', $user->state) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('state')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Pin code <span class="text-rose-500">*</span></label>
                                    <input name="pin_code" value="{{ old('pin_code', $user->pin_code) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('pin_code')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Council State <span class="text-rose-500">*</span></label>
                                    <input name="council_state" value="{{ old('council_state', $user->council_state) }}" required
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('council_state')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-2">Currently Working</label>
                                    <input name="currently_working" value="{{ old('currently_working', $user->currently_working) }}"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/5 transition-all"
                                        placeholder="Enter">
                                    @error('currently_working')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="xl:col-span-4 h-full">
                        <div class="rounded-[24px] border border-slate-100 bg-white p-5 md:p-6 shadow-sm h-full">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Documents</h2>
                                        <p class="mt-1 text-xs text-slate-500">Upload clear files (max 5 MB each).</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h7M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H8l-2 2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-5 space-y-4">
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <label class="block text-xs font-bold text-slate-800">Educational Certificate <span class="text-rose-500">*</span></label>
                                            @if($user->educational_certificate_path)
                                                <a class="text-xs font-extrabold text-indigo-600 hover:text-indigo-700" target="_blank"
                                                    href="{{ asset('storage/' . $user->educational_certificate_path) }}">View</a>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-[11px] text-slate-500 font-semibold">PDF/JPG/PNG</p>
                                        <input type="file" name="educational_certificate"
                                            class="mt-3 block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-extrabold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                                        @error('educational_certificate')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <label class="block text-xs font-bold text-slate-800">Aaadhar Card <span class="text-rose-500">*</span></label>
                                            @if($user->aadhar_card_path)
                                                <a class="text-xs font-extrabold text-indigo-600 hover:text-indigo-700" target="_blank"
                                                    href="{{ asset('storage/' . $user->aadhar_card_path) }}">View</a>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-[11px] text-slate-500 font-semibold">PDF/JPG/PNG</p>
                                        <input type="file" name="aadhar_card"
                                            class="mt-3 block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-extrabold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                                        @error('aadhar_card')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <label class="block text-xs font-bold text-slate-800">Passport Size Photo <span class="text-rose-500">*</span></label>
                                            @if($user->passport_photo_path)
                                                <a class="text-xs font-extrabold text-indigo-600 hover:text-indigo-700" target="_blank"
                                                    href="{{ asset('storage/' . $user->passport_photo_path) }}">View</a>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-[11px] text-slate-500 font-semibold">JPG/PNG</p>
                                        <input type="file" name="passport_photo" accept="image/*"
                                            class="mt-3 block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-extrabold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                                        @error('passport_photo')<p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                    </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-end pt-2">
                    <a href="{{ route('member.dashboard') }}"
                        class="px-8 py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl font-bold text-slate-700 transition-colors text-center">Cancel</a>
                    <button type="submit"
                        class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all">Save &amp; Continue</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

