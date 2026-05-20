<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorVisit extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'visitor_id',
        'visit_date',
        'service_type_id',
        'notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('visit_date', $date);
    }

    public function scopeByServiceType($query, $serviceTypeId)
    {
        return $query->where('service_type_id', $serviceTypeId);
    }
}
