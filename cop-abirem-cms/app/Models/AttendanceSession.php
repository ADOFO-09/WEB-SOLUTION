<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'ministry_id',
        'service_date',
        'start_time',
        'end_time',
        'theme',
        'preacher',
        'total_members',
        'total_visitors',
        'total_children',
        'total_attendance',
        'notes',
        'status',
        'created_by',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'service_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'closed_at' => 'datetime',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
