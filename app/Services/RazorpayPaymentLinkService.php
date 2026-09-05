<?php

namespace App\Services;

use App\Models\MemberSubscription;
use App\Models\MembershipSubscriptionSetting;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Support\MembershipPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Throwable;

class RazorpayPaymentLinkService
{
    /**
     * Create a Razorpay Payment Link for a member and record pending transaction.
     * Returns the short_url if created successfully, or fallback portal subscription URL on failure/unconfigured.
     */
    public function createPaymentLinkForUser(User $user, ?MembershipSubscriptionSetting $plan = null): string
    {
        $fallbackUrl = route('member.subscription.index');

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        if (blank($key) || blank($secret)) {
            Log::info('Razorpay credentials not configured, defaulting subscription URL to portal.');
            return $fallbackUrl;
        }

        if (! $plan) {
            // Default to 'New' yearly active membership plan
            $plan = MembershipSubscriptionSetting::query()
                ->where('subscription_type', 'New')
                ->where('is_active', true)
                ->orderBy('payment_type')
                ->first();
        }

        if (! $plan) {
            Log::warning('No active subscription plan found to generate Razorpay Payment Link.');
            return $fallbackUrl;
        }

        $registrationFee = ($plan->subscription_type === 'New' && $plan->registration_fee_enabled)
            ? (float) $plan->registration_fee
            : 0.0;
        $payableAmount = (float) $plan->membership_fee + $registrationFee;

        $referenceId = 'GNAT-' . $user->id . '-' . time();

        try {
            $api = new Api($key, $secret);

            $linkData = [
                'amount'          => (int) round($payableAmount * 100), // in paise
                'currency'        => 'INR',
                'accept_partial'  => false,
                'description'     => 'Membership Subscription - ' . $plan->name,
                'customer'        => [
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'contact' => $user->mobile ?? $user->phone,
                ],
                'notify'          => [
                    'sms'   => true,
                    'email' => true,
                ],
                'reminder_enable' => true,
                'notes'           => [
                    'user_id'               => (string) $user->id,
                    'membership_setting_id' => (string) $plan->id,
                    'subscription_type'     => $plan->subscription_type,
                ],
                'reference_id'    => $referenceId,
                'callback_url'    => route('payment.link.callback'),
                'callback_method' => 'get',
            ];

            $paymentLink = $api->paymentLink->create($linkData);

            $shortUrl = $paymentLink['short_url'] ?? $fallbackUrl;
            $plinkId  = $paymentLink['id'] ?? null;

            // Record pending transaction in database
            PaymentTransaction::create([
                'user_id'                            => $user->id,
                'membership_subscription_setting_id' => $plan->id,
                'razorpay_payment_link_id'           => $plinkId,
                'reference_id'                       => $referenceId,
                'amount'                             => $payableAmount,
                'status'                             => 'pending',
                'type'                               => $plan->subscription_type === 'New' ? 'new' : 'renewal',
                'raw_payload'                        => is_array($paymentLink) ? $paymentLink : null,
            ]);

            Log::info('Razorpay Payment Link created successfully for user', [
                'user_id'   => $user->id,
                'plink_id'  => $plinkId,
                'short_url' => $shortUrl,
            ]);

            return $shortUrl;
        } catch (Throwable $e) {
            Log::error('Failed to create Razorpay Payment Link', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return $fallbackUrl;
        }
    }

    /**
     * Query Razorpay API for the latest status of a Payment Link and update the local transaction.
     * If paid, activates the member subscription.
     */
    public function syncPaymentLinkStatus(PaymentTransaction $transaction): bool
    {
        if (blank($transaction->razorpay_payment_link_id)) {
            return false;
        }

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        if (blank($key) || blank($secret)) {
            return false;
        }

        try {
            $api = new Api($key, $secret);
            $plink = $api->paymentLink->fetch($transaction->razorpay_payment_link_id);
            $data = is_array($plink) ? $plink : $plink->toArray();

            $status = $data['status'] ?? '';
            $payments = $data['payments'] ?? [];
            $latestPayment = !empty($payments) ? end($payments) : [];

            $isPaid = ($status === 'paid') || (($latestPayment['status'] ?? '') === 'captured');

            if ($isPaid) {
                $paymentId = $latestPayment['payment_id'] ?? $transaction->razorpay_payment_id;
                $paymentMethod = $latestPayment['method'] ?? $transaction->payment_method;
                $orderId = $data['order_id'] ?? $transaction->razorpay_order_id;
                $paidAt = isset($latestPayment['created_at'])
                    ? Carbon::createFromTimestamp($latestPayment['created_at'])
                    : Carbon::now();

                $transaction->status = 'successful';
                $transaction->razorpay_payment_id = $paymentId;
                $transaction->razorpay_order_id = $orderId;
                $transaction->payment_method = $paymentMethod;
                $transaction->paid_at = $paidAt;
                $transaction->raw_payload = $data;
                $transaction->save();

                $this->activateSubscriptionForTransaction($transaction, $data);

                Log::info('Razorpay payment link synced successfully', [
                    'transaction_id' => $transaction->id,
                    'plink_id'       => $transaction->razorpay_payment_link_id,
                    'payment_id'     => $paymentId,
                ]);

                return true;
            } elseif (in_array($status, ['cancelled', 'expired'], true)) {
                $transaction->status = 'failed';
                $transaction->raw_payload = $data;
                $transaction->save();
            }

            return false;
        } catch (Throwable $e) {
            Log::error('Failed to sync Razorpay Payment Link', [
                'transaction_id' => $transaction->id,
                'plink_id'       => $transaction->razorpay_payment_link_id,
                'error'          => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Centralized and idempotent method to activate a member subscription from a successful transaction.
     */
    public function activateSubscriptionForTransaction(PaymentTransaction $transaction, ?array $rawPayload = null): ?MemberSubscription
    {
        $user = $transaction->user ?? User::find($transaction->user_id);
        if (! $user) {
            Log::error('User not found for payment transaction activation', ['transaction_id' => $transaction->id]);
            return null;
        }

        // Check if an active subscription for this order/payment already exists (idempotency)
        $existing = MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) use ($transaction) {
                if (filled($transaction->razorpay_order_id)) {
                    $q->orWhere('razorpay_order_id', $transaction->razorpay_order_id);
                }
                if (filled($transaction->razorpay_payment_id)) {
                    $q->orWhere('last_razorpay_payment_id', $transaction->razorpay_payment_id);
                }
            })
            ->first();

        if ($existing) {
            // Ensure user membership status is marked active
            $user->update([
                'is_approved'              => true,
                'membership_status'        => 'active',
                'membership_inactive_type' => null,
            ]);
            app(MembershipLifecycleService::class)->syncUser($user->fresh());
            return $existing;
        }

        $plan = $transaction->subscriptionPlan;
        if (! $plan && $transaction->membership_subscription_setting_id) {
            $plan = MembershipSubscriptionSetting::find($transaction->membership_subscription_setting_id);
        }

        if (! $plan) {
            $plan = MembershipSubscriptionSetting::query()
                ->where('subscription_type', 'New')
                ->where('is_active', true)
                ->orderBy('payment_type')
                ->first();
        }

        if (! $plan) {
            Log::error('No subscription plan found to activate for transaction', ['transaction_id' => $transaction->id]);
            return null;
        }

        $currentActive = $user->activeSubscription;
        $period = MembershipPeriod::buildPeriod($plan, $currentActive);
        $start = $period['start'];
        $end = $period['end'];
        $normalizedPaymentType = $period['payment_type'];

        // Expire older active subscriptions for user
        $today = Carbon::today()->toDateString();
        $superseded = MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        foreach ($superseded as $old) {
            $oldEnd = $old->end_date?->toDateString();
            if ($oldEnd !== null && $oldEnd < $today && $old->expiry_notification_sent_at === null) {
                try {
                    app(GnatMailService::class)->sendMembershipExpiredNotice($user->fresh(), $old);
                    $old->forceFill(['expiry_notification_sent_at' => now()])->save();
                } catch (Throwable $e) {
                    Log::warning('Failed sending membership expired notice: ' . $e->getMessage());
                }
            }
        }

        MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $subscription = MemberSubscription::create([
            'user_id'                            => $user->id,
            'membership_subscription_setting_id' => $plan->id,
            'subscription_type'                  => $plan->subscription_type,
            'payment_type'                       => $normalizedPaymentType ?? MembershipPeriod::normalizePaymentType($plan->payment_type),
            'amount'                             => $transaction->amount,
            'currency'                           => 'INR',
            'start_date'                         => $start,
            'end_date'                           => $end,
            'status'                             => 'active',
            'razorpay_order_id'                  => $transaction->razorpay_order_id,
            'last_razorpay_payment_id'           => $transaction->razorpay_payment_id,
        ]);

        $user->update([
            'is_approved'              => true,
            'membership_status'        => 'active',
            'membership_inactive_type' => null,
        ]);

        try {
            $transaction->refresh();
            app(GnatMailService::class)->sendMembershipActivated($user, $subscription, $transaction);
        } catch (Throwable $e) {
            Log::warning('Membership activated mail failed: ' . $e->getMessage());
        }

        try {
            if (filled($user->mobile)) {
                app(GnatSmsService::class)->membershipPaymentReceived($user->mobile, $user->name);
            }
        } catch (Throwable $e) {
            Log::warning('Membership activated SMS failed: ' . $e->getMessage());
        }

        try {
            app(MembershipLifecycleService::class)->syncUser($user->fresh());
        } catch (Throwable $e) {
            Log::warning('Membership lifecycle sync failed: ' . $e->getMessage());
        }

        return $subscription;
    }

    /**
     * Check and sync any pending payment links for a given user.
     */
    public function checkAndSyncPendingForUser(User $user): ?MemberSubscription
    {
        $pending = PaymentTransaction::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereNotNull('razorpay_payment_link_id')
            ->latest('id')
            ->get();

        foreach ($pending as $tx) {
            if ($this->syncPaymentLinkStatus($tx)) {
                return $user->activeSubscription()->first();
            }
        }

        return null;
    }
}
