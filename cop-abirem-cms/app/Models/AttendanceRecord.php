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
}
