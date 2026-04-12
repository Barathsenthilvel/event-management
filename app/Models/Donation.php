<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    public const PILL_SOURCES = ['donation', 'charity', 'association', 'community', 'custom'];

    protected $table = 'donations';

    protected $fillable = [
        'created_by_admin_id',
        'purpose',
        'short_description',
        'description',
        'pill_tag_1',
        'pill_tag_2',
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

    public static function pillLabelFromSource(string $source, ?string $custom): string
    {
        if ($source === 'custom') {
            $text = trim(strip_tags((string) $custom));

            return $text === '' ? 'Custom' : Str::limit($text, 48, '');
        }

        return match ($source) {
            'donation' => 'Donation',
            'charity' => 'Charity',
            'association' => 'Association',
            'community' => 'Community',
            default => 'Donation',
        };
    }

    /**
     * @return array{0: string, 1: string}
     */
    public static function sourceAndCustomFromStored(?string $storedLabel): array
    {
        $label = trim((string) $storedLabel);
        $presets = [
            'donation' => 'Donation',
            'charity' => 'Charity',
            'association' => 'Association',
            'community' => 'Community',
        ];
        foreach ($presets as $key => $text) {
            if ($text === $label) {
                return [$key, ''];
            }
        }

        return ['custom', $label];
    }

    /**
     * @return list<string>
     */
    public function pillTagLabels(): array
    {
        $a = trim((string) ($this->pill_tag_1 ?? ''));
        $b = trim((string) ($this->pill_tag_2 ?? ''));

        return [
            $a !== '' ? $a : 'Donation',
            $b !== '' ? $b : 'Charity',
        ];
    }
}
