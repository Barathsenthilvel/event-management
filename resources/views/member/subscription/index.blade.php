@extends('member.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[28px] border border-white bg-linear-to-br from-white via-white to-indigo-50/40 shadow-sm p-6 md:p-7">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Hello, {{ $user->first_name ?? 'Member' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Choose a subscription plan from the admin-added list and continue.</p>
            </div>
            <a href="{{ route('member.dashboard') }}"
                class="px-6 py-3 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all text-center">
                Back to Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-12">
            <div class="bg-white p-6 md:p-8 rounded-[28px] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Subscription Plans</h2>
                        <p class="text-xs text-slate-500">Fees are shown in INR. Registration fee applies only for new subscription (if enabled).</p>
                    </div>
                    <div class="shrink-0">
                        <span class="inline-flex items-center gap-2 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-2 text-[11px] font-extrabold text-indigo-700">
                            Active plans:
                            <span class="rounded-xl bg-white px-2 py-0.5 text-indigo-800 border border-indigo-100">{{ (int) ($settingsCount ?? 0) }}</span>
                        </span>
                    </div>
                </div>

                @if(session('error'))
                    <div class="mb-5 rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm text-rose-700 font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('member.subscription.checkout') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @forelse($plans as $plan)
                            @php
                                $isNew = ($plan->subscription_type === 'New');
                                $paymentLabel = match($plan->payment_type) {
                                    'monthly' => 'Monthly',
                                    'bi_monthly' => 'Bi - Monthly',
                                    'quarterly' => 'Quarterly',
                                    'half_yearly' => 'Half Yearly',
                                    'yearly' => 'Yearly',
                                    default => ucfirst((string) $plan->payment_type),
                                };
                                $registrationFee = ($isNew && $plan->registration_fee_enabled) ? (float) $plan->registration_fee : 0.0;
                                $payable = (float) $plan->membership_fee + $registrationFee;
                            @endphp

                            <label class="group block rounded-[28px] border border-slate-100 bg-white shadow-sm hover:shadow-lg transition-shadow cursor-pointer overflow-hidden">
                                <input
                                    type="radio"
                                    name="membership_setting_id"
                                    value="{{ $plan->id }}"
                                    class="sr-only peer"
                                    {{ old('membership_setting_id') == $plan->id ? 'checked' : '' }}
                                />

                                <div class="p-6 bg-linear-to-br from-indigo-600 to-slate-900 text-white">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-white/80">
                                                {{ $plan->subscription_type }} • {{ $paymentLabel }}
                                            </p>
                                            <h3 class="mt-1 text-lg font-extrabold tracking-tight">
                                                {{ $isNew ? 'New Members' : 'Renewal' }} Plan
                                            </h3>
                                        </div>
                                        <span class="inline-flex items-center rounded-2xl bg-white/10 px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest border border-white/15">
                                            Select
                                        </span>
                                    </div>
                                </div>

                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Membership fee</p>
                                            <p class="mt-1 text-sm font-extrabold text-slate-900">
                                                ₹ {{ number_format((float)$plan->membership_fee, 0) }}
                                                <span class="text-[10px] font-bold text-slate-500">({{ $paymentLabel }})</span>
                                            </p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Registration fee</p>
                                            <p class="mt-1 text-sm font-extrabold text-slate-900">
                                                @if($isNew)
                                                    ₹ {{ number_format((float)$plan->registration_fee, 0) }}
                                                    <span class="text-[10px] font-bold {{ $plan->registration_fee_enabled ? 'text-emerald-600' : 'text-slate-400' }}">
                                                        {{ $plan->registration_fee_enabled ? 'ENABLED' : 'DISABLED' }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 font-bold">N/A</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-100 bg-white p-4 flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Payable amount</p>
                                            <p class="mt-1 text-[10px] font-bold text-slate-500">{{ $isNew && $plan->registration_fee_enabled ? 'Membership fee + Registration fee' : 'Membership fee' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-extrabold text-slate-900">₹ {{ number_format($payable, 0) }}</p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-100 bg-indigo-50/40 p-4 hidden peer-checked:block">
                                        <p class="text-xs font-extrabold text-indigo-700">Selected</p>
                                        <p class="text-[10px] font-bold text-indigo-700/80 mt-1">Click “Pay Now” to continue to checkout.</p>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="md:col-span-2 lg:col-span-3 rounded-[28px] border border-slate-100 bg-slate-50 p-10 text-center">
                                <h3 class="text-lg font-extrabold text-slate-900">No active plans found</h3>
                                <p class="mt-1 text-sm text-slate-500">Ask admin to add and activate subscription settings.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="inline-flex items-center justify-center px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-extrabold shadow-lg shadow-indigo-200 transition-all">
                            Pay Now
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

