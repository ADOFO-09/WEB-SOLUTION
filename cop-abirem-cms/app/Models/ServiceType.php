<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceType extends Model
{
    use HasFactory;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
