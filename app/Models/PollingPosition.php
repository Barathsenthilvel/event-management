<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollingPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'polling_id',
        'position',
        'member_user_id',
    ];

    public function polling(): BelongsTo
    {
        return $this->belongsTo(Polling::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_user_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollingVote::class, 'position_id');
    }
}

