<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = auth()->user();
        $user->load(['role', 'loginHistory' => function ($q) {
            $q->take(5);
        }]);

        return view('admin.profile.show', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the change password form.
     */
    public function showChangePasswordForm()
    {
        return view('admin.profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        ActivityLog::log('password.changed');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show the change password form for forced password change.
     */
    public function showForceChangePasswordForm()
    {
        if (!auth()->user()->must_change_password) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.profile.force-change-password');
    }
}
