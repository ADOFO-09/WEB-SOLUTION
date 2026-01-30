<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'member_id',
        'visitor_id',
        'check_in_time',
        'check_out_time',
        'attendance_method',
        'is_late',
        'marked_by',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'is_late' => 'boolean',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getAttendeeNameAttribute(): string
    {
        if ($this->member) {
            return $this->member->full_name;
        }
        if ($this->visitor) {
            return $this->visitor->full_name . ' (Visitor)';
        }
        return 'Unknown';
    }

    public function getAttendeeTypeAttribute(): string
    {
        return $this->member_id ? 'member' : 'visitor';
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeMembers($query)
    {
        return $query->whereNotNull('member_id');
    }

    public function scopeVisitors($query)
    {
        return $query->whereNotNull('visitor_id');
    }

    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    public function scopeOnTime($query)
    {
        return $query->where('is_late', false);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('attendance_method', $method);
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const METHODS = [
        'manual' => 'Manual Entry',
        'qr_code' => 'QR Code Scan',
        'biometric' => 'Biometric',
        'face_recognition' => 'Face Recognition',
    ];
}
