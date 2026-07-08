<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MinistryGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'name',
        'description',
        'leader_member_id',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'leader_member_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Members assigned to this group via the member_ministry pivot.
     * Uses ministry_group_id as the FK on the pivot pointing to this group.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            Member::class,
            'member_ministry',
            'ministry_group_id',
            'member_id'
        )
        ->wherePivot('is_active', true)
        ->withPivot('role', 'ministry_id', 'joined_date');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMinistry($query, int $ministryId)
    {
        return $query->where('ministry_id', $ministryId);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}
