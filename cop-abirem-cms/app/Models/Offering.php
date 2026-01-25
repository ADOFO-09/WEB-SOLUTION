<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'member_id',
        'financial_year_id',
        'income_category_id',
        'session_id',
        'amount',
        'payment_date',
        'payment_method',
        'payment_reference',
        'is_anonymous',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
