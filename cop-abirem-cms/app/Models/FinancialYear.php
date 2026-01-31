<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'is_closed',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function tithes(): HasMany
    {
        return $this->hasMany(Tithe::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function current(): ?self
    {
        return self::active()->first() 
            ?? self::open()->orderBy('start_date', 'desc')->first();
    }

    public function getTotalTithes(): float
    {
        return $this->tithes()->sum('amount');
    }

    public function getTotalOfferings(): float
    {
        return $this->offerings()->sum('amount');
    }

    public function getTotalDonations(): float
    {
        return $this->donations()->sum('amount');
    }

    public function getTotalIncome(): float
    {
        return $this->getTotalTithes() + $this->getTotalOfferings() + $this->getTotalDonations();
    }

    public function close(): void
    {
        $this->update([
            'is_active' => false,
            'is_closed' => true,
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);
    }
}
