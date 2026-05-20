<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'visitor_id',
        'contact_date',
        'contact_method',
        'outcome',
        'notes',
        'next_follow_up_date',
        'contacted_by',
    ];

    protected $casts = [
        'contact_date' => 'datetime',
        'next_follow_up_date' => 'date',
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
        return $query->whereNotNull('next_follow_up_date')
                     ->where('next_follow_up_date', '<=', now()->toDateString());
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const CONTACT_METHODS = [
        'phone'    => 'Phone Call',
        'sms'      => 'SMS',
        'email'    => 'Email',
        'visit'    => 'Home Visit',
        'whatsapp' => 'WhatsApp',
    ];

    public const OUTCOMES = [
        'reached'        => 'Reached',
        'no_answer'      => 'No Answer',
        'callback'       => 'Callback Requested',
        'interested'     => 'Interested',
        'not_interested' => 'Not Interested',
    ];
}
