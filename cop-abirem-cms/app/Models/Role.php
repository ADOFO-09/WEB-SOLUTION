<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Get the permissions assigned to this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
                    ->withTimestamps();
    }

    /**
     * Get the users that belong to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions->contains('slug', $permissionSlug);
    }

    /**
     * Check if role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->permissions->whereIn('slug', $permissionSlugs)->isNotEmpty();
    }

    /**
     * Check if role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        $rolePermissions = $this->permissions->pluck('slug')->toArray();
        return empty(array_diff($permissionSlugs, $rolePermissions));
    }

    /**
     * Sync permissions to the role.
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Check if this is a system role (cannot be deleted).
     */
    public function isSystemRole(): bool
    {
        return $this->is_system;
    }

    /**
     * Scope a query to only include system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to exclude system roles.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Get role by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
