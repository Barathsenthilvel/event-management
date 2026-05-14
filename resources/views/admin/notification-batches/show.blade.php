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
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <a href="{{ route('admin.notification-batches.index') }}" class="text-[11px] font-extrabold text-indigo-600 hover:text-indigo-800">← All batches</a>
                <h1 class="text-xl font-extrabold text-slate-900 mt-2">Batch #{{ $batch->id }}</h1>
                <p class="text-sm font-bold text-slate-600 mt-1">{{ \App\Http\Controllers\GnatNotificationBatchController::typeLabel($batch->notification_type) }}</p>
                @if($batch->entity_label)
                    <p class="text-xs font-bold text-slate-500 mt-1">{{ $batch->entity_label }}</p>
                @endif
            </div>
            <div class="text-right space-y-1">
                @php
                    $st = $batch->status;
                    $cls = match ($st) {
                        'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'failed' => 'bg-rose-50 text-rose-800 border-rose-200',
                        default => 'bg-amber-50 text-amber-800 border-amber-200',
                    };
                @endphp
                <div><span class="inline-flex px-2 py-0.5 rounded-lg border text-[10px] font-black uppercase tracking-wide {{ $cls }}">{{ $st }}</span></div>
                <p class="text-[10px] font-bold text-slate-500">Chunks {{ (int) $batch->chunks_finished }} / {{ (int) $batch->chunks_total }}</p>
                <p class="text-[10px] font-bold text-slate-500">Recipients {{ (int) $batch->total_recipients }}</p>
                @if($batch->initiator)
                    <p class="text-[10px] font-bold text-slate-500">By {{ $batch->initiator->name }}</p>
                @endif
            </div>
        </div>
        @if(is_array($batch->meta) && count($batch->meta) > 0)
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-4 text-[11px] font-bold text-slate-600 space-y-1">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Request options</p>
                @foreach($batch->meta as $k => $v)
                    @if($k !== 'failure_message')
                        <p><span class="text-slate-400">{{ str_replace('_', ' ', $k) }}:</span> {{ is_bool($v) ? ($v ? 'yes' : 'no') : $v }}</p>
                    @endif
                @endforeach
                @if(!empty($batch->meta['failure_message']))
                    <p class="text-rose-700 pt-2 border-t border-slate-200 mt-2">{{ $batch->meta['failure_message'] }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-extrabold text-slate-900">Per-member delivery</h2>
            <p class="text-[10px] font-bold text-slate-500 mt-1">Email, SMS, and WhatsApp (WhatsApp uses the configured MSG91 flow when enabled).</p>
        </div>
        @if($logs->count() === 0)
            <div class="p-10 text-center text-sm font-bold text-slate-500">No delivery rows yet — wait for the queue worker to process this batch.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50">
                        <tr>
                            <th class="px-5 py-4">Member</th>
                            <th class="px-5 py-4">Email</th>
                            <th class="px-5 py-4">SMS</th>
                            <th class="px-5 py-4">WhatsApp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($logs as $log)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-extrabold text-slate-900">{{ $log->user?->name ?? 'User #'.$log->user_id }}</p>
                                    <p class="text-[10px] font-bold text-slate-500 truncate max-w-[200px]" title="{{ $log->user?->email }}">{{ $log->user?->email ?? '—' }}</p>
                                    <p class="text-[10px] font-bold text-slate-500">{{ $log->user?->mobile ?? '—' }}</p>
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
</div>
@endsection
