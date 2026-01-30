<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'contact_date',
        'contact_method',
        'outcome',
        'notes',
        'next_action',
        'next_action_date',
        'contacted_by',
    ];

    protected $casts = [
        'contact_date' => 'datetime',
        'next_action_date' => 'date',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function contactedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByOutcome($query, $outcome)
    {
        return $query->where('outcome', $outcome);
    }

    public function scopeByContactMethod($query, $method)
    {
        return $query->where('contact_method', $method);
    }

    public function scopePendingActions($query)
    {
        return $query->whereNotNull('next_action_date')
                     ->where('next_action_date', '<=', now()->toDateString());
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const CONTACT_METHODS = [
        'phone' => 'Phone Call',
        'sms' => 'SMS',
        'email' => 'Email',
        'visit' => 'Home Visit',
        'in_person' => 'In Person',
    ];

    public const OUTCOMES = [
        'reached' => 'Reached',
        'no_answer' => 'No Answer',
        'interested' => 'Interested',
        'not_interested' => 'Not Interested',
        'callback_requested' => 'Callback Requested',
        'wrong_number' => 'Wrong Number',
    ];
}
