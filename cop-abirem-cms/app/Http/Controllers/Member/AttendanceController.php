<?php

namespace App\Http\Controllers\Member;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display member's attendance history.
     */
    public function index(Request $request)
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        
        $query = AttendanceRecord::where('member_id', $member->id)
            ->whereHas('session', function ($q) use ($year, $month) {
                $q->whereYear('service_date', $year);
                if ($month) {
                    $q->whereMonth('service_date', $month);
                }
            })
            ->with(['session.serviceType']);
        
        $records = $query->orderBy('created_at', 'desc')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();
        
        // Monthly attendance summary
        $monthlySummary = [];
        for ($m = 1; $m <= 12; $m++) {
            $count = AttendanceRecord::where('member_id', $member->id)
                ->whereHas('session', function ($q) use ($year, $m) {
                    $q->whereYear('service_date', $year)
                      ->whereMonth('service_date', $m);
                })
                ->count();
            
            $monthlySummary[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'month_num' => $m,
                'count' => $count,
            ];
        }
        
        // Yearly stats
        $stats = [
            'total_year' => AttendanceRecord::where('member_id', $member->id)
                ->whereHas('session', fn($q) => $q->whereYear('service_date', $year))
                ->count(),
            'total_sessions' => AttendanceSession::whereYear('service_date', $year)
                ->where('status', 'closed')
                ->count(),
            'this_month' => AttendanceRecord::where('member_id', $member->id)
                ->whereHas('session', function ($q) {
                    $q->whereYear('service_date', now()->year)
                      ->whereMonth('service_date', now()->month);
                })
                ->count(),
        ];
        
        if ($stats['total_sessions'] > 0) {
            $stats['attendance_rate'] = round(($stats['total_year'] / $stats['total_sessions']) * 100);
        } else {
            $stats['attendance_rate'] = 0;
        }
        
        $years = range(now()->year, now()->year - 3);
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        return view('member.attendance.index', compact(
            'records', 'monthlySummary', 'stats', 'year', 'month', 'years', 'months'
        ));
    }

    /**
     * Show QR scanner page for self check-in.
     */
    public function showScanner(Request $request)
    {
        $member = $request->user()->member;

        if (!$member) {
            return redirect()->route('member.dashboard')
                ->with('error', 'No member profile is linked to your account.');
        }

        $recentAttendance = AttendanceRecord::where('member_id', $member->id)
            ->with('session.serviceType')
            ->latest()
            ->take(5)
            ->get();

        return view('member.attendance.scan', compact('recentAttendance'));
    }

    /**
     * Verify a session QR token and show confirmation page.
     */
    public function verifyQr(Request $request, string $token)
    {
        $member = $request->user()->member;

        if (!$member) {
            return redirect()->route('member.dashboard')
                ->with('error', 'No member profile is linked to your account.');
        }

        $session = AttendanceSession::where('qr_token', $token)
            ->with('serviceType')
            ->first();

        if (!$session) {
            return redirect()->route('member.attendance.scan')
                ->with('error', 'Invalid QR code. Please scan the correct session QR code.');
        }

        if (!$session->isQrValid()) {
            if ($session->status === 'closed') {
                $msg = 'This session has been closed and is no longer accepting attendance.';
            } elseif ($session->qr_expires_at && $session->qr_expires_at->isPast()) {
                $msg = 'This QR code has expired. Please ask the admin to update it.';
            } else {
                $msg = 'QR attendance is currently disabled for this session.';
            }
            return redirect()->route('member.attendance.scan')->with('error', $msg);
        }

        // Already marked?
        $existing = AttendanceRecord::where('session_id', $session->id)
            ->where('member_id', $member->id)
            ->first();

        if ($existing) {
            return redirect()->route('member.attendance.scan')
                ->with('info', 'You have already marked attendance for this session ('
                    . $session->service_title . ').');
        }

        return view('member.attendance.confirm', compact('session', 'member'));
    }

    /**
     * Record attendance after member confirms.
     */
    public function recordAttendance(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:attendance_sessions,id',
        ]);

        $member = $request->user()->member;

        if (!$member) {
            return back()->with('error', 'No member profile found.');
        }

        $session = AttendanceSession::findOrFail($validated['session_id']);

        if (!$session->isQrValid()) {
            return back()->with('error', 'This session QR is no longer valid.');
        }

        // Prevent duplicates
        $exists = AttendanceRecord::where('session_id', $session->id)
            ->where('member_id', $member->id)
            ->exists();

        if ($exists) {
            return redirect()->route('member.attendance.scan')
                ->with('info', 'Attendance already recorded for this session.');
        }

        AttendanceRecord::create([
            'session_id'       => $session->id,
            'member_id'        => $member->id,
            'check_in_time'    => now(),
            'attendance_method' => 'qr_code',
            'is_late'          => $session->isLateCheckIn(),
            'marked_by'        => $request->user()->id,
        ]);

        // Update session member count
        $session->update([
            'total_members'    => $session->records()->whereNotNull('member_id')->count(),
            'total_attendance' => $session->records()->count(),
        ]);

        return redirect()->route('member.attendance.scan')
            ->with('success', 'Attendance recorded for: ' . $session->service_title . '.');
    }
}
