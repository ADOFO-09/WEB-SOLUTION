<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::getGroupedByModule();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'is_system' => false,
        ]);

        // Sync permissions
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        ActivityLog::log('role.created', $role);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $permissionsByModule = $role->permissions->groupBy('module');

        return view('admin.roles.show', compact('role', 'permissionsByModule'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::getGroupedByModule();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Cannot change slug of system roles
        $updateData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
        ];

        if (!$role->is_system) {
            $updateData['slug'] = Str::slug($validated['name']);
        }

        $role->update($updateData);

        // Sync permissions
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Show the permission management form for a role.
     */
    public function permissions(Role $role)
    {
        $allPermissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.permissions', compact('role', 'allPermissions', 'rolePermissionIds'));
    }

    /**
     * Update the permissions assigned to a role.
     * System Administrator (is_system + slug=admin) cannot be edited.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        // Protect the System Administrator role
        if ($role->is_system && $role->slug === 'admin') {
            return back()->with('error', 'System Administrator permissions cannot be modified.');
        }

        $validated = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        ActivityLog::log('role.permissions_updated', $role);

        return redirect()->route('admin.roles.index')
            ->with('success', "Permissions for '{$role->name}' updated successfully.");
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Cannot delete system roles
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        // Cannot delete roles with users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users. Please reassign users first.');
        }

        $roleName = $role->name;
        
        // Detach all permissions
        $role->permissions()->detach();
        
        // Delete the role
        $role->delete();

        ActivityLog::log('role.deleted', $role);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' deleted successfully.");
    }
}
