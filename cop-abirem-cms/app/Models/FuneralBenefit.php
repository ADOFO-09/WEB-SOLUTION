<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuneralBenefit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'funeral_benefits';

    protected $fillable = [
        'member_id',
        'benefactor_name',
        'deceased_name',
        'funeral_date',
        'funeral_year',
        'venue',
        'amount_donated',
        'description',
        'approved_by',
        'recorded_by',
    ];

    protected $casts = [
        'funeral_date'   => 'date',
        'amount_donated' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function funeralBenefitExpenses(): HasMany
    {
        return $this->hasMany(FuneralBenefitExpense::class);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getTotalOtherExpensesAttribute(): float
    {
        return (float) $this->funeralBenefitExpenses->sum('amount');
    }

    public function getTotalCostAttribute(): float
    {
        return round((float) $this->amount_donated + $this->total_other_expenses, 2);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByYear($query, $year)
    {
        return $query->where('funeral_year', $year);
    }
}
