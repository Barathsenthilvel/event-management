@extends('member.layouts.app')

@section('content')
@if(Auth::user()?->profile_completed && !Auth::user()?->is_approved)
    <div x-data="{ open: true }">
        <div x-show="open" x-cloak
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[110] flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="bg-white border border-slate-100 rounded-[28px] shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="p-7 bg-linear-to-br from-slate-900 via-slate-900 to-indigo-700 text-white">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-widest text-white/75">Approval pending</p>
                            <h3 class="mt-1 text-xl font-extrabold tracking-tight text-white">Please wait for admin approval</h3>
                            <p class="mt-1 text-xs font-bold text-white/75">
                                We received your profile details. Once the admin approves, you can purchase membership plans.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-7 space-y-4">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                        <p class="text-xs font-extrabold text-slate-900">What happens next?</p>
                        <ul class="mt-3 space-y-2 text-xs font-bold text-slate-600">
                            <li>- Admin will verify your documents and profile information.</li>
                            <li>- After approval, the Membership menu and subscription plans will be enabled.</li>
                            <li>- If anything is missing, admin may ask you to update your profile.</li>
                        </ul>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-1">
                        <a href="{{ route('member.profile.edit') }}"
                           class="px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-extrabold shadow-sm transition-all">
                            Review Profile
                        </a>
                        <button type="button" @click="open=false"
                            class="px-8 py-3 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all">
                            OK
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endif

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

    @if(Auth::user()?->profile_completed && !Auth::user()?->is_approved)
        <div x-data="{ open: {{ session('approval_pending_modal') ? 'true' : 'false' }} }">
            <div x-show="open" x-cloak
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[110] flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div class="bg-white border border-slate-100 rounded-[28px] shadow-2xl w-full max-w-lg overflow-hidden">
                    <div class="p-7 pb-4 border-b border-slate-100 bg-white">
                        <div class="flex flex-col items-center text-center gap-3">
                            <div class="h-14 w-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-lg">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Approval pending</p>
                                <h3 class="mt-1 text-xl font-extrabold tracking-tight text-slate-900">Please wait for admin approval</h3>
                                <p class="mt-1 text-xs font-bold text-slate-600">
                                    We received your profile details. Once the admin approves, you can purchase membership plans.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-7 space-y-4">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <p class="text-xs font-extrabold text-slate-900">What happens next?</p>
                            <ul class="mt-3 space-y-2 text-xs font-bold text-slate-600">
                                <li>- Admin will verify your documents and profile information.</li>
                                <li>- After approval, the Membership menu and subscription plans will be enabled.</li>
                                <li>- If anything is missing, admin may ask you to update your profile.</li>
                            </ul>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-1">
                            <a href="{{ route('member.profile.edit') }}"
                               class="px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-extrabold shadow-sm transition-all">
                                Review Profile
                            </a>
                            <button type="button" @click="open=false"
                                class="px-8 py-3 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all">
                                OK
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
@endsection

