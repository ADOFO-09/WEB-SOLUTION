<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WelfareRate extends Model
{
    use HasFactory;

    protected $table = 'welfare_rates';

    protected $fillable = [
        'amount',
        'effective_from',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'amount'         => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // STATIC HELPERS
    // ==========================================

    /**
     * Return the rate row with the latest effective_from that is <= the first day of the given month/year.
     */
    public static function rateForPeriod(int $month, int $year): ?self
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();

        return self::where('effective_from', '<=', $periodStart)
            ->orderByDesc('effective_from')
            ->first();
    }
}
