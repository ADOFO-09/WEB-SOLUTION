<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return ['auth'];
    }

    /**
     * Show the user's profile.
     */
    public function show()
    {
        return view('admin.profile.show', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        return view('admin.profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|max:255|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the password change form.
     */
    public function password()
    {
        return view('admin.profile.password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = auth()->user();
        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show the forced first-login password change form.
     */
    public function forceChangePassword()
    {
        return view('admin.profile.force-change-password');
    }

    /**
     * Handle the forced password change submission.
     */
    public function storeForceChangePassword(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        /** @var User $user */
        $user = auth()->user();
        $user->password             = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route(RoleHelper::getDashboardRoute($user))
            ->with('success', 'Password updated. Welcome!');
    }
}
