<?php

namespace App\Traits;

use App\Models\Role;

trait HasRoles
{
    /**
     * Check if user has a specific role by slug.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->role && in_array($this->role->slug, $roleSlugs);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Admin has all permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->role && $this->role->hasPermission($permissionSlug);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        // Admin has all permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->role && $this->role->hasAnyPermission($permissionSlugs);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        // Admin has all permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->role && $this->role->hasAllPermissions($permissionSlugs);
    }

    /**
     * Get all permissions for the user.
     */
    public function getAllPermissions(): array
    {
        if (!$this->role) {
            return [];
        }

        return $this->role->permissions->pluck('slug')->toArray();
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string $roleSlug): bool
    {
        $role = Role::findBySlug($roleSlug);
        
        if (!$role) {
            return false;
        }

        $this->update(['role_id' => $role->id]);
        
        return true;
    }

    /**
     * Get the role name.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role?->name ?? 'No Role';
    }

    /**
     * Check if user can perform action on a specific module.
     */
    public function canAccessModule(string $module): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        // Check if user has any permission for this module
        $permissions = $this->getAllPermissions();
        
        foreach ($permissions as $permission) {
            if (str_starts_with($permission, $module . '.')) {
                return true;
            }
        }

        return false;
    }
}
