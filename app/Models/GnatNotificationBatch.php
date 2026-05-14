<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class GnatNotificationBatch extends Model
{
    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $table = 'gnat_notification_batches';

    protected $fillable = [
        'initiated_by_admin_id',
        'notification_type',
        'entity_id',
        'entity_label',
        'total_recipients',
        'chunk_size',
        'chunks_total',
        'chunks_finished',
        'status',
        'meta',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'initiated_by_admin_id');
    }

    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(GnatNotificationDeliveryLog::class, 'batch_id');
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function start(
        ?int $adminId,
        string $notificationType,
        int $entityId,
        string $entityLabel,
        int $totalRecipients,
        int $chunkSize,
        array $meta = []
    ): self {
        $chunkSize = max(1, $chunkSize);
        $chunksTotal = $totalRecipients > 0 ? (int) ceil($totalRecipients / $chunkSize) : 0;

        return self::query()->create([
            'initiated_by_admin_id' => $adminId,
            'notification_type' => $notificationType,
            'entity_id' => $entityId,
            'entity_label' => mb_substr($entityLabel, 0, 255),
            'total_recipients' => $totalRecipients,
            'chunk_size' => $chunkSize,
            'chunks_total' => $chunksTotal,
            'chunks_finished' => 0,
            'status' => self::STATUS_PROCESSING,
            'meta' => $meta !== [] ? $meta : null,
        ]);
    }

    public function markChunkFinished(): void
    {
        DB::transaction(function () {
            /** @var self $batch */
            $batch = self::query()->lockForUpdate()->find($this->id);
            if (! $batch) {
                return;
            }
            $batch->increment('chunks_finished');
            $batch->refresh();
            if ($batch->chunks_total > 0 && $batch->chunks_finished >= $batch->chunks_total) {
                $batch->forceFill([
                    'status' => self::STATUS_COMPLETED,
                    'completed_at' => now(),
                ])->save();
            }
        });
    }

    public function markFailed(?string $message = null): void
    {
        $meta = $this->meta ?? [];
        if ($message) {
            $meta['failure_message'] = $message;
        }
        $this->forceFill([
            'status' => self::STATUS_FAILED,
            'meta' => $meta,
            'completed_at' => now(),
        ])->save();
    }
}
