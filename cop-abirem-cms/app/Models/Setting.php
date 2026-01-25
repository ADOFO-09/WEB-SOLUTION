<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Cache key for settings.
     */
    protected static string $cacheKey = 'app_settings';

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $settings = static::getAllCached();
        
        if (!isset($settings[$key])) {
            return $default;
        }

        $setting = $settings[$key];
        
        // Cast value based on type
        return match($setting['type']) {
            'boolean' => filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($setting['value']) ? (float) $setting['value'] : $default,
            'json' => json_decode($setting['value'], true) ?? $default,
            default => $setting['value'] ?? $default,
        };
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value): bool
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return false;
        }

        // Convert value to string for storage
        if (is_array($value)) {
            $value = json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $setting->update(['value' => $value]);
        
        // Clear cache
        static::clearCache();

        return true;
    }

    /**
     * Get all settings cached.
     */
    public static function getAllCached(): array
    {
        return Cache::remember(static::$cacheKey, 3600, function () {
            return static::all()->keyBy('key')->map(function ($setting) {
                return [
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                ];
            })->toArray();
        });
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup(string $group): array
    {
        $settings = static::getAllCached();
        
        return array_filter($settings, fn($s) => $s['group'] === $group);
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(static::$cacheKey);
    }

    /**
     * Get all available groups.
     */
    public static function getGroups(): array
    {
        return static::distinct()->pluck('group')->toArray();
    }

    /**
     * Boot function to clear cache on changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
