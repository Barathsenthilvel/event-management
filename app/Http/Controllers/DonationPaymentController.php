<?php

namespace App\Http\Controllers;

use App\Models\DonationPayment;
use App\Services\GnatMailService;
use App\Support\RazorpayPaymentMode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class DonationPaymentController extends Controller
{
    private const MAX_INR = 5_000_000;

    public function createOrder(Request $request): JsonResponse
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');
        if (!$key || !$secret) {
            return response()->json([
                'message' => 'Online donations are not available right now. Please try again later or contact us.',
            ], 503);
        }

        $user = $request->user();

        $rules = [
            'amount' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_INR],
            'donation_id' => ['nullable', 'integer', 'exists:donations,id'],
        ];

        if (!$user) {
            $rules['donor_name'] = ['required', 'string', 'max:255'];
            $rules['donor_email'] = ['required', 'email', 'max:255'];
            $rules['donor_mobile'] = ['required', 'string', 'max:30'];
            $rules['wants_membership'] = ['sometimes', 'boolean'];
        }

        $data = $request->validate($rules);

        $amountInr = round((float) $data['amount'], 2);
        $amountPaise = (int) round($amountInr * 100);

        if ($amountPaise < 100) {
            return response()->json(['message' => 'Minimum donation is ₹1.'], 422);
        }

        $donorName = $user ? (string) $user->name : trim((string) $data['donor_name']);
        $donorEmail = $user ? (string) $user->email : trim((string) $data['donor_email']);
        $donorMobile = $user
            ? trim((string) ($user->mobile ?? ''))
            : preg_replace('/\D/', '', (string) $data['donor_mobile']);

        if (!$user && strlen($donorMobile) < 10) {
            return response()->json(['message' => 'Please enter a valid mobile number (at least 10 digits).'], 422);
        }

        $donationId = isset($data['donation_id']) ? (int) $data['donation_id'] : null;
        $wantsMembership = !$user && filter_var($request->input('wants_membership'), FILTER_VALIDATE_BOOLEAN);

        $api = new Api($key, $secret);
        $order = $api->order->create([
            'amount' => $amountPaise,
            'currency' => 'INR',
            'payment_capture' => 1,
            'notes' => [
                'type' => 'public_donation',
                'donor_name' => $donorName,
                'donor_email' => $donorEmail,
            ],
        ]);

        $payment = DonationPayment::create([
            'donation_id' => $donationId ?: null,
            'user_id' => $user?->id,
            'donor_name' => $donorName,
            'donor_email' => $donorEmail,
            'donor_mobile' => $donorMobile !== '' ? $donorMobile : null,
            'amount' => $amountInr,
            'currency' => 'INR',
            'payment_gateway' => 'razorpay',
            'order_id' => $order['id'],
            'status' => 'pending',
            'meta' => [
                'wants_membership' => $wantsMembership,
            ],
        ]);

        $request->session()->put('public_donation.razorpay_order_id', $order['id']);
        $request->session()->put('public_donation.amount_inr', $amountInr);
        $request->session()->put('public_donation.donation_payment_id', $payment->id);

        return response()->json([
            'order_id' => $order['id'],
            'key' => $key,
            'amount' => $amountInr,
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $secret = config('services.razorpay.secret');
        if (!$secret) {
            return response()->json(['success' => false, 'message' => 'Payment verification is unavailable.'], 503);
        }

        $computed = hash_hmac(
            'sha256',
            $data['razorpay_order_id'] . '|' . $data['razorpay_payment_id'],
            $secret
        );
        if (!hash_equals($computed, $data['razorpay_signature'])) {
            return response()->json([
                'success' => false,
                'message' => 'Payment could not be verified. If money was debited, contact us with your payment ID.',
            ], 422);
        }

        $paymentId = $request->session()->get('public_donation.donation_payment_id');
        $payment = DonationPayment::query()
            ->when($paymentId, fn ($q) => $q->where('id', $paymentId))
            ->where('order_id', $data['razorpay_order_id'])
            ->latest('id')
            ->first();

        if (!$payment) {
            $payment = DonationPayment::query()
                ->where('order_id', $data['razorpay_order_id'])
                ->latest('id')
                ->first();
        }

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'We could not match this payment to your donation. Please contact us with your payment ID.',
            ], 422);
        }

        $amountInr = (float) ($payment->amount ?? $request->session()->get('public_donation.amount_inr', 0));

        $meta = $payment->meta ?? [];
        $meta['verified_at'] = now()->toIso8601String();

        $mode = RazorpayPaymentMode::fetch($data['razorpay_payment_id']);
        if ($mode['method']) {
            $meta['razorpay'] = array_filter([
                'method' => $mode['method'],
                'details' => $mode['details'],
            ]);
        }

        $payment->update([
            'status' => 'successful',
            'payment_id' => $data['razorpay_payment_id'],
            'payment_method' => $mode['method'],
            'paid_at' => now(),
            'meta' => $meta,
        ]);

        $payment->refresh();
        app(GnatMailService::class)->sendDonationReceipt($payment);

        $request->session()->forget([
            'public_donation.razorpay_order_id',
            'public_donation.amount_inr',
            'public_donation.donation_payment_id',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for supporting GNAT Association!',
            'amount' => $amountInr,
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_order_id' => $data['razorpay_order_id'],
        ]);
    }
}
