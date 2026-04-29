<?php

namespace App\Traits;

use App\Models\LedgerAuditLog;

trait HasLedgerCorrections
{
    public static function bootHasLedgerCorrections(): void
    {
        static::created(function ($model) {
            $model->logAudit('created', null, $model->toArray());
        });

        static::updated(function ($model) {
            $correctionFields = [
                'ledger_status', 'voided_by', 'voided_at', 'void_reason',
                'adjusted_by_id', 'adjusts_entry_id', 'is_adjustment',
            ];
            $nonCorrectionDirty = array_diff_key($model->getDirty(), array_flip($correctionFields));
            if (!empty($nonCorrectionDirty)) {
                $model->logAudit('updated', $model->getOriginal(), $model->getChanges());
            }
        });
    }

    // ==========================================
    // CORE CORRECTION METHODS
    // ==========================================

    public function voidEntry(string $reason): void
    {
        $old = $this->toArray();
        $this->update([
            'ledger_status' => 'voided',
            'voided_by'     => auth()->id(),
            'voided_at'     => now(),
            'void_reason'   => $reason,
        ]);
        $this->logAudit('voided', $old, $this->fresh()->toArray(), $reason);
    }

    public function restoreEntry(): void
    {
        $old = $this->toArray();
        $this->update([
            'ledger_status' => 'active',
            'voided_by'     => null,
            'voided_at'     => null,
            'void_reason'   => null,
        ]);
        $this->logAudit('restored', $old, $this->fresh()->toArray());
    }

    public function createAdjustment(array $newData, string $reason): static
    {
        $base = $this->getFillableData();
        $adjustmentData = array_merge($base, $newData, [
            'reference_number'  => 'ADJ-' . $this->reference_number,
            'is_adjustment'     => true,
            'adjusts_entry_id'  => $this->id,
            'ledger_status'     => 'active',
        ]);

        $adjustment = static::create($adjustmentData);

        $old = $this->toArray();
        $this->update([
            'ledger_status'  => 'adjusted',
            'adjusted_by_id' => $adjustment->id,
        ]);

        $this->logAudit('adjusted', $old, $this->fresh()->toArray(), $reason);

        return $adjustment;
    }

    // ==========================================
    // AUDIT LOG QUERY
    // ==========================================

    public function getAuditLogs()
    {
        return LedgerAuditLog::with('performer')
            ->where('entry_type', $this->getEntryType())
            ->where('entry_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function voidedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'voided_by');
    }

    public function adjustmentEntry()
    {
        return $this->belongsTo(static::class, 'adjusted_by_id');
    }

    public function originalEntry()
    {
        return $this->belongsTo(static::class, 'adjusts_entry_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('ledger_status', 'active');
    }

    public function scopeVoided($query)
    {
        return $query->where('ledger_status', 'voided');
    }

    public function scopeAdjusted($query)
    {
        return $query->where('ledger_status', 'adjusted');
    }

    public function scopeNotVoided($query)
    {
        return $query->where('ledger_status', '!=', 'voided');
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isVoided(): bool   { return $this->ledger_status === 'voided'; }
    public function isAdjusted(): bool { return $this->ledger_status === 'adjusted'; }
    public function isAdjustment(): bool { return (bool) $this->is_adjustment; }
    public function isActive(): bool   { return ($this->ledger_status ?? 'active') === 'active'; }

    public function getLedgerStatusBadgeAttribute(): string
    {
        return match($this->ledger_status ?? 'active') {
            'voided'   => 'bg-red-100 text-red-800',
            'adjusted' => 'bg-yellow-100 text-yellow-800',
            default    => 'bg-green-100 text-green-800',
        };
    }

    // ==========================================
    // PRIVATE
    // ==========================================

    private function getEntryType(): string
    {
        return match(static::class) {
            \App\Models\Tithe::class    => 'tithe',
            \App\Models\Offering::class => 'offering',
            \App\Models\Donation::class => 'donation',
            \App\Models\Expense::class  => 'expense',
            default => strtolower(class_basename(static::class)),
        };
    }

    private function getFillableData(): array
    {
        $skip = [
            'reference_number', 'receipt_number', 'voucher_number',
            'is_adjustment', 'adjusts_entry_id', 'adjusted_by_id',
            'ledger_status', 'voided_by', 'voided_at', 'void_reason',
        ];
        return collect($this->getFillable())
            ->reject(fn($f) => in_array($f, $skip))
            ->mapWithKeys(fn($f) => [$f => $this->getAttribute($f)])
            ->toArray();
    }

    private function logAudit(string $action, ?array $old, ?array $new, ?string $reason = null): void
    {
        try {
            LedgerAuditLog::create([
                'entry_type'   => $this->getEntryType(),
                'entry_id'     => $this->id,
                'action'       => $action,
                'old_values'   => $old,
                'new_values'   => $new,
                'reason'       => $reason,
                'performed_by' => auth()->id() ?? 1,
                'ip_address'   => request()->ip(),
                'user_agent'   => substr(request()->userAgent() ?? '', 0, 255),
            ]);
        } catch (\Throwable) {
            // Audit failures must never break the main operation
        }
    }
}
