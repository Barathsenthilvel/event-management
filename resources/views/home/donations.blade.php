<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations — GNAT Association</title>
    @include('home.partials.head')
    @include('home.partials.styles')
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#f8f6fa] text-[#351c42]">
    @include('home.partials.header')

    <main class="mx-auto max-w-7xl px-4 py-8 space-y-7">
        <section class="rounded-3xl border border-[#351c42]/10 bg-white/85 backdrop-blur p-5 md:p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">Donations</p>
                    <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">Support our campaigns</h1>
                    <p class="mt-1 text-sm text-[#351c42]/65">Browse active donation purposes and learn how you can help.</p>
                </div>
            </div>

            <form method="get" action="{{ route('donations.index') }}" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="search" name="q" value="{{ $q }}" placeholder="Search purpose or description…"
                       class="min-w-0 flex-1 rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25">
                <button type="submit" class="rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                    Search
                </button>
            </form>
        </section>

        @if($donations->isEmpty())
            <section class="rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
                <p class="text-sm font-bold text-[#351c42]/80">No donations available right now.</p>
            </section>
        @else
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                @foreach($donations as $donation)
                    @php
                        $coverSrc = $donation->cover_image_path
                            ? asset('storage/' . $donation->cover_image_path)
                            : 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300"><rect fill="#e8e3dc" width="100%" height="100%"/></svg>');
                        $excerpt = $donation->short_description
                            ?: \Illuminate\Support\Str::limit(strip_tags((string) $donation->description), 220);
                        [$pillA, $pillB] = $donation->pillTagLabels();
                    @endphp
                    <article class="donation-slide min-w-0 h-full w-full rounded-3xl overflow-hidden border border-[#351c42]/10 bg-white shadow-md flex flex-col sm:flex-row min-h-[280px] sm:min-h-[240px]">
                        <div class="relative sm:w-[42%] min-h-[200px] sm:min-h-full overflow-hidden">
                            <img src="{{ $coverSrc }}" alt="{{ $donation->purpose }}" class="absolute inset-0 h-full w-full object-cover" width="400" height="300">
                        </div>
                        <div class="flex flex-1 flex-col justify-center p-5 sm:p-6 bg-[linear-gradient(180deg,#faf8f5_0%,#f3f0ea_100%)]">
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full border border-[#351c42]/20 bg-white px-3 py-1 text-xs font-semibold text-[#351c42]">{{ $pillA }}</span>
                                <span class="rounded-full border border-[#351c42]/20 bg-white px-3 py-1 text-xs font-semibold text-[#351c42]">{{ $pillB }}</span>
                            </div>
                            <h4 class="mt-4 text-lg sm:text-xl font-extrabold text-[#351c42] leading-snug">{{ $donation->purpose }}</h4>
                            <p class="mt-2 text-sm text-[#351c42]/65 line-clamp-2">{{ $excerpt }}</p>
                            <button type="button" data-open-donate-modal data-donation-id="{{ $donation->id }}" class="click-btn click-btn--sm btn-style506 mt-4 self-start text-left" aria-label="Donate now">
                                <span class="click-btn__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                                        <path d="M8 8l3 4-3 4M13 8l3 4-3 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <span class="click-btn__label">Donate Now</span>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <section class="mt-6 rounded-2xl border border-[#351c42]/10 bg-white p-4">
                {{ $donations->links() }}
            </section>
        @endif
    </main>

    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
