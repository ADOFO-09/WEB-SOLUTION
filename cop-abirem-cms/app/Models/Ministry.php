<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ministry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'leader_id',
        'meeting_day',
        'meeting_time',
        'is_active',
    ];

    protected $casts = [
        'meeting_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ministry) {
            if (empty($ministry->slug)) {
                $ministry->slug = Str::slug($ministry->name);
            }
        });

        static::updating(function ($ministry) {
            if ($ministry->isDirty('name') && !$ministry->isDirty('slug')) {
                $ministry->slug = Str::slug($ministry->name);
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'leader_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_ministry')
            ->withPivot('role', 'joined_date', 'left_date', 'is_active')
            ->withTimestamps();
    }

    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('is_active', true);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assigned_to_ministry_id');
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getMemberCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    public function getMeetingScheduleAttribute(): ?string
    {
        if ($this->meeting_day && $this->meeting_time) {
            return $this->meeting_day . ' at ' . $this->meeting_time->format('g:i A');
        }
        return null;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function addMember(Member $member, string $role = 'member', ?string $joinedDate = null): void
    {
        $this->members()->syncWithoutDetaching([
            $member->id => [
                'role' => $role,
                'joined_date' => $joinedDate ?? now()->toDateString(),
                'is_active' => true,
            ]
        ]);
    }

    public function removeMember(Member $member): void
    {
        $this->members()->updateExistingPivot($member->id, [
            'is_active' => false,
            'left_date' => now()->toDateString(),
        ]);
    }

    public function getLeaders()
    {
        return $this->activeMembers()
            ->wherePivotIn('role', ['leader', 'assistant_leader'])
            ->get();
    }
}
