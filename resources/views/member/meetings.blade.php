@extends('member.layouts.portal')

@section('title', 'Meetings — GNAT Association')

@section('portal_main_id', 'member-meetings-main')

@section('content')
    <header class="scroll-mt-28 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Meetings</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Your meetings</h1>
            <p class="mt-1 max-w-2xl text-sm text-[#351c42]/65">Open meetings and your invited meeting schedules.</p>
        </div>
        <a href="{{ route('member.dashboard') }}" class="shrink-0 text-sm font-semibold text-[#965995] hover:text-[#351c42]">← Back to dashboard</a>
    </header>

    @if(($upcomingMeetings ?? collect())->isNotEmpty())
        @include('member.partials.dashboard-meetings', ['upcomingMeetings' => $upcomingMeetings])
    @else
        <section class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-6 shadow-md sm:p-8">
            <p class="rounded-2xl border border-dashed border-[#351c42]/20 bg-[#faf9fc] px-6 py-8 text-center text-sm font-semibold text-[#351c42]/70">
                No upcoming meetings right now.
            </p>
        </section>
    @endif
@endsection
