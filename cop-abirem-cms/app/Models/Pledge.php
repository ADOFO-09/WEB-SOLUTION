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
        'purpose',
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

    protected $appends = ['balance', 'progress_percentage'];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->pledge_number)) {
                $model->pledge_number = self::generatePledgeNumber();
            }
            if (empty($model->financial_year_id)) {
                $model->financial_year_id = FinancialYear::current()?->id;
            }
            if (empty($model->amount_paid)) {
                $model->amount_paid = 0;
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function incomeCategory(): BelongsTo
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PledgePayment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFulfilled($query)
    {
        return $query->where('status', 'fulfilled');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                     ->where('due_date', '<', now())
                     ->whereColumn('amount_paid', '<', 'total_amount');
    }

    public function scopeDueSoon($query, $days = 30)
    {
        return $query->where('status', 'active')
                     ->whereBetween('due_date', [now(), now()->addDays($days)])
                     ->whereColumn('amount_paid', '<', 'total_amount');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getBalanceAttribute(): float
    {
        return max(0, $this->total_amount - $this->amount_paid);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_amount == 0) return 0;
        return min(100, round(($this->amount_paid / $this->total_amount) * 100, 1));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'active' 
            && $this->due_date 
            && $this->due_date->isPast() 
            && $this->balance > 0;
    }

    public function getIsFulfilledAttribute(): bool
    {
        return $this->balance <= 0;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function generatePledgeNumber(): string
    {
        $prefix = 'PL';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $last ? ((int)substr($last->pledge_number, -5) + 1) : 1;
        
        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public function recordPayment(float $amount, array $data = []): PledgePayment
    {
        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'payment_method' => $data['payment_method'] ?? 'cash',
            'payment_reference' => $data['payment_reference'] ?? null,
            'receipt_number' => PledgePayment::generateReceiptNumber(),
            'notes' => $data['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        $this->updateAmountPaid();
        $this->checkAndUpdateStatus();

        return $payment;
    }

    public function updateAmountPaid(): void
    {
        $this->amount_paid = $this->payments()->sum('amount');
        $this->save();
    }

    public function checkAndUpdateStatus(): void
    {
        if ($this->balance <= 0) {
            $this->update(['status' => 'fulfilled']);
        }
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . "\n[Cancelled: " . ($reason ?? 'No reason provided') . "]"
        ]);
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const STATUSES = [
        'active' => 'Active',
        'fulfilled' => 'Fulfilled',
        'cancelled' => 'Cancelled',
        'defaulted' => 'Defaulted',
    ];

    public const PAYMENT_FREQUENCIES = [
        'one_time' => 'One Time',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'annually' => 'Annually',
    ];
}
