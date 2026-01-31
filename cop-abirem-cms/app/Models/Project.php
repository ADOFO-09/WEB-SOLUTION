<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'amount_raised',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_amount' => 'decimal:2',
        'amount_raised' => 'decimal:2',
    ];

    protected $appends = ['progress_percentage', 'balance'];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount == 0) return 0;
        return min(100, round(($this->amount_raised / $this->target_amount) * 100, 1));
    }

    public function getBalanceAttribute(): float
    {
        return max(0, $this->target_amount - $this->amount_raised);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function updateAmountRaised(): void
    {
        $donations = $this->donations()->sum('amount');
        $pledgePayments = PledgePayment::whereIn('pledge_id', $this->pledges()->pluck('id'))->sum('amount');
        
        $this->update(['amount_raised' => $donations + $pledgePayments]);
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const STATUSES = [
        'active' => 'Active',
        'completed' => 'Completed',
        'on_hold' => 'On Hold',
        'cancelled' => 'Cancelled',
    ];
}
