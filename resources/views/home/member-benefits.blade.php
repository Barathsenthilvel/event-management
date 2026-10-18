<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Benefits of GNAT Membership — Graduate Nurses Association of Tamil Nadu</title>
    <meta name="description" content="Discover the extensive privileges of GNAT membership: professional development, higher education sponsorship, 10-point workplace legal support, 100% medical assistance, assured jobs, and overseas guidance for nurses in Tamil Nadu.">
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
        .benefit-card-glow {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease, border-color 0.35s ease;
        }
        .benefit-card-glow:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(53, 28, 66, 0.12);
        }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42] antialiased selection:bg-[#fddc6a] selection:text-[#351c42]">
    @include('home.partials.header')

    <!-- Hero Banner Section -->
    <header class="relative overflow-hidden bg-gradient-to-br from-[#351c42] via-[#2f173b] to-[#1e0d27] text-white py-16 lg:py-24">
        <!-- Ambient Decorative Glow Elements -->
        <div class="pointer-events-none absolute -left-24 -top-24 h-96 w-96 rounded-full bg-[#965995]/25 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 top-1/2 h-80 w-80 -translate-y-1/2 rounded-full bg-[#fddc6a]/20 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/3 bottom-0 h-52 w-96 rounded-full bg-[#7c3aed]/15 blur-2xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
            <!-- Breadcrumbs -->
            <nav class="mb-6 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-white/60" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-[#fddc6a] transition-colors">Home</a>
                <span class="text-white/30">/</span>
                <span class="text-[#fddc6a]">Member Benefits</span>
            </nav>

            <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-8 space-y-6">
                    <!-- Eyebrow Badge -->
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 backdrop-blur-md shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-[#fddc6a] animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#fddc6a]">
                            {{ $member_benefits['intro']['badge'] ?? 'GNAT Member Privileges' }}
                        </span>
                    </div>

                    <!-- Main H1 Heading -->
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-[1.15] text-white">
                        Comprehensive Benefits of <span class="relative inline-block text-[#fddc6a]">GNAT Membership
                            <span class="absolute left-0 right-0 -bottom-1.5 h-1.5 bg-[#fddc6a]/40 rounded-full"></span>
                        </span>
                    </h1>

                    <!-- Lead Paragraph -->
                    <p class="max-w-2xl text-base sm:text-lg leading-relaxed text-white/85">
                        {{ $member_benefits['intro']['subtitle'] ?? 'The Graduate Nurses Association of Tamilnadu (GNAT) is committed to empowering its members with a wide range of benefits that enhance their professional growth, personal well-being, and networking opportunities.' }}
                    </p>

                    <!-- Key Highlights Pill Row -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/15 px-3.5 py-2 text-xs font-semibold text-white/90 backdrop-blur-sm">
                            <svg class="h-4 w-4 text-[#fddc6a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>100% Medical Aid</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/15 px-3.5 py-2 text-xs font-semibold text-white/90 backdrop-blur-sm">
                            <svg class="h-4 w-4 text-[#fddc6a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>10-Point Legal Protection Cell</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/15 px-3.5 py-2 text-xs font-semibold text-white/90 backdrop-blur-sm">
                            <svg class="h-4 w-4 text-[#fddc6a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Assured Jobs & Abroad Pathways</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/15 px-3.5 py-2 text-xs font-semibold text-white/90 backdrop-blur-sm">
                            <svg class="h-4 w-4 text-[#fddc6a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Higher Education Sponsorship</span>
                        </div>
                    </div>

                    <!-- Action CTAs -->
                    <div class="pt-4 flex flex-wrap items-center gap-4">
                        <a href="{{ Auth::check() ? route('member.dashboard') : route('member.register') }}" class="click-btn click-btn--nav btn-style506 shadow-xl shadow-black/25">
                            <span class="click-btn__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                                    <path d="M8 8l3 4-3 4M13 8l3 4-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="click-btn__label">{{ Auth::check() ? 'Go to Member Dashboard' : 'Join as GNAT Member' }}</span>
                        </a>

                        <a href="#benefits-catalog" class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/5 backdrop-blur-sm px-6 py-3.5 text-sm font-bold text-white hover:bg-white/15 hover:border-[#fddc6a] transition-all">
                            <span>Explore All 6 Categories</span>
                            <svg class="h-4 w-4 text-[#fddc6a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Quick Privilege Highlights Card -->
                <div class="lg:col-span-4">
                    <div class="relative overflow-hidden rounded-3xl border border-white/20 bg-gradient-to-b from-white/15 to-white/5 p-6 sm:p-8 backdrop-blur-xl shadow-2xl">
                        <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-[#fddc6a]/20 blur-xl"></div>
                        <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                            <svg class="h-5 w-5 text-[#fddc6a]" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                            </svg>
                            Why Join GNAT?
                        </h2>
                        <p class="mt-2 text-xs text-white/75 leading-relaxed">
                            Established under Society Act 27 of 1975, GNAT stands firmly on <strong>F.A.C.T.</strong>—Fidelity, Assertiveness, Commitment, and Teamwork.
                        </p>
                        <div class="mt-5 space-y-3 border-t border-white/10 pt-4 text-xs font-medium text-white/90">
                            <div class="flex items-center justify-between py-1 border-b border-white/5">
                                <span class="text-white/70">Membership Status</span>
                                <span class="font-bold text-[#fddc6a]">Official State Association</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-white/5">
                                <span class="text-white/70">Legal Coverage</span>
                                <span class="font-bold text-[#fddc6a]">Workplace, Salary &amp; Rights</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-white/5">
                                <span class="text-white/70">Higher Education</span>
                                <span class="font-bold text-[#fddc6a]">Sponsorship Opportunities</span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span class="text-white/70">Global Mobility</span>
                                <span class="font-bold text-[#fddc6a]">UK, Germany, Ireland, Gulf</span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ Auth::check() ? route('member.dashboard') : route('member.register') }}" class="block w-full text-center rounded-xl bg-[#fddc6a] px-4 py-2.5 text-xs font-extrabold text-[#351c42] hover:bg-white transition-colors shadow-md">
                                Register as a Member Today &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Quick Navigation Sticky Filter Strip -->
    <nav class="sticky top-[58px] z-30 border-y border-[#351c42]/10 bg-white/95 backdrop-blur-md shadow-sm" aria-label="Category jump links">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex items-center gap-2 overflow-x-auto py-3 no-scrollbar text-xs sm:text-sm font-bold text-[#351c42]/80">
                <span class="shrink-0 text-[11px] font-black uppercase tracking-wider text-[#965995] pr-2 border-r border-[#351c42]/15">
                    Jump To:
                </span>
                <a href="#professional-development" class="shrink-0 rounded-full px-3.5 py-1.5 hover:bg-[#351c42]/5 hover:text-[#965995] transition-colors">
                    1. Professional Development
                </a>
                <a href="#networking" class="shrink-0 rounded-full px-3.5 py-1.5 hover:bg-[#351c42]/5 hover:text-[#965995] transition-colors">
                    2. Networking &amp; Community
                </a>
                <a href="#support-assistance" class="shrink-0 rounded-full px-3.5 py-1.5 hover:bg-[#351c42]/5 hover:text-[#965995] transition-colors">
                    3. Support &amp; Legal Shield
                </a>
                <a href="#information-resources" class="shrink-0 rounded-full px-3.5 py-1.5 hover:bg-[#351c42]/5 hover:text-[#965995] transition-colors">
                    4. Information &amp; Resources
                </a>
                <a href="#job-opportunities" class="shrink-0 rounded-full px-3.5 py-1.5 hover:bg-[#351c42]/5 hover:text-[#965995] transition-colors">
                    5. Job Opportunities
                </a>
                <a href="#communication" class="shrink-0 rounded-full px-3.5 py-1.5 hover:bg-[#351c42]/5 hover:text-[#965995] transition-colors">
                    6. Communication &amp; Expression
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main id="benefits-catalog" class="mx-auto max-w-7xl px-4 sm:px-6 py-12 lg:py-16 space-y-16">

        <!-- CATEGORY 1: Professional Development -->
        <section id="professional-development" class="scroll-mt-36">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-[#351c42]/10">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-[#7c3aed]">
                        <span class="h-2 w-2 rounded-full bg-[#7c3aed]"></span>
                        Category 01
                    </div>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#351c42]">
                        Professional Development
                    </h2>
                    <p class="mt-1 text-sm text-[#351c42]/70 max-w-2xl">
                        Ensuring continuous learning, competitive exam mastery, advanced credentials, and career growth for graduate nurses.
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-[#7c3aed]/10 px-3.5 py-1 text-xs font-extrabold text-[#7c3aed]">
                    5 Key Benefits Included
                </span>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- 1.1 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#7c3aed]/10 text-[#7c3aed]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#7c3aed] bg-[#7c3aed]/5 px-2.5 py-1 rounded-full border border-[#7c3aed]/10">01</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">Professional Growth and Development</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            GNAT ensures your professional growth through conferences, state workshops, hands-on skills training sessions, and high-impact networking opportunities with senior healthcare leaders and officials.
                        </p>
                    </div>
                </div>

                <!-- 1.2 -->
                <div class="benefit-card-glow rounded-2xl border-2 border-[#7c3aed]/20 bg-gradient-to-b from-[#7c3aed]/[0.02] to-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#7c3aed] text-white shadow-md shadow-[#7c3aed]/20">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </span>
                            <span class="text-[11px] font-extrabold text-[#7c3aed] bg-[#7c3aed]/10 px-2.5 py-1 rounded-full border border-[#7c3aed]/20">Sponsorship</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">Higher Education Support &amp; Sponsorship</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Avail financial sponsorship and institutional backing for higher nursing education programs (M.Sc, Ph.D, Fellowships).
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#7c3aed]/10 flex items-center gap-1.5 text-[11px] font-bold text-[#7c3aed]">
                        <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>Subject to min. 2 years active membership</span>
                    </div>
                </div>

                <!-- 1.3 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#059669]/10 text-[#059669]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#059669] bg-[#059669]/10 px-2.5 py-1 rounded-full">100% Free</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">Government Exam Preparation &amp; Guidelines</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Receive comprehensive examination guidance, mock question banks, syllabus updates, and notification alerts for both state (MRB/TNPSC) and central (NORCET, AIIMS, ESIC, RRB) examinations, completely free of cost.
                        </p>
                    </div>
                </div>

                <!-- 1.4 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#2563eb]/10 text-[#2563eb]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#2563eb] bg-[#2563eb]/10 px-2.5 py-1 rounded-full">Family Included</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">Free Career Counseling</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Expert counseling and mentorship for nursing students, registered members, and their children—guiding them through higher studies, career transitions, and secured job placements.
                        </p>
                    </div>
                </div>

                <!-- 1.5 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between sm:col-span-2 lg:col-span-2">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#0d9488]/10 text-[#0d9488]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#0d9488] bg-[#0d9488]/10 px-2.5 py-1 rounded-full">NRI Support</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">Nursing Council Registration and Renewal Guidance</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Avoid regulatory headaches and licensing delays. Get hands-on guidance on Nursing Council updates, state registration transfers, CNE credit tracking, and license renewal verification, including specialized remote support for NRI nursing members abroad.
                        </p>
                    </div>
                </div>
            </div>
        </section>


        <!-- CATEGORY 2: Networking and Community Engagement -->
        <section id="networking" class="scroll-mt-36">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-[#351c42]/10">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-[#0d9488]">
                        <span class="h-2 w-2 rounded-full bg-[#0d9488]"></span>
                        Category 02
                    </div>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#351c42]">
                        Networking &amp; Community Engagement
                    </h2>
                    <p class="mt-1 text-sm text-[#351c42]/70 max-w-2xl">
                        Connect with fellow nursing professionals across India and globally, celebrate community spirit, and enjoy work-life harmony.
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-[#0d9488]/10 px-3.5 py-1 text-xs font-extrabold text-[#0d9488]">
                    3 Core Programs
                </span>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-3">
                <!-- 2.1 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#0d9488]/10 text-[#0d9488] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">Global Networking Opportunities</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Connect with clinical nursing professionals, nurse educators, researchers, and administrators across India and abroad through GNAT's extensive member community.
                    </p>
                </div>

                <!-- 2.2 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#f59e0b]/10 text-[#f59e0b] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">Annual Family Tour</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Enjoy an annual vacation and retreat with family members organized by GNAT, fostering camaraderie, relaxation, work-life rejuvenation, and lifelong bonds.
                    </p>
                </div>

                <!-- 2.3 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#db2777]/10 text-[#db2777] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">Extra Curriculum Support</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Nurture your creative talents outside the hospital ward. GNAT sponsors and organizes cultural events, sports tournaments, literary meets, and fine arts activities.
                    </p>
                </div>
            </div>
        </section>


        <!-- CATEGORY 3: Support and Assistance (SPOTLIGHT SECTION) -->
        <section id="support-assistance" class="scroll-mt-36">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-[#351c42]/10">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-[#d97706]">
                        <span class="h-2 w-2 rounded-full bg-[#d97706]"></span>
                        Category 03 • Legal &amp; Welfare Shield
                    </div>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#351c42]">
                        Support, Welfare &amp; Legal Assistance
                    </h2>
                    <p class="mt-1 text-sm text-[#351c42]/70 max-w-2xl">
                        You are never alone. GNAT provides 360-degree legal defense against workplace exploitation, 100% medical aid, and emergency monetary support.
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-[#d97706]/10 px-3.5 py-1 text-xs font-extrabold text-[#d97706]">
                    Dedicated Protection Cell
                </span>
            </div>

            <!-- Spotlight: Comprehensive 10-Point Legal Support Cell -->
            <div class="mt-8 overflow-hidden rounded-3xl border-2 border-[#351c42] bg-gradient-to-br from-[#351c42] via-[#2f173b] to-[#251030] text-white p-6 sm:p-10 shadow-xl relative">
                <div class="pointer-events-none absolute -right-16 -bottom-16 h-72 w-72 rounded-full bg-[#fddc6a]/15 blur-3xl"></div>

                <div class="relative flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-white/10">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 rounded-full bg-[#fddc6a]/20 border border-[#fddc6a]/30 px-3 py-1 text-xs font-extrabold text-[#fddc6a]">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/></svg>
                            Flagship Protection
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-white">
                            1. Legal Support and Guidance Cell
                        </h3>
                        <p class="text-sm text-white/80 max-w-3xl leading-relaxed">
                            Graduate nurses face severe challenges: withheld certificates, arbitrary salary deductions, excessive duty hours, and unfair contracts. GNAT legal counsel directly intervenes for your current or previous workplace disputes:
                        </p>
                    </div>
                </div>

                <!-- 10-Point Legal Checklist Grid -->
                <div class="relative mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-2">
                    @php
                        $legalPoints = [
                            ['title' => 'Legal Advice for Workplace Issues', 'desc' => 'Direct consultation with experienced advocates specializing in hospital employment law.'],
                            ['title' => 'Salary / PF / ESI / Contract Dispute Support', 'desc' => 'Intervention against unpaid dues, PF non-deposit, and unlawful bond penalties.'],
                            ['title' => 'Wrongful Termination Support', 'desc' => 'Legal defense and grievance filing against unjust dismissal without due process.'],
                            ['title' => 'Harassment Complaint Support', 'desc' => 'Confidential protection and immediate intervention in workplace harassment cases.'],
                            ['title' => 'Workplace Safety Complaint Cell', 'desc' => 'Ensuring occupational health, safety norms, and nurse security in private & govt centers.'],
                            ['title' => 'Duty Hours / Overtime Rights Awareness', 'desc' => 'Defending regulated shift limits and claiming legitimate overtime compensation.'],
                            ['title' => 'Maternity Rights Guidance', 'desc' => 'Enforcing statutory maternity leaves, benefits, and non-discrimination protection.'],
                            ['title' => 'Labour Law Awareness for Nurses', 'desc' => 'Workshops and legal literacy educating nurses on their fundamental constitutional rights.'],
                            ['title' => 'Documentation Support for Complaints', 'desc' => 'Drafting formal notices, police petitions, and labour department representation letters.'],
                            ['title' => 'Professional Indemnity Guidance', 'desc' => 'Protection guidelines against frivolous clinical malpractice or patient family disputes.'],
                        ];
                    @endphp

                    @foreach ($legalPoints as $idx => $lp)
                        <div class="flex items-start gap-3 rounded-xl bg-white/5 border border-white/10 p-3.5 hover:bg-white/10 transition-colors">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#fddc6a] text-[#351c42] font-black text-xs">
                                ✓
                            </span>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-[#fddc6a]">{{ $lp['title'] }}</h4>
                                <p class="text-xs text-white/75 mt-0.5 leading-snug">{{ $lp['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Other Welfare Benefits Cards: Medical, Monetary, Blood, Hotels -->
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- 3.2 Medical -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#e11d48]/10 text-[#e11d48]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#e11d48] bg-[#e11d48]/10 px-2.5 py-1 rounded-full">100% TN Coverage</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">2. Medical Assistance</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Avail medical assistance for you and your family, with 100% coverage across Tamil Nadu and extended medical support network across India and abroad.
                        </p>
                    </div>
                </div>

                <!-- 3.3 Monetary -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#059669]/10 text-[#059669]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#059669] bg-[#059669]/10 px-2.5 py-1 rounded-full">Emergency Relief</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">3. Monetary Support</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Receive compassionate financial relief in cases of accidents, major illness, or family bereavement through GNAT's internal solidarity welfare funds.
                        </p>
                    </div>
                </div>

                <!-- 3.4 Free Blood Donation -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#dc2626]/10 text-[#dc2626]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#dc2626] bg-[#dc2626]/10 px-2.5 py-1 rounded-full">24/7 Helpline</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">4. Free Blood Donation Network</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Instant access to emergency blood donation coordination for you, your patients, and your family members free of cost through GNAT's statewide nurse volunteer network.
                        </p>
                    </div>
                </div>

                <!-- 3.5 Safe Hotels -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#6366f1]/10 text-[#6366f1]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-[#6366f1] bg-[#6366f1]/10 px-2.5 py-1 rounded-full">Safe Travel</span>
                        </div>
                        <h3 class="text-base font-extrabold text-[#351c42]">5. Safe Hotels &amp; Rooms Assistance</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                            Enjoy verified, safe, and secure accommodation arrangements while traveling for examinations, conferences, council work, or interviews with GNAT community support.
                        </p>
                    </div>
                </div>
            </div>
        </section>


        <!-- CATEGORY 4: Information and Resources -->
        <section id="information-resources" class="scroll-mt-36">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-[#351c42]/10">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-[#2563eb]">
                        <span class="h-2 w-2 rounded-full bg-[#2563eb]"></span>
                        Category 04
                    </div>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#351c42]">
                        Information, Knowledge &amp; Research
                    </h2>
                    <p class="mt-1 text-sm text-[#351c42]/70 max-w-2xl">
                        Stay ahead of clinical standards with authoritative news, digital medical libraries, publication support, and research grant guidance.
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-[#2563eb]/10 px-3.5 py-1 text-xs font-extrabold text-[#2563eb]">
                    Academic Excellence
                </span>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- 4.1 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#2563eb]/10 text-[#2563eb] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">1. Latest Nursing/Medical News</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Stay updated with clinical guidelines, regulatory orders, pay revisions, and advancements in global nursing and healthcare.
                    </p>
                </div>

                <!-- 4.2 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#7c3aed]/10 text-[#7c3aed] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">2. Free E-Nursing &amp; Medical Books</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Download high-yield e-books, clinical procedure manuals, pharmacology handbooks, and reference literature completely free.
                    </p>
                </div>

                <!-- 4.3 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#0d9488]/10 text-[#0d9488] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">3. Journal &amp; Patent Publications</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Get step-by-step guidance on manuscript drafting, indexing, peer review, and patent registration from distinguished research faculty.
                    </p>
                </div>

                <!-- 4.4 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#ea580c]/10 text-[#ea580c] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">4. Research Funding Support</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Assistance in identifying and applying for ICMR, state, and international research grants for innovative clinical nursing proposals.
                    </p>
                </div>
            </div>
        </section>


        <!-- CATEGORY 5: Job Opportunities & Global Careers -->
        <section id="job-opportunities" class="scroll-mt-36">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-[#351c42]/10">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-[#059669]">
                        <span class="h-2 w-2 rounded-full bg-[#059669]"></span>
                        Category 05
                    </div>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#351c42]">
                        100% Job Opportunities &amp; Abroad Pathways
                    </h2>
                    <p class="mt-1 text-sm text-[#351c42]/70 max-w-2xl">
                        Direct institutional recruitment, campus-style hospital tie-ups, and verified international placement assistance without fraudulent agent exploitation.
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-[#059669]/10 px-3.5 py-1 text-xs font-extrabold text-[#059669]">
                    Domestic &amp; Overseas
                </span>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- 5.1 -->
                <div class="benefit-card-glow rounded-2xl border-2 border-[#059669]/20 bg-gradient-to-b from-[#059669]/[0.02] to-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#059669] text-white shadow-md shadow-[#059669]/20">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <span class="text-xs font-extrabold text-[#059669] bg-[#059669]/10 px-2.5 py-1 rounded-full">Guaranteed Priority</span>
                    </div>
                    <h3 class="text-base font-extrabold text-[#351c42]">1. 100% Assured Job Opportunity</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Avail assured employment opportunities with prioritized recruitment pipelines, exclusive to active GNAT registered members.
                    </p>
                </div>

                <!-- 5.2 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#0d9488]/10 text-[#0d9488] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">2. Job Placement Assistance</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Dedicated career placement support connecting members with leading multispecialty hospitals, colleges of nursing, and healthcare institutions.
                    </p>
                </div>

                <!-- 5.3 -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#2563eb]/10 text-[#2563eb] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">3. Hospital Tie-up Recruitment</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Benefit from official MoUs and institutional tie-ups between GNAT and premier hospital chains for direct interview drives and fair salary baselines.
                    </p>
                </div>

                <!-- 5.4 Abroad Job Guidance -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm sm:col-span-1 lg:col-span-2">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#7c3aed]/10 text-[#7c3aed]">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div class="flex flex-wrap gap-1">
                            <span class="text-[10px] font-extrabold text-[#7c3aed] bg-[#7c3aed]/10 px-2 py-0.5 rounded-full">UK</span>
                            <span class="text-[10px] font-extrabold text-[#7c3aed] bg-[#7c3aed]/10 px-2 py-0.5 rounded-full">Germany</span>
                            <span class="text-[10px] font-extrabold text-[#7c3aed] bg-[#7c3aed]/10 px-2 py-0.5 rounded-full">Ireland</span>
                            <span class="text-[10px] font-extrabold text-[#7c3aed] bg-[#7c3aed]/10 px-2 py-0.5 rounded-full">Gulf Countries</span>
                        </div>
                    </div>
                    <h3 class="text-base font-extrabold text-[#351c42]">4. Abroad Job Guidance (UK, Germany, Ireland, Gulf)</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Step-by-step guidance on licensing exams (OET, IELTS, CBT, OSCE, German B2 level, Prometric/DHA/MOH), verification protocols, and transparent relocation pathways.
                    </p>
                </div>

                <!-- 5.5 Abroad Processing -->
                <div class="benefit-card-glow rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#059669]/10 text-[#059669] mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-extrabold text-[#351c42]">5. Abroad Processing &amp; Verification</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#351c42]/75 leading-relaxed">
                        Get guided on international credential verification, document attestation, visa clearance, and proper channels with authorized statutory bodies.
                    </p>
                </div>
            </div>
        </section>


        <!-- CATEGORY 6: Communication and Expression -->
        <section id="communication" class="scroll-mt-36">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-[#351c42]/10">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-[#db2777]">
                        <span class="h-2 w-2 rounded-full bg-[#db2777]"></span>
                        Category 06
                    </div>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#351c42]">
                        Communication, Voice &amp; Expression
                    </h2>
                    <p class="mt-1 text-sm text-[#351c42]/70 max-w-2xl">
                        A recognized statewide platform to articulate nurse experiences, professional opinions, and leadership ideas.
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-[#db2777]/10 px-3.5 py-1 text-xs font-extrabold text-[#db2777]">
                    Member Voice
                </span>
            </div>

            <div class="mt-8 rounded-3xl border border-[#351c42]/10 bg-white p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div class="space-y-2 max-w-2xl">
                        <div class="inline-flex items-center gap-2 text-xs font-bold text-[#db2777]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                            </svg>
                            <span>Share Your Thoughts, Stories &amp; Ideas</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-[#351c42]">Your Voice Matters to the Nursing Fraternity</h3>
                        <p class="text-sm text-[#351c42]/75 leading-relaxed">
                            Express yourself and inspire thousands of nurses across Tamil Nadu by contributing articles, clinical case studies, opinions, and videos through GNAT’s official blogs, newsletters, Vlogs, and social media media platforms.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('blogs.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#351c42] px-6 py-3.5 text-sm font-bold text-[#fddc6a] hover:bg-[#4d2a5c] transition-colors shadow-md">
                            <span>Read GNAT Blogs</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <!-- Bottom Closing Commitment Banner -->
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#351c42] via-[#2f173b] to-[#1e0d27] p-8 sm:p-12 text-white text-center shadow-xl">
            <div class="pointer-events-none absolute -left-10 -bottom-10 h-64 w-64 rounded-full bg-[#fddc6a]/15 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-10 -top-10 h-64 w-64 rounded-full bg-[#965995]/25 blur-3xl"></div>

            <div class="relative mx-auto max-w-3xl space-y-5">
                <span class="inline-block rounded-full bg-[#fddc6a]/20 px-4 py-1 text-xs font-black uppercase tracking-[0.2em] text-[#fddc6a]">
                    Join Our Mission
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">
                    By Becoming a GNAT Member, You Join a Community That Prioritizes Your Growth, Well-being, and Success.
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                    Protect your career, access higher education sponsorship, receive free exam coaching, and secure your professional legal rights under Tamil Nadu's leading nursing association.
                </p>

                <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ Auth::check() ? route('member.dashboard') : route('member.register') }}" class="click-btn click-btn--nav btn-style506 shadow-xl">
                        <span class="click-btn__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                                <path d="M8 8l3 4-3 4M13 8l3 4-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="click-btn__label">{{ Auth::check() ? 'Go to Member Area' : 'Register for GNAT Membership' }}</span>
                    </a>

                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-6 py-3.5 text-sm font-bold text-white hover:bg-white/20 transition-all">
                        <span>Have Questions? Contact Us</span>
                    </a>
                </div>
            </div>
        </section>

    </main>

    @include('home.partials.footer')
    @include('home.partials.floating')
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
