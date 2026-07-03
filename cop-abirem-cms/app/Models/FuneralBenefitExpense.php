<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuneralBenefitExpense extends Model
{
    protected $table = 'funeral_benefit_expenses';

    protected $fillable = [
        'funeral_benefit_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function funeralBenefit(): BelongsTo
    {
        return $this->belongsTo(FuneralBenefit::class);
    }
}
