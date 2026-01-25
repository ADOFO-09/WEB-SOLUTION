<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'member_id',
        'financial_year_id',
        'income_category_id',
        'project_id',
        'amount',
        'donation_type',
        'in_kind_description',
        'estimated_value',
        'payment_date',
        'payment_method',
        'payment_reference',
        'receipt_number',
        'is_anonymous',
        'sms_sent',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'sms_sent' => 'boolean',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
