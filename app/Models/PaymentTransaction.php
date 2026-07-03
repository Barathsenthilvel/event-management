<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'membership_subscription_setting_id',
        'razorpay_payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'payment_method',
        'amount',
        'status',
        'type',
        'paid_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function paymentModeLabel(): string
    {
        $stored = $this->payment_method;
        if (filled($stored)) {
            return \App\Support\RazorpayPaymentMode::labelFromStored($stored);
        }

        $payloadMethod = data_get($this->raw_payload, 'method')
            ?? data_get($this->raw_payload, 'razorpay_method');
        if (filled($payloadMethod)) {
            return \App\Support\RazorpayPaymentMode::labelFromStored((string) $payloadMethod);
        }

        return '—';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(MembershipSubscriptionSetting::class, 'membership_subscription_setting_id');
    }
}
