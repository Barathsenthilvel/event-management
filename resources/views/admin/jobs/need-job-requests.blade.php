@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Need Job Requests</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">Members who submitted the Need Job form.</p>
            </div>
            <a href="{{ route('admin.jobs.index') }}" class="inline-flex px-4 py-2 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-700 hover:bg-slate-50">
                Back to Jobs
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-4">
        <form method="GET" class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search name, email, mobile, position"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
            <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">All status</option>
                @foreach(['new' => 'New', 'reviewed' => 'Reviewed', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $k => $v)
                    <option value="{{ $k }}" {{ $status === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-50">
                    <tr>
                        <th class="px-4 py-3">Member</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Profile</th>
                        <th class="px-4 py-3">Details</th>
                        <th class="px-4 py-3">Resume</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $row)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-extrabold text-slate-800">{{ $row->name }}</p>
                                <p class="text-[11px] text-slate-500">User: {{ $row->user?->name ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <p>{{ $row->email }}</p>
                                <p class="text-[11px] text-slate-500">{{ $row->mobile ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <p><span class="font-bold">Qualification:</span> {{ $row->qualification ?: '-' }}</p>
                                <p class="text-[11px]"><span class="font-bold">Position:</span> {{ $row->position_looking_for ?: '-' }}</p>
                                <p class="text-[11px]"><span class="font-bold">Experience:</span> {{ $row->experience ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700 max-w-xs">
                                <p class="line-clamp-3">{{ $row->details ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($row->resume_path)
                                    <a href="{{ asset('storage/' . $row->resume_path) }}" target="_blank" class="text-indigo-700 font-extrabold hover:underline">View PDF</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.jobs.need-job.requests.status', $row->id) }}">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-200 px-2 py-1 text-[11px] font-bold">
                                        @foreach(['new' => 'New', 'reviewed' => 'Reviewed', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $k => $v)
                                            <option value="{{ $k }}" {{ $row->status === $k ? 'selected' : '' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->created_at?->format('d M Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 font-bold">No requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>
</div>
@endsection

