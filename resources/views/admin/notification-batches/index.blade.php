@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Member notification logs</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Bulk sends from events, meetings, nominations, jobs, and polling — per member email, SMS, and WhatsApp status.</p>
            </div>
            <form method="GET" class="flex items-center gap-2 w-full md:max-w-md">
                <input type="search" name="q" value="{{ $q }}" placeholder="Search type or subject…"
                    class="flex-1 min-w-0 pl-3 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($batches->count() === 0)
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No batches yet</p>
                <p class="text-xs font-bold text-slate-500 mt-1">Send an invite or alert from the admin area; logs appear here after you queue notifications.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                        <tr>
                            <th class="px-5 py-4">ID</th>
                            <th class="px-5 py-4">Type</th>
                            <th class="px-5 py-4">Subject</th>
                            <th class="px-5 py-4">Recipients</th>
                            <th class="px-5 py-4">Chunks</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Started</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($batches as $batch)
                            <tr>
                                <td class="px-5 py-4 font-mono text-[11px] text-slate-600">#{{ $batch->id }}</td>
                                <td class="px-5 py-4 text-sm font-extrabold text-slate-900">{{ \App\Http\Controllers\GnatNotificationBatchController::typeLabel($batch->notification_type) }}</td>
                                <td class="px-5 py-4 text-[11px] font-bold text-slate-600 max-w-xs truncate" title="{{ $batch->entity_label }}">{{ $batch->entity_label ?: '—' }}</td>
                                <td class="px-5 py-4 text-[11px] font-bold text-slate-600">{{ (int) $batch->total_recipients }}</td>
                                <td class="px-5 py-4 text-[11px] font-bold text-slate-600">{{ (int) $batch->chunks_finished }} / {{ (int) $batch->chunks_total }}</td>
                                <td class="px-5 py-4">
                                    @php
                                        $st = $batch->status;
                                        $cls = match ($st) {
                                            'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                            'failed' => 'bg-rose-50 text-rose-800 border-rose-200',
                                            default => 'bg-amber-50 text-amber-800 border-amber-200',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-lg border text-[10px] font-black uppercase tracking-wide {{ $cls }}">{{ $st }}</span>
                                </td>
                                <td class="px-5 py-4 text-[11px] font-bold text-slate-500 whitespace-nowrap">{{ $batch->created_at?->format('d M Y, h:i A') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.notification-batches.show', $batch->id) }}" class="inline-flex px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-[10px] font-extrabold">View log</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $batches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
