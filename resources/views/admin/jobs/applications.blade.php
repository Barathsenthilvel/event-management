@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 uppercase">{{ $job->title }}</h1>
            <p class="text-xs font-bold text-slate-500 mt-1">Posted On Date & Time</p>
            <p class="text-xs font-bold text-slate-700 mt-1">No. of Seats - {{ $job->no_of_openings }} | Applied - {{ $applications->total() }}</p>
        </div>
        <a href="{{ route('admin.jobs.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold">Back</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
        <div class="flex items-center justify-end mb-3">
            <form method="GET">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-56 outline-none focus:ring-2 focus:ring-indigo-200">
            </form>
        </div>
        <div class="space-y-2">
            @forelse($applications as $application)
                <div class="flex items-center gap-3 border border-slate-200 rounded-xl p-3">
                    <div class="w-8 h-8 rounded border border-slate-400 flex items-center justify-center text-sm">{{ strtoupper(substr($application->user->name ?? 'M', 0, 1)) }}</div>
                    <div class="w-48">
                        <p class="text-sm font-extrabold">{{ $application->user->name ?? 'Member' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $application->job->code }}</p>
                    </div>
                    <div class="w-44 text-[11px] text-slate-600">{{ $application->user->email ?? '-' }}<br>{{ $application->user->mobile ?? '-' }}</div>
                    <div class="w-28 text-[11px] text-slate-700">Member</div>
                    <div class="w-40 text-[11px] text-slate-700">{{ optional($application->submitted_at)->format('d M Y h:i A') ?: '-' }}</div>
                    <div class="flex-1">
                        <form method="POST" action="{{ route('admin.jobs.applications.status', [$job->id, $application->id]) }}" class="inline-flex items-center gap-2">
                            @csrf
                            <select name="application_status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold">
                                @foreach(['pending' => 'Pending','selected' => 'Selected','not_selected' => 'Not Selected','joined' => 'Joined','not_joined' => 'Not Joined'] as $k => $v)
                                    <option value="{{ $k }}" {{ $application->application_status === $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-slate-400">{{ $application->status_emailed_at ? 'mail-triggered' : '' }}</span>
                        </form>
                    </div>
                    <div>
                        @php $resumePath = $application->resume_path ?: ($application->user->educational_certificate_path ?? null); @endphp
                        @if($resumePath)
                            <a href="{{ asset('storage/' . $resumePath) }}" target="_blank" class="text-[11px] font-extrabold text-indigo-700">Resume</a>
                        @else
                            <span class="text-[11px] text-slate-400">Resume</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-500 font-bold">No applications found.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $applications->links() }}</div>
    </div>
</div>
@endsection

