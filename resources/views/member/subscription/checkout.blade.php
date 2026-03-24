@extends('member.layouts.gnat')

@section('title', 'Checkout — GNAT Donation')

@section('content')
<div class="space-y-6">
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

                <div class="mt-6" x-data="checkoutPage()">
                    <button @click="initPayment()" x-bind:disabled="isProcessing"
                        class="inline-flex w-full items-center justify-center px-10 py-4 bg-slate-900 hover:bg-indigo-600 text-white rounded-2xl font-extrabold shadow-lg transition-all disabled:opacity-50">
                        <span x-text="isProcessing ? 'Processing...' : 'Proceed to Pay'"></span>
                    </button>

                    <!-- Success Modal -->
                    <template x-teleport="body">
                        <div x-show="showModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>
                            
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <div x-show="showModal" x-transition
                                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 p-6">
                                    
                                    <!-- Step 1: Success Message -->
                                    <div x-show="modalStep === 1">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 mb-4">
                                            <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-extrabold text-slate-900 text-center" id="modal-title">Payment Successful!</h3>
                                        <p class="mt-2 text-sm text-slate-500 text-center">
                                            Your transaction has been securely processed.
                                        </p>
                                        <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Plan purchased</p>
                                            <p class="mt-1 text-sm font-extrabold text-slate-900 text-center" x-text="planSummary"></p>
                                            <div class="mt-3 grid grid-cols-2 gap-3">
                                                <div class="rounded-xl border border-slate-100 bg-white p-3 text-center">
                                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Amount paid</p>
                                                    <p class="mt-1 text-sm font-extrabold text-emerald-700" x-text="amountPaid"></p>
                                                </div>
                                                <div class="rounded-xl border border-slate-100 bg-white p-3 text-center">
                                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Valid till</p>
                                                    <p class="mt-1 text-sm font-extrabold text-slate-900" x-text="validTill"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 p-3 bg-slate-50 border border-slate-100 rounded-xl text-center">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Razorpay Payment ID</p>
                                            <p class="mt-1 font-mono text-xs font-bold text-slate-700" x-text="paymentId"></p>
                                        </div>
                                        <div class="mt-6">
                                            <button @click="advanceModal()" class="w-full inline-flex justify-center rounded-xl bg-slate-900 px-3 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                OK
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Step 2: Plan Details -->
                                    <div x-show="modalStep === 2" style="display: none;">
                                        <h3 class="text-xl font-extrabold text-slate-900 text-center mb-4">Purchased Plan Details</h3>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                                <span class="text-xs font-bold text-slate-500">Plan Type</span>
                                                <span class="text-sm font-extrabold text-slate-900" x-text="plan?.subscription_type || '-'"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                                <span class="text-xs font-bold text-slate-500">Billing Cycle</span>
                                                <span class="text-sm font-extrabold text-slate-900" x-text="humanCycle(plan?.payment_type)"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                                <span class="text-xs font-bold text-slate-500">Amount Paid</span>
                                                <span class="text-sm font-extrabold text-emerald-600" x-text="amountPaid"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                                <span class="text-xs font-bold text-slate-500">Start Date</span>
                                                <span class="text-sm font-extrabold text-slate-900" x-text="subscription?.start_date || '-'"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                                <span class="text-xs font-bold text-slate-500">End Date</span>
                                                <span class="text-sm font-extrabold text-slate-900" x-text="subscription?.end_date || '-'"></span>
                                            </div>
                                        </div>
                                        <div class="mt-6 flex flex-col gap-3">
                                            <a :href="`{{ url('/member/subscription/invoice') }}/${transactionId}`" target="_blank" 
                                               class="w-full inline-flex justify-center rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm font-extrabold text-slate-700 shadow-sm hover:bg-slate-50">
                                                Download Invoice
                                            </a>
                                            <button type="button" @click="advanceModal()"
                                                class="w-full inline-flex justify-center rounded-xl bg-indigo-600 px-3 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-indigo-500">
                                                Go to Membership Dashboard
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutPage', () => ({
            isProcessing: false,
            showModal: false,
            modalStep: 1,
            paymentId: '',
            transactionId: '',
            plan: null,
            subscription: null,
            planSummary: '',
            amountPaid: '',
            validTill: '',

            humanCycle(v) {
                return String(v || '').replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
            },

            formatInr(n) {
                const v = Number(n || 0);
                return '₹ ' + v.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            },

            advanceModal() {
                if (this.modalStep === 1) {
                    this.modalStep = 2;
                    return;
                }
                window.location.href = "{{ route('member.dashboard') }}";
            },
            
            async initPayment() {
                if(this.isProcessing) return;
                this.isProcessing = true;
                
                try {
                    // 1. Create Order on Backend
                    const response = await fetch("{{ route('member.subscription.order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            membership_setting_id: {{ (int) $plan->id }}
                        })
                    });
                    
                    const orderData = await response.json();
                    
                    if(!response.ok) throw new Error('Order creation failed');
                    
                    // 2. Setup Razorpay Options
                    const options = {
                        "key": orderData.key,
                        "amount": Math.round(Number(orderData.amount || 0) * 100),
                        "currency": "INR",
                        "name": "Event Management",
                        "description": "Membership Subscription",
                        "order_id": orderData.order_id,
                        "handler": async (response) => {
                            this.paymentId = response.razorpay_payment_id;
                            
                            // 3. Verify on backend
                            const verifyResponse = await fetch("{{ route('member.subscription.verify') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                })
                            });
                            
                            const verifyData = await verifyResponse.json();
                            if (!verifyResponse.ok || !verifyData?.success) {
                                throw new Error(verifyData?.message || 'Payment verification failed');
                            }

                            this.transactionId = verifyData.transaction_id || '';
                            this.plan = verifyData.plan || null;
                            this.subscription = verifyData.subscription || null;
                            this.planSummary = `${verifyData?.plan?.subscription_type || ''} • ${this.humanCycle(verifyData?.plan?.payment_type)} Plan`;
                            const amt = verifyData?.subscription?.amount ?? orderData.amount ?? 0;
                            this.amountPaid = `${this.formatInr(amt)} ${verifyData?.subscription?.currency || 'INR'}`;
                            this.validTill = verifyData?.subscription?.end_date || '-';
                            
                            // 4. Show success modal
                            this.isProcessing = false;
                            this.modalStep = 1;
                            this.showModal = true;
                        },
                        "prefill": {
                            "name": "{{ $user->name ?? '' }}",
                            "email": "{{ $user->email ?? '' }}"
                        },
                        "theme": {
                            "color": "#4f46e5"
                        },
                        "modal": {
                            "ondismiss": () => {
                                this.isProcessing = false;
                            }
                        }
                    };
                    
                    const rzp = new window.Razorpay(options);
                    rzp.open();
                    
                } catch (error) {
                    console.error('Payment Error', error);
                    alert('Could not initialize payment. Please try again.');
                    this.isProcessing = false;
                }
            }
        }));
    });
</script>
@endsection

