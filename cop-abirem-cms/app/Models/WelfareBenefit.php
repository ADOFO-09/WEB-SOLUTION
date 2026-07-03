<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WelfareBenefit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'welfare_benefits';

    protected $fillable = [
        'member_id',
        'benefactor_name',
        'purpose',
        'benefit_date',
        'benefit_year',
        'amount',
        'description',
        'approved_by',
        'recorded_by',
    ];

    protected $casts = [
        'benefit_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const PURPOSES = [
        'marriage'   => 'Marriage',
        'funeral'    => 'Funeral',
        'childbirth' => 'Childbirth',
        'other'      => 'Other',
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

    public function welfareBenefitExpenses(): HasMany
    {
        return $this->hasMany(WelfareBenefitExpense::class);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getTotalOtherExpensesAttribute(): float
    {
        return (float) $this->welfareBenefitExpenses->sum('amount');
    }

    public function getTotalCostAttribute(): float
    {
        return round((float) $this->amount + $this->total_other_expenses, 2);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByYear($query, $year)
    {
        return $query->where('benefit_year', $year);
    }

    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }
}
