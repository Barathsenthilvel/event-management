<?php

namespace App\Services;

use App\Models\MembershipSubscriptionSetting;
use App\Models\PaymentTransaction;
use App\Models\User;
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
                'callback_url'    => route('member.subscription.index'),
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
}
