<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — GNAT Association</title>
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
@include('home.partials.header')

<main class="mx-auto max-w-7xl px-4 py-8 space-y-7">
    <section class="rounded-[28px] border border-[#351c42]/10 bg-white/85 backdrop-blur p-6 md:p-10 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">Contact</p>
                <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">Let’s talk</h1>
                <p class="mt-1 text-sm text-[#351c42]/65">Send a message and our team will get back to you.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="mailto:{{ $contact['email'] }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-[#351c42]/15 bg-white px-4 py-2.5 text-xs font-extrabold text-[#351c42] hover:bg-[#351c42]/5">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-[#351c42]/5">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M4 4h16v16H4z" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="m22 6-10 7L2 6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ $contact['email'] }}
                </a>
                @if(!empty($contact['phones'][0]['tel']))
                    <a href="tel:{{ $contact['phones'][0]['tel'] }}"
                       class="inline-flex items-center gap-2 rounded-2xl border border-[#351c42]/15 bg-white px-4 py-2.5 text-xs font-extrabold text-[#351c42] hover:bg-[#351c42]/5">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-[#351c42]/5">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M22 16.92V20a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3.09a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L8 9.83a16 16 0 0 0 6.17 6.17l1.38-1.38a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ $contact['phones'][0]['label'] ?? $contact['phones'][0]['tel'] }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Server-side fallbacks (JS disabled). When JS is enabled, we show a modal without reload. --}}
        @if(session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900">
                <p class="text-sm font-extrabold">Success</p>
                <p class="mt-1 text-sm text-emerald-900/80">{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-900">
                <p class="text-sm font-extrabold">Please fix the errors</p>
                <ul class="mt-2 space-y-1 text-sm text-rose-900/80 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    <section class="grid grid-cols-1 gap-5 lg:grid-cols-5">
        <div class="lg:col-span-3 rounded-3xl border border-[#351c42]/10 bg-white p-6 md:p-8 shadow-sm">
            <h2 class="text-lg font-extrabold tracking-tight">Send a message</h2>
            <p class="mt-1 text-sm text-[#351c42]/65">We usually respond within 1–2 working days.</p>

            <form method="POST" action="{{ route('contact.submit') }}" class="mt-6 space-y-4" data-contact-form>
                @csrf

                <div id="contact-form-errors" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-900">
                    <p class="text-sm font-extrabold">Please fix the errors</p>
                    <ul id="contact-form-errors-list" class="mt-2 space-y-1 text-sm text-rose-900/80 list-disc list-inside"></ul>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-[0.2em] text-[#965995]" for="name">Full name</label>
                        <input id="name" name="name" value="{{ old('name') }}" required
                               class="mt-2 w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25"
                               placeholder="Your name" />
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-[0.2em] text-[#965995]" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="mt-2 w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25"
                               placeholder="you@example.com" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-[0.2em] text-[#965995]" for="phone">Phone (optional)</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}"
                               class="mt-2 w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25"
                               placeholder="+91 90000 00000" />
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-[0.2em] text-[#965995]" for="subject">Subject</label>
                        <input id="subject" name="subject" value="{{ old('subject') }}" required
                               class="mt-2 w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25"
                               placeholder="How can we help?" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-[0.2em] text-[#965995]" for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required
                              class="mt-2 w-full rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25"
                              placeholder="Write your message...">{{ old('message') }}</textarea>
                    <p class="mt-2 text-xs text-[#351c42]/55">Please don’t share passwords or payment information.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pt-2">
                    <button type="submit"
                            data-contact-submit
                            class="inline-flex items-center justify-center rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15 disabled:opacity-60 disabled:cursor-not-allowed">
                        <span data-contact-submit-label>Send Message</span>
                    </button>
                    <a href="{{ route('home.give') }}" class="text-sm font-bold text-[#965995] hover:underline">
                        Want to donate instead?
                    </a>
                </div>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-5">
            <div class="rounded-3xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-extrabold tracking-tight">Our address</h3>
                <p class="mt-2 text-sm text-[#351c42]/70 leading-6">{{ $contact['address'] }}</p>
                <a class="mt-4 inline-flex items-center gap-2 text-sm font-extrabold text-[#351c42] hover:underline"
                   target="_blank" rel="noopener"
                   href="https://www.google.com/maps?q={{ $contact['maps_query'] }}">
                    Open in Google Maps
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M7 17L17 7" stroke-linecap="round"/>
                        <path d="M7 7h10v10" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="rounded-3xl border border-[#351c42]/10 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-extrabold tracking-tight">Quick links</h3>
                <div class="mt-4 grid grid-cols-1 gap-2">
                    <a href="{{ route('events.index') }}" class="rounded-2xl border border-[#351c42]/10 px-4 py-3 text-sm font-bold hover:bg-[#351c42]/5">View Events</a>
                    <a href="{{ route('blogs.index') }}" class="rounded-2xl border border-[#351c42]/10 px-4 py-3 text-sm font-bold hover:bg-[#351c42]/5">Read Blogs</a>
                    <a href="{{ route('donations.index') }}" class="rounded-2xl border border-[#351c42]/10 px-4 py-3 text-sm font-bold hover:bg-[#351c42]/5">Donation Campaigns</a>
                </div>
            </div>
        </div>
    </section>
</main>

<div
    id="contact-success-modal"
    class="fixed inset-0 z-[210] hidden items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="contact-success-title"
>
    <div class="absolute inset-0 bg-[#351c42]/55" data-contact-success-backdrop></div>
    <div class="relative w-full max-w-md rounded-3xl border border-[#351c42]/10 bg-white p-6 shadow-2xl shadow-[#351c42]/20">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-black uppercase tracking-[0.22em] text-emerald-700">Success</p>
                <h2 id="contact-success-title" class="mt-1 text-lg font-extrabold text-[#351c42]">Message sent</h2>
            </div>
            <button type="button" class="rounded-2xl p-2 text-[#351c42]/50 hover:bg-[#351c42]/5 hover:text-[#351c42]" data-contact-success-close aria-label="Close">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
        </div>
        <p id="contact-success-body" class="mt-4 text-sm text-[#351c42]/75 leading-relaxed">
            Thanks! Your message has been sent.
        </p>
        <div class="mt-6 flex flex-col gap-2">
            <button type="button" class="w-full rounded-2xl bg-[#351c42] px-5 py-3 text-sm font-extrabold text-[#fddc6a] shadow-md hover:bg-[#4d2a5c]" data-contact-success-ok>
                OK
            </button>
        </div>
    </div>
</div>

@include('home.partials.footer')
@include('home.partials.floating')
@include('home.partials.donate-modal')
@include('home.partials.donate-payment-modals')
@include('home.partials.scripts')
</body>
</html>

