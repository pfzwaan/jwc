<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings (logo, contact, socials, …). Anything sensitive is
 * stored with is_public = false and never exposed via the API.
 */
#[Fillable(['key', 'value', 'is_public'])]
class Setting extends Model
{
    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }

    /** All settings as key => value (cached). */
    public static function values(): array
    {
        return Cache::rememberForever('settings.all', fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::values()[$key] ?? $default;
    }

    public static function set(string $key, ?string $value, bool $isPublic = true): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'is_public' => $isPublic]);
    }

    /** Public settings only (safe to expose to the frontend). */
    public static function publicValues(): array
    {
        return static::query()->where('is_public', true)->pluck('value', 'key')->all();
    }
}
