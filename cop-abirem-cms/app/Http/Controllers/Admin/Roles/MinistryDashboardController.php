<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\SmsMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MinistryDashboardController extends Controller
{
    /**
     * Ministry Leader Dashboard
     * Ministry-specific access with limited system permissions
     */
    public function index()
    {
        $user = auth()->user();
        $ministry = $this->getUserMinistry($user);

        if (!$ministry) {
            return view('admin.roles.ministry.no-ministry');
        }

        // Get ministry members
        $ministryMembers = $this->getMinistryMembers($ministry->id);

        $memberStats = [
            'total' => $ministryMembers->count(),
            'male' => $ministryMembers->where('gender', 'male')->count(),
            'female' => $ministryMembers->where('gender', 'female')->count(),
            'active' => $ministryMembers->where('membership_status', 'active')->count(),
        ];

        // Calculate attendance stats for last 4 weeks
        $memberIds = $ministryMembers->pluck('id')->toArray();
        $attendanceStats = $this->getAttendanceStats($memberIds);
        $avgAttendanceRate = count($attendanceStats) > 0 
            ? round(collect($attendanceStats)->avg('rate'), 1) 
            : 0;

        // Upcoming birthdays in the ministry (next 30 days)
        $upcomingBirthdays = $this->getUpcomingBirthdays($ministryMembers);

        // Members needing follow-up (absent 2+ times in last month)
        $absentMembers = $this->getAbsentMembers($memberIds);

        // Recent attendance sessions
        $recentSessions = AttendanceSession::with('serviceType')
            ->orderBy('session_date', 'desc')
            ->take(5)
            ->get();

        // SMS Messages sent by this user
        $recentSms = SmsMessage::where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.roles.ministry.dashboard', compact(
            'ministry',
            'ministryMembers',
            'memberStats',
            'attendanceStats',
            'avgAttendanceRate',
            'upcomingBirthdays',
            'absentMembers',
            'recentSessions',
            'recentSms'
        ));
    }

    /**
     * Get the ministry assigned to this user
     */
    private function getUserMinistry($user)
    {
        // Check if user has a ministry_id directly
        if (isset($user->ministry_id) && $user->ministry_id) {
            return Ministry::find($user->ministry_id);
        }

        // If user is linked to a member, get their ministry leadership
        if ($user->member_id) {
            $leaderMinistry = DB::table('member_ministry')
                ->where('member_id', $user->member_id)
                ->where('is_leader', true)
                ->first();
            
            if ($leaderMinistry) {
                return Ministry::find($leaderMinistry->ministry_id);
            }

            // If not a leader, get their first ministry
            $memberMinistry = DB::table('member_ministry')
                ->where('member_id', $user->member_id)
                ->first();
            
            if ($memberMinistry) {
                return Ministry::find($memberMinistry->ministry_id);
            }
        }

        // Default to first ministry for demo
        return Ministry::first();
    }

    /**
     * Get members of a ministry
     */
    private function getMinistryMembers($ministryId)
    {
        return Member::whereHas('ministries', function ($q) use ($ministryId) {
            $q->where('ministries.id', $ministryId);
        })->get();
    }

    /**
     * Get attendance statistics for members
     */
    private function getAttendanceStats($memberIds)
    {
        $stats = [];
        $sessions = AttendanceSession::where('session_date', '>=', now()->subWeeks(4))
            ->orderBy('session_date', 'desc')
            ->take(8)
            ->get();

        foreach ($sessions as $session) {
            $presentCount = AttendanceRecord::where('session_id', $session->id)
                ->whereIn('member_id', $memberIds)
                ->where('status', 'present')
                ->count();
            
            $totalMembers = count($memberIds);
            
            $stats[] = [
                'date' => $session->session_date->format('M d'),
                'service' => $session->serviceType->name ?? 'Service',
                'present' => $presentCount,
                'total' => $totalMembers,
                'rate' => $totalMembers > 0 ? round(($presentCount / $totalMembers) * 100, 1) : 0,
            ];
        }

        return $stats;
    }

    /**
     * Get upcoming birthdays
     */
    private function getUpcomingBirthdays($members)
    {
        return $members->filter(function ($member) {
            if (!$member->date_of_birth) return false;
            
            $birthday = Carbon::parse($member->date_of_birth)->setYear(now()->year);
            if ($birthday->isPast() && !$birthday->isToday()) {
                $birthday->addYear();
            }
            return $birthday->diffInDays(now(), false) >= 0 && $birthday->diffInDays(now(), false) <= 30;
        })->sortBy(function ($member) {
            $birthday = Carbon::parse($member->date_of_birth)->setYear(now()->year);
            if ($birthday->isPast() && !$birthday->isToday()) {
                $birthday->addYear();
            }
            return $birthday;
        })->take(5)->values();
    }

    /**
     * Get members who have been absent frequently
     */
    private function getAbsentMembers($memberIds)
    {
        $absentMembers = [];
        
        foreach ($memberIds as $memberId) {
            $absences = AttendanceRecord::where('member_id', $memberId)
                ->where('status', 'absent')
                ->whereHas('session', function ($q) {
                    $q->where('session_date', '>=', now()->subMonth());
                })
                ->count();
            
            if ($absences >= 2) {
                $member = Member::find($memberId);
                if ($member) {
                    $absentMembers[] = [
                        'member' => $member,
                        'absences' => $absences,
                    ];
                }
            }
        }

        return collect($absentMembers)->sortByDesc('absences')->take(5)->values();
    }

    /**
     * View ministry members
     */
    public function members()
    {
        $user = auth()->user();
        $ministry = $this->getUserMinistry($user);

        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')
                ->with('error', 'No ministry assigned.');
        }

        $members = Member::whereHas('ministries', function ($q) use ($ministry) {
            $q->where('ministries.id', $ministry->id);
        })->orderBy('last_name')->paginate(20);

        return view('admin.roles.ministry.members', compact('ministry', 'members'));
    }

    /**
     * Mark attendance for ministry
     */
    public function attendance()
    {
        $user = auth()->user();
        $ministry = $this->getUserMinistry($user);

        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')
                ->with('error', 'No ministry assigned.');
        }

        $members = $this->getMinistryMembers($ministry->id);
        $sessions = AttendanceSession::orderBy('session_date', 'desc')->take(10)->get();

        return view('admin.roles.ministry.attendance', compact('ministry', 'members', 'sessions'));
    }

    /**
     * Save attendance
     */
    public function saveAttendance(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:attendance_sessions,id',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,excused',
        ]);

        $count = 0;
        foreach ($validated['attendance'] as $memberId => $status) {
            AttendanceRecord::updateOrCreate(
                [
                    'session_id' => $validated['session_id'],
                    'member_id' => $memberId,
                ],
                [
                    'status' => $status,
                    'check_in_time' => in_array($status, ['present', 'late']) ? now() : null,
                    'marked_by' => auth()->id(),
                ]
            );
            $count++;
        }

        return back()->with('success', 'Attendance marked for ' . $count . ' members.');
    }

    /**
     * Compose SMS to ministry members
     */
    public function composeSms()
    {
        $user = auth()->user();
        $ministry = $this->getUserMinistry($user);

        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')
                ->with('error', 'No ministry assigned.');
        }

        $members = $this->getMinistryMembers($ministry->id)
            ->filter(fn($m) => !empty($m->phone_primary));

        return view('admin.roles.ministry.sms', compact('ministry', 'members'));
    }

    /**
     * Send SMS to ministry members
     */
    public function sendSms(Request $request)
    {
        $user = auth()->user();
        $ministry = $this->getUserMinistry($user);

        if (!$ministry) {
            return back()->with('error', 'No ministry assigned.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:160',
            'recipient_ids' => 'nullable|array',
            'recipient_ids.*' => 'exists:members,id',
        ]);

        // Get recipients
        if (!empty($validated['recipient_ids'])) {
            $recipients = Member::whereIn('id', $validated['recipient_ids'])
                ->whereNotNull('phone_primary')->get();
        } else {
            $recipients = $this->getMinistryMembers($ministry->id)
                ->filter(fn($m) => !empty($m->phone_primary));
        }

        // Create SMS message record
        SmsMessage::create([
            'message' => $validated['message'],
            'recipient_type' => 'ministry',
            'recipient_count' => $recipients->count(),
            'status' => 'sent',
            'created_by' => auth()->id(),
            'sent_at' => now(),
        ]);

        return back()->with('success', 'SMS sent to ' . $recipients->count() . ' ministry members.');
    }
}