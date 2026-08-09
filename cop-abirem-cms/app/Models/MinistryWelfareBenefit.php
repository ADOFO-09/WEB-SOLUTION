<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MinistryWelfareBenefit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ministry_id', 'member_id', 'beneficiary_name',
        'purpose', 'benefit_date', 'benefit_year',
        'amount', 'description', 'approved_by', 'recorded_by',
    ];

    protected $casts = [
        'benefit_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public const PURPOSES = [
        'marriage'   => 'Marriage',
        'funeral'    => 'Funeral',
        'childbirth' => 'Childbirth',
        'sickness'   => 'Sickness',
        'other'      => 'Other',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->recorded_by)) {
                $m->recorded_by = auth()->id();
            }
            if (empty($m->benefit_year) && !empty($m->benefit_date)) {
                $m->benefit_year = date('Y', strtotime($m->benefit_date));
            }
        });
    }

    public function ministry(): BelongsTo    { return $this->belongsTo(Ministry::class); }
    public function member(): BelongsTo      { return $this->belongsTo(Member::class); }
    public function approvedBy(): BelongsTo  { return $this->belongsTo(User::class, 'approved_by'); }
    public function recordedBy(): BelongsTo  { return $this->belongsTo(User::class, 'recorded_by'); }

    public function expenses(): HasMany
    {
        return $this->hasMany(MinistryWelfareBenefitExpense::class, 'ministry_welfare_benefit_id');
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses->sum('amount');
    }

    public function getTotalCostAttribute(): float
    {
        return round((float) $this->amount + $this->total_expenses, 2);
    }
}
