<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use CanResetPassword, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'qualification',
        'blood_group',
        'rnrm_number_with_date',
        'college_name',
        'door_no',
        'locality_area',
        'state',
        'pin_code',
        'council_state',
        'currently_working',
        'educational_certificate_path',
        'aadhar_card_path',
        'passport_photo_path',
        'email',
        'mobile',
        'profile_completed',
        'is_approved',
        'designation_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'profile_completed' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(MemberSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(MemberSubscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function eventInvites(): HasMany
    {
        return $this->hasMany(EventInvite::class);
    }
}
