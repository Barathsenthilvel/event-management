<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EBook extends Model
{
    use HasFactory;

    protected $table = 'ebooks';

    protected $fillable = [
        'created_by_admin_id',
        'title',
        'code',
        'short_description',
        'description',
        'pricing_type',
        'price',
        'cover_image_path',
        'banner_image_path',
        'material_path',
        'promote_front',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'promote_front' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
