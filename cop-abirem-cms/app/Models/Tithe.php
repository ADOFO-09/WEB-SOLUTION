<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tithe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'member_id',
        'financial_year_id',
        'amount',
        'payment_date',
        'payment_method',
        'payment_reference',
        'month_for',
        'receipt_number',
        'notes',
        'sms_sent',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'month_for' => 'date',
        'amount' => 'decimal:2',
        'sms_sent' => 'boolean',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
