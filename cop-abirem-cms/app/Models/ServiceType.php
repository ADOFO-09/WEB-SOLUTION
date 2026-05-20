<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'day_of_week',
        'default_start_time',
        'description',
        'is_active',
    ];

    protected $casts = [
        'default_start_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::uniqueSlug(Str::slug($model->name));
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && !$model->isDirty('slug')) {
                $model->slug = static::uniqueSlug(Str::slug($model->name), $model->id);
            }
        });
    }

    private static function uniqueSlug(string $base, int $excludeId = null): string
    {
        $slug  = $base;
        $count = 2;
        while (
            static::withTrashed()
                ->where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $count++;
        }
        return $slug;
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getScheduleAttribute(): string
    {
        $schedule = $this->day_of_week ?? 'As scheduled';
        if ($this->default_start_time) {
            $schedule .= ' at ' . $this->default_start_time->format('g:i A');
        }
        return $schedule;
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const DAYS_OF_WEEK = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ];
}
