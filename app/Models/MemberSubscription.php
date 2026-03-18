<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_subscription_setting_id',
        'subscription_type',
        'payment_type',
        'amount',
        'currency',
        'start_date',
        'end_date',
        'status',
        'razorpay_order_id',
        'last_razorpay_payment_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(MembershipSubscriptionSetting::class, 'membership_subscription_setting_id');
    }
}

