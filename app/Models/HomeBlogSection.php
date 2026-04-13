<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeBlogSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_admin_id',
        'section_badge',
        'section_title',
        'section_description',
        'section_button_text',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
