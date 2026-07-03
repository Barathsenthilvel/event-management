<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'user_id',
        'donor_name',
        'donor_email',
        'donor_mobile',
        'amount',
        'currency',
        'payment_gateway',
        'payment_method',
        'payment_id',
        'order_id',
        'status',
        'paid_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function paymentModeLabel(): string
    {
        $stored = $this->payment_method;
        if (filled($stored)) {
            return \App\Support\RazorpayPaymentMode::labelFromStored($stored);
        }

        $metaMethod = data_get($this->meta, 'razorpay.method');
        if (filled($metaMethod)) {
            return \App\Support\RazorpayPaymentMode::labelFromStored((string) $metaMethod);
        }

        return '—';
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

