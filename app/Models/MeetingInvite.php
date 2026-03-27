<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'notify_whatsapp',
        'notify_sms',
        'notify_email',
        'invited_at',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'notify_whatsapp' => 'boolean',
            'notify_sms' => 'boolean',
            'notify_email' => 'boolean',
            'invited_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

