<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeGalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_admin_id',
        'category_key',
        'layout_type',
        'image_path',
        'alt_text',
        'eyebrow',
        'title',
        'description_text',
        'sort_order',
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
}
