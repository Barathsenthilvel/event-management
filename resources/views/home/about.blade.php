<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — GNAT Association</title>
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42] antialiased">
@include('home.partials.header')

<main class="mx-auto max-w-7xl px-4 py-8 space-y-8">
    <!-- Top Hero Section: About GNAT Story -->
    <section class="relative overflow-hidden rounded-[28px] border border-[#351c42]/10 bg-white p-6 md:p-10 lg:p-12 shadow-sm">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-[#fddc6a]/30 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-[#965995]/15 blur-3xl pointer-events-none"></div>

        <div class="relative grid grid-cols-1 gap-10 lg:grid-cols-12 lg:items-center">
            <!-- Left Column: Story Copy -->
            <div class="lg:col-span-7 min-w-0 space-y-6">
                <!-- Eyebrow & Title -->
                <div>
                    <span class="inline-block rounded-full bg-[#965995]/10 px-3.5 py-1 text-[11px] font-black uppercase tracking-[0.2em] text-[#965995]">
                        About GNAT Association
                    </span>
                    <h1 class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[#351c42] leading-tight">
                        Graduate Nurses Association of Tamil Nadu
                    </h1>
                </div>

                <!-- Highlight Core Belief Lead Card -->
                <div class="relative rounded-2xl border-l-4 border-[#fddc6a] bg-[#f8f6fa] p-5 shadow-sm border border-[#351c42]/5">
                    <p class="text-base sm:text-lg font-bold leading-relaxed text-[#351c42]/90">
                        Graduate Nurses Association of tamilnadu believe that qualified nurses are the backbone of health care delivery system; they function as the orbit of the caring process by shouldering the major care needs of the patients and families in the hospital and community.
                    </p>
                </div>

                <!-- Full-Width Readable Story Paragraphs -->
                <div class="space-y-4 text-sm sm:text-base leading-relaxed text-[#351c42]/80">
                    <p>
                        We strongly believe that the skilful hands of the nurses can make magical miracles not only when caring the patients but also in the empowerment of themselves and the nursing profession. We believe that fidelity, assertiveness, commitment and teamwork(FACT) would achieve the objectives of our association. GNAT was established on 2015. In April 30, 2015 the thought of having a professional organization for graduate nurses in Tamilnadu was brought up by few graduate nurses.
                    </p>
                    <p>
                        The birth of such an organization was eagerly anticipated and welcomed by many nurses who are given the stress of work overload, paid less and mistreated even well educated and hands on skills. Do you feel they need help?? For that reason we stepped forward on September 28, 2014 the first meeting of GNAT was held at children’s park Guindy, Chennai. We elected the board of directors and committee members. We raised call for the interested nurses who are looking for the great change in nursing profession. We registered the association under society act 27, of 1975 on April 30,2015. Today the association has grown with hundreds of members.
                    </p>
                </div>

                <!-- Explore Events & Contact Us CTAs -->
                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('events.index') }}"
                       class="inline-flex items-center justify-center gap-2.5 rounded-2xl bg-[#351c42] px-7 py-3.5 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15 transition-all hover:scale-[1.01]">
                        <span>Explore Events</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-2xl border border-[#351c42]/15 bg-white px-7 py-3.5 text-sm font-extrabold text-[#351c42] hover:bg-[#351c42]/5 transition-all">
                        <span>Contact Us</span>
                    </a>
                </div>
            </div>

            <!-- Right Column: Visual Showcase Frame -->
            <div class="lg:col-span-5 relative flex justify-center">
                <div class="group relative w-full overflow-hidden rounded-[28px] border border-[#351c42]/10 bg-[#f8f6fa] p-2 shadow-sm transition-all duration-500 hover:shadow-lg">
                    <div class="overflow-hidden rounded-[22px] bg-white">
                        <img
                            src="{{ asset(($about['main_image'] ?? 'gnat-images/1000989135.png')) }}"
                            alt="About GNAT Association"
                            class="h-auto w-full object-contain transition-transform duration-700 ease-out group-hover:scale-[1.02]"
                            loading="lazy"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission, Vision & Values Cards Section (Spacious Full-Width 3-Column Layout) -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Our Mission Card -->
        <div class="group relative flex flex-col justify-between rounded-[24px] border border-[#351c42]/10 bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="space-y-3.5">
                <div class="inline-flex items-center gap-2 rounded-xl bg-[#965995]/10 px-3 py-1.5 text-xs font-black tracking-wider text-[#965995] uppercase">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Our Mission</span>
                </div>
                <p class="text-sm leading-relaxed text-[#351c42]/80">
                    The mission of the Graduate Nurses Association of Tamil Nadu is to empower nurses toprovide moral &amp; permissible support to the nursing fraternity for regulated professional &amp; ethical benefits and rights.
                </p>
            </div>
            <div class="mt-5 pt-4 border-t border-[#351c42]/5 text-xs font-bold text-[#965995] flex items-center justify-between">
                <span>GNAT Mission</span>
                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span>
            </div>
        </div>

        <!-- Our Vision Card -->
        <div class="group relative flex flex-col justify-between rounded-[24px] border border-[#351c42]/10 bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="space-y-3.5">
                <div class="inline-flex items-center gap-2 rounded-xl bg-[#fddc6a]/30 px-3 py-1.5 text-xs font-black tracking-wider text-[#351c42] uppercase">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Our Vision</span>
                </div>
                <p class="text-sm leading-relaxed text-[#351c42]/80">
                    Graduate nurses work in a nurtured and cultured environment enjoying unbiased monetary benefits that promote focused holistic service to stakeholders and community .
                </p>
            </div>
            <div class="mt-5 pt-4 border-t border-[#351c42]/5 text-xs font-bold text-[#351c42] flex items-center justify-between">
                <span>GNAT Vision</span>
                <span class="h-1.5 w-1.5 rounded-full bg-[#fddc6a]"></span>
            </div>
        </div>

        <!-- Values Card -->
        <div class="group relative flex flex-col justify-between rounded-[24px] border border-[#351c42]/10 bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="space-y-3.5">
                <div class="inline-flex items-center gap-2 rounded-xl bg-[#351c42]/10 px-3 py-1.5 text-xs font-black tracking-wider text-[#351c42] uppercase">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span>Values</span>
                </div>
                <p class="text-sm leading-relaxed text-[#351c42]/80">
                    Graduate Nurses Association of tamilnadu believe that function as the orbit of the caring process by shouldering the major care needs of the patients and families in the hospital and community.
                </p>
            </div>
            <div class="mt-5 pt-4 border-t border-[#351c42]/5 text-xs font-bold text-[#351c42] flex items-center justify-between">
                <span>GNAT Values</span>
                <span class="h-1.5 w-1.5 rounded-full bg-[#351c42]"></span>
            </div>
        </div>
    </section>

    <!-- Bottom Section: Original Purpose Cards (Unchanged) -->
    <section class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="rounded-3xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fddc6a]/40 text-[#351c42]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="mt-4 text-[11px] font-black uppercase tracking-[0.18em] text-[#965995]">For the Nurses</p>
            <h2 class="mt-1 text-lg font-extrabold tracking-tight text-[#351c42]">Purpose: Service &amp; Welfare</h2>
            <p class="mt-3 text-sm text-[#351c42]/70 leading-6">Everything GNAT does is centered on the well-being of nurses.</p>
            <ul class="mt-4 space-y-2.5 text-sm text-[#351c42]/75 leading-6">
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Welfare support during need</span>
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Career development and training</span>
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Protecting rights, dignity and growth of the nursing profession</span>
                </li>
            </ul>
            <p class="mt-4 text-sm font-extrabold text-[#351c42]">GNAT exists to serve nurses first.</p>
        </div>
        <div class="rounded-3xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#965995]/15 text-[#351c42]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="mt-4 text-[11px] font-black uppercase tracking-[0.18em] text-[#965995]">By the Nurses</p>
            <h2 class="mt-1 text-lg font-extrabold tracking-tight text-[#351c42]">Purpose: Leadership &amp; Ownership</h2>
            <p class="mt-3 text-sm text-[#351c42]/70 leading-6">The association is run, led, and driven by nurses themselves.</p>
            <ul class="mt-4 space-y-2.5 text-sm text-[#351c42]/75 leading-6">
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Decisions are made by nurses who understand ground realities</span>
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Programs are designed by people from the profession</span>
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Your voice directly shapes GNAT’s actions</span>
                </li>
            </ul>
            <p class="mt-4 text-sm font-extrabold text-[#351c42]">It’s our association, run by us.</p>
        </div>
        <div class="rounded-3xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#351c42]/10 text-[#351c42]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="mt-4 text-[11px] font-black uppercase tracking-[0.18em] text-[#965995]">Of the Nurses</p>
            <h2 class="mt-1 text-lg font-extrabold tracking-tight text-[#351c42]">Purpose: Community &amp; Belonging</h2>
            <p class="mt-3 text-sm text-[#351c42]/70 leading-6">GNAT belongs to every member. It represents the collective strength of the nursing community.</p>
            <ul class="mt-4 space-y-2.5 text-sm text-[#351c42]/75 leading-6">
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>It’s built from the nurses, for the nurses</span>
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Every member is a stakeholder and part of the GNAT family</span>
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[#965995]"></span>
                    <span>Together we create impact across Tamil Nadu</span>
                </li>
            </ul>
            <p class="mt-4 text-sm font-extrabold text-[#351c42]">GNAT is us. We are GNAT.</p>
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
