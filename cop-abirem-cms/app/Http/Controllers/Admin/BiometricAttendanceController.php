<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Member;
use Illuminate\Http\Request;

class BiometricAttendanceController extends Controller
{
    /**
     * Show the biometric attendance kiosk for a session.
     * Sends enrolled member templates to the browser for client-side matching.
     */
    public function showStation(AttendanceSession $session)
    {
        if ($session->status !== 'open') {
            return redirect()->route('admin.attendance.show', $session)
                ->with('error', 'This session is not open for attendance.');
        }

        // Load enrolled members — templates go to the browser for client-side matching.
        $enrolledMembersJson = Member::where('biometric_enrolled', true)
            ->whereNotNull('fingerprint_template_1')
            ->where('membership_status', 'active')
            ->select('id', 'first_name', 'last_name', 'member_id',
                     'photo_path', 'fingerprint_template_1', 'fingerprint_template_2')
            ->get()
            ->map(function ($m) {
                return [
                    'id'    => $m->id,
                    'name'  => $m->first_name . ' ' . $m->last_name,
                    'mid'   => $m->member_id,
                    'photo' => $m->photo_path ? '/storage/' . $m->photo_path : null,
                    't1'    => $m->fingerprint_template_1,
                    't2'    => $m->fingerprint_template_2,
                ];
            })
            ->values();

        // Members already checked in this session (to skip duplicates on the client)
        $checkedInIds = AttendanceRecord::where('session_id', $session->id)
            ->whereNotNull('member_id')
            ->pluck('member_id')
            ->toArray();

        $scanCount = AttendanceRecord::where('session_id', $session->id)
            ->where('attendance_method', 'biometric')
            ->count();

        return view('admin.attendance.biometric-station', compact(
            'session',
            'enrolledMembersJson',
            'checkedInIds',
            'scanCount'
        ));
    }

    /**
     * Record attendance after client-side fingerprint match.
     * Receives session_id + member_id (matched on client), records the attendance.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:attendance_sessions,id',
            'member_id'  => 'required|exists:members,id',
        ]);

        $session = AttendanceSession::findOrFail($request->session_id);
        $member  = Member::findOrFail($request->member_id);

        if ($session->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Session is closed.',
            ], 400);
        }

        // Prevent duplicate attendance
        $existing = AttendanceRecord::where('session_id', $session->id)
            ->where('member_id', $member->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success'     => false,
                'already_in'  => true,
                'message'     => $member->full_name . ' is already checked in.',
                'member_name' => $member->full_name,
            ]);
        }

        AttendanceRecord::create([
            'session_id'         => $session->id,
            'member_id'          => $member->id,
            'check_in_time'      => now(),
            'attendance_method'  => 'biometric',
            'marked_by'          => auth()->id(),
        ]);

        // Update session running total
        $session->increment('total_attendance');

        $photoUrl = $member->photo_path
            ? asset('storage/' . $member->photo_path)
            : null;

        return response()->json([
            'success'      => true,
            'message'      => 'Attendance recorded.',
            'member_name'  => $member->full_name,
            'member_id'    => $member->member_id,
            'photo_url'    => $photoUrl,
            'check_in_time'=> now()->format('g:i A'),
            'total_count'  => AttendanceRecord::where('session_id', $session->id)->count(),
        ]);
    }
}
