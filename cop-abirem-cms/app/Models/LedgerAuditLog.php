<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerAuditLog extends Model
{
    protected $fillable = [
        'entry_type',
        'entry_id',
        'action',
        'old_values',
        'new_values',
        'reason',
        'performed_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function getRelatedEntry(): ?Model
    {
        return match($this->entry_type) {
            'tithe'    => Tithe::find($this->entry_id),
            'offering' => Offering::find($this->entry_id),
            'donation' => Donation::find($this->entry_id),
            'expense'  => Expense::find($this->entry_id),
            default    => null,
        };
    }

    public function scopeForEntry($query, string $type, int $id)
    {
        return $query->where('entry_type', $type)->where('entry_id', $id);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'voided'   => 'bg-red-100 text-red-700',
            'adjusted' => 'bg-yellow-100 text-yellow-700',
            'restored' => 'bg-green-100 text-green-700',
            'created'  => 'bg-gray-100 text-gray-700',
            'updated'  => 'bg-blue-100 text-blue-700',
            default    => 'bg-gray-100 text-gray-600',
        };
    }

    public function getEntryTypeColorAttribute(): string
    {
        return match($this->entry_type) {
            'tithe'    => 'bg-green-100 text-green-700',
            'offering' => 'bg-blue-100 text-blue-700',
            'donation' => 'bg-purple-100 text-purple-700',
            'expense'  => 'bg-red-100 text-red-700',
            default    => 'bg-gray-100 text-gray-700',
        };
    }
}
