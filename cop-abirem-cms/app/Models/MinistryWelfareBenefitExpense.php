<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryWelfareBenefitExpense extends Model
{
    protected $fillable = ['ministry_welfare_benefit_id', 'description', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(MinistryWelfareBenefit::class, 'ministry_welfare_benefit_id');
    }
}
