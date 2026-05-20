<?php

namespace App\Models;

use App\Support\MembershipPeriod;
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
        'renewal_reminder_sent_at',
        'expiry_notification_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'renewal_reminder_sent_at' => 'datetime',
            'expiry_notification_sent_at' => 'datetime',
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

    public function isValidThrough(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return MembershipPeriod::isValidThrough($this->end_date);
    }

    public function formattedEndDate(): string
    {
        return MembershipPeriod::formatDate($this->end_date);
    }

    public function formattedStartDate(): string
    {
        return MembershipPeriod::formatDate($this->start_date);
    }
}

