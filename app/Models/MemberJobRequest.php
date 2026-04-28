<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberJobRequest extends Model
{
    use HasFactory;

    protected $table = 'member_job_requests';

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'email',
        'qualification',
        'position_looking_for',
        'experience',
        'details',
        'resume_path',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

