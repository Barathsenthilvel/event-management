@extends('admin.layouts.app')

@section('content')
@php
    $channelPill = function (?string $status, ?string $error) {
        $status = $status ?? '—';
        $cls = match ($status) {
            'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'failed' => 'bg-rose-50 text-rose-800 border-rose-200',
            'skipped' => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
        $title = $error ? $error : $status;
        return '<span class="inline-flex max-w-full truncate px-2 py-0.5 rounded-lg border text-[10px] font-black uppercase tracking-wide '.$cls.'" title="'.e($title).'">'.e($status).'</span>';
    };
@endphp

<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Member notification logs</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">Transactional & account notifications across all 32 scenarios — Email, SMS, and MSG91 WhatsApp delivery status.</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2 w-full md:max-w-xl">
                <input type="hidden" name="view" value="{{ $view ?? 'batches' }}" />
                <input type="search" name="q" value="{{ $q }}" placeholder="Search member, email, mobile, or scenario…"
                    class="flex-1 min-w-[180px] pl-3 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold bg-white outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">All statuses</option>
                    <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>Failed delivery</option>
                    <option value="success" {{ ($status ?? '') === 'success' ? 'selected' : '' }}>Successful</option>
                    <option value="skipped" {{ ($status ?? '') === 'skipped' ? 'selected' : '' }}>Skipped</option>
                </select>
                <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Filter</button>
            </form>
        </div>

        {{-- Navigation View Tabs --}}
        <div class="flex items-center gap-2 border-t border-slate-100 pt-3">
            <a href="{{ route('admin.notification-batches.index', ['view' => 'batches', 'status' => $status, 'q' => $q]) }}"
               class="px-3.5 py-1.5 rounded-xl border text-xs font-extrabold transition-all {{ ($view ?? 'batches') === 'batches' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Campaign Batches
            </a>
            <a href="{{ route('admin.notification-batches.index', ['view' => 'logs', 'status' => $status, 'q' => $q]) }}"
               class="px-3.5 py-1.5 rounded-xl border text-xs font-extrabold transition-all {{ ($view ?? '') === 'logs' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                All Member Logs (32 Scenarios)
            </a>
        </div>
    </div>

    @if(($view ?? '') === 'logs')
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            @if(!$logs || $logs->count() === 0)
                <div class="p-10 text-center">
                    <p class="text-sm font-extrabold text-slate-900">No delivery logs found</p>
                    <p class="text-xs font-bold text-slate-500 mt-1">Logs for all 32 member transactional & account notification scenarios appear here as notifications are dispatched.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                            <tr>
                                <th class="px-5 py-4">Member</th>
                                <th class="px-5 py-4">Notification Scenario</th>
                                <th class="px-5 py-4">Email</th>
                                <th class="px-5 py-4">SMS</th>
                                <th class="px-5 py-4">WhatsApp</th>
                                <th class="px-5 py-4">Sent At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($logs as $log)
                                <tr>
                                    <td class="px-5 py-4">
                                        <p class="text-sm font-extrabold text-slate-900">{{ $log->user?->name ?: (trim(($log->user?->first_name ?? '').' '.($log->user?->last_name ?? ''))) ?: 'User #'.$log->user_id }}</p>
                                        <p class="text-[10px] font-bold text-slate-500 truncate max-w-[200px]" title="{{ $log->user?->email }}">{{ $log->user?->email ?: '—' }}</p>
                                        <p class="text-[10px] font-bold text-slate-500">{{ $log->user?->mobile ?: '—' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="text-xs font-extrabold text-slate-900">{{ \App\Http\Controllers\GnatNotificationBatchController::typeLabel($log->batch?->notification_type ?? '') }}</p>
                                        @if($log->batch?->entity_label)
                                            <p class="text-[10px] font-bold text-slate-500 truncate max-w-xs" title="{{ $log->batch->entity_label }}">{{ $log->batch->entity_label }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 align-top space-y-1">
                                        {!! $channelPill($log->email_status, $log->email_error) !!}
                                    </td>
                                    <td class="px-5 py-4 align-top space-y-1">
                                        {!! $channelPill($log->sms_status, $log->sms_error) !!}
                                    </td>
                                    <td class="px-5 py-4 align-top space-y-1">
                                        {!! $channelPill($log->whatsapp_status, $log->whatsapp_error) !!}
                                    </td>
                                    <td class="px-5 py-4 text-[11px] font-bold text-slate-500 whitespace-nowrap">
                                        {{ $log->created_at?->format('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            @if(!$batches || $batches->count() === 0)
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
    @endif
</div>
@endsection
