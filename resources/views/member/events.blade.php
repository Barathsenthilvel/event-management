@extends('member.layouts.portal')

@section('title', 'Events — GNAT Association')

@section('portal_main_id', 'member-events-main')

@section('content')
    <header class="scroll-mt-28 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Events</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Your events</h1>
            <p class="mt-1 max-w-2xl text-sm text-[#351c42]/65">Events you’ve shown interest in — attendance and certificates appear here.</p>
        </div>
        <a href="{{ route('member.dashboard') }}" class="shrink-0 text-sm font-semibold text-[#965995] hover:text-[#351c42]">← Back to dashboard</a>
    </header>

    @include('member.partials.member-events-panel', [
        'myEventInvites' => $myEventInvites,
    ])
@endsection
