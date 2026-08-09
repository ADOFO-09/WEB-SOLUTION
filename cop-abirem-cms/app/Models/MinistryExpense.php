<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ministry_id',
        'reference_number',
        'category',
        'amount',
        'expense_date',
        'description',
        'paid_to',
        'payment_method',
        'payment_reference',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public const CATEGORIES = [
        'welfare_payment' => 'Welfare Payment',
        'program'         => 'Program / Event',
        'materials'       => 'Materials & Supplies',
        'transport'       => 'Transport',
        'communication'   => 'Communication',
        'admin'           => 'Administration',
        'other'           => 'Other',
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

        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = static::generateRef();
            }
            if (empty($model->recorded_by)) {
                $model->recorded_by = auth()->id();
            }
        });
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function generateRef(): string
    {
        $prefix = 'ME';
        $year   = date('Y');
        $last   = static::withTrashed()
            ->whereYear('created_at', $year)
            ->where('reference_number', 'like', $prefix . $year . '%')
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last && preg_match('/' . $prefix . $year . '(\d+)$/', $last->reference_number, $m)) {
            $seq = (int) $m[1] + 1;
        }

        do {
            $ref = $prefix . $year . str_pad($seq++, 5, '0', STR_PAD_LEFT);
        } while (static::withTrashed()->where('reference_number', $ref)->exists());

        return $ref;
    }
}
