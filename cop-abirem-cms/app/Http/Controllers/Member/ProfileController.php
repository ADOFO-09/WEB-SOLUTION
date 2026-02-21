<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display member profile.
     */
    public function show(Request $request)
    {
        $member = $request->user()->member;
        $member->load('ministries');
        
        return view('member.profile.show', compact('member'));
    }
    
    /**
     * Show edit profile form.
     */
    public function edit(Request $request)
    {
        $member = $request->user()->member;
        
        return view('member.profile.edit', compact('member'));
    }
    
    /**
     * Update member profile.
     * 
     * Note: Members can only update limited fields. 
     * Sensitive fields require admin approval.
     */
    public function update(Request $request)
    {
        $member = $request->user()->member;
        
        $validated = $request->validate([
            'email' => 'nullable|email|max:255',
            'phone_primary' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);
        
        $member->update($validated);
        
        return redirect()->route('member.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Show password change form.
     */
    public function password()
    {
        return view('member.profile.password');
    }
    
    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);
        
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return redirect()->route('member.profile.show')
            ->with('success', 'Password changed successfully.');
    }
    
    /**
     * Update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $member = $request->user()->member;
        
        // Delete old photo if exists
        if ($member->photo_path) {
            Storage::disk('public')->delete($member->photo_path);
        }
        
        // Store new photo
        $path = $request->file('photo')->store('members/photos', 'public');
        
        $member->update(['photo_path' => $path]);
        
        return redirect()->route('member.profile.show')
            ->with('success', 'Profile photo updated successfully.');
    }
}
