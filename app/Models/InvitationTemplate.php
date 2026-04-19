<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class InvitationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'thumbnail', 'preview_image', 'demo_slug', 'theme',
        'primary_color', 'secondary_color', 'font_family',
        'price', 'promo_label', 'promo_description', 'promo_discount_percent', 'promo_expires_at',
        'has_music', 'default_music', 'is_active', 'is_premium', 'sort_order',
        'demo_content', 'demo_gallery', 'media_slots',
    ];

    protected $casts = [
        'has_music' => 'boolean',
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'promo_discount_percent' => 'decimal:2',
        'promo_expires_at' => 'date',
        'demo_content' => 'array',
        'demo_gallery' => 'array',
        'media_slots' => 'array',
    ];

    protected $appends = [
        'formatted_price',
        'formatted_effective_price',
        'has_active_promo',
        'demo_url',
        'parsed_demo_content',
    ];

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'template_id');
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

    public function getHasActivePromoAttribute(): bool
    {
        return $this->hasActivePromo();
    }

    public function getPromoPriceAttribute(): ?float
    {
        if (!$this->hasActivePromo()) {
            return null;
        }

        $discount = (float) $this->promo_discount_percent;
        $price = (float) ($this->price ?? 0);

        return max(0, round($price * (1 - ($discount / 100)), 2));
    }

    public function getEffectivePriceAttribute(): float
    {
        $basePrice = (float) ($this->price ?? 0);
        return (float) ($this->promo_price ?? $basePrice);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->price ?? 0), 0, ',', '.');
    }

    public function getFormattedEffectivePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_price, 0, ',', '.');
    }

    public function getDemoUrlAttribute(): ?string
    {
        if ($this->demo_slug) {
            return route('invitation.show', $this->demo_slug);
        }

        if ($this->preview_image) {
            return asset('storage/' . $this->preview_image);
        }

        return null;
    }

    public function getParsedDemoContentAttribute(): array
    {
        $defaults = [
            'groom_name' => 'Rahman Aditya',
            'bride_name' => 'Nadia Wulandari',
            'groom_father' => 'Putra Bpk. Hendra & Ibu Wati',
            'groom_mother' => null,
            'bride_father' => 'Putri Bpk. Surya & Ibu Sinta',
            'bride_mother' => null,
            'akad_datetime' => now()->addWeeks(4)->setTime(10, 0)->toIso8601String(),
            'akad_venue' => 'Masjid Raya Al-Fatih',
            'akad_address' => 'Jl. Kenanga No.12, Surabaya',
            'reception_datetime' => now()->addWeeks(4)->setTime(19, 0)->toIso8601String(),
            'reception_venue' => 'Grand Ballroom Serenity',
            'reception_address' => 'Jl. Pahlawan No. 8, Surabaya',
            'maps_link' => 'https://goo.gl/maps/abcdemo',
            'love_story' => 'Berawal dari kampus hingga resmi menikah.',
            'opening_quote' => 'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu.',
            'closing_message' => 'Doa restu Anda adalah hadiah terindah.',
            'hashtag' => '#RahmanNadiaForever',
            'music_file_url' => null,
            'photo_prewedding_url' => null,
            'gallery_photo_urls' => [],
            'rsvp_enabled' => false,
        ];

        $content = $this->demo_content ?? [];
        $gallery = $this->demo_gallery ?? [];

        if (!empty($gallery)) {
            $defaults['gallery_photo_urls'] = collect($gallery)
                ->map(fn($path) => asset('storage/' . $path))
                ->filter()
                ->values()
                ->all();
        }

        if (!empty($content['gallery_photo_urls'])) {
            $content['gallery_photo_urls'] = collect($content['gallery_photo_urls'])
                ->map(fn($url) => filter_var($url, FILTER_VALIDATE_URL) ? $url : asset('storage/' . ltrim($url, '/')))
                ->values()
                ->all();
        }

        if (!empty($content['music_file_url']) && !filter_var($content['music_file_url'], FILTER_VALIDATE_URL)) {
            $content['music_file_url'] = asset('storage/' . ltrim($content['music_file_url'], '/'));
        }

        if (!empty($content['photo_prewedding_url']) && !filter_var($content['photo_prewedding_url'], FILTER_VALIDATE_URL)) {
            $content['photo_prewedding_url'] = asset('storage/' . ltrim($content['photo_prewedding_url'], '/'));
        }

        return array_merge($defaults, $content);
    }
}
