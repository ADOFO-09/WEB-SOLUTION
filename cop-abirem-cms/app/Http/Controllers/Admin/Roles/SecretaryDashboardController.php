<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Visitor;
use App\Models\AttendanceSession;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use Carbon\Carbon;

class SecretaryDashboardController extends Controller
{
    /**
     * Local Secretary Dashboard
     * Member management, visitors, attendance, SMS, read-only finance.
     */
    public function index()
    {
        $currentMonth = Carbon::now();
        $currentYear  = Carbon::now()->year;

        // ── Member Statistics ──────────────────────────────────────────────
        $memberStats = [
            'total'          => Member::count(),
            'active'         => Member::where('membership_status', 'active')->count(),
            'inactive'       => Member::where('membership_status', 'inactive')->count(),
            'new_this_month' => Member::whereMonth('created_at', $currentMonth->month)
                                    ->whereYear('created_at', $currentYear)->count(),
        ];

        // Data quality: members missing key fields
        $memberAlerts = [
            'missing_phone'   => Member::whereNull('phone_primary')->orWhere('phone_primary', '')->count(),
            'missing_email'   => Member::whereNull('email')->orWhere('email', '')->count(),
            'missing_dob'     => Member::whereNull('date_of_birth')->count(),
        ];

        // ── Visitor Statistics ─────────────────────────────────────────────
        $visitorStats = [
            'total'           => Visitor::count(),
            'this_month'      => Visitor::whereMonth('first_visit_date', $currentMonth->month)
                                    ->whereYear('first_visit_date', $currentYear)->count(),
            'pending_followup'=> Visitor::where('follow_up_status', 'pending')->count(),
            'converted'       => Visitor::where('follow_up_status', 'converted')->count(),
        ];

        $recentVisitors = Visitor::orderBy('first_visit_date', 'desc')->take(5)->get();

        // ── Financial Summary (read-only) ──────────────────────────────────
        $financeStats = [
            'tithes'    => Tithe::whereMonth('payment_date', $currentMonth->month)
                            ->whereYear('payment_date', $currentYear)->sum('amount') ?? 0,
            'offerings' => Offering::whereMonth('payment_date', $currentMonth->month)
                            ->whereYear('payment_date', $currentYear)->sum('amount') ?? 0,
            'donations' => Donation::whereMonth('payment_date', $currentMonth->month)
                            ->whereYear('payment_date', $currentYear)->sum('amount') ?? 0,
        ];
        $financeStats['total_income'] = $financeStats['tithes']
            + $financeStats['offerings']
            + $financeStats['donations'];

        // ── Attendance Overview ────────────────────────────────────────────
        $recentAttendance = AttendanceSession::with('serviceType')
            ->orderBy('service_date', 'desc')
            ->take(6)
            ->get();

        $avgAttendance = AttendanceSession::where('service_date', '>=', now()->subWeeks(4))
            ->avg('total_attendance') ?? 0;

        // ── Upcoming Birthdays (next 30 days) ─────────────────────────────
        $today    = now();
        $in30Days = now()->addDays(30);

        // Pull active members with a DOB and filter in PHP to handle year-boundary correctly
        $upcomingBirthdays = Member::where('membership_status', 'active')
            ->whereNotNull('date_of_birth')
            ->get()
            ->filter(function ($member) use ($today, $in30Days) {
                $dob  = $member->date_of_birth;
                // Set DOB to current year for comparison
                $thisYear = $dob->copy()->year($today->year);
                if ($thisYear->lt($today->startOfDay())) {
                    $thisYear->addYear();
                }
                return $thisYear->between($today->copy()->startOfDay(), $in30Days->copy()->endOfDay());
            })
            ->sortBy(function ($member) use ($today) {
                $dob  = $member->date_of_birth;
                $next = $dob->copy()->year($today->year);
                if ($next->lt($today->startOfDay())) {
                    $next->addYear();
                }
                return $next->timestamp;
            })
            ->take(8)
            ->values();

        return view('admin.roles.secretary.dashboard', compact(
            'memberStats',
            'memberAlerts',
            'visitorStats',
            'recentVisitors',
            'financeStats',
            'recentAttendance',
            'avgAttendance',
            'upcomingBirthdays'
        ));
    }
}
