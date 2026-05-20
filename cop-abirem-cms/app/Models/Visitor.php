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

    protected $appends = ['full_name', 'visit_count'];

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getVisitCountAttribute(): int
    {
        return $this->visits()->count();
    }

    public function getLastVisitDateAttribute()
    {
        $lastVisit = $this->visits()->latest('visit_date')->first();
        return $lastVisit ? $lastVisit->visit_date : $this->first_visit_date;
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

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

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function scopeByFollowUpStatus($query, $status)
    {
        return $query->where('follow_up_status', $status);
    }

    public function scopeByReferralSource($query, $source)
    {
        return $query->where('referral_source', $source);
    }

    public function scopeNotConverted($query)
    {
        return $query->whereNull('converted_to_member_id');
    }

    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_to_member_id');
    }

    public function scopeRecentVisitors($query, $days = 30)
    {
        return $query->where('first_visit_date', '>=', now()->subDays($days));
    }

    public function scopePendingFollowUp($query)
    {
        return $query->whereIn('follow_up_status', ['pending', 'contacted', 'interested']);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function isConverted(): bool
    {
        return !is_null($this->converted_to_member_id);
    }

    public function canBeConverted(): bool
    {
        return !$this->isConverted() && $this->follow_up_status !== 'not_interested';
    }

    public function recordVisit(int $serviceTypeId = null, string $notes = null): VisitorVisit
    {
        return $this->visits()->create([
            'service_type_id' => $serviceTypeId,
            'visit_date'      => now()->toDateString(),
            'notes'           => $notes,
        ]);
    }

    public function addFollowUpLog(string $contactMethod, string $outcome, string $notes = null, int $userId = null): FollowUpLog
    {
        return $this->followUpLogs()->create([
            'contact_date' => now(),
            'contact_method' => $contactMethod,
            'outcome' => $outcome,
            'notes' => $notes,
            'contacted_by' => $userId ?? auth()->id(),
        ]);
    }

    public function convertToMember(array $memberData): Member
    {
        $memberData['first_name'] = $memberData['first_name'] ?? $this->first_name;
        $memberData['last_name'] = $memberData['last_name'] ?? $this->last_name;
        $memberData['phone_primary'] = $memberData['phone_primary'] ?? $this->phone;
        $memberData['email'] = $memberData['email'] ?? $this->email;
        $memberData['address'] = $memberData['address'] ?? $this->address;
        $memberData['date_joined'] = $memberData['date_joined'] ?? now()->toDateString();
        if (empty($memberData['member_id'])) {
            $memberData['member_id'] = Member::generateMemberId();
        }
        $memberData['created_by'] = auth()->id();

        $member = Member::create($memberData);

        $this->update([
            'converted_to_member_id' => $member->id,
            'follow_up_status' => 'converted',
        ]);

        return $member;
    }
}
