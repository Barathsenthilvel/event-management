@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[24px] border border-white bg-linear-to-br from-white via-white to-indigo-50/40 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Donation Payments</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">All donation amounts given by donors.</p>
            </div>
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search donor / purpose..."
                    class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20 w-64">
                <select name="status"
                    class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/20">
                    @foreach(['all','pending','successful','failed','refunded'] as $s)
                        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button
                    class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-900">Payments</p>
                <p class="text-[11px] font-bold text-slate-500 mt-1">Total: {{ $payments->total() }}</p>
            </div>
        </div>

        @if($payments->count() === 0)
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No donation payments yet</p>
                <p class="mt-1 text-xs font-bold text-slate-500">When donors complete payments, they will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Donor</th>
                            <th class="px-6 py-4">Campaign</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($payments as $p)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-slate-800">{{ $p->created_at?->format('d M Y') }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $p->created_at?->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-extrabold text-slate-900">
                                        {{ $p->donor_name ?? $p->user?->name ?? '—' }}
                                    </p>
                                    <p class="text-[11px] font-bold text-slate-500">
                                        {{ $p->donor_email ?? $p->user?->email ?? '' }}
                                    </p>
                                    @if($p->donor_mobile)
                                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">{{ $p->donor_mobile }}</p>
                                    @endif
                                    @if(!empty($p->meta['wants_membership']))
                                        <p class="mt-1"><span class="rounded-full bg-indigo-100 px-2 py-0.5 text-[9px] font-black uppercase tracking-wide text-indigo-800">Interested in member</span></p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $p->donation?->purpose ?? 'General' }}
                                    </p>
                                    @if($p->payment_id)
                                        <p class="text-[10px] font-mono text-slate-500 mt-0.5">
                                            {{ $p->payment_id }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="text-sm font-extrabold text-slate-900">
                                        {{ $p->currency }} {{ number_format((float) $p->amount, 2) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($p->status) {
                                            'successful' => 'bg-emerald-100 text-emerald-800',
                                            'pending' => 'bg-amber-100 text-amber-900',
                                            'failed', 'refunded' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

