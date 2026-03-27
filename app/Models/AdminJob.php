<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminJob extends Model
{
    use HasFactory;

    protected $table = 'admin_jobs';

    protected $fillable = [
        'created_by_admin_id',
        'hospital',
        'title',
        'code',
        'no_of_openings',
        'vacancy_permanent',
        'vacancy_temporary',
        'vacancy_any',
        'preference_wfh',
        'preference_onsite',
        'preference_any',
        'description',
        'key_skills',
        'promote_front',
        'listing_status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'vacancy_permanent' => 'boolean',
            'vacancy_temporary' => 'boolean',
            'vacancy_any' => 'boolean',
            'preference_wfh' => 'boolean',
            'preference_onsite' => 'boolean',
            'preference_any' => 'boolean',
            'promote_front' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(AdminJobAlert::class, 'job_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(AdminJobApplication::class, 'job_id');
    }
}

