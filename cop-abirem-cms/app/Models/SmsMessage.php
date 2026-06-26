<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_type',
        'category',
        'subject',
        'message_content',
        'manual_placeholder_values',
        'recipient_count',
        'successful_count',
        'failed_count',
        'cost',
        'status',
        'scheduled_at',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'cost'                      => 'decimal:2',
        'scheduled_at'              => 'datetime',
        'sent_at'                   => 'datetime',
        'manual_placeholder_values' => 'array',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function recipients(): HasMany
    {
        return $this->hasMany(SmsRecipient::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'sending' => 'bg-yellow-100 text-yellow-800',
            'sent' => 'bg-green-100 text-green-800',
            'partially_sent' => 'bg-orange-100 text-orange-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getDeliveryRateAttribute(): float
    {
        if ($this->recipient_count == 0) return 0;
        return round(($this->successful_count / $this->recipient_count) * 100, 1);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function send(): void
    {
        $this->update([
            'status' => 'sending',
            'sent_at' => now(),
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($this->recipients()->where('status', 'pending')->get() as $recipient) {
            try {
                // In production, integrate with Africa's Talking API here
                // For now, simulate sending
                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $recipient->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $failCount++;
            }
        }

        $this->update([
            'successful_count' => $successCount,
            'failed_count' => $failCount,
            'status' => $failCount === 0 ? 'sent' : ($successCount === 0 ? 'failed' : 'partially_sent'),
        ]);
    }

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const MESSAGE_TYPES = [
        'bulk' => 'Bulk Message',
        'individual' => 'Individual',
        'automated' => 'Automated',
    ];

    public const CATEGORIES = [
        'general' => 'General',
        'financial' => 'Financial',
        'attendance' => 'Attendance',
        'event' => 'Event',
        'reminder' => 'Reminder',
        'birthday' => 'Birthday',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'sending' => 'Sending',
        'sent' => 'Sent',
        'partially_sent' => 'Partially Sent',
        'failed' => 'Failed',
    ];
}
