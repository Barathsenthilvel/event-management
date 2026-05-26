<?php

namespace App\Models;

use App\Services\EventScheduleStatusService;
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
        'member_notification_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'promote_front' => 'boolean',
            'is_active' => 'boolean',
            'member_notification_sent_at' => 'datetime',
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

    /**
     * Ensure cover and banner images appear in the event album gallery.
     */
    public function syncMediaToAlbum(): void
    {
        foreach (['cover_image_path', 'banner_image_path'] as $column) {
            $path = ltrim((string) $this->{$column}, '/');
            if ($path === '') {
                continue;
            }

            $this->photos()->firstOrCreate(['photo_path' => $path]);
        }
    }

    public function photoPathInUseAsEventMedia(string $path): bool
    {
        $normalized = ltrim($path, '/');

        return $normalized !== ''
            && ($normalized === ltrim((string) $this->cover_image_path, '/')
                || $normalized === ltrim((string) $this->banner_image_path, '/'));
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Organizer label on the public site and member portal (not the admin account name).
     */
    public function publicOrganizerName(): string
    {
        if (filled($this->created_by_admin_id)) {
            return (string) config('homepage.event_organizer_name', 'GNAT Association');
        }

        return $this->creator?->name
            ?: (string) config('homepage.event_organizer_name', 'GNAT Association');
    }

    /**
     * Upcoming / live / completed derived from event dates (same rules as auto status sync).
     */
    public function resolvedScheduleStatus(): ?string
    {
        $this->loadMissing('dates');

        return app(EventScheduleStatusService::class)->resolveStatusFromSchedule($this);
    }

    public function isPastRegistrationDeadline(): bool
    {
        if ($this->status === 'cancelled') {
            return true;
        }

        return $this->resolvedScheduleStatus() === 'completed';
    }

    /**
     * Public site: whether visitors can still register interest / attend (upcoming + live only, until the event ends).
     */
    public function acceptsPublicAttendance(): bool
    {
        if (! $this->is_active || $this->status === 'cancelled') {
            return false;
        }

        return in_array($this->resolvedScheduleStatus(), ['upcoming', 'live'], true);
    }

    public function isAtSeatLimit(): bool
    {
        return $this->seat_mode === 'limited'
            && $this->seat_limit !== null
            && (int) $this->interested_count >= (int) $this->seat_limit;
    }

    /**
     * Recompute interested_count: confirmed member invites (interested or attended) plus public interests
     * for users not already counted as confirmed invitees (avoids double-counting).
     */
    public function syncInterestedCountFromRegistrations(): void
    {
        $registeredUserIds = $this->invites()
            ->where('has_confirmed_interest', true)
            ->whereIn('participation_status', ['interested', 'participated'])
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $registeredInviteCount = $this->invites()
            ->where('has_confirmed_interest', true)
            ->whereIn('participation_status', ['interested', 'participated'])
            ->count();

        $extraInterests = $this->interests()
            ->where(function ($q) use ($registeredUserIds) {
                $q->whereNull('user_id');
                if ($registeredUserIds !== []) {
                    $q->orWhereNotIn('user_id', $registeredUserIds);
                }
            })
            ->count();

        $this->update([
            'interested_count' => $registeredInviteCount + $extraInterests,
        ]);
    }
}
