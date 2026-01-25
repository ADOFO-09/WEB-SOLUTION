<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'related_member_id',
        'relationship_type',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function relatedMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'related_member_id');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public static function createBidirectional(int $memberId, int $relatedMemberId, string $type): void
    {
        $inverseType = self::getInverseRelationship($type);

        // Create forward relationship
        self::updateOrCreate(
            [
                'member_id' => $memberId,
                'related_member_id' => $relatedMemberId,
                'relationship_type' => $type,
            ]
        );

        // Create inverse relationship
        self::updateOrCreate(
            [
                'member_id' => $relatedMemberId,
                'related_member_id' => $memberId,
                'relationship_type' => $inverseType,
            ]
        );
    }

    public static function removeBidirectional(int $memberId, int $relatedMemberId): void
    {
        self::where(function ($q) use ($memberId, $relatedMemberId) {
            $q->where('member_id', $memberId)
              ->where('related_member_id', $relatedMemberId);
        })->orWhere(function ($q) use ($memberId, $relatedMemberId) {
            $q->where('member_id', $relatedMemberId)
              ->where('related_member_id', $memberId);
        })->delete();
    }

    public static function getInverseRelationship(string $type): string
    {
        return match ($type) {
            'spouse' => 'spouse',
            'child' => 'parent',
            'parent' => 'child',
            'sibling' => 'sibling',
            default => $type,
        };
    }

    public function getRelationshipLabelAttribute(): string
    {
        return match ($this->relationship_type) {
            'spouse' => 'Spouse',
            'child' => 'Child',
            'parent' => 'Parent',
            'sibling' => 'Sibling',
            default => ucfirst($this->relationship_type),
        };
    }
}
