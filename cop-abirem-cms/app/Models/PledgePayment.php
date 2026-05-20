<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PledgePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pledge_id',
        'reference_number',
        'amount',
        'payment_date',
        'payment_method',
        'payment_reference',
        'receipt_number',
        'notes',
        'recorded_by',
        'sms_sent',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
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
        });

        static::created(function ($model) {
            $model->pledge->updateAmountPaid();
            $model->pledge->checkAndUpdateStatus();
        });

        static::deleted(function ($model) {
            $model->pledge->updateAmountPaid();
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function pledge(): BelongsTo
    {
        return $this->belongsTo(Pledge::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function generateReferenceNumber(): string
    {
        $prefix = 'PP';
        $year   = date('Y');
        $last   = self::whereYear('created_at', $year)
            ->where('reference_number', 'like', $prefix . $year . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($last && preg_match('/' . $prefix . $year . '(\d+)$/', $last->reference_number, $m)) {
            $sequence = (int) $m[1] + 1;
        }

        do {
            $ref = $prefix . $year . str_pad($sequence++, 5, '0', STR_PAD_LEFT);
        } while (self::where('reference_number', $ref)->exists());

        return $ref;
    }

    public static function generateReceiptNumber(): string
    {
        $prefix = 'PRC';
        $year   = date('Y');
        $last   = self::whereYear('created_at', $year)
            ->where('receipt_number', 'like', $prefix . $year . '%')
            ->whereNotNull('receipt_number')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($last && preg_match('/' . $prefix . $year . '(\d+)$/', $last->receipt_number, $m)) {
            $sequence = (int) $m[1] + 1;
        }

        do {
            $ref = $prefix . $year . str_pad($sequence++, 5, '0', STR_PAD_LEFT);
        } while (self::where('receipt_number', $ref)->exists());

        return $ref;
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
