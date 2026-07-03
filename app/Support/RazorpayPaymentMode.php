<?php

namespace App\Support;

use Razorpay\Api\Api;
use Throwable;

class RazorpayPaymentMode
{
    /**
     * Fetch payment method details from Razorpay for a payment id.
     *
     * @return array{method: ?string, label: string, details: array<string, mixed>}
     */
    public static function fetch(string $razorpayPaymentId): array
    {
        $empty = [
            'method' => null,
            'label' => '—',
            'details' => [],
        ];

        $razorpayPaymentId = trim($razorpayPaymentId);
        if ($razorpayPaymentId === '') {
            return $empty;
        }

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');
        if (! $key || ! $secret) {
            return $empty;
        }

        try {
            $api = new Api($key, $secret);
            $payment = $api->payment->fetch($razorpayPaymentId);

            $payload = [
                'method' => $payment['method'] ?? null,
                'bank' => $payment['bank'] ?? null,
                'wallet' => $payment['wallet'] ?? null,
                'vpa' => $payment['vpa'] ?? null,
                'card' => [
                    'network' => data_get($payment, 'card.network') ?? ($payment['card']['network'] ?? null),
                    'type' => data_get($payment, 'card.type') ?? ($payment['card']['type'] ?? null),
                ],
            ];

            return self::fromPayload($payload);
        } catch (Throwable) {
            return $empty;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{method: ?string, label: string, details: array<string, mixed>}
     */
    public static function fromPayload(array $payload): array
    {
        $method = isset($payload['method']) ? strtolower(trim((string) $payload['method'])) : null;
        if ($method === '') {
            $method = null;
        }

        $details = array_filter([
            'bank' => $payload['bank'] ?? null,
            'wallet' => $payload['wallet'] ?? null,
            'vpa' => $payload['vpa'] ?? null,
            'card_network' => data_get($payload, 'card.network'),
            'card_type' => data_get($payload, 'card.type'),
        ], fn ($value) => filled($value));

        return [
            'method' => $method,
            'label' => self::label($method, $details),
            'details' => $details,
        ];
    }

    /**
     * Human-readable mode label for admin tables.
     *
     * @param  array<string, mixed>  $details
     */
    public static function label(?string $method, array $details = []): string
    {
        if ($method === null || trim($method) === '') {
            return '—';
        }

        $base = match (strtolower($method)) {
            'upi' => 'UPI',
            'card' => 'Card',
            'netbanking' => 'Net Banking',
            'wallet' => 'Wallet',
            'emi' => 'EMI',
            'cardless_emi' => 'Cardless EMI',
            'paylater' => 'Pay Later',
            'bank_transfer' => 'Bank Transfer',
            default => ucwords(str_replace('_', ' ', $method)),
        };

        $extra = $details['bank']
            ?? $details['wallet']
            ?? $details['card_network']
            ?? $details['vpa']
            ?? null;

        if (is_string($extra) && trim($extra) !== '') {
            return $base.' ('.trim($extra).')';
        }

        return $base;
    }

    public static function labelFromStored(?string $method): string
    {
        return self::label($method);
    }
}
