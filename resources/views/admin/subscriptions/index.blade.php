@extends('admin.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Member Subscriptions</h1>
                <p class="text-xs font-bold text-slate-500 mt-1">View member subscriptions with transaction details.</p>
            </div>
            <form method="GET" class="flex items-center gap-2 flex-wrap">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search member / transaction / order"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold w-64 outline-none focus:ring-2 focus:ring-indigo-200">
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">All Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="successful" {{ $status === 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold">Search</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($transactions->count() === 0)
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-900">No subscriptions found</p>
                <p class="text-xs font-bold text-slate-500 mt-1">No payment transactions available for the selected filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <tr>
                            <th class="px-5 py-4">Member</th>
                            <th class="px-5 py-4">Transaction ID</th>
                            <th class="px-5 py-4">Order / Payment Ref</th>
                            <th class="px-5 py-4">Subscription Type</th>
                            <th class="px-5 py-4">Amount</th>
                            <th class="px-5 py-4">Payment Status</th>
                            <th class="px-5 py-4">Subscription Status</th>
                            <th class="px-5 py-4">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transactions as $tx)
                            @php
                                $key = $tx->user_id . '|' . $tx->razorpay_order_id;
                                $sub = $subscriptions->get($key);
                            @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-extrabold text-slate-900">{{ $tx->user->name ?? 'Member' }}</p>
                                    <p class="text-[11px] font-bold text-slate-500">{{ $tx->user->email ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-extrabold text-slate-800">#{{ $tx->id }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700 break-all">{{ $tx->razorpay_order_id ?: '-' }}</p>
                                    <p class="text-[11px] text-slate-500 break-all">{{ $tx->razorpay_payment_id ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700 uppercase">{{ $tx->type ?: '-' }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $tx->subscriptionPlan->payment_type ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-extrabold text-slate-800">INR {{ number_format((float) $tx->amount, 2) }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $tx->status === 'successful' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $tx->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $tx->status === 'failed' ? 'bg-rose-100 text-rose-700' : '' }}">
                                        {{ $tx->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($sub)
                                        <p class="text-[11px] font-bold text-slate-700 capitalize">{{ $sub->status }}</p>
                                        <p class="text-[11px] text-slate-500">
                                            {{ $sub->start_date?->format('d M Y') }} - {{ $sub->end_date?->format('d M Y') }}
                                        </p>
                                    @else
                                        <p class="text-[11px] font-bold text-slate-400">N/A</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-[11px] font-bold text-slate-700">{{ $tx->paid_at?->format('d M Y h:i A') ?? '-' }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
