<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WelfareContribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'welfare_contributions';

    protected $fillable = [
        'reference_number',
        'member_id',
        'amount',
        'period_month',
        'period_year',
        'payment_date',
        'payment_method',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
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
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('period_year', $year);
    }

    public function scopeByPeriod($query, int $month, int $year)
    {
        return $query->where('period_month', $month)->where('period_year', $year);
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const PAYMENT_METHODS = [
        'cash'          => 'Cash',
        'mobile_money'  => 'Mobile Money',
        'bank_transfer' => 'Bank Transfer',
        'cheque'        => 'Cheque',
    ];

    // ==========================================
    // HELPERS
    // ==========================================

    public static function generateReferenceNumber(): string
    {
        $prefix = 'WC';
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
}
