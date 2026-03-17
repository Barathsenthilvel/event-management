@extends('member.layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
    <div class="rounded-[28px] border border-white bg-linear-to-br from-white via-white to-indigo-50/40 shadow-sm p-6 md:p-7">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Checkout</h1>
                <p class="mt-1 text-sm text-slate-500">Confirm your selected plan and proceed to payment.</p>
            </div>
            <a href="{{ route('member.subscription.index') }}"
                class="px-6 py-3 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-extrabold shadow-lg transition-all text-center">
                Change Plan
            </a>
        </div>
    </div>

    @php
        $paymentLabel = match($plan->payment_type) {
            'monthly' => 'Monthly',
            'bi_monthly' => 'Bi - Monthly',
            'quarterly' => 'Quarterly',
            'half_yearly' => 'Half Yearly',
            'yearly' => 'Yearly',
            default => ucfirst((string) $plan->payment_type),
        };
        $isNew = ($plan->subscription_type === 'New');
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7">
            <div class="rounded-[28px] border border-slate-100 bg-white shadow-sm overflow-hidden">
                <div class="p-6 bg-linear-to-br from-indigo-600 to-slate-900 text-white">
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/80">
                        {{ $plan->subscription_type }} • {{ $paymentLabel }}
                    </p>
                    <h2 class="mt-1 text-lg md:text-xl font-extrabold tracking-tight">
                        {{ $isNew ? 'New Members' : 'Renewal' }} Plan
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Membership fee</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900">₹ {{ number_format((float)$plan->membership_fee, 0) }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Registration fee</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900">
                                @if($isNew && $plan->registration_fee_enabled)
                                    ₹ {{ number_format((float)$plan->registration_fee, 0) }}
                                @else
                                    <span class="text-slate-400 font-bold">₹ 0</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-white p-5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total payable</p>
                            <p class="mt-1 text-xs font-bold text-slate-500">You will be charged for the selected plan.</p>
                        </div>
                        <p class="text-xl font-extrabold text-slate-900">₹ {{ number_format((float)$payableAmount, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="rounded-[28px] border border-slate-100 bg-white shadow-sm p-6 md:p-7">
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Payment</h3>
                <p class="mt-1 text-xs text-slate-500">Payment gateway integration can be connected here (Razorpay/Stripe/etc.).</p>

                <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-extrabold text-amber-800">Next step</p>
                    <p class="mt-1 text-[11px] font-bold text-amber-800/80">
                        Tell me which payment gateway you want (Razorpay / Stripe / Cashfree / PayU). I’ll connect the real “Proceed to pay” button.
                    </p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('member.dashboard') }}"
                        class="inline-flex w-full items-center justify-center px-10 py-4 bg-slate-900 hover:bg-indigo-600 text-white rounded-2xl font-extrabold shadow-lg transition-all">
                        Continue (Temporary)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

