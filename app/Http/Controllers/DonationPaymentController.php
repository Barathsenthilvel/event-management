<?php

namespace App\Http\Controllers;

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

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_INR],
        ]);

        $amountInr = round((float) $data['amount'], 2);
        $amountPaise = (int) round($amountInr * 100);

        if ($amountPaise < 100) {
            return response()->json(['message' => 'Minimum donation is ₹1.'], 422);
        }

        $api = new Api($key, $secret);
        $order = $api->order->create([
            'amount' => $amountPaise,
            'currency' => 'INR',
            'payment_capture' => 1,
            'notes' => [
                'type' => 'public_donation',
            ],
        ]);

        $request->session()->put('public_donation.razorpay_order_id', $order['id']);
        $request->session()->put('public_donation.amount_inr', $amountInr);

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

        $expectedOrderId = $request->session()->get('public_donation.razorpay_order_id');
        $amountInr = (float) $request->session()->get('public_donation.amount_inr', 0);

        if (!$expectedOrderId || $expectedOrderId !== $data['razorpay_order_id']) {
            return response()->json([
                'success' => false,
                'message' => 'This payment session is no longer valid. Please start again.',
            ], 422);
        }

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

        $request->session()->forget([
            'public_donation.razorpay_order_id',
            'public_donation.amount_inr',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for supporting GNAT Donation!',
            'amount' => $amountInr,
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_order_id' => $data['razorpay_order_id'],
        ]);
    }
}
