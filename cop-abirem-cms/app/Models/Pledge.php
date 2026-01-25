<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pledge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pledge_number',
        'member_id',
        'financial_year_id',
        'income_category_id',
        'project_id',
        'total_amount',
        'amount_paid',
        'pledge_date',
        'due_date',
        'payment_frequency',
        'status',
        'reminder_sent_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'pledge_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'reminder_sent_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PledgePayment::class);
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_amount - $this->amount_paid;
    }
}
