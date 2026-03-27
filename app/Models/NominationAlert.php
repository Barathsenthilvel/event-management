<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominationAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomination_id',
        'user_id',
        'notify_whatsapp',
        'notify_sms',
        'notify_email',
        'alert_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'notify_whatsapp' => 'boolean',
            'notify_sms' => 'boolean',
            'notify_email' => 'boolean',
            'alert_sent_at' => 'datetime',
        ];
    }

    public function nomination(): BelongsTo
    {
        return $this->belongsTo(Nomination::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

