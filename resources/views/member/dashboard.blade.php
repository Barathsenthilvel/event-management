@extends('member.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Dashboard</h1>
        <p class="text-sm text-slate-500">Hello, {{ Auth::user()->first_name ?? 'Member' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-sm font-bold text-slate-600 mb-2">Profile</h3>
            <p class="text-2xl font-bold text-slate-900">{{ $profileIncomplete ? 'Incomplete' : 'Completed' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-sm font-bold text-slate-600 mb-2">Email</h3>
            <p class="text-sm font-bold text-slate-900 break-all">{{ Auth::user()->email }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-sm font-bold text-slate-600 mb-2">Mobile</h3>
            <p class="text-sm font-bold text-slate-900">{{ Auth::user()->mobile ?? '-' }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">Membership</h3>
                @if(!empty($activeSubscription))
                    <p class="text-lg font-extrabold text-slate-900">
                        Active • {{ $activeSubscription->subscription_type }} • {{ ucfirst(str_replace('_', ' ', (string) $activeSubscription->payment_type)) }}
                    </p>
                    <p class="text-xs font-bold text-slate-500 mt-1">
                        Valid till: {{ optional($activeSubscription->end_date)->format('M d, Y') }}
                    </p>
                @else
                    <p class="text-lg font-extrabold text-slate-900">No active membership</p>
                    <p class="text-xs font-bold text-slate-500 mt-1">Subscribe to activate your membership.</p>
                @endif
            </div>
            <div class="shrink-0 flex gap-3">
                @if(!empty($activeSubscription))
                    <a href="{{ route('member.subscription.index', ['type' => 'Renewal']) }}"
                       class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-lg transition-all text-center">
                        Renew
                    </a>
                @endif
                <a href="{{ route('member.subscription.index') }}"
                   class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold shadow-lg transition-all text-center">
                    View Plans
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-600">Recent Payments</h3>
                <p class="text-xs font-bold text-slate-400 mt-1">Last 10 membership transactions.</p>
            </div>
            <a href="{{ route('member.subscription.index') }}"
               class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white text-[11px] font-extrabold shadow-lg transition-all text-center">
                Make a Payment
            </a>
        </div>

        @if(($transactions ?? collect())->isEmpty())
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6 text-center">
                <p class="text-sm font-extrabold text-slate-900">No transactions yet</p>
                <p class="text-xs font-bold text-slate-500 mt-1">Once you purchase/renew a plan, your payments will show here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <tr>
                            <th class="py-3 pr-4">Date</th>
                            <th class="py-3 pr-4">Plan</th>
                            <th class="py-3 pr-4">Type</th>
                            <th class="py-3 pr-4">Amount</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 text-right">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transactions as $t)
                            @php
                                $planLabel = trim(($t->subscriptionPlan?->subscription_type ?? '') . ' • ' . ucfirst(str_replace('_',' ', (string) ($t->subscriptionPlan?->payment_type ?? ''))));
                                $status = strtolower((string) $t->status);
                            @endphp
                            <tr>
                                <td class="py-3 pr-4 font-bold text-slate-700">
                                    {{ optional($t->paid_at ?? $t->created_at)->format('M d, Y') }}
                                </td>
                                <td class="py-3 pr-4 font-extrabold text-slate-900">
                                    {{ $planLabel ?: '—' }}
                                </td>
                                <td class="py-3 pr-4 font-bold text-slate-600 uppercase">
                                    {{ $t->type }}
                                </td>
                                <td class="py-3 pr-4 font-extrabold text-slate-900">
                                    ₹ {{ number_format((float) $t->amount, 0) }}
                                </td>
                                <td class="py-3 pr-4">
                                    @if($status === 'successful')
                                        <span class="inline-flex items-center rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700">Successful</span>
                                    @elseif($status === 'pending')
                                        <span class="inline-flex items-center rounded-xl border border-amber-100 bg-amber-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-amber-800">Pending</span>
                                    @else
                                        <span class="inline-flex items-center rounded-xl border border-slate-100 bg-slate-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-600">{{ $t->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    @if($status === 'successful')
                                        <a href="{{ route('member.subscription.invoice', $t->id) }}" target="_blank"
                                           class="inline-flex items-center justify-center px-4 py-2 rounded-2xl border border-slate-200 bg-white text-[11px] font-extrabold text-slate-700 hover:bg-slate-50">
                                            Download
                                        </a>
                                    @else
                                        <span class="text-[11px] font-bold text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($profileIncomplete)
        <div x-data="{ open: true }">
            <div x-show="open" x-cloak
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div class="bg-white border border-slate-100 rounded-[28px] shadow-2xl w-full max-w-md p-10 text-center">
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Hello, Member</h2>
                    <p class="text-slate-600 mb-8">Your Profile is incomplete, Please Complete your Profile to be a part of GNAT Member</p>
                    <a href="{{ route('member.profile.edit') }}"
                        class="inline-flex items-center justify-center px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all">
                        Update
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

