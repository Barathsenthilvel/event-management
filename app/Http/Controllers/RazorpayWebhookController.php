<?php

namespace App\Http\Controllers;

use App\Models\MemberSubscription;
use App\Models\MembershipSubscriptionSetting;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\GnatMailService;
use App\Services\GnatSmsService;
use App\Services\MembershipLifecycleService;
use App\Support\MembershipPeriod;
use App\Support\RazorpayPaymentMode;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RazorpayWebhookController extends Controller
{
    /**
     * Handle Razorpay Webhook Callbacks (e.g. payment_link.paid, payment.captured)
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $webhookSecret = config('services.razorpay.webhook_secret');

        // 1. Verify Razorpay Webhook Signature if secret is configured
        if (filled($webhookSecret)) {
            if (blank($signature)) {
                Log::warning('Razorpay Webhook: Missing X-Razorpay-Signature header');
                return response()->json(['status' => 'error', 'message' => 'Signature header missing'], 400);
            }

            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            if (! hash_equals($expectedSignature, (string) $signature)) {
                Log::warning('Razorpay Webhook: Signature verification failed', [
                    'received_sig' => $signature,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid webhook signature'], 400);
            }
        }

        $data = json_decode($payload, true);
        if (! is_array($data)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid JSON payload'], 400);
        }

        $event = $data['event'] ?? '';
        Log::info('Razorpay Webhook received', ['event' => $event]);

        // 2. Route supported events
        if (in_array($event, ['payment_link.paid', 'payment.captured', 'order.paid'], true)) {
            return $this->processPaymentSuccess($data);
        }

        return response()->json(['status' => 'ignored', 'event' => $event], 200);
    }

    /**
     * Process successful payment from Razorpay webhook payload
     */
    private function processPaymentSuccess(array $data): JsonResponse
    {
        $payloadData       = $data['payload'] ?? [];
        $paymentLinkEntity = data_get($payloadData, 'payment_link.entity', []);
        $paymentEntity     = data_get($payloadData, 'payment.entity', []);
        $orderEntity       = data_get($payloadData, 'order.entity', []);

        // Extract key identifier fields
        $plinkId     = data_get($paymentLinkEntity, 'id');
        $referenceId = data_get($paymentLinkEntity, 'reference_id') ?? data_get($orderEntity, 'receipt');
        $orderId     = data_get($orderEntity, 'id') ?? data_get($paymentEntity, 'order_id');
        $paymentId   = data_get($paymentEntity, 'id');
        $rawMethod   = data_get($paymentEntity, 'method');
        $notes       = data_get($paymentLinkEntity, 'notes') ?: data_get($paymentEntity, 'notes', []);

        // Parse payment method
        $paymentMethod = filled($rawMethod) ? (string) $rawMethod : null;

        // 3. Find matching PaymentTransaction in DB
        $transaction = null;

        if (filled($plinkId)) {
            $transaction = PaymentTransaction::query()
                ->where('razorpay_payment_link_id', $plinkId)
                ->first();
        }

        if (! $transaction && filled($referenceId)) {
            $transaction = PaymentTransaction::query()
                ->where('reference_id', $referenceId)
                ->first();
        }

        if (! $transaction && filled($orderId)) {
            $transaction = PaymentTransaction::query()
                ->where('razorpay_order_id', $orderId)
                ->first();
        }

        if (! $transaction && is_array($notes) && isset($notes['user_id'])) {
            $transaction = PaymentTransaction::query()
                ->where('user_id', $notes['user_id'])
                ->whereIn('status', ['pending', 'failed'])
                ->latest()
                ->first();
        }

        // Handle case where transaction wasn't found in DB
        if (! $transaction) {
            Log::warning('Razorpay Webhook: Matching transaction not found', [
                'plink_id'     => $plinkId,
                'reference_id' => $referenceId,
                'order_id'     => $orderId,
                'payment_id'   => $paymentId,
                'notes'        => $notes,
            ]);

            return response()->json(['status' => 'transaction_not_found'], 404);
        }

        // 4. Idempotency Check: if already completed/successful, exit cleanly
        if (in_array($transaction->status, ['successful', 'completed'], true)) {
            Log::info('Razorpay Webhook: Transaction already processed', ['transaction_id' => $transaction->id]);
            return response()->json(['status' => 'already_processed'], 200);
        }

        // 5. Update PaymentTransaction record
        $transaction->razorpay_payment_id      = $paymentId ?? $transaction->razorpay_payment_id;
        $transaction->razorpay_order_id        = $orderId ?? $transaction->razorpay_order_id;
        $transaction->razorpay_payment_link_id = $plinkId ?? $transaction->razorpay_payment_link_id;
        $transaction->payment_method           = $paymentMethod ?? $transaction->payment_method;
        $transaction->status                   = 'successful';
        $transaction->paid_at                  = Carbon::now();
        $transaction->raw_payload              = $data;
        $transaction->save();

        // 6. Retrieve user & plan to activate membership subscription
        $user = User::find($transaction->user_id);
        if (! $user) {
            Log::error('Razorpay Webhook: User not found for transaction', ['transaction_id' => $transaction->id, 'user_id' => $transaction->user_id]);
            return response()->json(['status' => 'user_not_found'], 404);
        }

        $plan = $transaction->subscriptionPlan;
        if (! $plan && isset($notes['membership_setting_id'])) {
            $plan = MembershipSubscriptionSetting::find($notes['membership_setting_id']);
        }

        if ($plan) {
            $currentActive = $user->activeSubscription;
            $period = MembershipPeriod::buildPeriod($plan, $currentActive);
            $start = $period['start'];
            $end = $period['end'];
            $normalizedPaymentType = $period['payment_type'];

            // Expire previous active subscriptions natural lapse notification
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
                        Log::warning('Failed sending membership expired notice: '.$e->getMessage());
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

            // Update user status
            $user->update([
                'is_approved'       => true,
                'membership_status' => 'active',
            ]);

            // Send notification services
            try {
                $transaction->refresh();
                app(GnatMailService::class)->sendMembershipActivated($user, $subscription, $transaction);
            } catch (Throwable $e) {
                Log::warning('Razorpay Webhook: Mail send failed: '.$e->getMessage());
            }

            try {
                app(GnatSmsService::class)->membershipPaymentReceived($user->mobile, $user->name);
            } catch (Throwable $e) {
                Log::warning('Razorpay Webhook: SMS send failed: '.$e->getMessage());
            }

            try {
                app(MembershipLifecycleService::class)->syncUser($user->fresh());
            } catch (Throwable $e) {
                Log::warning('Razorpay Webhook: Lifecycle sync failed: '.$e->getMessage());
            }
        }

        Log::info('Razorpay Webhook: Payment processed and subscription activated successfully', [
            'user_id'        => $user->id,
            'transaction_id' => $transaction->id,
        ]);

        return response()->json([
            'status'         => 'success',
            'transaction_id' => $transaction->id,
            'user_id'        => $user->id,
        ], 200);
    }
}
