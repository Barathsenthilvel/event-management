<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipSubscriptionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_type',
        'membership_fee',
        'registration_fee',
        'registration_fee_enabled',
        'payment_type',
        'grace_period',
        'discount_based_on_payment',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'membership_fee'           => 'decimal:2',
            'registration_fee'         => 'decimal:2',
            'registration_fee_enabled' => 'boolean',
            'grace_period'             => 'integer',
            'discount_based_on_payment' => 'boolean',
            'is_active'                => 'boolean',
        ];
    }
}
