<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('role')->withTrashed();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false)->whereNull('deleted_at');
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            }
        } else {
            $query->whereNull('deleted_at');
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
            'send_welcome_email' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_active' => $validated['is_active'] ?? true,
            'must_change_password' => true,
            'created_by' => auth()->id(),
        ]);

        // Log activity
        ActivityLog::log('user.created', $user);

        // TODO: Send welcome email if requested
        // if ($request->boolean('send_welcome_email')) {
        //     $user->notify(new WelcomeNotification($validated['password']));
        // }

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['role', 'creator', 'loginHistory' => function ($q) {
            $q->take(10);
        }]);

        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.users.show', compact('user', 'activityLogs'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Prevent editing own role
        if ($user->id === auth()->id() && $request->role_id != $user->role_id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        // Prevent deactivating own account
        if ($user->id === auth()->id() && !($validated['is_active'] ?? true)) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $oldValues = $user->toArray();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting last admin
        if ($user->hasRole('admin')) {
            $adminCount = User::whereHas('role', function ($q) {
                $q->where('slug', 'admin');
            })->count();

            if ($adminCount <= 1) {
                return back()->with('error', 'Cannot delete the last administrator.');
            }
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log('user.deleted', $user);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' deleted successfully.");
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        ActivityLog::log('user.restored', $user);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' restored successfully.");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'force_change' => ['boolean'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => $validated['force_change'] ?? true,
        ]);

        ActivityLog::log('password.reset', $user);

        return back()->with('success', "Password reset successfully for '{$user->name}'.");
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent toggling own status
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own status.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User '{$user->name}' has been {$status}.");
    }

    /**
     * Unlock a locked user account.
     */
    public function unlock(User $user)
    {
        $user->unlockAccount();

        return back()->with('success', "User '{$user->name}' has been unlocked.");
    }
}
