<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     * 
     * Permissions are system-defined and cannot be created/edited/deleted through the UI.
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $permissions = $query->orderBy('module')->orderBy('name')->get();
        $permissionsByModule = $permissions->groupBy('module');
        $modules = Permission::getModules();

        return view('admin.permissions.index', compact('permissionsByModule', 'modules'));
    }
}
