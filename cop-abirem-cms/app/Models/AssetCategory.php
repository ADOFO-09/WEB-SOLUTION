<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AssetCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'depreciation_rate',
        'depreciation_method',
        'useful_life_years',
    ];

    protected $casts = [
        'depreciation_rate' => 'decimal:2',
        'useful_life_years' => 'integer',
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

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    public const DEPRECIATION_METHODS = [
        'straight_line'     => 'Straight Line',
        'declining_balance' => 'Declining Balance',
        'none'              => 'None',
    ];
}
