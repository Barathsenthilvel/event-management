<?php

namespace App\Http\Controllers;

use App\Models\MemberSubscription;
use App\Models\MembershipSubscriptionSetting;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Illuminate\Http\JsonResponse;

class MemberSubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeSubscription = $user?->activeSubscription;

        if (!$user->profile_completed || !$user->is_approved) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Your profile is awaiting admin approval. Subscription plans will be visible after approval.');
        }

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

        // If validity is NOT expired, show renewal blocked popup message.
        if (
            $filterType === 'Renewal'
            && !empty($activeSubscription?->end_date)
            && Carbon::parse($activeSubscription->end_date)->isFuture()
        ) {
            session()->flash('renewal_blocked', true);
            session()->flash(
                'renewal_blocked_message',
                'Your membership is still active. Please wait until it expires, then renew.'
            );
        }

        $plans = $query->get();

        return view('member.subscription.index', [
            'user' => $user,
            'plans' => $plans,
            'settingsCount' => $plans->count(),
            'activeSubscription' => $activeSubscription,
            'filterType' => $filterType,
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
            && !empty($activeSubscription?->end_date)
            && Carbon::parse($activeSubscription->end_date)->isFuture()
        ) {
            return redirect()
                ->route('member.subscription.index', ['type' => 'New'])
                ->with('renewal_blocked', true)
                ->with('renewal_blocked_message', 'Your current membership is still active. Please wait until it expires, then you can purchase a Renewal plan.');
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
            && !empty($activeSubscription?->end_date)
            && Carbon::parse($activeSubscription->end_date)->isFuture()
        ) {
            return response()->json([
                'message' => 'Renewal not available while your membership is active. Please wait until your plan expires, then renew.',
                'renewal_blocked' => true,
                'valid_till' => optional($activeSubscription->end_date)->format('d M Y'),
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
    public function verifyPayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $settingId = $request->session()->get('member.selected_membership_setting_id');
        $expectedOrderId = $request->session()->get('member.razorpay_order_id');
        $payableAmount = (float) $request->session()->get('member.selected_membership_payable_amount', 0);

        $plan = MembershipSubscriptionSetting::query()
            ->where('id', $settingId)
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Subscription plan not found.'], 400);
        }

        if (!$expectedOrderId || $expectedOrderId !== $data['razorpay_order_id']) {
            return response()->json(['success' => false, 'message' => 'Invalid order context.'], 422);
        }

        $secret = config('services.razorpay.secret');
        $computedSignature = hash_hmac('sha256', $data['razorpay_order_id'] . '|' . $data['razorpay_payment_id'], $secret);
        if (!hash_equals($computedSignature, $data['razorpay_signature'])) {
            return response()->json(['success' => false, 'message' => 'Payment signature verification failed.'], 422);
        }

        $transaction = PaymentTransaction::query()
            ->where('user_id', Auth::id())
            ->where('razorpay_order_id', $data['razorpay_order_id'])
            ->latest()
            ->first();

        if (!$transaction) {
            $transaction = PaymentTransaction::create([
                'user_id' => Auth::id(),
                'membership_subscription_setting_id' => $plan->id,
                'razorpay_order_id' => $data['razorpay_order_id'],
                'amount' => $payableAmount > 0 ? $payableAmount : (float) $plan->membership_fee,
                'status' => 'pending',
                'type' => $plan->subscription_type === 'New' ? 'new' : 'renewal',
            ]);
        }

        $transaction->razorpay_payment_id = $data['razorpay_payment_id'];
        $transaction->razorpay_signature = $data['razorpay_signature'];
        $transaction->status = 'successful';
        $transaction->paid_at = now();
        $transaction->raw_payload = $data;
        $transaction->save();

        $start = Carbon::today();
        $months = match ((string) $plan->payment_type) {
            'monthly' => 1,
            'bi_monthly' => 2,
            'quarterly' => 3,
            'half_yearly' => 6,
            'yearly' => 12,
            default => 12,
        };
        $end = (clone $start)->addMonthsNoOverflow($months);

        // If a grace period exists (days), extend the end date.
        if (!empty($plan->grace_period) && (int) $plan->grace_period > 0) {
            $end = (clone $end)->addDays((int) $plan->grace_period);
        }

        // Expire any previous active subscriptions.
        MemberSubscription::query()
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $subscription = MemberSubscription::create([
            'user_id' => Auth::id(),
            'membership_subscription_setting_id' => $plan->id,
            'subscription_type' => $plan->subscription_type,
            'payment_type' => (string) $plan->payment_type,
            'amount' => $transaction->amount,
            'currency' => 'INR',
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'active',
            'razorpay_order_id' => $data['razorpay_order_id'],
            'last_razorpay_payment_id' => $data['razorpay_payment_id'],
        ]);

        $request->session()->forget([
            'member.selected_membership_setting_id',
            'member.selected_membership_payable_amount',
            'member.razorpay_order_id',
        ]);

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'subscription_id' => $subscription->id,
            'message' => 'Payment successful',
            'plan' => [
                'id' => $plan->id,
                'subscription_type' => $plan->subscription_type,
                'payment_type' => (string) $plan->payment_type,
                'membership_fee' => (float) $plan->membership_fee,
                'registration_fee' => (float) $plan->registration_fee,
                'registration_fee_enabled' => (bool) $plan->registration_fee_enabled,
                'grace_period' => (int) ($plan->grace_period ?? 0),
            ],
            'subscription' => [
                'start_date' => $subscription->start_date?->format('d M Y'),
                'end_date' => $subscription->end_date?->format('d M Y'),
                'amount' => (float) $subscription->amount,
                'currency' => $subscription->currency,
            ],
        ]);
    }

    public function downloadInvoice($id)
    {
        $transaction = \App\Models\PaymentTransaction::with('subscriptionPlan', 'user')
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            
        return view('member.subscription.invoice', [
            'transaction' => $transaction,
            'user' => Auth::user(),
            'plan' => $transaction->subscriptionPlan,
        ]);
    }
}
