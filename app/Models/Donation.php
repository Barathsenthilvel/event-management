<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';

    protected $fillable = [
        'created_by_admin_id',
        'purpose',
        'short_description',
        'description',
        'cover_image_path',
        'banner_image_path',
        'promote_front',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'promote_front' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}

