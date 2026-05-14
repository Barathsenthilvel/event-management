<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GnatNotificationDeliveryLog extends Model
{
    protected $table = 'gnat_notification_delivery_logs';

    protected $fillable = [
        'batch_id',
        'user_id',
        'email_status',
        'email_error',
        'sms_status',
        'sms_error',
        'whatsapp_status',
        'whatsapp_error',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(GnatNotificationBatch::class, 'batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  array<string, array{status: string, error?: ?string}>  $channels  keys: email, sms, whatsapp
     */
    public static function recordChannelResults(int $batchId, int $userId, array $channels): void
    {
        $log = static::query()->firstOrNew([
            'batch_id' => $batchId,
            'user_id' => $userId,
        ]);

        foreach (['email', 'sms', 'whatsapp'] as $key) {
            if (! isset($channels[$key])) {
                continue;
            }
            $payload = $channels[$key];
            $log->{$key.'_status'} = $payload['status'];
            $log->{$key.'_error'} = $payload['error'] ?? null;
        }

        $log->save();
    }
}
