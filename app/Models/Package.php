<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    public const CATEGORY_LABELS = [
        'rumahan' => 'Paket Rumahan',
        'gedung' => 'Paket Gedung',
        'intimate' => 'Intimate Wedding',
        'rias' => 'Paket Rias & Wisuda',
        'lainnya' => 'Paket Lainnya',
    ];

    protected $fillable = [
        'name', 'slug', 'tier', 'category', 'price', 'description',
        'features', 'includes_digital_invitation', 'max_guests', 'duration', 'image',
        'promo_label', 'promo_description', 'promo_discount_percent', 'promo_expires_at',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'includes_digital_invitation' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'promo_discount_percent' => 'decimal:2',
        'promo_expires_at' => 'date',
    ];

    protected $appends = [
        'feature_sections',
        'feature_items',
        'has_digital_invitation',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function mediaItems()
    {
        return $this->hasMany(PackageMediaItem::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getDpAmountAttribute(): float
    {
        return round($this->effective_price * 0.30, 2);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedEffectivePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_price, 0, ',', '.');
    }

    public function getFormattedPromoPriceAttribute(): ?string
    {
        if (!$this->hasActivePromo()) {
            return null;
        }

        return 'Rp ' . number_format($this->promo_price, 0, ',', '.');
    }

    public function getCategoryLabelAttribute(): string
    {
        if (!$this->category) {
            return 'Paket Lainnya';
        }

        return self::CATEGORY_LABELS[$this->category] ?? ucfirst($this->category);
    }

    public function isIndividualService(): bool
    {
        $individualCategories = ['rias'];
        $keywords = ['wisuda', 'bridesmaid', 'bridesmade', 'pagar ayu', 'pagarayu', 'makeup'];
        $name = Str::lower($this->name ?? '');
        $slug = Str::lower($this->slug ?? '');

        if (in_array($this->category, $individualCategories, true)) {
            return true;
        }

        foreach ($keywords as $keyword) {
            if (Str::contains($name, $keyword) || Str::contains($slug, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function hasActivePromo(): bool
    {
        $discount = (float) ($this->promo_discount_percent ?? 0);
        if ($discount <= 0) {
            return false;
        }

        if (!$this->promo_expires_at) {
            return true;
        }

        return Carbon::today()->lessThanOrEqualTo($this->promo_expires_at);
    }

    public function getPromoPriceAttribute(): ?float
    {
        if (!$this->hasActivePromo()) {
            return null;
        }

        $discount = (float) $this->promo_discount_percent;
        $price = (float) $this->price;

        return round($price * (1 - ($discount / 100)), 2);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->promo_price ?? (float) $this->price;
    }

    public function getFeatureSectionsAttribute(): array
    {
        $features = $this->features ?? [];
        if (empty($features)) {
            return [];
        }

        // Legacy flat array support
        if (isset($features[0]) && is_string($features[0])) {
            return [[
                'title' => null,
                'items' => $this->sanitizeFeatureItems($features),
            ]];
        }

        return collect($features)
            ->map(function ($section) {
                $items = $this->sanitizeFeatureItems($section['items'] ?? []);
                $title = isset($section['title']) && $section['title'] !== ''
                    ? (string) $section['title']
                    : null;

                if (!$title && empty($items)) {
                    return null;
                }

                return [
                    'title' => $title,
                    'items' => $items,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function getHasDigitalInvitationAttribute(): bool
    {
        return (bool) ($this->includes_digital_invitation ?? true);
    }

    protected function sanitizeFeatureItems($items): array
    {
        return collect($items)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    protected static function booted(): void
    {
        static::deleting(function (Package $package) {
            $package->loadMissing('mediaItems');

            foreach ($package->mediaItems as $media) {
                if ($media->media_path) {
                    Storage::disk('public')->delete($media->media_path);
                }
            }

            if ($package->image && !Str::startsWith($package->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($package->image);
            }
        });
    }

    public function getFeatureItemsAttribute(): array
    {
        return collect($this->feature_sections)
            ->flatMap(fn ($section) => $section['items'] ?? [])
            ->reject(fn ($item) => str_starts_with($item, '## '))
            ->values()
            ->all();
    }
}
