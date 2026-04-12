<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollingPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'polling_id',
        'position',
        'winner_user_id',
    ];

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function polling(): BelongsTo
    {
        return $this->belongsTo(Polling::class);
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'polling_position_candidates')
            ->withTimestamps()
            ->orderByPivot('id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollingVote::class, 'position_id');
    }
}
