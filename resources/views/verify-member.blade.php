<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $member ? 'Verified Member: ' . $member->name . ' — GNAT' : 'Member Verification — GNAT' }}</title>
    @include('partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
        .mono { font-family: "JetBrains Mono", monospace; }
        .vm-bg {
            background-color: #0f172a;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(150, 89, 149, 0.25), transparent),
                radial-gradient(ellipse 60% 40% at 100% 100%, rgba(53, 28, 66, 0.4), transparent);
            min-height: 100vh;
        }
        .vm-glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }
        .vm-seal-glow {
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.35);
        }
    </style>
</head>
<body class="vm-bg text-slate-800 antialiased flex flex-col min-h-screen">

    {{-- Public Site Header / Simple Nav --}}
    <header class="border-b border-white/10 bg-slate-900/60 backdrop-blur-md sticky top-0 z-40">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="GNAT Logo" class="h-10 w-auto transition group-hover:scale-105">
                <div>
                    <span class="block text-xs font-black uppercase tracking-[0.2em] text-[#fddc6a]">Official Verification</span>
                    <span class="block text-sm font-bold text-white leading-none">GNAT Association</span>
                </div>
            </a>

            <a href="{{ route('home') }}" class="rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold text-white/90 hover:bg-white/20 transition">
                ← Back to Home
            </a>
        </div>
    </header>

    <main class="flex-1 py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- Member Verification Search Bar --}}
            <div class="mb-6">
                <form action="{{ route('member.verify') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            name="code"
                            value="{{ $searchCode }}"
                            placeholder="Enter Member Code (e.g. GNAT-9715-0006 or 0006)..."
                            class="w-full rounded-2xl border border-white/20 bg-slate-800/80 px-5 py-3 text-sm text-white placeholder-slate-400 focus:border-[#fddc6a] focus:outline-none focus:ring-2 focus:ring-[#fddc6a]/30"
                        >
                    </div>
                    <button type="submit" class="rounded-2xl bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-6 py-3 text-sm font-extrabold text-[#fddc6a] shadow-lg hover:brightness-110 transition shrink-0">
                        Verify Member
                    </button>
                </form>
            </div>

            @if(!$searchCode)
                {{-- Initial Landing State --}}
                <div class="vm-glass-card rounded-3xl p-8 sm:p-12 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#351c42] text-[#fddc6a] shadow-xl">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <h1 class="mt-6 text-2xl font-extrabold text-[#351c42] sm:text-3xl">GNAT Member Verification Portal</h1>
                    <p class="mt-3 mx-auto max-w-lg text-sm text-slate-600 leading-relaxed">
                        Scan the QR code printed on any official GNAT ID Card or enter the Member ID above to verify authentic membership status and view full credential details.
                    </p>
                </div>
            @elseif(!$member)
                {{-- Member Not Found State --}}
                <div class="vm-glass-card rounded-3xl p-8 sm:p-12 text-center border-rose-300">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-rose-100 text-rose-600 shadow-xl ring-4 ring-rose-50">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="mt-6 inline-flex rounded-full bg-rose-100 px-3.5 py-1 text-xs font-bold text-rose-700 uppercase tracking-widest">Invalid Credential</span>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Member Record Not Found</h2>
                    <p class="mt-2 mx-auto max-w-md text-sm text-slate-600">
                        No active GNAT member was found for code "<span class="font-mono font-bold text-slate-900">{{ $searchCode }}</span>". Please check the ID number and try again.
                    </p>
                </div>
            @else
                {{-- MEMBER FOUND: FULL VERIFIED CREDENTIAL DETAILS --}}
                @php
                    $isApproved = (bool) $member->is_approved;
                    $hasActiveSub = (bool) $activeSubscription;
                    $statusLabel = 'UNVERIFIED';
                    $statusBadgeBg = 'bg-rose-100 text-rose-800 border-rose-300';

                    if ($isApproved && $hasActiveSub) {
                        $statusLabel = 'OFFICIALLY VERIFIED ACTIVE MEMBER';
                        $statusBadgeBg = 'bg-emerald-500 text-white border-emerald-400 shadow-lg shadow-emerald-500/30';
                    } elseif ($isApproved && !$hasActiveSub) {
                        $statusLabel = 'APPROVED MEMBER — SUBSCRIPTION EXPIRED / PENDING';
                        $statusBadgeBg = 'bg-amber-500 text-white border-amber-400 shadow-lg shadow-amber-500/30';
                    } elseif (!$isApproved) {
                        $statusLabel = 'MEMBERSHIP APPROVAL PENDING';
                        $statusBadgeBg = 'bg-amber-100 text-amber-900 border-amber-300';
                    }
                @endphp

                <div class="space-y-6">

                    {{-- Main Verified Status Banner --}}
                    <div class="vm-glass-card rounded-3xl p-6 sm:p-8 relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 h-44 w-44 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-start gap-4 sm:gap-5">
                                {{-- Member Photo or Avatar --}}
                                <div class="relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-2xl border-4 border-white bg-slate-100 shadow-xl">
                                    @if($member->passport_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($member->passport_photo_path))
                                        <img src="{{ asset('storage/' . $member->passport_photo_path) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                    @else
                                        @include('partials.user-letter-avatar', ['user' => $member, 'class' => 'h-full w-full text-2xl font-bold border-0'])
                                    @endif
                                    @if($isApproved && $hasActiveSub)
                                        <div class="absolute bottom-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white shadow ring-2 ring-white" title="Verified">
                                            ✓
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-black tracking-wider uppercase border {{ $statusBadgeBg }}">
                                            @if($isApproved && $hasActiveSub)
                                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            @endif
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl leading-tight">{{ $member->name }}</h1>
                                    <p class="text-sm font-semibold text-[#965995]">
                                        {{ $member->designation?->name ?? ($member->profile_type ?? 'GNAT Member') }}
                                    </p>
                                    <p class="mt-1 font-mono text-sm font-extrabold text-[#351c42]">
                                        Member ID: <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ $memberCodeFormatted }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- QR Seal / Timestamp info --}}
                            <div class="flex flex-col items-start md:items-end justify-between border-t md:border-t-0 border-slate-100 pt-4 md:pt-0">
                                <div class="text-left md:text-right text-xs text-slate-500">
                                    <p class="font-bold text-slate-700">Graduate Nurses Association of TN</p>
                                    <p class="mt-0.5">Registered under TN Society Act 27 of 1975</p>
                                    <p class="mt-2 font-mono text-[11px] text-slate-400">Scan Verified: {{ $verifiedAt->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detailed Info Section Grid --}}
                    <div class="grid gap-6 md:grid-cols-2">

                        {{-- Card 1: Membership & Subscription Details --}}
                        <div class="vm-glass-card rounded-3xl p-6">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#351c42] text-[#fddc6a]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <h2 class="text-base font-extrabold text-slate-900">Membership Status</h2>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Account Approval</dt>
                                    <dd class="mt-1 font-bold {{ $isApproved ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $isApproved ? 'Approved by Admin' : 'Pending Approval' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Subscription Plan</dt>
                                    <dd class="mt-1 font-bold text-slate-800">
                                        {{ $activeSubscription ? ($activeSubscription->subscription_type . ' Plan') : 'No Active Plan' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Validity Period</dt>
                                    <dd class="mt-1 font-mono font-bold text-slate-900">
                                        {{ $activeSubscription ? $activeSubscription->formattedValidTillDate() : '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Member Since</dt>
                                    <dd class="mt-1 font-semibold text-slate-800">
                                        {{ $member->created_at?->format('d M Y') ?? '—' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Card 2: Professional & Personal Credentials --}}
                        <div class="vm-glass-card rounded-3xl p-6">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#351c42] text-[#fddc6a]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </div>
                                <h2 class="text-base font-extrabold text-slate-900">Professional Credentials</h2>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                <div class="col-span-2 sm:col-span-1">
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">RNRM Reg Number</dt>
                                    <dd class="mt-1 font-mono font-bold text-slate-900">
                                        {{ $member->rnrm_number_with_date ?: '—' }}
                                    </dd>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Qualification</dt>
                                    <dd class="mt-1 font-semibold text-slate-800">
                                        {{ $member->qualification ?: '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Blood Group</dt>
                                    <dd class="mt-1 font-extrabold text-rose-700">
                                        {{ $member->blood_group ?: '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">State / Region</dt>
                                    <dd class="mt-1 font-semibold text-slate-800">
                                        {{ $member->state ?: ($member->council_state ?: 'Tamil Nadu') }}
                                    </dd>
                                </div>
                                @if($member->currently_working)
                                    <div class="col-span-2">
                                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Currently Working At</dt>
                                        <dd class="mt-1 font-semibold text-slate-800">
                                            {{ $member->currently_working }}
                                        </dd>
                                    </div>
                                @endif
                                @if($member->college_name)
                                    <div class="col-span-2">
                                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Institution / College</dt>
                                        <dd class="mt-1 font-semibold text-slate-800">
                                            {{ $member->college_name }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    {{-- ID Card Display / Image Preview --}}
                    @if($cardUrls && !empty($cardUrls['front']))
                        <div class="vm-glass-card rounded-3xl p-6 text-center" x-data="{ viewSide: 'front' }">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                                <div>
                                    <h2 class="text-base font-extrabold text-slate-900 text-left">Official Digital ID Card</h2>
                                    <p class="text-xs text-slate-500 text-left">Authorized credential issued by GNAT</p>
                                </div>

                                <div class="inline-flex rounded-xl bg-slate-100 p-1 text-xs font-bold">
                                    <button type="button" @click="viewSide = 'front'" :class="viewSide === 'front' ? 'bg-[#351c42] text-[#fddc6a] shadow' : 'text-slate-600 hover:text-slate-900'" class="rounded-lg px-3 py-1.5 transition">Front</button>
                                    <button type="button" @click="viewSide = 'back'" :class="viewSide === 'back' ? 'bg-[#351c42] text-[#fddc6a] shadow' : 'text-slate-600 hover:text-slate-900'" class="rounded-lg px-3 py-1.5 transition">Back</button>
                                </div>
                            </div>

                            <div class="my-6 flex justify-center">
                                <div class="relative w-full max-w-[280px] sm:max-w-[320px] aspect-[1845/3137] overflow-hidden rounded-2xl shadow-2xl ring-1 ring-slate-900/10 bg-slate-900">
                                    <img src="{{ $cardUrls['front'] }}" alt="GNAT ID Front" x-show="viewSide === 'front'" class="h-full w-full object-contain">
                                    <img src="{{ $cardUrls['back'] }}" alt="GNAT ID Back" x-show="viewSide === 'back'" class="h-full w-full object-contain" x-cloak>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endif

        </div>
    </main>

    <footer class="border-t border-white/10 bg-slate-900/80 py-6 text-center text-xs text-slate-400">
        <p>© {{ date('Y') }} Graduate Nurses Association of Tamil Nadu (GNAT). All rights reserved.</p>
        <p class="mt-1 text-[11px] text-slate-500">Official Membership Verification &amp; Security Portal · www.gnat.in</p>
    </footer>

</body>
</html>
