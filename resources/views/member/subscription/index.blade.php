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

    @if(!empty($activeSubscription))
        <div class="rounded-[28px] border border-emerald-100 bg-emerald-50/60 shadow-sm p-6 md:p-7">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Active subscription</p>
                    <p class="mt-1 text-sm font-extrabold text-slate-900">
                        {{ $activeSubscription->subscription_type }} • {{ ucfirst(str_replace('_', ' ', (string) $activeSubscription->payment_type)) }}
                    </p>
                    <p class="mt-1 text-xs font-bold text-emerald-800/80">
                        Valid till: {{ optional($activeSubscription->end_date)->format('M d, Y') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('member.subscription.index', ['type' => 'Renewal']) }}"
                       class="px-6 py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-extrabold shadow-lg transition-all text-center">
                        Renew Membership
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-12">
            <div class="bg-white p-6 md:p-8 rounded-[28px] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Subscription Plans</h2>
                        <p class="text-xs text-slate-500">
                            Fees are shown in INR. Registration fee applies only for new subscription (if enabled).
                            @if(in_array(($filterType ?? null), ['New','Renewal'], true))
                                <span class="font-extrabold text-indigo-700">Showing: {{ $filterType }}</span>
                            @endif
                        </p>
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

                <form id="subscription-form" class="space-y-6">
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

                            <label class="group block rounded-[28px] border border-slate-100 bg-white shadow-sm hover:shadow-lg transition-shadow cursor-pointer overflow-hidden"
                                   data-payable="{{ $payable }}">
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
                        <button type="button" onclick="startRazorpayCheckout()"
                            class="inline-flex items-center justify-center px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-extrabold shadow-lg shadow-indigo-200 transition-all">
                            Pay Now
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Renewal blocked modal -->
<div id="renewal-blocked-modal" class="fixed inset-0 z-[130] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-[28px] bg-white border border-slate-100 shadow-2xl overflow-hidden">
            <div class="p-6 bg-linear-to-br from-slate-900 via-slate-900 to-amber-700 text-white">
                <div class="flex flex-col items-center text-center">
                    <div class="h-14 w-14 rounded-2xl bg-white/10 border border-white/15 shadow-lg flex items-center justify-center">
                        <div class="h-11 w-11 rounded-xl bg-white/95 flex items-center justify-center shadow-sm">
                            <svg class="h-6 w-6 text-amber-700" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-white/75">Renewal not available</p>
                    <h3 class="mt-1 text-xl font-extrabold tracking-tight text-white">Please renew after validity completion</h3>
                    <p class="mt-1 text-xs font-bold text-white/75">Renewal will be enabled once your current validity ends.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm font-extrabold text-slate-900">Message</p>
                    <p class="mt-1 text-xs font-bold text-slate-600" id="renewal-blocked-message">
                        {{ session('renewal_blocked_message') }}
                    </p>
                </div>
                @if(!empty($activeSubscription) && !empty($activeSubscription->end_date))
                    <div class="rounded-2xl border border-slate-100 bg-white p-4 flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-500">Current validity ends on</p>
                        <p class="text-xs font-extrabold text-slate-900">{{ optional($activeSubscription->end_date)->format('M d, Y') }}</p>
                    </div>
                @endif
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="closeRenewalBlockedModal()"
                        class="inline-flex items-center justify-center px-8 py-3 bg-slate-900 hover:bg-indigo-600 text-white rounded-2xl font-extrabold shadow-lg transition-all">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2-step payment success modal -->
<div id="payment-success-modal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-[28px] bg-white border border-slate-100 shadow-2xl overflow-hidden">
            <div class="p-6 bg-linear-to-br from-emerald-600 to-slate-900 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-white/80">Payment Successful</p>
                <h3 class="mt-1 text-lg font-extrabold tracking-tight" id="success-title">Thank you!</h3>
                <p class="mt-2 text-xs font-bold text-white/80" id="success-subtitle"></p>
            </div>

            <div class="p-6 space-y-4">
                <div id="success-step-1">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Plan purchased</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900" id="plan-summary"></p>
                        <p class="mt-1 text-xs font-bold text-slate-600" id="payment-id"></p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-white p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Amount paid</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900" id="amount-paid"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Valid till</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900" id="valid-till"></p>
                        </div>
                    </div>
                </div>

                <div id="success-step-2" class="hidden">
                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-700">Plan details</p>
                        <ul class="mt-3 space-y-2 text-xs font-bold text-indigo-900/80">
                            <li><span class="font-black text-indigo-900">Subscription Type:</span> <span id="detail-subscription-type"></span></li>
                            <li><span class="font-black text-indigo-900">Cycle:</span> <span id="detail-payment-type"></span></li>
                            <li><span class="font-black text-indigo-900">Membership Fee:</span> <span id="detail-membership-fee"></span></li>
                            <li><span class="font-black text-indigo-900">Registration Fee:</span> <span id="detail-registration-fee"></span></li>
                            <li><span class="font-black text-indigo-900">Grace Period (days):</span> <span id="detail-grace-period"></span></li>
                        </ul>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-white p-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Next</p>
                        <p class="mt-1 text-xs font-bold text-slate-600">Click below to continue to your membership dashboard.</p>
                    </div>
                </div>

                <div class="flex gap-3 justify-end pt-2">
                    <button id="success-btn" type="button"
                        class="inline-flex items-center justify-center px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-extrabold shadow-lg transition-all">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
let successModalStep = 1;
let postSuccessRedirectUrl = "{{ route('member.dashboard') }}";

function openRenewalBlockedModal() {
    const modal = document.getElementById('renewal-blocked-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeRenewalBlockedModal() {
    const modal = document.getElementById('renewal-blocked-modal');
    if (modal) modal.classList.add('hidden');
}

function formatInr(n) {
    const v = Number(n || 0);
    return '₹ ' + v.toLocaleString('en-IN', { maximumFractionDigits: 0 });
}

function humanCycle(s) {
    return String(s || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function openSuccessModal(payload) {
    const modal = document.getElementById('payment-success-modal');
    if (!modal) return;

    // Step 1 data
    document.getElementById('success-subtitle').textContent = payload?.message || '';
    document.getElementById('plan-summary').textContent =
        `${payload?.plan?.subscription_type || ''} • ${humanCycle(payload?.plan?.payment_type)} Plan`;
    document.getElementById('payment-id').textContent =
        `Razorpay Payment ID: ${payload?.razorpay_payment_id || '-'}`;
    document.getElementById('amount-paid').textContent =
        `${formatInr(payload?.subscription?.amount)} ${payload?.subscription?.currency || 'INR'}`;
    document.getElementById('valid-till').textContent = payload?.subscription?.end_date || '-';

    // Step 2 data
    document.getElementById('detail-subscription-type').textContent = payload?.plan?.subscription_type || '-';
    document.getElementById('detail-payment-type').textContent = humanCycle(payload?.plan?.payment_type);
    document.getElementById('detail-membership-fee').textContent = formatInr(payload?.plan?.membership_fee);
    const regFee = (payload?.plan?.subscription_type === 'New' && payload?.plan?.registration_fee_enabled)
        ? payload?.plan?.registration_fee
        : 0;
    document.getElementById('detail-registration-fee').textContent = formatInr(regFee);
    document.getElementById('detail-grace-period').textContent = String(payload?.plan?.grace_period ?? 0);

    // Reset steps
    successModalStep = 1;
    document.getElementById('success-step-1').classList.remove('hidden');
    document.getElementById('success-step-2').classList.add('hidden');
    document.getElementById('success-btn').textContent = 'OK';

    modal.classList.remove('hidden');
}

function advanceSuccessModal() {
    if (successModalStep === 1) {
        successModalStep = 2;
        document.getElementById('success-step-1').classList.add('hidden');
        document.getElementById('success-step-2').classList.remove('hidden');
        document.getElementById('success-btn').textContent = 'Go to Dashboard';
        return;
    }
    window.location.href = postSuccessRedirectUrl;
}

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('success-btn');
    if (btn) btn.addEventListener('click', advanceSuccessModal);

    @if(session('renewal_blocked'))
    openRenewalBlockedModal();
    @endif
});

async function startRazorpayCheckout() {
    const selected = document.querySelector('label.group input[name="membership_setting_id"]:checked');
    if (!selected) {
        alert('Please select a subscription plan first.');
        return;
    }

    const membershipSettingId = selected.value;

    try {
        const res = await fetch("{{ route('member.subscription.order') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
            },
            body: JSON.stringify({ membership_setting_id: membershipSettingId })
        });

        if (!res.ok) {
            throw new Error('Failed to create Razorpay order.');
        }

        const data = await res.json();

        const options = {
            key: data.key,
            amount: data.amount * 100,
            currency: 'INR',
            name: 'GNAT Membership',
            description: 'Membership Subscription',
            order_id: data.order_id,
            handler: async function (response) {
                try {
                    const verifyRes = await fetch("{{ route('member.subscription.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature
                        })
                    });

                    const payload = await verifyRes.json();
                    if (!verifyRes.ok || !payload?.success) {
                        throw new Error(payload?.message || 'Payment verification failed.');
                    }

                    // include payment id for UI
                    payload.razorpay_payment_id = response.razorpay_payment_id;
                    openSuccessModal(payload);
                } catch (err) {
                    console.error(err);
                    alert('Payment done, but verification failed. Please contact support.');
                }
            },
            prefill: {
                name: "{{ $user->name }}",
                email: "{{ $user->email }}",
                contact: "{{ $user->mobile }}"
            },
            theme: {
                color: '#4f46e5'
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    } catch (e) {
        console.error(e);
        alert('Unable to start payment. Please try again.');
    }
}
</script>
@endsection

