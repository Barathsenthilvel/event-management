@extends('member.layouts.gnat')

@section('title', 'Membership plans — GNAT Association')

@section('content')
<div class="space-y-6">
    <div class="rounded-[28px] border border-[#351c42]/10 bg-gradient-to-br from-white via-white to-[#965995]/8 shadow-sm p-6 md:p-7">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#351c42] tracking-tight">Hello, {{ $user->first_name ?? 'Member' }}</h1>
                <p class="mt-1 text-sm text-[#351c42]/60">Choose a subscription plan from the admin-added list and continue.</p>
            </div>
            @if(!empty($activeSubscription))
                <a href="{{ route('member.dashboard') }}"
                    class="px-6 py-3 rounded-2xl bg-[#351c42] hover:bg-[#4d2a5c] text-[#fddc6a] text-xs font-extrabold shadow-lg transition-all text-center">
                    Back to Dashboard
                </a>
            @else
                <a href="{{ route('member.profile.edit') }}"
                    class="px-6 py-3 rounded-2xl border-2 border-[#351c42]/20 bg-white text-[#351c42] hover:bg-[#faf8fc] text-xs font-extrabold shadow-sm transition-all text-center">
                    Account profile
                </a>
            @endif
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
                                <span class="font-extrabold text-[#965995]">Showing: {{ $filterType }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span class="inline-flex items-center gap-2 rounded-2xl border border-[#965995]/25 bg-[#965995]/10 px-4 py-2 text-[11px] font-extrabold text-[#351c42]">
                            Active plans:
                            <span class="rounded-xl bg-white px-2 py-0.5 text-[#965995] border border-[#965995]/20">{{ (int) ($settingsCount ?? 0) }}</span>
                        </span>
                    </div>
                </div>

                @if(session('error'))
                    <div class="mb-5 rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm text-rose-700 font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('member_gate_error'))
                    <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-950">
                        {{ session('member_gate_error') }}
                    </div>
                @endif

                <form id="subscription-form" class="space-y-6"
                    x-data="{
                        viewType: 'grid',
                        selectedPlan: {{ old('membership_setting_id') !== null && old('membership_setting_id') !== '' ? (int) old('membership_setting_id') : 'null' }},
                        pick(id) {
                            this.selectedPlan = id;
                            const el = document.getElementById('mp-' + id);
                            if (el) el.checked = true;
                        }
                    }"
                    @change="if ($event.target?.name === 'membership_setting_id') selectedPlan = parseInt($event.target.value, 10)">
                    {{-- Single radio group: grid + table both control these inputs --}}
                    <div class="sr-only" aria-hidden="true">
                        @foreach($plans as $p)
                            <input type="radio" name="membership_setting_id" id="mp-{{ $p->id }}" value="{{ $p->id }}"
                                {{ (string) old('membership_setting_id') === (string) $p->id ? 'checked' : '' }} />
                        @endforeach
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-auto max-sm:w-full">View</span>
                        <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-200/80 shadow-inner" role="group" aria-label="Plan view mode">
                            <button type="button" @click="viewType = 'grid'"
                                :class="viewType === 'grid' ? 'bg-white shadow text-[#351c42] ring-1 ring-slate-200/80' : 'text-slate-400 hover:text-slate-600'"
                                class="p-2.5 rounded-lg transition-all"
                                title="Grid view (2 cards per row)"
                                aria-label="Grid view">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                            <button type="button" @click="viewType = 'list'"
                                :class="viewType === 'list' ? 'bg-white shadow text-[#351c42] ring-1 ring-slate-200/80' : 'text-slate-400 hover:text-slate-600'"
                                class="p-2.5 rounded-lg transition-all"
                                title="Table list view"
                                aria-label="Table list view">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Grid: admin-style cards (light blue accents); 1 / 2 / 3 cols like reference --}}
                    <div x-show="viewType === 'grid'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
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
                                $cycleBadge = strtoupper(str_replace('_', '-', (string) $plan->payment_type));
                                $registrationFee = ($isNew && $plan->registration_fee_enabled) ? (float) $plan->registration_fee : 0.0;
                                $payable = (float) $plan->membership_fee + $registrationFee;
                                $graceDays = (int) ($plan->grace_period ?? 0);
                            @endphp

                            <label for="mp-{{ $plan->id }}"
                                class="relative flex flex-col rounded-[20px] border border-[#351c42]/12 bg-white p-5 shadow-sm transition-all cursor-pointer"
                                :class="selectedPlan === {{ (int) $plan->id }} ? 'ring-2 ring-[#965995] ring-offset-2 border-[#965995]/50 shadow-md' : 'hover:border-[#965995]/35 hover:shadow-md'"
                                data-payable="{{ $payable }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#965995]/15 text-lg font-black text-[#351c42]">
                                        {{ $isNew ? 'N' : 'R' }}
                                    </div>
                                    <span class="shrink-0 rounded-full bg-[#965995]/15 px-3 py-1.5 text-[10px] font-black tracking-wide text-[#351c42]">
                                        {{ $cycleBadge }}
                                    </span>
                                </div>
                                <h3 class="mt-4 text-xl font-black tracking-tight text-slate-900">
                                    {{ $isNew ? 'New Membership' : 'Renewal Membership' }}
                                </h3>

                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-xl border border-slate-200/70 bg-slate-100/70 px-3 py-3">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Membership fee</p>
                                        <p class="mt-1 text-base font-black tabular-nums text-slate-900">₹ {{ number_format((float) $plan->membership_fee, 2) }}</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-200/70 bg-slate-100/70 px-3 py-3">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Reg. fee</p>
                                        @if($isNew)
                                            <p class="mt-1 text-base font-black tabular-nums text-slate-900">₹ {{ number_format((float) $plan->registration_fee, 2) }}</p>
                                            @if($plan->registration_fee_enabled)
                                                <span class="mt-0.5 inline-block text-[10px] font-black uppercase text-emerald-600">On</span>
                                            @else
                                                <span class="mt-0.5 inline-block text-[10px] font-black uppercase text-slate-400">Off</span>
                                            @endif
                                        @else
                                            <p class="mt-1 text-base font-bold text-slate-400">—</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3 rounded-xl border border-slate-200/70 bg-slate-100/70 px-3 py-3">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Grace period</p>
                                    <p class="mt-1 text-base font-black text-slate-900">{{ $graceDays }} {{ $graceDays === 1 ? 'day' : 'days' }}</p>
                                </div>

                                <div class="mt-3 rounded-xl border border-[#965995]/25 bg-[#965995]/8 px-3 py-2.5 flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#351c42]/70">Total payable</span>
                                    <span class="text-sm font-black tabular-nums text-[#351c42]">₹ {{ number_format($payable, 2) }}</span>
                                </div>

                                <div class="mt-4 flex gap-2">
                                    <span class="flex flex-1 items-center justify-center rounded-xl bg-[#965995]/15 py-3 text-sm font-black text-[#351c42] transition-colors"
                                        :class="selectedPlan === {{ (int) $plan->id }} ? 'bg-[#351c42] text-[#fddc6a]' : ''"
                                        x-text="selectedPlan === {{ (int) $plan->id }} ? 'Selected' : 'Select plan'">
                                    </span>
                                    <span class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-xl border border-[#965995]/30 bg-[#965995]/10 text-[#351c42]"
                                        title="Selected for payment"
                                        :class="selectedPlan === {{ (int) $plan->id }} ? 'border-[#351c42] bg-[#351c42] text-[#fddc6a]' : ''">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                </div>
                                <p class="mt-2 text-center text-[10px] font-bold text-slate-400">Then tap <span class="font-extrabold text-[#965995]">Pay Now</span> below</p>
                            </label>
                        @empty
                            <div class="sm:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-12 text-center">
                                <h3 class="text-lg font-extrabold text-slate-900">No active plans found</h3>
                                <p class="mt-1 text-sm text-slate-500">Ask admin to add plans under Membership module.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- List: table view --}}
                    <div x-show="viewType === 'list'" x-cloak class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-[720px] w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/90 text-[10px] font-black uppercase tracking-widest text-slate-500">
                                    <th class="w-12 px-4 py-3.5"></th>
                                    <th class="px-4 py-3.5">Type</th>
                                    <th class="px-4 py-3.5">Cycle</th>
                                    <th class="px-4 py-3.5 text-right">Membership</th>
                                    <th class="px-4 py-3.5 text-right">Registration</th>
                                    <th class="px-4 py-3.5 text-center">Grace</th>
                                    <th class="px-4 py-3.5 text-right">Payable</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
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
                                    <tr class="subscription-plan-row cursor-pointer transition-colors hover:bg-[#965995]/10"
                                        data-payable="{{ $payable }}"
                                        @click="pick({{ (int) $plan->id }})"
                                        :class="selectedPlan === {{ (int) $plan->id }} ? 'bg-[#965995]/15' : ''">
                                        <td class="px-4 py-3 align-middle text-center">
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border-2 transition-colors"
                                                :class="selectedPlan === {{ (int) $plan->id }} ? 'border-[#351c42] bg-[#351c42]' : 'border-slate-300 bg-white'">
                                                <svg class="h-3 w-3 text-white" :class="selectedPlan === {{ (int) $plan->id }} ? 'opacity-100' : 'opacity-0'" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-extrabold text-slate-900">{{ $plan->subscription_type }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $paymentLabel }}</td>
                                        <td class="px-4 py-3 text-right font-bold tabular-nums">₹ {{ number_format((float) $plan->membership_fee, 0) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-slate-700">
                                            @if($isNew)
                                                ₹ {{ number_format((float) $plan->registration_fee, 0) }}
                                                @if(!$plan->registration_fee_enabled)
                                                    <span class="block text-[10px] font-bold text-slate-400">(off)</span>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-slate-600">{{ (int) ($plan->grace_period ?? 0) }}d</td>
                                        <td class="px-4 py-3 text-right font-black text-[#351c42] tabular-nums">₹ {{ number_format($payable, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-12 text-center text-slate-500">No active plans. Admin can add them in Membership module.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-center">
                        <button
                            type="button"
                            id="pay-now-btn"
                            onclick="startRazorpayCheckout()"
                            class="inline-flex items-center justify-center gap-2 px-10 py-4 bg-[#351c42] hover:bg-[#4d2a5c] text-[#fddc6a] rounded-2xl font-extrabold shadow-lg shadow-[#351c42]/25 transition-all disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            <svg id="pay-now-spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-90" fill="currentColor" d="M22 12a10 10 0 00-10-10v3a7 7 0 017 7h3z"></path>
                            </svg>
                            <span id="pay-now-label">Pay Now</span>
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
            <div class="p-6 bg-gradient-to-br from-slate-900 via-slate-900 to-amber-700 text-white">
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
                        class="inline-flex items-center justify-center px-8 py-3 bg-[#351c42] hover:bg-[#4d2a5c] text-[#fddc6a] rounded-2xl font-extrabold shadow-lg transition-all">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment success: theme modal + download invoice -->
<div id="payment-success-modal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeSuccessModal()"></div>
    <div class="relative min-h-full flex items-center justify-center p-4 pointer-events-none">
        <div class="w-full max-w-lg rounded-[28px] bg-white border border-[#351c42]/10 shadow-2xl overflow-hidden pointer-events-auto" onclick="event.stopPropagation()">
            <div class="p-6 bg-gradient-to-br from-[#351c42] via-[#4d2a5c] to-[#965995] text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-[#fddc6a]/90">Payment successful</p>
                <h3 class="mt-1 text-xl font-extrabold tracking-tight text-white" id="success-title">Thank you!</h3>
                <p class="mt-2 text-xs font-bold text-white/85" id="success-subtitle"></p>
            </div>

            <div class="p-6 space-y-4 max-h-[min(70vh,520px)] overflow-y-auto">
                <div class="rounded-2xl border border-[#351c42]/10 bg-slate-50 p-4 space-y-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-[#965995]">Plan</p>
                    <p class="text-sm font-extrabold text-[#351c42]" id="plan-summary"></p>
                    <p class="text-xs font-mono font-bold text-slate-600 break-all" id="razorpay-order-line"></p>
                    <p class="text-xs font-mono font-bold text-slate-600 break-all" id="payment-id"></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-[#351c42]/10 bg-white p-4">
                        <p class="text-[10px] font-black uppercase text-slate-500">Amount paid</p>
                        <p class="mt-1 text-sm font-extrabold text-[#351c42]" id="amount-paid"></p>
                    </div>
                    <div class="rounded-2xl border border-[#351c42]/10 bg-white p-4">
                        <p class="text-[10px] font-black uppercase text-slate-500">Valid till</p>
                        <p class="mt-1 text-sm font-extrabold text-[#351c42]" id="valid-till"></p>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#965995]/25 bg-[#965995]/8 p-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-[#351c42]">Plan details</p>
                    <ul class="mt-3 space-y-2 text-xs font-bold text-[#351c42]/90">
                        <li class="flex justify-between gap-2"><span class="text-slate-500">Type</span><span id="detail-subscription-type"></span></li>
                        <li class="flex justify-between gap-2"><span class="text-slate-500">Cycle</span><span id="detail-payment-type"></span></li>
                        <li class="flex justify-between gap-2"><span class="text-slate-500">Membership fee</span><span id="detail-membership-fee"></span></li>
                        <li class="flex justify-between gap-2"><span class="text-slate-500">Registration fee</span><span id="detail-registration-fee"></span></li>
                        <li class="flex justify-between gap-2"><span class="text-slate-500">Grace (days)</span><span id="detail-grace-period"></span></li>
                    </ul>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-1">
                    <a id="success-invoice-link" href="#" target="_blank" rel="noopener"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl border-2 border-[#351c42] bg-white px-6 py-3.5 text-sm font-extrabold text-[#351c42] hover:bg-[#351c42]/5 transition-all">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download invoice
                    </a>
                    <button id="success-btn" type="button"
                        class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[#351c42] px-6 py-3.5 text-sm font-extrabold text-[#fddc6a] shadow-lg hover:bg-[#4d2a5c] transition-all">
                        Go to dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment failed / cancelled -->
<div id="payment-failure-modal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeFailureModal()"></div>
    <div class="relative min-h-full flex items-center justify-center p-4 pointer-events-none">
        <div class="w-full max-w-md rounded-[28px] bg-white border border-rose-100 shadow-2xl overflow-hidden pointer-events-auto" onclick="event.stopPropagation()">
            <div class="p-6 bg-gradient-to-br from-rose-600 to-[#351c42] text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-white/80" id="failure-badge">Payment issue</p>
                <h3 class="mt-1 text-lg font-extrabold" id="failure-title">Something went wrong</h3>
                <p class="mt-2 text-sm font-bold text-white/90" id="failure-message"></p>
            </div>
            <div class="p-6">
                <p class="text-xs font-semibold text-slate-600 whitespace-pre-wrap" id="failure-detail"></p>
                <button type="button" onclick="closeFailureModal()"
                    class="mt-6 w-full rounded-2xl bg-[#351c42] py-3.5 text-sm font-extrabold text-[#fddc6a] hover:bg-[#4d2a5c] transition-all">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const postSuccessRedirectUrl = "{{ route('member.dashboard') }}";

function invoiceUrl(transactionId) {
    return @json(url('/member/subscription/invoice')) + '/' + encodeURIComponent(String(transactionId));
}

function openRenewalBlockedModal() {
    const modal = document.getElementById('renewal-blocked-modal');
    if (modal) modal.classList.remove('hidden');
}

function setRenewalBlockedMessage(message) {
    const el = document.getElementById('renewal-blocked-message');
    if (el && message) el.textContent = String(message);
}

function closeRenewalBlockedModal() {
    const modal = document.getElementById('renewal-blocked-modal');
    if (modal) modal.classList.add('hidden');
}

function closeSuccessModal() {
    const modal = document.getElementById('payment-success-modal');
    if (modal) modal.classList.add('hidden');
}

function openFailureModal(title, message, detail, badge) {
    const modal = document.getElementById('payment-failure-modal');
    if (!modal) return;
    document.getElementById('failure-title').textContent = title || 'Something went wrong';
    document.getElementById('failure-message').textContent = message || '';
    const det = document.getElementById('failure-detail');
    det.textContent = detail || '';
    det.classList.toggle('hidden', !detail);
    document.getElementById('failure-badge').textContent = badge || 'Payment issue';
    modal.classList.remove('hidden');
}

function closeFailureModal() {
    const modal = document.getElementById('payment-failure-modal');
    if (modal) modal.classList.add('hidden');
}

function formatInr(n) {
    const v = Number(n || 0);
    return '₹ ' + v.toLocaleString('en-IN', { maximumFractionDigits: 2 });
}

function humanCycle(s) {
    return String(s || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function openSuccessModal(payload) {
    const modal = document.getElementById('payment-success-modal');
    if (!modal) return;

    document.getElementById('success-subtitle').textContent = payload?.message || 'Your membership payment was received.';
    document.getElementById('plan-summary').textContent =
        `${payload?.plan?.subscription_type || ''} • ${humanCycle(payload?.plan?.payment_type)}`;
    document.getElementById('razorpay-order-line').textContent =
        `Order ID: ${payload?.razorpay_order_id || '—'}`;
    document.getElementById('payment-id').textContent =
        `Payment ID: ${payload?.razorpay_payment_id || '—'}`;
    document.getElementById('amount-paid').textContent =
        `${formatInr(payload?.subscription?.amount)} ${payload?.subscription?.currency || 'INR'}`;
    document.getElementById('valid-till').textContent = payload?.subscription?.end_date || '—';

    document.getElementById('detail-subscription-type').textContent = payload?.plan?.subscription_type || '—';
    document.getElementById('detail-payment-type').textContent = humanCycle(payload?.plan?.payment_type);
    document.getElementById('detail-membership-fee').textContent = formatInr(payload?.plan?.membership_fee);
    const regFee = (payload?.plan?.subscription_type === 'New' && payload?.plan?.registration_fee_enabled)
        ? payload?.plan?.registration_fee
        : 0;
    document.getElementById('detail-registration-fee').textContent = formatInr(regFee);
    document.getElementById('detail-grace-period').textContent = String(payload?.plan?.grace_period ?? 0);

    const tid = payload?.transaction_id;
    const inv = document.getElementById('success-invoice-link');
    if (inv && tid) {
        inv.href = invoiceUrl(tid);
        inv.classList.remove('pointer-events-none', 'opacity-50');
    } else if (inv) {
        inv.href = '#';
        inv.classList.add('pointer-events-none', 'opacity-50');
    }

    modal.classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('success-btn');
    if (btn) {
        btn.addEventListener('click', () => {
            closeSuccessModal();
            window.location.href = postSuccessRedirectUrl;
        });
    }

    @if(session('renewal_blocked'))
    openRenewalBlockedModal();
    @endif
});

async function startRazorpayCheckout() {
    const payNowBtn = document.getElementById('pay-now-btn');
    const payNowLabel = document.getElementById('pay-now-label');
    const payNowSpinner = document.getElementById('pay-now-spinner');
    if (payNowBtn?.dataset.loading === 'true') {
        return;
    }
    const setPayNowLoading = (loading) => {
        if (!payNowBtn) return;
        payNowBtn.disabled = loading;
        payNowBtn.dataset.loading = loading ? 'true' : 'false';
        if (payNowLabel) payNowLabel.textContent = loading ? 'Loading...' : 'Pay Now';
        if (payNowSpinner) payNowSpinner.classList.toggle('hidden', !loading);
    };

    const selected = document.querySelector('input[name="membership_setting_id"]:checked');
    if (!selected) {
        openFailureModal('No plan selected', 'Please choose a subscription plan before paying.', '', 'Action needed');
        return;
    }

    setPayNowLoading(true);
    const membershipSettingId = selected.value;
    let razorpayOutcome = 'idle';

    try {
        const res = await fetch("{{ route('member.subscription.order') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ membership_setting_id: membershipSettingId })
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            if (data?.renewal_blocked) {
                setRenewalBlockedMessage(data?.message || 'Renewal is not available right now.');
                openRenewalBlockedModal();
                setPayNowLoading(false);
                return;
            }
            const msg = data?.message || 'Could not start payment. Please try again.';
            openFailureModal('Payment could not start', msg, '', 'Error');
            setPayNowLoading(false);
            return;
        }

        const options = {
            key: data.key,
            amount: Math.round(Number(data.amount || 0) * 100),
            currency: 'INR',
            name: 'GNAT Membership',
            description: 'Membership subscription',
            order_id: data.order_id,
            handler: async function (response) {
                razorpayOutcome = 'processing';
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

                    const payload = await verifyRes.json().catch(() => ({}));
                    if (!verifyRes.ok || !payload?.success) {
                        razorpayOutcome = 'failed';
                        openFailureModal(
                            'Verification failed',
                            payload?.message || 'We could not confirm this payment. If money was debited, contact support with your payment ID.',
                            `Order: ${response.razorpay_order_id || '—'}\nPayment: ${response.razorpay_payment_id || '—'}`,
                            'Failed'
                        );
                        return;
                    }

                    razorpayOutcome = 'success';
                    payload.razorpay_payment_id = response.razorpay_payment_id;
                    payload.razorpay_order_id = response.razorpay_order_id;
                    openSuccessModal(payload);
                } catch (err) {
                    console.error(err);
                    razorpayOutcome = 'failed';
                    openFailureModal(
                        'Something went wrong',
                        'Payment may have gone through but we could not verify it. Please contact support.',
                        String(err?.message || err),
                        'Error'
                    );
                }
            },
            prefill: {
                name: @json($user->name ?? ''),
                email: @json($user->email ?? ''),
                contact: @json($user->mobile ?? '')
            },
            theme: {
                color: '#351c42'
            },
            modal: {
                ondismiss: function () {
                    if (razorpayOutcome === 'success' || razorpayOutcome === 'processing' || razorpayOutcome === 'failed') return;
                    openFailureModal(
                        'Payment not completed',
                        'The Razorpay window was closed before the payment finished.',
                        'You can tap Pay again when you are ready.',
                        'Cancelled'
                    );
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (resp) {
            razorpayOutcome = 'failed';
            const err = resp?.error || {};
            const desc = err.description || err.reason || 'The payment was declined or failed.';
            const code = err.code ? `Code: ${err.code}` : '';
            const step = err.step ? `Step: ${err.step}` : '';
            openFailureModal('Payment failed', desc, [code, step].filter(Boolean).join('\n'), 'Failed');
        });
        rzp.open();
        setPayNowLoading(false);
    } catch (e) {
        console.error(e);
        openFailureModal('Unable to open payment', 'Please check your connection and try again.', String(e?.message || e), 'Error');
        setPayNowLoading(false);
    }
}
</script>
@endsection

