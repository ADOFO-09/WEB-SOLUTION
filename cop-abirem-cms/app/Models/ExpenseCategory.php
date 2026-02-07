<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'budget_amount',
        'is_active',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
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

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getSpentAmount($year = null): float
    {
        $year = $year ?? date('Y');
        return $this->expenses()
            ->paid()
            ->whereYear('expense_date', $year)
            ->sum('amount');
    }

    public function getBudgetUsedPercentage($year = null): float
    {
        if ($this->budget_amount == 0) return 0;
        return min(100, round(($this->getSpentAmount($year) / $this->budget_amount) * 100, 1));
    }
}
