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
use App\Helpers\SettingHelper;

class MinistryDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $ministry = $this->getUserMinistry($user);

        if (!$ministry) {
            return view('admin.roles.ministry.no-ministry');
        }

        $ministryMembers = $this->getMinistryMembers($ministry->id);
        $memberIds = $ministryMembers->pluck('id');

        $memberStats = [
            'total'   => $ministryMembers->count(),
            'male'    => $ministryMembers->where('gender', 'male')->count(),
            'female'  => $ministryMembers->where('gender', 'female')->count(),
            'active'  => $ministryMembers->where('membership_status', 'active')->count(),
        ];

        $attendanceStats = $this->getAttendanceStats($memberIds);
        $avgAttendanceRate = collect($attendanceStats)->avg('rate') ?? 0;

        $upcomingBirthdays = $this->getUpcomingBirthdays($ministryMembers);
        $absentMembers     = $this->getAbsentMembers($memberIds);

        $recentSessions = AttendanceSession::with('serviceType')
            ->orderBy('service_date', 'desc')
            ->take(5)
            ->get();

        $recentSms = SmsMessage::where('created_by', $user->id)
            ->latest()
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

    private function getUserMinistry($user)
    {
        if ($user->ministry_id) {
            return Ministry::find($user->ministry_id);
        }

        if ($user->member_id) {
            $ministryId = DB::table('member_ministry')
                ->where('member_id', $user->member_id)
                ->where('is_active', 1)
                ->orderByRaw("role = 'leader' DESC")
                ->value('ministry_id');

            if ($ministryId) {
                return Ministry::find($ministryId);
            }
        }

        return null;
    }

    private function getMinistryMembers($ministryId)
    {
        return Member::whereHas('ministries', function ($q) use ($ministryId) {
            $q->where('ministries.id', $ministryId);
        })->get();
    }

    private function getAttendanceStats($memberIds)
    {
        if ($memberIds->isEmpty()) return [];

        $sessions = AttendanceSession::where('service_date', '>=', now()->subWeeks(4))
            ->orderBy('service_date', 'desc')
            ->take(8)
            ->get();

        $stats = [];

        foreach ($sessions as $session) {

            $presentCount = AttendanceRecord::where('session_id', $session->id)
                ->whereIn('member_id', $memberIds)
                ->whereNotNull('check_in_time')
                ->count();

            $totalMembers = $memberIds->count();

            $stats[] = [
                'date'    => Carbon::parse($session->service_date)->format('M d'),
                'service' => optional($session->serviceType)->name ?? 'Service',
                'present' => $presentCount,
                'total'   => $totalMembers,
                'rate'    => $totalMembers > 0
                    ? round(($presentCount / $totalMembers) * 100, 1)
                    : 0,
            ];
        }

        return $stats;
    }

    private function getUpcomingBirthdays($members)
    {
        return $members->filter(function ($member) {

            if (!$member->date_of_birth) return false;

            $birthday = Carbon::parse($member->date_of_birth)
                ->setYear(now()->year);

            if ($birthday->isPast() && !$birthday->isToday()) {
                $birthday->addYear();
            }

            return $birthday->between(now(), now()->addDays(30));

        })->sortBy(function ($member) {

            $birthday = Carbon::parse($member->date_of_birth)
                ->setYear(now()->year);

            if ($birthday->isPast() && !$birthday->isToday()) {
                $birthday->addYear();
            }

            return $birthday;

        })->take(5)->values();
    }

    private function getAbsentMembers($memberIds)
    {
        if ($memberIds->isEmpty()) return collect();

        $recentSessions = AttendanceSession::where('service_date', '>=', now()->subMonth())
            ->pluck('id');

        $totalSessions = $recentSessions->count();

        if ($totalSessions === 0) return collect();

        $attendanceCounts = AttendanceRecord::whereIn('session_id', $recentSessions)
            ->whereIn('member_id', $memberIds)
            ->select('member_id', DB::raw('COUNT(*) as attended'))
            ->groupBy('member_id')
            ->pluck('attended', 'member_id');

        $absentMembers = [];

        foreach ($memberIds as $memberId) {

            $attended = $attendanceCounts[$memberId] ?? 0;
            $absences = $totalSessions - $attended;

            if ($absences >= 2) {
                $member = Member::find($memberId);

                if ($member) {
                    $absentMembers[] = [
                        'member'   => $member,
                        'absences' => $absences,
                    ];
                }
            }
        }

        return collect($absentMembers)
            ->sortByDesc('absences')
            ->take(5)
            ->values();
    }

    public function members()
    {
        $ministry = $this->getUserMinistry(auth()->user());

        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')
                ->with('error', 'No ministry assigned.');
        }

        $members = Member::whereHas('ministries', function ($q) use ($ministry) {
            $q->where('ministries.id', $ministry->id);
        })->orderBy('last_name')
          ->paginate(SettingHelper::perPage());

        return view('admin.roles.ministry.members', compact('ministry', 'members'));
    }

    public function attendance()
    {
        $ministry = $this->getUserMinistry(auth()->user());

        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')
                ->with('error', 'No ministry assigned.');
        }

        $members  = $this->getMinistryMembers($ministry->id);
        $sessions = AttendanceSession::orderBy('service_date', 'desc')
            ->take(10)
            ->get();

        return view('admin.roles.ministry.attendance', compact('ministry', 'members', 'sessions'));
    }

    public function saveAttendance(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:attendance_sessions,id',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late',
        ]);

        foreach ($validated['attendance'] as $memberId => $status) {

            if ($status === 'absent') {
                AttendanceRecord::where([
                    'session_id' => $validated['session_id'],
                    'member_id'  => $memberId,
                ])->delete();

                continue;
            }

            AttendanceRecord::updateOrCreate(
                [
                    'session_id' => $validated['session_id'],
                    'member_id'  => $memberId,
                ],
                [
                    'check_in_time'     => now(),
                    'is_late'           => $status === 'late' ? 1 : 0,
                    'attendance_method' => 'manual',
                    'marked_by'         => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Attendance updated successfully.');
    }

    public function composeSms()
    {
        $ministry = $this->getUserMinistry(auth()->user());

        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')
                ->with('error', 'No ministry assigned.');
        }

        $members = $this->getMinistryMembers($ministry->id)
            ->whereNotNull('phone_primary');

        return view('admin.roles.ministry.sms', compact('ministry', 'members'));
    }

    public function sendSms(Request $request)
    {
        $ministry = $this->getUserMinistry(auth()->user());

        if (!$ministry) {
            return back()->with('error', 'No ministry assigned.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:160',
            'recipient_ids' => 'nullable|array',
            'recipient_ids.*' => 'exists:members,id',
        ]);

        $recipients = !empty($validated['recipient_ids'])
            ? Member::whereIn('id', $validated['recipient_ids'])
                ->whereNotNull('phone_primary')
                ->get()
            : $this->getMinistryMembers($ministry->id)
                ->whereNotNull('phone_primary');

        SmsMessage::create([
            'message'         => $validated['message'],
            'recipient_type'  => 'ministry',
            'recipient_count' => $recipients->count(),
            'status'          => 'sent',
            'created_by'      => auth()->id(),
            'sent_at'         => now(),
        ]);

        return back()->with('success', 'SMS sent to '.$recipients->count().' members.');
    }
}