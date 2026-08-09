<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryWelfareContribution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ministry_id', 'member_id', 'reference_number',
        'amount', 'period_month', 'period_year',
        'payment_date', 'payment_method', 'received_by', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public const PAYMENT_METHODS = [
        'cash'          => 'Cash',
        'mobile_money'  => 'Mobile Money',
        'bank_transfer' => 'Bank Transfer',
        'cheque'        => 'Cheque',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->reference_number)) {
                $m->reference_number = static::generateRef();
            }
            if (empty($m->received_by)) {
                $m->received_by = auth()->id();
            }
        });
    }

    public function ministry(): BelongsTo  { return $this->belongsTo(Ministry::class); }
    public function member(): BelongsTo    { return $this->belongsTo(Member::class); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }

    public static function generateRef(): string
    {
        $prefix = 'MWC';
        $year   = date('Y');
        $last   = static::withTrashed()
            ->whereYear('created_at', $year)
            ->where('reference_number', 'like', $prefix . $year . '%')
            ->orderByDesc('id')->first();
        $seq = 1;
        if ($last && preg_match('/' . $prefix . $year . '(\d+)$/', $last->reference_number, $m)) {
            $seq = (int) $m[1] + 1;
        }
        do { $ref = $prefix . $year . str_pad($seq++, 5, '0', STR_PAD_LEFT); }
        while (static::withTrashed()->where('reference_number', $ref)->exists());
        return $ref;
    }
}
