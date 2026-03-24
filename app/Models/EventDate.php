<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_date',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
