<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryWelfareRate extends Model
{
    protected $fillable = ['ministry_id', 'amount', 'effective_from', 'notes', 'created_by'];

    protected $casts = [
        'effective_from' => 'date',
        'amount'         => 'decimal:2',
    ];

    public function ministry(): BelongsTo  { return $this->belongsTo(Ministry::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public static function rateForPeriod(int $ministryId, int $month, int $year): ?self
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();

        return self::where('ministry_id', $ministryId)
            ->where('effective_from', '<=', $periodStart)
            ->orderByDesc('effective_from')
            ->first();
    }
}
