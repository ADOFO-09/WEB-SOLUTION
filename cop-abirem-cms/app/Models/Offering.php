<?php

namespace App\Models;

use App\Traits\HasLedgerCorrections;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offering extends Model
{
    use HasFactory, SoftDeletes, HasLedgerCorrections;

    protected $fillable = [
        'reference_number',
        'member_id',
        'financial_year_id',
        'income_category_id',
        'session_id',
        'pledge_id',
        'amount',
        'payment_date',
        'payment_method',
        'payment_reference',
        'is_anonymous',
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
        'is_anonymous' => 'boolean',
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

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function pledge(): BelongsTo
    {
        return $this->belongsTo(Pledge::class);
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

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('income_category_id', $categoryId);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    public function scopeNonAnonymous($query)
    {
        return $query->where('is_anonymous', false);
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

    public function getContributorNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }
        return $this->member ? $this->member->full_name : 'Unknown';
    }

    public function getParticularNameAttribute(): string
    {
        return $this->incomeCategory?->name ?? 'General Offering';
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function generateReferenceNumber(): string
    {
        $prefix = 'OF';
        $year   = date('Y');
        $last   = self::withTrashed()
            ->whereYear('created_at', $year)
            ->where('reference_number', 'like', $prefix . $year . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($last && preg_match('/' . $prefix . $year . '(\d+)$/', $last->reference_number, $m)) {
            $sequence = (int) $m[1] + 1;
        }

        do {
            $ref = $prefix . $year . str_pad($sequence++, 5, '0', STR_PAD_LEFT);
        } while (self::withTrashed()->where('reference_number', $ref)->exists());

        return $ref;
    }

    public static function getSessionTotal($sessionId): float
    {
        return self::bySession($sessionId)->sum('amount');
    }

    public static function getMonthlyTotal($month = null, $year = null): float
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        return self::whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->sum('amount');
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'mobile_money' => 'Mobile Money',
        'bank_transfer' => 'Bank Transfer',
        'cheque' => 'Cheque',
    ];
}
