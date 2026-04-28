<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSavedJob extends Model
{
    use HasFactory;

    protected $table = 'member_saved_jobs';

    protected $fillable = [
        'job_id',
        'user_id',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(AdminJob::class, 'job_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

