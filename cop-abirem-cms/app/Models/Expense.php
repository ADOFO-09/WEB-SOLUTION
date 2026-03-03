<?php

namespace App\Models;

use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\FinancialYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'financial_year_id',
        'expense_category_id',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'payment_reference',
        'payee_name',
        'payee_phone',
        'voucher_number',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = self::generateReferenceNumber();
            }
            if (empty($model->financial_year_id)) {
                $model->financial_year_id = FinancialYear::current()?->id;
            }
            if (empty($model->requested_by)) {
                $model->requested_by = auth()->id();
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('expense_category_id', $categoryId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
                     ->whereYear('expense_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('expense_date', now()->year);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'paid' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getCanApproveAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getCanPayAttribute(): bool
    {
        return $this->status === 'approved';
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function generateReferenceNumber(): string
    {
        $prefix = 'EXP';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $last ? ((int)substr($last->reference_number, -5) + 1) : 1;
        
        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid(array $data = []): void
    {
        $this->update(array_merge([
            'status' => 'paid',
        ], array_filter([
            'voucher_number' => $data['voucher_number'] ?? null,
            'payment_reference' => $data['payment_reference'] ?? null,
        ])));
    }

    public static function getMonthlyTotal($month = null, $year = null): float
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        return self::paid()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'paid' => 'Paid',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'mobile_money' => 'Mobile Money',
        'bank_transfer' => 'Bank Transfer',
        'cheque' => 'Cheque',
    ];
}
