<?php

namespace App\Models;

use App\Traits\HasLedgerCorrections;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory, SoftDeletes, HasLedgerCorrections;

    protected $fillable = [
        'reference_number',
        'member_id',
        'donor_name',
        'donor_phone',
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
        'ledger_status',
        'voided_by',
        'voided_at',
        'void_reason',
        'adjusted_by_id',
        'adjusts_entry_id',
        'is_adjustment',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'sms_sent' => 'boolean',
        'voided_at' => 'datetime',
        'is_adjustment' => 'boolean',
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
            if (empty($model->receipt_number)) {
                $model->receipt_number = self::generateReceiptNumber();
            }
            if (empty($model->financial_year_id)) {
                $model->financial_year_id = FinancialYear::current()?->id;
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

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
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

    public function scopeByType($query, $type)
    {
        return $query->where('donation_type', $type);
    }

    public function scopeCash($query)
    {
        return $query->where('donation_type', 'cash');
    }

    public function scopeInKind($query)
    {
        return $query->where('donation_type', 'in_kind');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
                     ->whereYear('payment_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('payment_date', now()->year);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getDonorDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }
        if ($this->member) {
            return $this->member->full_name;
        }
        return $this->donor_name ?? 'Unknown';
    }

    public function getEffectiveAmountAttribute(): float
    {
        return $this->donation_type === 'in_kind' 
            ? ($this->estimated_value ?? 0) 
            : $this->amount;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function generateReferenceNumber(): string
    {
        $prefix = 'DN';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $last ? ((int)substr($last->reference_number, -5) + 1) : 1;
        
        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public static function generateReceiptNumber(): string
    {
        $prefix = 'DRC';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)
            ->whereNotNull('receipt_number')
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($last && preg_match('/(\d{5})$/', $last->receipt_number, $matches)) {
            $sequence = (int)$matches[1] + 1;
        }
        
        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public static function getProjectTotal($projectId): float
    {
        return self::byProject($projectId)->sum('amount');
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const DONATION_TYPES = [
        'cash' => 'Cash/Monetary',
        'in_kind' => 'In-Kind',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'mobile_money' => 'Mobile Money',
        'bank_transfer' => 'Bank Transfer',
        'cheque' => 'Cheque',
    ];
}
