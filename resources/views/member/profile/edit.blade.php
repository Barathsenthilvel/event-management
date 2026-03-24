@php
    $member = $user;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Member profile — GNAT Donation</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
        .md-page-bg {
            background-color: #f6f7fb;
            background-image:
                radial-gradient(ellipse 70% 45% at 50% -15%, rgba(37, 99, 235, 0.08), transparent),
                radial-gradient(ellipse 50% 35% at 100% 20%, rgba(150, 89, 149, 0.1), transparent);
            min-height: 100vh;
        }
        .md-glass-header {
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(53, 28, 66, 0.07);
        }
        .md-nav-link {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #5c5a6b;
        }
        .md-nav-link:hover { color: #351c42; }
        .md-sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            border-radius: 0.875rem;
            padding: 0.65rem 0.9rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(53, 28, 66, 0.72);
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }
        .md-sidebar-link:hover { background: rgba(53, 28, 66, 0.06); color: #351c42; }
        .md-sidebar-link.is-active {
            background: linear-gradient(135deg, rgba(53, 28, 66, 0.12), rgba(150, 89, 149, 0.1));
            color: #351c42;
            box-shadow: inset 0 0 0 1px rgba(53, 28, 66, 0.08);
        }
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
        }
        .ml-inp:focus {
            border-color: rgba(150, 89, 149, 0.55);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
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
</head>
<body class="md-page-bg text-[#351c42] antialiased">
    <header class="sticky top-0 z-40 md-glass-header">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3.5 lg:gap-6">
            <a href="{{ route('home') }}" class="flex min-w-0 max-w-[200px] shrink-0 sm:max-w-[220px]" aria-label="Home">
                <img src="{{ asset('logo.png') }}" alt="GNAT Donation" class="h-8 w-auto max-h-11 object-contain sm:h-11" width="200" height="48" />
            </a>
            <nav class="hidden flex-1 justify-center gap-6 lg:flex xl:gap-9" aria-label="Primary">
                <a href="{{ route('home') }}#home" class="md-nav-link">Home</a>
                <a href="{{ route('home') }}#about2" class="md-nav-link">About us</a>
                <a href="{{ route('home') }}#events" class="md-nav-link">Events</a>
                <a href="{{ route('home') }}#gallery" class="md-nav-link">Gallery</a>
                <a href="{{ route('home') }}#contact" class="md-nav-link">Contact us</a>
            </nav>
            <a href="{{ route('member.dashboard') }}" class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-[#351c42]/10 sm:h-11 sm:w-11" aria-label="Account">
                <svg class="h-5 w-5 text-[#351c42]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </a>
        </div>
    </header>

    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:flex-row lg:gap-8 lg:py-10">
        <aside class="lg:w-60 lg:rounded-2xl lg:border lg:border-[#351c42]/10 lg:bg-white/80 lg:p-4 lg:shadow-lg lg:shadow-[#351c42]/5">
            <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-widest text-[#965995]">Menu</p>
            <nav class="flex flex-col gap-1" aria-label="Member">
                <a href="{{ route('member.dashboard') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Dashboard</a>
                <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link is-active"><span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Profile</a>
                <a href="{{ route('member.profile.edit') }}" class="md-sidebar-link"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Account settings</a>
            </nav>
            <form method="POST" action="{{ route('member.logout') }}" class="mt-8 border-t border-[#351c42]/10 pt-4">
                @csrf
                <button type="submit" class="md-sidebar-link w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Log out
                </button>
            </form>
        </aside>

        <main class="min-w-0 flex-1">
            <div class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-6 shadow-md sm:p-8">
                <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Account</p>
                        <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">My profile</h1>
                        <p class="mt-2 text-sm text-[#351c42]/65">Complete your details to activate your membership.</p>
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

                <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <div class="grid gap-8 lg:grid-cols-3">
                        <div class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Personal
                            </h3>
                            <div>
                                <label class="ml-label">First name <span class="text-red-500">*</span></label>
                                <input name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Last name <span class="text-red-500">*</span></label>
                                <input name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Email ID</label>
                                <input value="{{ $user->email }}" disabled class="ml-inp bg-slate-100 text-slate-500" />
                            </div>
                            <div>
                                <label class="ml-label">Mobile <span class="text-red-500">*</span></label>
                                <input name="mobile" value="{{ old('mobile', $user->mobile) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">DOB <span class="text-red-500">*</span></label>
                                <input type="date" name="dob" value="{{ old('dob', optional($user->dob)->format('Y-m-d')) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Gender <span class="text-red-500">*</span></label>
                                @php($gender = old('gender', $user->gender))
                                <select name="gender" required class="ml-inp">
                                    <option value="">Select</option>
                                    <option value="Male" @selected($gender === 'Male')>Male</option>
                                    <option value="Female" @selected($gender === 'Female')>Female</option>
                                    <option value="Other" @selected($gender === 'Other')>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Professional &amp; address
                            </h3>
                            <div>
                                <label class="ml-label">Qualification <span class="text-red-500">*</span></label>
                                @php($qualification = old('qualification', $user->qualification))
                                <select name="qualification" required class="ml-inp">
                                    <option value="">Select</option>
                                    <option value="Diploma" @selected($qualification === 'Diploma')>Diploma</option>
                                    <option value="B.Sc" @selected($qualification === 'B.Sc')>B.Sc</option>
                                    <option value="B.Tech" @selected($qualification === 'B.Tech')>B.Tech</option>
                                    <option value="M.Sc" @selected($qualification === 'M.Sc')>M.Sc</option>
                                    <option value="M.Tech" @selected($qualification === 'M.Tech')>M.Tech</option>
                                    <option value="PhD" @selected($qualification === 'PhD')>PhD</option>
                                    <option value="Other" @selected($qualification === 'Other')>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="ml-label">Blood group <span class="text-red-500">*</span></label>
                                @php($blood = old('blood_group', $user->blood_group))
                                <select name="blood_group" required class="ml-inp">
                                    <option value="">Select</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" @selected($blood === $bg)>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="ml-label">RNRM number with date <span class="text-red-500">*</span></label>
                                <input name="rnrm_number_with_date" value="{{ old('rnrm_number_with_date', $user->rnrm_number_with_date) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">College name <span class="text-red-500">*</span></label>
                                <input name="college_name" value="{{ old('college_name', $user->college_name) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Door no <span class="text-red-500">*</span></label>
                                <input name="door_no" value="{{ old('door_no', $user->door_no) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Locality / area <span class="text-red-500">*</span></label>
                                <input name="locality_area" value="{{ old('locality_area', $user->locality_area) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">State <span class="text-red-500">*</span></label>
                                <input name="state" value="{{ old('state', $user->state) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Pin code <span class="text-red-500">*</span></label>
                                <input name="pin_code" value="{{ old('pin_code', $user->pin_code) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Council state <span class="text-red-500">*</span></label>
                                <input name="council_state" value="{{ old('council_state', $user->council_state) }}" required class="ml-inp" />
                            </div>
                            <div>
                                <label class="ml-label">Currently working</label>
                                <input name="currently_working" value="{{ old('currently_working', $user->currently_working) }}" class="ml-inp" />
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Documents
                            </h3>
                            <div class="ml-upload-zone p-4">
                                <label class="ml-label">Educational certificate <span class="text-red-500">*</span></label>
                                @if($user->educational_certificate_path)
                                    <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->educational_certificate_path) }}">View current</a>
                                @endif
                                <input type="file" name="educational_certificate" class="w-full text-sm" />
                            </div>
                            <div class="ml-upload-zone p-4">
                                <label class="ml-label">Aadhar card <span class="text-red-500">*</span></label>
                                @if($user->aadhar_card_path)
                                    <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->aadhar_card_path) }}">View current</a>
                                @endif
                                <input type="file" name="aadhar_card" class="w-full text-sm" />
                            </div>
                            <div class="ml-upload-zone p-4">
                                <label class="ml-label">Passport size photo <span class="text-red-500">*</span></label>
                                @if($user->passport_photo_path)
                                    <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="{{ asset('storage/' . $user->passport_photo_path) }}">View current</a>
                                @endif
                                <input type="file" name="passport_photo" accept="image/*" class="w-full text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-[#351c42]/10 pt-6 sm:flex-row sm:justify-end sm:gap-4">
                        <a href="{{ route('member.dashboard') }}" class="ml-btn-secondary w-full sm:w-auto">Cancel</a>
                        <button type="submit" class="ml-btn-primary w-full sm:w-auto">Save &amp; continue</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

