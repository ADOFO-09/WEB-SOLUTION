<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'first_visit_date',
        'referral_source',
        'referred_by_member_id',
        'prayer_request',
        'follow_up_status',
        'notes',
        'converted_to_member_id',
        'created_by',
    ];

    protected $casts = [
        'first_visit_date' => 'date',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'referred_by_member_id');
    }

    public function convertedToMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'converted_to_member_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(VisitorVisit::class);
    }

    public function followUpLogs(): HasMany
    {
        return $this->hasMany(FollowUpLog::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
