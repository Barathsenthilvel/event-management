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
<body class="bg-[#f8f6fa] text-[#351c42]">
@include('home.partials.header')

<main class="mx-auto max-w-7xl px-4 py-8 space-y-7">
    <section class="relative overflow-hidden rounded-[28px] border border-[#351c42]/10 bg-white/85 backdrop-blur p-6 md:p-10 shadow-sm">
        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-[#fddc6a]/30 blur-2xl"></div>
        <div class="absolute -left-16 -bottom-16 h-56 w-56 rounded-full bg-[#965995]/15 blur-2xl"></div>

        <div class="relative grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-center">
            <div class="min-w-0">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">About</p>
                <h1 class="mt-2 text-3xl md:text-4xl font-extrabold tracking-tight text-[#351c42]">
                Graduate Nurses Association of tamilnadu believe that qualified nurses are the backbone of health care delivery system; they function as the orbit of the caring process by shouldering the major care needs of the patients and families in the hospital and community.
                </h1>
                <p class="mt-4 text-[#351c42]/70 leading-7">
                We strongly believe that the skilful hands of the nurses can make magical miracles not only when caring the patients but also in the empowerment of themselves and the nursing profession. We believe that fidelity, assertiveness, commitment and teamwork(FACT) would achieve the objectives of our association. GNAT was established on 2015. In April 30, 2015 the thought of having a professional organization for graduate nurses in Tamilnadu was brought up by few graduate nurses. The birth of such an organization was eagerly anticipated and welcomed by many nurses who are given the stress of work overload, paid less and mistreated even well educated and hands on skills. Do you feel they need help?? For that reason we stepped forward on September 28, 2014 the first meeting of GNAT was held at children’s park Guindy, Chennai. We elected the board of directors and committee members. We raised call for the interested nurses who are looking for the great change in nursing profession. We registered the association under society act 27, of 1975 on April 30,2015. Today the association has grown with hundreds of members.
                </p>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-[#351c42]/10 bg-white p-4">
                        <p class="text-xs font-black tracking-wide text-[#965995] uppercase">Our Mission</p>
                        <p class="mt-2 text-sm text-[#351c42]/70 leading-6">The mission of the Graduate Nurses Association of Tamil Nadu is to empower nurses toprovide moral & permissible support to the nursing fraternity for regulated professional & ethical benefits and rights.</p>
                    </div>
                    <div class="rounded-2xl border border-[#351c42]/10 bg-white p-4">
                        <p class="text-xs font-black tracking-wide text-[#965995] uppercase">Our Vision</p>
                        <p class="mt-2 text-sm text-[#351c42]/70 leading-6">Graduate nurses work in a nurtured and cultured environment enjoying unbiased monetary benefits that promote focused holistic service to stakeholders and community .</p>
                    </div>
                    <div class="rounded-2xl border border-[#351c42]/10 bg-white p-4">
                        <p class="text-xs font-black tracking-wide text-[#965995] uppercase">Values</p>
                        <p class="mt-2 text-sm text-[#351c42]/70 leading-6">Graduate Nurses Association of tamilnadu believe that function as the orbit of the caring process by shouldering the major care needs of the patients and families in the hospital and community.</p>
                    </div>
                </div>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('events.index') }}"
                       class="inline-flex items-center justify-center rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                        Explore Events
                    </a>
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-[#351c42]/15 bg-white px-6 py-3 text-sm font-extrabold text-[#351c42] hover:bg-[#351c42]/5">
                        Contact Us
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="aspect-[4/3] overflow-hidden rounded-[26px] border border-[#351c42]/10 bg-white shadow-sm">
                    <img
                        src="{{ asset(($about['main_image'] ?? 'images/events/event-1-2.jpg')) }}"
                        alt="About GNAT Association"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    />
                </div>
                <div class="absolute -bottom-6 -left-6 hidden md:block w-48 overflow-hidden rounded-[22px] border border-[#351c42]/10 bg-white shadow-lg">
                    <img
                        src="{{ asset(($about['accent_image'] ?? 'images/events/event-1-1.jpg')) }}"
                        alt="GNAT Association community"
                        class="h-32 w-full object-cover"
                        loading="lazy"
                    />
                    <div class="p-3">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#965995]">Trusted work</p>
                        <p class="mt-1 text-xs font-bold text-[#351c42]/80">Programs with clear outcomes</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

