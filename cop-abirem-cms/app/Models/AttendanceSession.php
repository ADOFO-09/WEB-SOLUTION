<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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
        'total_members' => 'integer',
        'total_visitors' => 'integer',
        'total_children' => 'integer',
        'total_attendance' => 'integer',
    ];

    protected $appends = ['is_open', 'formatted_date'];

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'open';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->service_date->format('l, F d, Y');
    }

    public function getServiceTitleAttribute(): string
    {
        $title = $this->serviceType->name ?? 'Service';
        return $title . ' - ' . $this->service_date->format('M d, Y');
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

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

    public function memberRecords(): HasMany
    {
        return $this->records()->whereNotNull('member_id');
    }

    public function visitorRecords(): HasMany
    {
        return $this->records()->whereNotNull('visitor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByServiceType($query, $serviceTypeId)
    {
        return $query->where('service_type_id', $serviceTypeId);
    }

    public function scopeByMinistry($query, $ministryId)
    {
        return $query->where('ministry_id', $ministryId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('service_date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('service_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('service_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('service_date', now()->month)
                     ->whereYear('service_date', now()->year);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function markMemberAttendance(int $memberId, string $method = 'manual'): AttendanceRecord
    {
        // Check if already marked
        $existing = $this->records()->where('member_id', $memberId)->first();
        if ($existing) {
            return $existing;
        }

        $isLate = $this->isLateCheckIn();

        return $this->records()->create([
            'member_id' => $memberId,
            'check_in_time' => now(),
            'attendance_method' => $method,
            'is_late' => $isLate,
            'marked_by' => auth()->id(),
        ]);
    }

    public function markVisitorAttendance(int $visitorId, string $method = 'manual'): AttendanceRecord
    {
        // Check if already marked
        $existing = $this->records()->where('visitor_id', $visitorId)->first();
        if ($existing) {
            return $existing;
        }

        return $this->records()->create([
            'visitor_id' => $visitorId,
            'check_in_time' => now(),
            'attendance_method' => $method,
            'is_late' => $this->isLateCheckIn(),
            'marked_by' => auth()->id(),
        ]);
    }

    public function unmarkAttendance(int $recordId): bool
    {
        return $this->records()->where('id', $recordId)->delete() > 0;
    }

    public function isLateCheckIn(): bool
    {
        if (!$this->start_time) {
            return false;
        }

        $graceMinutes = Setting::get('attendance_grace_minutes', 15);
        $lateTime = Carbon::parse($this->start_time)->addMinutes($graceMinutes);

        return now()->gt($lateTime);
    }

    public function isMemberMarked(int $memberId): bool
    {
        return $this->records()->where('member_id', $memberId)->exists();
    }

    public function isVisitorMarked(int $visitorId): bool
    {
        return $this->records()->where('visitor_id', $visitorId)->exists();
    }

    public function updateTotals(): void
    {
        $this->update([
            'total_members' => $this->memberRecords()->count(),
            'total_visitors' => $this->visitorRecords()->count(),
            'total_attendance' => $this->records()->count(),
        ]);
    }

    public function close(): void
    {
        $this->updateTotals();
        
        $this->update([
            'status' => 'closed',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'closed_by' => null,
            'closed_at' => null,
        ]);
    }

    public function getAttendedMemberIds(): array
    {
        return $this->memberRecords()->pluck('member_id')->toArray();
    }

    public function getAttendedVisitorIds(): array
    {
        return $this->visitorRecords()->pluck('visitor_id')->toArray();
    }
}
