<?php

namespace App\Http\Controllers;

use App\Models\MemberSubscription;
use App\Models\MembershipSubscriptionSetting;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\GnatMailService;
use App\Services\MembershipLifecycleService;
use App\Support\MembershipPeriod;
use App\Support\RazorpayPaymentMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Illuminate\Http\JsonResponse;

class MemberSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->profile_completed || !$user->is_approved) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Your profile is awaiting admin approval. Subscription plans will be visible after approval.');
        }

        $paymentLinkService = app(\App\Services\RazorpayPaymentLinkService::class);

        // Handle Razorpay Payment Link redirect callback
        $plinkId = $request->query('razorpay_payment_link_id');
        $paymentId = $request->query('razorpay_payment_id');
        if (filled($plinkId) || filled($paymentId)) {
            $tx = PaymentTransaction::query()
                ->where('user_id', $user->id)
                ->when(filled($plinkId), fn ($q) => $q->where('razorpay_payment_link_id', $plinkId))
                ->when(filled($paymentId), fn ($q) => $q->orWhere('razorpay_payment_id', $paymentId))
                ->latest('id')
                ->first();

            if ($tx) {
                $paymentLinkService->syncPaymentLinkStatus($tx);
                if ($tx->fresh()->status === 'successful') {
                    return redirect()->route('member.subscription.index')
                        ->with('success', 'Payment successful! Your membership subscription is now active.');
                }
            }
        }

        // Auto-sync any pending payment links for this user if no active subscription
        if (! $user->activeSubscription()->exists()) {
            $activatedSub = $paymentLinkService->checkAndSyncPendingForUser($user);
            if ($activatedSub) {
                $user->refresh();
                session()->flash('success', 'Payment verified! Your membership subscription has been activated.');
            }
        }

        $activeSubscription = $user?->activeSubscription;

        $query = MembershipSubscriptionSetting::query()
            ->where('is_active', true)
            ->orderBy('subscription_type')
            ->orderBy('payment_type')
        ;

        // UI/UX rule:
        // - If member already has a subscription record (activeSubscription exists), show ONLY Renewal plans.
        // - If member has no subscription record, show ONLY New plans.
        $filterType = !empty($activeSubscription) ? 'Renewal' : 'New';
        $query->where('subscription_type', $filterType);

        // New registrations are only offered the yearly pass by default.
        if ($filterType === 'New') {
            $query->where('payment_type', 'yearly');
        }

        // Block renewal during paid period only; grace period allows renewal.
        if (
            $filterType === 'Renewal'
            && MembershipLifecycleService::renewalBlockedDuringPaidPeriod($activeSubscription)
        ) {
            session()->flash('renewal_blocked', true);
            session()->flash(
                'renewal_blocked_message',
                'Your current billing period is still active. You can renew during the grace period after it ends, or once access expires.'
            );
        }

        $plans = $query->get();

        $preselectedPlanId = null;
        $candidate = (int) $request->query('plan', 0);
        if ($candidate > 0 && $plans->contains(fn ($p) => (int) $p->id === $candidate)) {
            $preselectedPlanId = $candidate;
        }

        return view('member.subscription.index', [
            'user' => $user,
            'plans' => $plans,
            'settingsCount' => $plans->count(),
            'activeSubscription' => $activeSubscription,
            'filterType' => $filterType,
            'preselectedPlanId' => $preselectedPlanId,
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'membership_setting_id' => ['required', 'integer', 'exists:membership_subscription_settings,id'],
        ]);

        $plan = MembershipSubscriptionSetting::query()
            ->where('id', $data['membership_setting_id'])
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return redirect()->route('member.subscription.index')
                ->with('error', 'Selected subscription plan is not available.');
        }

        $user = Auth::user();
        $activeSubscription = $user?->activeSubscription;
        if (
            $plan->subscription_type === 'Renewal'
            && MembershipLifecycleService::renewalBlockedDuringPaidPeriod($activeSubscription)
        ) {
            return redirect()
                ->route('member.subscription.index', ['type' => 'New'])
                ->with('renewal_blocked', true)
                ->with('renewal_blocked_message', 'Your current billing period is still active. You can renew during the grace period after it ends.');
        }

        $registrationFee = 0.0;
        if ($plan->subscription_type === 'New' && $plan->registration_fee_enabled) {
            $registrationFee = (float) $plan->registration_fee;
        }

        $payableAmount = (float) $plan->membership_fee + $registrationFee;

        $request->session()->put('member.selected_membership_setting_id', $plan->id);
        $request->session()->put('member.selected_membership_payable_amount', $payableAmount);

        return redirect()->route('member.subscription.checkout.show');
    }

    public function showCheckout(Request $request)
    {
        $id = $request->session()->get('member.selected_membership_setting_id');
        if (!$id) {
            return redirect()->route('member.subscription.index')
                ->with('error', 'Please select a subscription plan first.');
        }

        $plan = MembershipSubscriptionSetting::query()->find($id);
        if (!$plan) {
            $request->session()->forget([
                'member.selected_membership_setting_id',
                'member.selected_membership_payable_amount',
            ]);
            return redirect()->route('member.subscription.index')
                ->with('error', 'Selected subscription plan is no longer available.');
        }

        $payableAmount = (float) $request->session()->get(
            'member.selected_membership_payable_amount',
            (float) $plan->membership_fee
        );

        return view('member.subscription.checkout', [
            'user' => Auth::user(),
            'plan' => $plan,
            'payableAmount' => $payableAmount,
        ]);
    }

    public function createOrder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'membership_setting_id' => ['required', 'integer', 'exists:membership_subscription_settings,id'],
        ]);

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        $api = new Api($key, $secret);

        $plan = MembershipSubscriptionSetting::query()
            ->where('id', $data['membership_setting_id'])
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return response()->json(['message' => 'Selected subscription plan is not available.'], 422);
        }

        $user = Auth::user();
        $activeSubscription = $user?->activeSubscription;
        if (
            $plan->subscription_type === 'Renewal'
            && MembershipLifecycleService::renewalBlockedDuringPaidPeriod($activeSubscription)
        ) {
            return response()->json([
                'message' => 'Renewal is not available during the paid billing period. You can renew during the grace period after it ends.',
                'renewal_blocked' => true,
                'valid_till' => $activeSubscription->formattedValidTillDate(),
            ], 422);
        }

        $registrationFee = 0.0;
        if ($plan->subscription_type === 'New' && $plan->registration_fee_enabled) {
            $registrationFee = (float) $plan->registration_fee;
        }
        $payableAmount = (float) $plan->membership_fee + $registrationFee;

        $order = $api->order->create([
            'amount'          => (int) round($payableAmount * 100), // paise
            'currency'        => 'INR',
            'payment_capture' => 1,
        ]);

        $request->session()->put('member.selected_membership_setting_id', $plan->id);
        $request->session()->put('member.selected_membership_payable_amount', $payableAmount);
        $request->session()->put('member.razorpay_order_id', $order['id']);

        $transaction = PaymentTransaction::create([
            'user_id' => Auth::id(),
            'membership_subscription_setting_id' => $plan->id,
            'razorpay_order_id' => $order['id'],
            'amount' => $payableAmount,
            'status' => 'pending',
            'type' => $plan->subscription_type === 'New' ? 'new' : 'renewal',
        ]);

        return response()->json([
            'order_id' => $order['id'],
            'key'      => $key,
            'amount'   => $payableAmount,
            'transaction_id' => $transaction->id,
        ]);
    }
    /**
     * Public callback handler for Razorpay Payment Link (mobile web and desktop web).
     */
    public function handlePaymentLinkCallback(Request $request)
    {
        $plinkId = $request->query('razorpay_payment_link_id');
        $paymentId = $request->query('razorpay_payment_id');
        $referenceId = $request->query('razorpay_payment_link_reference_id');

        $tx = null;
        if (filled($plinkId)) {
            $tx = PaymentTransaction::where('razorpay_payment_link_id', $plinkId)->latest('id')->first();
        }
        if (! $tx && filled($referenceId)) {
            $tx = PaymentTransaction::where('reference_id', $referenceId)->latest('id')->first();
        }
        if (! $tx && filled($paymentId)) {
            $tx = PaymentTransaction::where('razorpay_payment_id', $paymentId)->latest('id')->first();
        }

        if ($tx) {
            $service = app(\App\Services\RazorpayPaymentLinkService::class);
            $service->syncPaymentLinkStatus($tx);

            $user = User::find($tx->user_id);
            if ($user && ! Auth::check()) {
                Auth::login($user);
            }

            if ($tx->fresh()->status === 'successful') {
                return redirect()->route('member.subscription.index')
                    ->with('success', 'Payment of INR ' . number_format((float) $tx->amount, 2) . ' completed successfully! Your membership is active.');
            }
        }

        if (Auth::check()) {
            return redirect()->route('member.subscription.index');
        }

        return redirect()->route('member.login')
            ->with('success', 'Payment completed successfully! Please log in to view your membership.');
    }

    public function verifyPayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $secret = config('services.razorpay.secret');
        $computedSignature = hash_hmac('sha256', $data['razorpay_order_id'] . '|' . $data['razorpay_payment_id'], $secret);
        if (!hash_equals($computedSignature, $data['razorpay_signature'])) {
            return response()->json(['success' => false, 'message' => 'Payment signature verification failed.'], 422);
        }

        // Find transaction by order_id
        $transaction = PaymentTransaction::query()
            ->where('razorpay_order_id', $data['razorpay_order_id'])
            ->latest('id')
            ->first();

        $userId = Auth::id() ?? $transaction?->user_id;
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User context not found.'], 422);
        }

        // If mobile browser lost auth session during app switch (UPI/GPay/PhonePe), re-authenticate user
        if (!Auth::check() && $userId) {
            Auth::loginUsingId($userId);
        }

        $settingId = $transaction?->membership_subscription_setting_id 
            ?? $request->session()->get('member.selected_membership_setting_id');
        $payableAmount = (float) ($transaction?->amount 
            ?? $request->session()->get('member.selected_membership_payable_amount', 0));

        $plan = MembershipSubscriptionSetting::query()
            ->where('id', $settingId)
            ->first();

        if (!$plan && $transaction) {
            $plan = $transaction->subscriptionPlan;
        }

        if (!$transaction) {
            $transaction = PaymentTransaction::create([
                'user_id' => $userId,
                'membership_subscription_setting_id' => $plan?->id,
                'razorpay_order_id' => $data['razorpay_order_id'],
                'amount' => $payableAmount > 0 ? $payableAmount : (float) ($plan?->membership_fee ?? 0),
                'status' => 'pending',
                'type' => ($plan?->subscription_type === 'New') ? 'new' : 'renewal',
            ]);
        }

        $mode = RazorpayPaymentMode::fetch($data['razorpay_payment_id']);
        $payload = $data;
        if ($mode['method']) {
            $payload['method'] = $mode['method'];
            $payload['payment_mode_details'] = $mode['details'];
        }

        $transaction->razorpay_payment_id = $data['razorpay_payment_id'];
        $transaction->razorpay_signature = $data['razorpay_signature'];
        $transaction->payment_method = $mode['method'];
        $transaction->status = 'successful';
        $transaction->paid_at = now();
        $transaction->raw_payload = $payload;
        $transaction->save();

        // Centralized activation in member_subscriptions and users tables
        $subscription = app(\App\Services\RazorpayPaymentLinkService::class)->activateSubscriptionForTransaction($transaction, $payload);

        $request->session()->forget([
            'member.selected_membership_setting_id',
            'member.selected_membership_payable_amount',
            'member.razorpay_order_id',
        ]);

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'subscription_id' => $subscription?->id,
            'message' => 'Payment successful',
            'plan' => $plan ? [
                'id' => $plan->id,
                'subscription_type' => $plan->subscription_type,
                'payment_type' => (string) $plan->payment_type,
                'membership_fee' => (float) $plan->membership_fee,
                'registration_fee' => (float) $plan->registration_fee,
                'registration_fee_enabled' => (bool) $plan->registration_fee_enabled,
                'grace_period' => (int) ($plan->grace_period ?? 0),
            ] : null,
            'subscription' => $subscription ? [
                'start_date' => $subscription->formattedStartDate(),
                'end_date' => $subscription->formattedValidTillDate(),
                'access_until' => $subscription->formattedEndDate(),
                'grace_days' => $subscription->graceDays(),
                'amount' => (float) $subscription->amount,
                'currency' => $subscription->currency,
            ] : null,
        ]);
    }

    public function downloadInvoice($id)
    {
        $transaction = PaymentTransaction::with('subscriptionPlan', 'user')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $subscription = null;
        if ($transaction->razorpay_order_id) {
            $subscription = MemberSubscription::query()
                ->where('user_id', $transaction->user_id)
                ->where('razorpay_order_id', $transaction->razorpay_order_id)
                ->first();
        }

        $contact = config('homepage.contact', []);

        return view('member.subscription.invoice', [
            'transaction' => $transaction,
            'user' => $transaction->user ?? Auth::user(),
            'plan' => $transaction->subscriptionPlan,
            'subscription' => $subscription,
            'company' => [
                'name' => 'GNAT Association',
                'address' => (string) ($contact['address'] ?? ''),
                'email' => (string) ($contact['email'] ?? ''),
            ],
        ]);
    }
}
