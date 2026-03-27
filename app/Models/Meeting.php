<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_admin_id',
        'title',
        'meeting_link',
        'description',
        'meeting_mode',
        'status',
        'cover_image_path',
        'banner_image_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(MeetingSchedule::class)->orderBy('meeting_date');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(MeetingInvite::class);
    }
}

