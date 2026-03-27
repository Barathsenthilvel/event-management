<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminJobAlert extends Model
{
    use HasFactory;

    protected $table = 'admin_job_alerts';

    protected $fillable = [
        'job_id',
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

    public function job(): BelongsTo
    {
        return $this->belongsTo(AdminJob::class, 'job_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

