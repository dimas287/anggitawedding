<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    public static function getValue(string $key, $default = null)
    {
        return Cache::rememberForever(static::cacheKey($key), function () use ($key, $default) {
            return optional(static::query()->where('key', $key)->first())->value ?? $default;
        });
    }

    public static function getJson(string $key, $default = [])
    {
        $raw = static::getValue($key);
        if (is_null($raw)) {
            return $default;
        }

        $decoded = json_decode($raw, true);
        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : $default;
    }

    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::cacheKey($key));
    }

    public static function setJson(string $key, $value): void
    {
        static::setValue($key, json_encode($value));
    }

    protected static function cacheKey(string $key): string
    {
        return "site_setting_{$key}";
    }

    public static function portfolioStatsDefaults(): array
    {
        return [
            'happy_couples' => [
                'label' => 'Pasangan Bahagia',
                'value' => 200,
                'suffix' => '+',
                'decimals' => 0,
            ],
            'years_experience' => [
                'label' => 'Tahun Pengalaman',
                'value' => 6,
                'suffix' => '+',
                'decimals' => 0,
            ],
            'trusted_vendors' => [
                'label' => 'Vendor Terpercaya',
                'value' => 50,
                'suffix' => '+',
                'decimals' => 0,
            ],
            'average_rating' => [
                'label' => 'Rating Rata-rata',
                'value' => 4.9,
                'suffix' => '',
                'decimals' => 1,
            ],
        ];
    }
}
