<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function interests(): HasMany
    {
        return $this->hasMany(EventInterest::class)->orderByDesc('created_at');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function isPastRegistrationDeadline(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }

        $this->loadMissing('dates');
        $lastDate = $this->dates->sortBy('event_date')->last();
        if (! $lastDate?->event_date) {
            return false;
        }

        $endTime = $lastDate->end_time ?: '23:59';
        $endAt = Carbon::parse($lastDate->event_date->format('Y-m-d').' '.$endTime);

        return now()->greaterThan($endAt);
    }

    public function isAtSeatLimit(): bool
    {
        return $this->seat_mode === 'limited'
            && $this->seat_limit !== null
            && (int) $this->interested_count >= (int) $this->seat_limit;
    }

    /**
     * Recompute interested_count: all invites plus guest interests and member interests
     * that are not already represented by an invite (avoids double-counting the same user).
     */
    public function syncInterestedCountFromRegistrations(): void
    {
        $invitedUserIds = $this->invites()->pluck('user_id')->filter()->unique()->values()->all();

        $extraInterests = $this->interests()
            ->where(function ($q) use ($invitedUserIds) {
                $q->whereNull('user_id');
                if ($invitedUserIds !== []) {
                    $q->orWhereNotIn('user_id', $invitedUserIds);
                }
            })
            ->count();

        $this->update([
            'interested_count' => $this->invites()->count() + $extraInterests,
        ]);
    }
}
