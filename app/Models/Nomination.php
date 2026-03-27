<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_admin_id',
        'title',
        'terms',
        'polling_date',
        'polling_from',
        'polling_to',
        'cover_image_path',
        'banner_image_path',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'polling_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(NominationPosition::class)->orderBy('id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(NominationAlert::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(NominationEntry::class);
    }
}

