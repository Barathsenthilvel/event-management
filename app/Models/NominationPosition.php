<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NominationPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomination_id',
        'position',
        'member_user_id',
    ];

    public function nomination(): BelongsTo
    {
        return $this->belongsTo(Nomination::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_user_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(NominationEntry::class, 'position_id');
    }
}

