<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffPositionController extends Controller
{
    // Staff roles the church assigns to real people (not system admin, not base member)
    private const STAFF_SLUGS = ['elder', 'secretary', 'finance', 'ministry_leader'];

    public function index()
    {
        $staffRoles = Role::whereIn('slug', self::STAFF_SLUGS)
            ->withCount('users')
            ->with(['users' => function ($q) {
                $q->where('is_active', true)->with('member');
            }])
            ->orderBy('id')
            ->get();

        return view('admin.users.staff-positions', compact('staffRoles'));
    }

    public function assignForm(Role $role)
    {
        abort_unless(in_array($role->slug, self::STAFF_SLUGS), 404);

        $currentHolders = $role->users()->where('is_active', true)->with('member')->get();

        $members = Member::where('membership_status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->with('user')
            ->get();

        // Build a map of member_id → user info for Alpine.js
        $memberUserMap = $members->mapWithKeys(fn ($m) => [
            $m->id => [
                'has_user' => $m->user !== null,
                'email'    => $m->user?->email,
                'name'     => $m->full_name,
            ]
        ]);

        return view('admin.users.staff-positions-assign', compact('role', 'currentHolders', 'members', 'memberUserMap'));
    }

    public function assign(Request $request, Role $role)
    {
        abort_unless(in_array($role->slug, self::STAFF_SLUGS), 404);

        $member = Member::findOrFail($request->input('member_id'));

        // Validate
        $rules = ['member_id' => 'required|exists:members,id'];

        // Email is required only if the member has no user account yet
        if (!$member->user) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Get or create the user account for this member
        if ($member->user) {
            $newUser = $member->user;
            $newUser->update([
                'role_id'   => $role->id,
                'is_active' => true,
            ]);
        } else {
            $newUser = User::create([
                'name'      => $member->full_name,
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'role_id'   => $role->id,
                'member_id' => $member->id,
                'is_active' => true,
            ]);
        }

        // Demote all previous holders of this role (except the new one) to Member
        $memberRole = Role::where('slug', 'member')->firstOrFail();
        User::where('role_id', $role->id)
            ->where('id', '!=', $newUser->id)
            ->update(['role_id' => $memberRole->id]);

        return redirect()->route('admin.staff-positions.index')
            ->with('success', "{$member->full_name} has been assigned as {$role->name}.");
    }
}
