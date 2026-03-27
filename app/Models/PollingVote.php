<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollingVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'polling_id',
        'position_id',
        'candidate_user_id',
        'voter_user_id',
        'voted_at',
    ];

    protected function casts(): array
    {
        return [
            'voted_at' => 'datetime',
        ];
    }

    public function polling(): BelongsTo
    {
        return $this->belongsTo(Polling::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(PollingPosition::class, 'position_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_user_id');
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voter_user_id');
    }
}

