<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WelfareBenefitExpense extends Model
{
    protected $table = 'welfare_benefit_expenses';

    protected $fillable = [
        'welfare_benefit_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function welfareBenefit(): BelongsTo
    {
        return $this->belongsTo(WelfareBenefit::class);
    }
}
