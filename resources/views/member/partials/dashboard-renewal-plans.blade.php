{{-- Renewal plan picker for member dashboard — matches membership /subscription card UI --}}
@php
    $renewalPlans = $renewalPlans ?? collect();
    $firstPlanId = $renewalPlans->first()?->id;
@endphp

<div
    class="rounded-[28px] border border-[#351c42]/10 bg-white p-5 shadow-sm sm:p-7 md:p-8"
    x-data="{
        viewType: 'grid',
        selectedPlan: {{ $firstPlanId !== null ? (int) $firstPlanId : 'null' }},
        pick(id) {
            this.selectedPlan = parseInt(id, 10);
        },
        membershipUrlWithPlan() {
            const base = @js(route('member.subscription.index'));
            return this.selectedPlan ? `${base}?plan=${this.selectedPlan}` : base;
        }
    }"
>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0">
            <h2 id="plans-heading" class="text-xl font-extrabold tracking-tight text-[#351c42] sm:text-2xl md:text-[1.65rem]">Subscription Plans</h2>
            <p class="mt-1 text-xs text-[#351c42]/60 sm:text-sm">
                Fees are shown in INR. Registration fee applies only for new subscription (if enabled).
                <span class="font-extrabold text-[#965995]">Showing: Renewal</span>
            </p>
            <p class="mt-2 text-sm text-[#351c42]/55">Your membership is active. Compare renewal cycles below, then continue to the membership page to pay when your current period ends.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 lg:flex-col lg:items-end">
            <span class="inline-flex items-center gap-2 rounded-2xl border border-[#965995]/25 bg-[#965995]/10 px-4 py-2 text-[11px] font-extrabold text-[#351c42]">
                Active plans:
                <span class="rounded-xl bg-white px-2 py-0.5 text-[#965995] border border-[#965995]/20">{{ (int) $renewalPlans->count() }}</span>
            </span>
            <a href="{{ route('member.subscription.index') }}" class="text-sm font-bold text-[#965995] hover:text-[#351c42] transition-colors whitespace-nowrap">Open membership page →</a>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-[#351c42]/08 pt-6">
        <span class="text-[11px] font-bold text-[#351c42]/40 uppercase tracking-wider mr-auto max-sm:w-full">View</span>
        <div class="flex bg-[#351c42]/[0.04] p-1 rounded-xl border border-[#351c42]/10 shadow-inner" role="group" aria-label="Plan view mode">
            <button type="button" @click="viewType = 'grid'"
                :class="viewType === 'grid' ? 'bg-white shadow text-[#351c42] ring-1 ring-[#351c42]/10' : 'text-[#351c42]/40 hover:text-[#351c42]/70'"
                class="p-2.5 rounded-lg transition-all"
                title="Grid view"
                aria-label="Grid view">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </button>
            <button type="button" @click="viewType = 'list'"
                :class="viewType === 'list' ? 'bg-white shadow text-[#351c42] ring-1 ring-[#351c42]/10' : 'text-[#351c42]/40 hover:text-[#351c42]/70'"
                class="p-2.5 rounded-lg transition-all"
                title="Table list view"
                aria-label="Table list view">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="viewType === 'grid'" x-cloak class="mt-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($renewalPlans as $plan)
            @php
                $paymentLabel = match($plan->payment_type) {
                    'monthly' => 'Monthly',
                    'bi_monthly' => 'Bi - Monthly',
                    'quarterly' => 'Quarterly',
                    'half_yearly' => 'Half Yearly',
                    'yearly' => 'Yearly',
                    default => ucfirst((string) $plan->payment_type),
                };
                $cycleBadge = strtoupper(str_replace('_', '-', (string) $plan->payment_type));
                $payable = (float) $plan->membership_fee;
                $graceDays = (int) ($plan->grace_period ?? 0);
            @endphp
            <div
                role="button"
                tabindex="0"
                @click="pick({{ (int) $plan->id }})"
                @keydown.enter.prevent="pick({{ (int) $plan->id }})"
                @keydown.space.prevent="pick({{ (int) $plan->id }})"
                class="relative flex flex-col rounded-[20px] border border-[#351c42]/12 bg-white p-5 shadow-sm transition-all cursor-pointer outline-none focus-visible:ring-2 focus-visible:ring-[#965995] focus-visible:ring-offset-2"
                :class="selectedPlan === {{ (int) $plan->id }} ? 'ring-2 ring-[#965995] ring-offset-2 border-[#965995]/50 shadow-md' : 'hover:border-[#965995]/35 hover:shadow-md'"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#965995]/15 text-lg font-black text-[#351c42]" aria-hidden="true">R</div>
                    <span class="shrink-0 rounded-full bg-[#965995]/15 px-3 py-1.5 text-[10px] font-black tracking-wide text-[#351c42]">{{ $cycleBadge }}</span>
                </div>
                <h3 class="mt-4 text-xl font-black tracking-tight text-[#351c42]">Renewal Membership</h3>
                <p class="mt-0.5 text-xs font-semibold text-[#351c42]/50">{{ $paymentLabel }}</p>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-[#351c42]/10 bg-[#351c42]/[0.04] px-3 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#351c42]/50">Membership fee</p>
                        <p class="mt-1 text-base font-black tabular-nums text-[#351c42]">₹ {{ number_format((float) $plan->membership_fee, 2) }}</p>
                    </div>
                    <div class="rounded-xl border border-[#351c42]/10 bg-[#351c42]/[0.04] px-3 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#351c42]/50">Reg. fee</p>
                        <p class="mt-1 text-base font-bold text-[#351c42]/35">—</p>
                    </div>
                </div>

                <div class="mt-3 rounded-xl border border-[#351c42]/10 bg-[#351c42]/[0.04] px-3 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[#351c42]/50">Grace period</p>
                    <p class="mt-1 text-base font-black text-[#351c42]">{{ $graceDays }} {{ $graceDays === 1 ? 'day' : 'days' }}</p>
                </div>

                <div class="mt-3 rounded-xl border border-[#965995]/25 bg-[#965995]/8 px-3 py-2.5 flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#351c42]/70">Total payable</span>
                    <span class="text-sm font-black tabular-nums text-[#351c42]">₹ {{ number_format($payable, 2) }}</span>
                </div>

                <div class="mt-4 flex gap-2 pointer-events-none">
                    <span class="flex flex-1 items-center justify-center rounded-xl bg-[#965995]/15 py-3 text-sm font-black text-[#351c42] transition-colors"
                        :class="selectedPlan === {{ (int) $plan->id }} ? 'bg-[#351c42] text-[#fddc6a]' : ''"
                        x-text="selectedPlan === {{ (int) $plan->id }} ? 'Selected' : 'Select plan'"></span>
                    <span class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-xl border border-[#965995]/30 bg-[#965995]/10 text-[#351c42]"
                        :class="selectedPlan === {{ (int) $plan->id }} ? 'border-[#351c42] bg-[#351c42] text-[#fddc6a]' : ''"
                        aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                </div>
                <p class="mt-2 text-center text-[10px] font-bold text-[#351c42]/40">Then tap <span class="font-extrabold text-[#965995]">Pay Now</span> below</p>
            </div>
        @empty
            <div class="sm:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-[#351c42]/15 bg-[#f8f6fa] p-10 text-center">
                <p class="text-sm font-extrabold text-[#351c42]/75">No renewal plans available right now.</p>
                <p class="mt-1 text-xs text-[#351c42]/55">Please contact support if you need help.</p>
            </div>
        @endforelse
    </div>

    <div x-show="viewType === 'list'" x-cloak class="mt-5 overflow-x-auto rounded-2xl border border-[#351c42]/10 bg-white shadow-sm">
        <table class="min-w-[720px] w-full text-left text-sm">
            <thead>
                <tr class="border-b border-[#351c42]/10 bg-[#351c42]/[0.03] text-[10px] font-black uppercase tracking-widest text-[#351c42]/50">
                    <th class="w-12 px-4 py-3.5"></th>
                    <th class="px-4 py-3.5">Type</th>
                    <th class="px-4 py-3.5">Cycle</th>
                    <th class="px-4 py-3.5 text-right">Membership</th>
                    <th class="px-4 py-3.5 text-right">Registration</th>
                    <th class="px-4 py-3.5 text-center">Grace</th>
                    <th class="px-4 py-3.5 text-right">Payable</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#351c42]/08">
                @forelse($renewalPlans as $plan)
                    @php
                        $paymentLabel = match($plan->payment_type) {
                            'monthly' => 'Monthly',
                            'bi_monthly' => 'Bi - Monthly',
                            'quarterly' => 'Quarterly',
                            'half_yearly' => 'Half Yearly',
                            'yearly' => 'Yearly',
                            default => ucfirst((string) $plan->payment_type),
                        };
                        $payable = (float) $plan->membership_fee;
                    @endphp
                    <tr
                        class="cursor-pointer transition-colors hover:bg-[#965995]/10"
                        @click="pick({{ (int) $plan->id }})"
                        :class="selectedPlan === {{ (int) $plan->id }} ? 'bg-[#965995]/12' : ''"
                    >
                        <td class="px-4 py-3 align-middle text-center">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border-2 transition-colors"
                                :class="selectedPlan === {{ (int) $plan->id }} ? 'border-[#351c42] bg-[#351c42]' : 'border-[#351c42]/25 bg-white'">
                                <svg class="h-3 w-3 text-white" :class="selectedPlan === {{ (int) $plan->id }} ? 'opacity-100' : 'opacity-0'" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </td>
                        <td class="px-4 py-3 font-extrabold text-[#351c42]">Renewal</td>
                        <td class="px-4 py-3 text-[#351c42]/80">{{ $paymentLabel }}</td>
                        <td class="px-4 py-3 text-right font-bold tabular-nums">₹ {{ number_format((float) $plan->membership_fee, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-[#351c42]/45">—</td>
                        <td class="px-4 py-3 text-center text-[#351c42]/70">{{ (int) ($plan->grace_period ?? 0) }}d</td>
                        <td class="px-4 py-3 text-right font-black text-[#351c42] tabular-nums">₹ {{ number_format($payable, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-[#351c42]/55">No renewal plans configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center border-t border-[#351c42]/08 pt-8">
        <a
            x-bind:href="membershipUrlWithPlan()"
            class="inline-flex items-center justify-center gap-2 px-10 py-4 bg-[#351c42] hover:bg-[#4d2a5c] text-[#fddc6a] rounded-2xl text-sm font-extrabold shadow-lg shadow-[#351c42]/25 transition-all"
            :class="!selectedPlan && @json($renewalPlans->isNotEmpty()) ? 'opacity-60 pointer-events-none' : ''"
        >
            Pay Now
        </a>
    </div>
</div>
