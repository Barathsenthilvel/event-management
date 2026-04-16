@extends('member.layouts.portal')

@section('title', 'Career Jobs — GNAT Association')

@section('portal_main_id', 'member-jobs-main')

@section('content')
    <header class="scroll-mt-28 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Careers</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Available jobs</h1>
            <p class="mt-1 max-w-2xl text-sm text-[#351c42]/65">Browse current openings shared by our partner hospitals and institutions.</p>
        </div>
        <a href="{{ route('member.dashboard') }}" class="shrink-0 text-sm font-semibold text-[#965995] hover:text-[#351c42]">← Back to dashboard</a>
    </header>

    <section class="space-y-4">
        <form method="GET" class="rounded-2xl border border-[#351c42]/10 bg-white/85 p-3 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <input
                    type="search"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Search by title, hospital, skill, or code..."
                    class="w-full rounded-xl border border-[#351c42]/15 bg-white px-4 py-2.5 text-sm font-medium text-[#351c42] outline-none focus:border-[#965995]/60 focus:ring-2 focus:ring-[#965995]/20"
                />
                <button type="submit" class="shrink-0 rounded-xl bg-[#351c42] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#291331]">Search</button>
                @if($q !== '')
                    <a href="{{ route('member.jobs.index') }}" class="shrink-0 rounded-xl border border-[#351c42]/15 bg-white px-4 py-2.5 text-sm font-bold text-[#351c42]/80 hover:border-[#351c42]/30">Reset</a>
                @endif
            </div>
        </form>

        @if($jobs->isEmpty())
            <div class="rounded-2xl border border-[#351c42]/10 bg-white/85 p-8 text-center shadow-sm">
                <p class="text-sm font-semibold text-[#351c42]/70">No jobs found right now.</p>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($jobs as $job)
                    <article class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-[#965995]">{{ $job->hospital ?: 'Hospital' }}</p>
                                <h2 class="mt-1 text-lg font-extrabold text-[#351c42]">{{ $job->title }}</h2>
                            </div>
                            @if($job->promote_front)
                                <span class="rounded-full bg-[#965995]/10 px-3 py-1 text-[11px] font-bold text-[#965995]">Featured</span>
                            @endif
                        </div>
                        <p class="mt-2 text-xs font-semibold text-[#351c42]/60">Job code: {{ $job->code ?: 'N/A' }}</p>

                        @if($job->description)
                            <p class="mt-3 text-sm leading-relaxed text-[#351c42]/80 line-clamp-4">{{ $job->description }}</p>
                        @endif

                        @if($job->key_skills)
                            <p class="mt-3 text-xs font-semibold text-[#351c42]/65">
                                <span class="font-bold text-[#351c42]/80">Skills:</span> {{ $job->key_skills }}
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="pt-2">
                {{ $jobs->links() }}
            </div>
        @endif
    </section>
@endsection
