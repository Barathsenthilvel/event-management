<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Polling extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_admin_id',
        'title',
        'polling_date',
        'polling_date_to',
        'polling_from',
        'polling_to',
        'cover_image_path',
        'banner_image_path',
        'promote_front',
        'publish_status',
        'polling_status',
        'show_stats',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'polling_date' => 'date',
            'polling_date_to' => 'date',
            'promote_front' => 'boolean',
            'show_stats' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(PollingPosition::class)->orderBy('id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollingVote::class);
    }
}

