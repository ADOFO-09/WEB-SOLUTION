<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class IncomeCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'is_active',
        'sort_order',
        'is_system',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_system'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }

    public function tithes(): HasMany
    {
        return $this->hasMany(Tithe::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfferings($query)
    {
        return $query->whereIn('type', ['offering', 'special']);
    }

    public function scopeTithes($query)
    {
        return $query->where('type', 'tithe');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function getTotalForPeriod($startDate, $endDate): float
    {
        $offerings = $this->offerings()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
        
        $donations = $this->donations()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
        
        return $offerings + $donations;
    }
}
