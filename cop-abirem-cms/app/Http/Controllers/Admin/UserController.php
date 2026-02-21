<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:users.view', only: ['index', 'show']),
            new Middleware('permission:users.create', only: ['create', 'store']),
            new Middleware('permission:users.edit', only: ['edit', 'update', 'linkMember', 'unlinkMember']),
            new Middleware('permission:users.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'member']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by member link status
        if ($request->filled('member_status')) {
            if ($request->member_status === 'linked') {
                $query->whereNotNull('member_id');
            } else {
                $query->whereNull('member_id');
            }
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'linked' => User::whereNotNull('member_id')->count(),
            'unlinked' => User::whereNull('member_id')->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $members = Member::where('membership_status', 'active')
            ->whereDoesntHave('user')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.users.create', compact('roles', 'members'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'member_id' => 'nullable|exists:members,id|unique:users,member_id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['role', 'member', 'activityLogs' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $members = Member::where('membership_status', 'active')
            ->where(function ($q) use ($user) {
                $q->whereDoesntHave('user')
                  ->orWhere('id', $user->member_id);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.users.edit', compact('user', 'roles', 'members'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'member_id' => 'nullable|exists:members,id|unique:users,member_id,' . $user->id,
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' deleted successfully.");
    }

    /**
     * Show form to link a user to a member.
     */
    public function linkMemberForm(User $user)
    {
        if ($user->member_id) {
            return redirect()->route('admin.users.show', $user)
                ->with('info', 'This user is already linked to a member.');
        }

        $members = Member::where('membership_status', 'active')
            ->whereDoesntHave('user')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.users.link-member', compact('user', 'members'));
    }

    /**
     * Link a user to a member profile.
     */
    public function linkMember(Request $request, User $user)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id|unique:users,member_id',
        ]);

        $user->update(['member_id' => $validated['member_id']]);

        $member = Member::find($validated['member_id']);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "User linked to member '{$member->full_name}' successfully.");
    }

    /**
     * Unlink a user from their member profile.
     */
    public function unlinkMember(User $user)
    {
        if (!$user->member_id) {
            return back()->with('error', 'This user is not linked to any member.');
        }

        $memberName = $user->member->full_name ?? 'Unknown';
        $user->update(['member_id' => null]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "User unlinked from member '{$memberName}'.");
    }

    /**
     * Create a user account for a member.
     */
    public function createForMember(Member $member)
    {
        if ($member->user) {
            return redirect()->route('admin.members.show', $member)
                ->with('info', 'This member already has a user account.');
        }

        $roles = Role::orderBy('name')->get();

        return view('admin.users.create-for-member', compact('member', 'roles'));
    }

    /**
     * Store a user account for a member.
     */
    public function storeForMember(Request $request, Member $member)
    {
        if ($member->user) {
            return redirect()->route('admin.members.show', $member)
                ->with('error', 'This member already has a user account.');
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $member->full_name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'member_id' => $member->id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.members.show', $member)
            ->with('success', "User account created for '{$member->full_name}'.");
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User '{$user->name}' has been {$status}.");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "Password reset for '{$user->name}'.");
    }
}
