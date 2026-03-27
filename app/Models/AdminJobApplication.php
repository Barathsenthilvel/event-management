<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminJobApplication extends Model
{
    use HasFactory;

    protected $table = 'admin_job_applications';

    protected $fillable = [
        'job_id',
        'user_id',
        'application_status',
        'resume_path',
        'submitted_at',
        'status_emailed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'status_emailed_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(AdminJob::class, 'job_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

