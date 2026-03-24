<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_admin_id',
        'title',
        'description',
        'venue',
        'seat_mode',
        'seat_limit',
        'interested_count',
        'promote_front',
        'status',
        'is_active',
        'cover_image_path',
        'banner_image_path',
        'template_pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'promote_front' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function dates(): HasMany
    {
        return $this->hasMany(EventDate::class)->orderBy('event_date');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(EventInvite::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
