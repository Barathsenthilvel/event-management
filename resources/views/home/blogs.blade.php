<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs — GNAT Association</title>
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
                    <p class="text-[11px] font-black uppercase tracking-[0.22em] text-[#965995]">Blogs</p>
                    <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight text-[#351c42]">Explore All Posts</h1>
                    <p class="mt-1 text-sm text-[#351c42]/65">Posts added in admin will be listed here.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('blogs.index') }}" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="search" name="q" value="{{ $q }}" placeholder="Search blog title, tag, or excerpt…"
                       class="min-w-0 flex-1 rounded-2xl border border-[#351c42]/15 bg-white px-4 py-3 text-sm outline-none focus:border-[#965995]/40 focus:ring-2 focus:ring-[#965995]/25">
                <button type="submit" class="rounded-2xl bg-[#351c42] px-6 py-3 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] shadow-lg shadow-[#351c42]/15">
                    Search
                </button>
            </form>
        </section>

        @if($posts->isEmpty())
            <section class="rounded-2xl border border-dashed border-[#351c42]/20 bg-white p-10 text-center">
                <p class="text-sm font-bold text-[#351c42]/80">No blog posts found.</p>
            </section>
        @else
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6 justify-items-start">
                @foreach($posts as $post)
                    @include('home.partials.blog-card', ['post' => $post, 'heading' => 'h2'])
                @endforeach
            </div>

            <section class="mt-6 rounded-2xl border border-[#351c42]/10 bg-white p-4">
                {{ $posts->links() }}
            </section>
        @endif
    </main>

    @include('home.partials.footer')
    @include('home.partials.floating')
    @include('home.partials.donate-modal')
    @include('home.partials.donate-payment-modals')
    @include('home.partials.scripts')
</body>
</html>
