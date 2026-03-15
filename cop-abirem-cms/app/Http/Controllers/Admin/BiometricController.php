<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class BiometricController extends Controller
{
    /**
     * Show the biometric enrollment page for a member.
     */
    public function showEnrollment(Member $member)
    {
        return view('admin.members.biometric', compact('member'));
    }

    /**
     * Store a fingerprint template captured by the scanner.
     * Expects JSON: { fingerprint_template: "<base64>", finger_index: 1|2 }
     */
    public function enroll(Request $request, Member $member)
    {
        $request->validate([
            'fingerprint_template' => 'required|string',
            'finger_index'         => 'required|in:1,2',
        ]);

        $field = $request->finger_index == 1
            ? 'fingerprint_template_1'
            : 'fingerprint_template_2';

        $member->update([
            $field                  => $request->fingerprint_template,
            'biometric_enrolled'    => true,
            'biometric_enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint enrolled successfully',
            'finger'  => $request->finger_index == 1 ? 'Primary' : 'Backup',
        ]);
    }

    /**
     * Remove all biometric data for a member.
     */
    public function remove(Member $member)
    {
        $member->update([
            'fingerprint_template_1' => null,
            'fingerprint_template_2' => null,
            'biometric_enrolled'     => false,
            'biometric_enrolled_at'  => null,
        ]);

        return back()->with('success', 'Biometric data removed successfully.');
    }
}
