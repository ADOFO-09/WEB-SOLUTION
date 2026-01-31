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

    public function scopeByFinancialYear($query, $yearId)
    {
        return $query->where('financial_year_id', $yearId);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->whereMonth('month_for', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('month_for', $year);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
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

    public function getMonthForFormattedAttribute(): string
    {
        return $this->month_for ? $this->month_for->format('F Y') : 'N/A';
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function generateReferenceNumber(): string
    {
        $prefix = 'TT';
        $year = date('Y');
        $lastTithe = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTithe ? ((int)substr($lastTithe->reference_number, -5) + 1) : 1;
        
        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCT';
        $year = date('Y');
        $lastReceipt = self::whereYear('created_at', $year)
            ->whereNotNull('receipt_number')
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($lastReceipt && preg_match('/(\d{5})$/', $lastReceipt->receipt_number, $matches)) {
            $sequence = (int)$matches[1] + 1;
        }
        
        return $prefix . $year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public static function getMemberTotalForYear($memberId, $year = null): float
    {
        $year = $year ?? date('Y');
        return self::byMember($memberId)
            ->whereYear('payment_date', $year)
            ->sum('amount');
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
