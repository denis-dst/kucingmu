<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'label',
        'type',
    ];

    /**
     * Cache key for all settings.
     */
    private const CACHE_KEY = 'app_settings_all';
    private const CACHE_TTL = 60; // seconds

    /**
     * Get all settings as [key => value] array, cached for performance.
     * Eliminates per-request DB queries for global settings.
     */
    public static function getAllCached(): array
    {
        try {
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return static::pluck('value', 'key')->all();
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Clear the settings cache. Call after any settings update.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get a single setting value, reading from cache first.
     */
    public static function get(string $key, $default = null)
    {
        $all = static::getAllCached();
        return $all[$key] ?? $default;
    }

    /**
     * Check if a boolean setting is enabled, reading from cache.
     */
    public static function isEnabled(string $key, bool $default = true): bool
    {
        $val = static::get($key);
        if ($val === null) {
            return $default;
        }
        return (string)$val === '1' || $val === true;
    }
}
