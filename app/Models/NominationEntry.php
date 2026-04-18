<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominationEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomination_id',
        'position_id',
        'user_id',
        'response_status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'response_status' => 'string',
            'submitted_at' => 'datetime',
        ];
    }

    public function nomination(): BelongsTo
    {
        return $this->belongsTo(Nomination::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(NominationPosition::class, 'position_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

