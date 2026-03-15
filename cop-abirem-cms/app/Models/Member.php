<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\FamilyRelationship;


class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'marital_status',
        'email',
        'phone_primary',
        'phone_secondary',
        'address',
        'city',
        'region',
        'occupation',
        'employer',
        'emergency_contact_name',
        'emergency_contact_phone',
        'photo_path',
        'qr_code_path',
        'fingerprint_template_1',
        'fingerprint_template_2',
        'biometric_enrolled',
        'biometric_enrolled_at',
        'date_joined',
        'baptism_date',
        'baptism_type',
        'membership_status',
        'previous_church',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth'       => 'date',
        'date_joined'         => 'date',
        'baptism_date'        => 'date',
        'biometric_enrolled'  => 'boolean',
        'biometric_enrolled_at' => 'datetime',
    ];

    protected $appends = ['full_name', 'age'];

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        
        return $this->title ? $this->title . ' ' . $name : $name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Get the QR code URL for the member.
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'member_id');
    }

    public function ministries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'member_ministry')
            ->withPivot('role', 'joined_date', 'left_date', 'is_active')
            ->withTimestamps();
    }

    public function activeMinistries(): BelongsToMany
    {
        return $this->ministries()->wherePivot('is_active', true);
    }

    public function familyRelationships(): HasMany
    {
        return $this->hasMany(FamilyRelationship::class, 'member_id');
    }

    public function relatedTo(): HasMany
    {
        return $this->hasMany(FamilyRelationship::class, 'related_member_id');
    }

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

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function referredVisitors(): HasMany
    {
        return $this->hasMany(Visitor::class, 'referred_by_member_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('membership_status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('membership_status', 'inactive');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('membership_status', $status);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByMaritalStatus($query, $status)
    {
        return $query->where('marital_status', $status);
    }

    public function scopeInMinistry($query, $ministryId)
    {
        return $query->whereHas('ministries', function ($q) use ($ministryId) {
            $q->where('ministries.id', $ministryId)->where('is_active', true);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('member_id', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('middle_name', 'like', "%{$search}%")
              ->orWhere('phone_primary', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function scopeBirthdayThisMonth($query)
    {
        return $query->whereMonth('date_of_birth', now()->month);
    }

    public function scopeBirthdayToday($query)
    {
        return $query->whereMonth('date_of_birth', now()->month)
                     ->whereDay('date_of_birth', now()->day);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function generateMemberId(): string
    {
        $prefix = Setting::get('member_id_prefix', 'COP');
        $year = date('Y');
        
        $lastMember = self::withTrashed()
            ->where('member_id', 'like', "{$prefix}-{$year}-%")
            ->orderBy('member_id', 'desc')
            ->first();

        if ($lastMember) {
            $lastNumber = (int) substr($lastMember->member_id, -5);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return "{$prefix}-{$year}-{$newNumber}";
    }

    public function getQrCodeData(): array
    {
        return [
            'id' => $this->id,
            'member_id' => $this->member_id,
            'name' => $this->full_name,
            'phone' => $this->phone_primary,
        ];
    }

    public function getTotalTithes(?int $year = null): float
    {
        $query = $this->tithes();
        
        if ($year) {
            $query->whereYear('payment_date', $year);
        }
        
        return $query->sum('amount');
    }

    public function getTotalOfferings(?int $year = null): float
    {
        $query = $this->offerings();
        
        if ($year) {
            $query->whereYear('payment_date', $year);
        }
        
        return $query->sum('amount');
    }

    public function getAttendanceRate(?int $days = 90): float
    {
        $totalSessions = AttendanceSession::where('service_date', '>=', now()->subDays($days))
            ->where('status', 'closed')
            ->count();

        if ($totalSessions === 0) {
            return 0;
        }

        $attended = $this->attendanceRecords()
            ->whereHas('session', function ($q) use ($days) {
                $q->where('service_date', '>=', now()->subDays($days))
                  ->where('status', 'closed');
            })
            ->count();

        return round(($attended / $totalSessions) * 100, 1);
    }

    public function getSpouse()
    {
        $relationship = $this->familyRelationships()
            ->where('relationship_type', 'spouse')
            ->first();

        return $relationship ? $relationship->relatedMember : null;
    }

    public function getChildren()
    {
        return $this->familyRelationships()
            ->where('relationship_type', 'child')
            ->with('relatedMember')
            ->get()
            ->pluck('relatedMember');
    }

    public function family(Member $member)
    {
        $member->load(['familyRelationships.relatedMember']);
        
        // Get all members except current one for adding relationships
        $availableMembers = Member::where('id', '!=', $member->id)
            ->where('membership_status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        $relationshipTypes = [
            'spouse' => 'Spouse',
            'father' => 'Father',
            'mother' => 'Mother',
            'son' => 'Son',
            'daughter' => 'Daughter',
            'brother' => 'Brother',
            'sister' => 'Sister',
            'grandfather' => 'Grandfather',
            'grandmother' => 'Grandmother',
            'grandson' => 'Grandson',
            'granddaughter' => 'Granddaughter',
            'uncle' => 'Uncle',
            'aunt' => 'Aunt',
            'nephew' => 'Nephew',
            'niece' => 'Niece',
            'cousin' => 'Cousin',
            'in-law' => 'In-Law',
            'other' => 'Other',
        ];
        
        return view('admin.members.family', compact('member', 'availableMembers', 'relationshipTypes'));
    }

    /**
     * Store a new family relationship.
     */
    public function storeFamily(Request $request, Member $member)
    {
        $validated = $request->validate([
            'related_member_id' => 'required|exists:members,id|different:member',
            'relationship_type' => 'required|string|max:50',
        ]);
        
        // Check if relationship already exists
        $exists = FamilyRelationship::where('member_id', $member->id)
            ->where('related_member_id', $validated['related_member_id'])
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'This family relationship already exists.');
        }
        
        // Create the relationship
        FamilyRelationship::create([
            'member_id' => $member->id,
            'related_member_id' => $validated['related_member_id'],
            'relationship_type' => $validated['relationship_type'],
        ]);
        
        // Optionally create the inverse relationship
        $inverseType = $this->getInverseRelationship($validated['relationship_type'], $member->gender);
        if ($inverseType) {
            FamilyRelationship::firstOrCreate([
                'member_id' => $validated['related_member_id'],
                'related_member_id' => $member->id,
            ], [
                'relationship_type' => $inverseType,
            ]);
        }
        
        return back()->with('success', 'Family relationship added successfully.');
    }

    /**
     * Remove a family relationship.
     */
    public function destroyFamily(Member $member, FamilyRelationship $relationship)
    {
        // Ensure the relationship belongs to this member
        if ($relationship->member_id !== $member->id) {
            abort(403);
        }
        
        // Also remove the inverse relationship if exists
        FamilyRelationship::where('member_id', $relationship->related_member_id)
            ->where('related_member_id', $member->id)
            ->delete();
        
        $relationship->delete();
        
        return back()->with('success', 'Family relationship removed.');
    }

    /**
     * Get the inverse relationship type.
     */
    private function getInverseRelationship(string $type, ?string $gender): ?string
    {
        $inverses = [
            'spouse' => 'spouse',
            'father' => $gender === 'male' ? 'son' : 'daughter',
            'mother' => $gender === 'male' ? 'son' : 'daughter',
            'son' => $gender === 'male' ? 'father' : 'mother',
            'daughter' => $gender === 'male' ? 'father' : 'mother',
            'brother' => $gender === 'male' ? 'brother' : 'sister',
            'sister' => $gender === 'male' ? 'brother' : 'sister',
            'grandfather' => $gender === 'male' ? 'grandson' : 'granddaughter',
            'grandmother' => $gender === 'male' ? 'grandson' : 'granddaughter',
            'grandson' => $gender === 'male' ? 'grandfather' : 'grandmother',
            'granddaughter' => $gender === 'male' ? 'grandfather' : 'grandmother',
            'uncle' => $gender === 'male' ? 'nephew' : 'niece',
            'aunt' => $gender === 'male' ? 'nephew' : 'niece',
            'nephew' => $gender === 'male' ? 'uncle' : 'aunt',
            'niece' => $gender === 'male' ? 'uncle' : 'aunt',
            'cousin' => 'cousin',
        ];
        
        return $inverses[$type] ?? null;
    }

    

}