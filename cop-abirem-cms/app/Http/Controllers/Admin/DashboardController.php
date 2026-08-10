<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RoleHelper;
use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use App\Models\Member;
use App\Models\Visitor;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Pledge;
use App\Models\Expense;
use App\Models\SmsMessage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'admin.access',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!RoleHelper::isSystemAdmin($user)) {
            $route = RoleHelper::getDashboardRoute($user);
            if ($route !== 'admin.dashboard') {
                return redirect()->route($route);
            }
        }

        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = $financialYears->firstWhere('is_active', true)
            ?? $financialYears->first();

        $defaultYear = $activeFinancialYear
            ? $activeFinancialYear->start_date->year
            : (int) date('Y');

        $period = $request->get('period', 'month');
        $year   = (int) $request->get('year', $defaultYear);
        $month  = (int) $request->get('month', date('m'));

        $selectedFY = $financialYears->first(
            fn($fy) => $fy->start_date->year === $year
        );

        $startDate = match($period) {
            'week'    => Carbon::now()->startOfWeek(),
            'month'   => Carbon::create($year, $month, 1)->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year'    => $selectedFY
                            ? $selectedFY->start_date->startOfDay()
                            : Carbon::create($year, 1, 1)->startOfYear(),
            default   => Carbon::create($year, $month, 1)->startOfMonth(),
        };
        $endDate = match($period) {
            'week'    => Carbon::now()->endOfWeek(),
            'month'   => Carbon::create($year, $month, 1)->endOfMonth(),
            'quarter' => Carbon::now()->endOfQuarter(),
            'year'    => $selectedFY
                            ? $selectedFY->end_date->endOfDay()
                            : Carbon::create($year, 12, 31)->endOfYear(),
            default   => Carbon::create($year, $month, 1)->endOfMonth(),
        };

        $memberStats = [
            'total'            => Member::count(),
            'active'           => Member::where('membership_status', 'active')->count(),
            'new_this_period'  => Member::whereBetween('created_at', [$startDate, $endDate])->count(),
            'by_gender'        => Member::selectRaw('gender, COUNT(*) as count')
                                    ->groupBy('gender')
                                    ->pluck('count', 'gender')
                                    ->toArray(),
        ];

        $visitorStats = [
            'total'            => Visitor::count(),
            'new_this_period'  => Visitor::whereBetween('first_visit_date', [$startDate, $endDate])->count(),
            'converted'        => Visitor::where('follow_up_status', 'converted')->count(),
            'pending_followup' => Visitor::where('follow_up_status', 'pending')->count(),
        ];

        $attendanceStats = [
            'sessions_this_period' => AttendanceSession::whereBetween('service_date', [$startDate, $endDate])->count(),
            'total_attendance'     => AttendanceRecord::whereHas('session', fn($q) =>
                $q->whereBetween('service_date', [$startDate, $endDate])
            )->count(),
            'average_attendance'   => $this->getAverageAttendance($startDate, $endDate),
            'last_sunday'          => $this->getLastSundayAttendance(),
        ];

        $financeStats = [
            'tithes'        => Tithe::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'offerings'     => Offering::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'donations'     => Donation::where('donation_type', 'cash')
                                ->whereBetween('payment_date', [$startDate, $endDate])
                                ->sum('amount'),
            'total_income'  => 0,
            'expenses_paid' => Expense::where('status', 'paid')
                                ->whereBetween('expense_date', [$startDate, $endDate])
                                ->sum('amount'),
            'pledges_active' => Pledge::where('status', 'active')
                                ->selectRaw('SUM(total_amount - amount_paid) as remaining')
                                ->value('remaining') ?? 0,
        ];
        $financeStats['total_income'] = $financeStats['tithes'] + $financeStats['offerings'] + $financeStats['donations'];
        $financeStats['net_income']   = $financeStats['total_income'] - $financeStats['expenses_paid'];

        $recentMembers   = Member::latest()->take(5)->get();
        $recentVisitors  = Visitor::latest()->take(5)->get();
        $pendingExpenses = Expense::where('status', 'pending')->latest()->take(5)->get();

        $charts = [
            'attendance_trend' => $this->getAttendanceTrend($year),
            'income_breakdown' => [
                'Tithes'    => $financeStats['tithes'],
                'Offerings' => $financeStats['offerings'],
                'Donations' => $financeStats['donations'],
            ],
            'monthly_income'   => $this->getMonthlyIncome($year),
        ];

        $quickStats = [
            ['label' => 'Total Members',          'value' => number_format($memberStats['total']),                                                    'icon' => 'users',    'color' => 'blue'],
            ['label' => 'This Period Attendance',  'value' => number_format($attendanceStats['total_attendance']),                                     'icon' => 'calendar', 'color' => 'green'],
            ['label' => 'Total Income',            'value' => SettingHelper::currencySymbol() . ' ' . number_format($financeStats['total_income'], 2), 'icon' => 'currency', 'color' => 'emerald'],
            ['label' => 'Pending Expenses',        'value' => Expense::pending()->count(),                                                             'icon' => 'receipt',  'color' => 'yellow'],
        ];

        return view('admin.dashboard.index', compact(
            'memberStats', 'visitorStats', 'attendanceStats', 'financeStats',
            'recentMembers', 'recentVisitors', 'pendingExpenses',
            'charts', 'quickStats', 'period', 'year', 'month',
            'financialYears'
        ));
    }

    public function finance(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));

        // Replace 12 × 4 = 48 queries with 4 GROUP BY queries
        $rawTithes    = Tithe::whereYear('payment_date', $year)
            ->selectRaw('MONTH(payment_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawOfferings = Offering::whereYear('payment_date', $year)
            ->selectRaw('MONTH(payment_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawDonations = Donation::where('donation_type', 'cash')
            ->whereYear('payment_date', $year)
            ->selectRaw('MONTH(payment_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawExpenses  = Expense::where('status', 'paid')
            ->whereYear('expense_date', $year)
            ->selectRaw('MONTH(expense_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'month'     => Carbon::create($year, $m, 1)->format('M'),
                'tithes'    => (float) ($rawTithes[$m]    ?? 0),
                'offerings' => (float) ($rawOfferings[$m] ?? 0),
                'donations' => (float) ($rawDonations[$m] ?? 0),
                'expenses'  => (float) ($rawExpenses[$m]  ?? 0),
            ];
        }

        $topTithers = Tithe::selectRaw('member_id, SUM(amount) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('member_id')
            ->orderByDesc('total')
            ->with('member')
            ->take(10)
            ->get();

        $expensesByCategory = Expense::selectRaw('expense_category_id, SUM(amount) as total')
            ->where('status', 'paid')
            ->whereYear('expense_date', $year)
            ->groupBy('expense_category_id')
            ->with('expenseCategory')
            ->orderByDesc('total')
            ->get();

        $yearTotals = [
            'tithes'    => array_sum($rawTithes),
            'offerings' => array_sum($rawOfferings),
            'donations' => array_sum($rawDonations),
            'expenses'  => array_sum($rawExpenses),
        ];
        $yearTotals['total_income'] = $yearTotals['tithes'] + $yearTotals['offerings'] + $yearTotals['donations'];
        $yearTotals['net']          = $yearTotals['total_income'] - $yearTotals['expenses'];

        return view('admin.dashboard.finance', compact(
            'monthlyData', 'topTithers', 'expensesByCategory', 'yearTotals', 'year'
        ));
    }

    public function attendance(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));

        $sessionsByType = AttendanceSession::selectRaw('service_type_id, COUNT(*) as sessions, SUM(total_members + total_visitors) as total_attendance')
            ->whereYear('service_date', $year)
            ->groupBy('service_type_id')
            ->with('serviceType')
            ->get();

        // Replace 12 × 3 = 36 queries with 1 GROUP BY query
        $rawAttendance = AttendanceSession::whereYear('service_date', $year)
            ->selectRaw('MONTH(service_date) as m, COUNT(*) as sessions, SUM(total_members) as members, SUM(total_visitors) as visitors')
            ->groupBy('m')
            ->get()->keyBy('m');

        $monthlyAttendance = [];
        for ($m = 1; $m <= 12; $m++) {
            $row = $rawAttendance[$m] ?? null;
            $monthlyAttendance[] = [
                'month'    => Carbon::create($year, $m, 1)->format('M'),
                'sessions' => $row ? (int) $row->sessions  : 0,
                'members'  => $row ? (int) $row->members   : 0,
                'visitors' => $row ? (int) $row->visitors  : 0,
            ];
        }

        $recentSessions = AttendanceSession::with('serviceType')
            ->orderByDesc('service_date')
            ->take(10)
            ->get();

        return view('admin.dashboard.attendance', compact(
            'sessionsByType', 'monthlyAttendance', 'recentSessions', 'year'
        ));
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    private function getAverageAttendance($startDate, $endDate): int
    {
        // Single SQL AVG instead of loading all session rows into PHP
        $avg = AttendanceSession::whereBetween('service_date', [$startDate, $endDate])
            ->selectRaw('AVG(total_members + total_visitors) as avg_attendance')
            ->value('avg_attendance');

        return (int) round($avg ?? 0);
    }

    private function getLastSundayAttendance(): ?array
    {
        $lastSunday = Carbon::now()->previous(Carbon::SUNDAY);
        $session    = AttendanceSession::whereDate('service_date', $lastSunday)->first();

        if (!$session) return null;

        return [
            'date'     => $lastSunday->format('M d, Y'),
            'members'  => $session->total_members,
            'visitors' => $session->total_visitors,
            'total'    => $session->total_members + $session->total_visitors,
        ];
    }

    private function getAttendanceTrend(int $year): array
    {
        // Replace 12 queries with 1 GROUP BY query
        $raw = AttendanceSession::whereYear('service_date', $year)
            ->selectRaw('MONTH(service_date) as m, SUM(total_members + total_visitors) as total')
            ->groupBy('m')
            ->pluck('total', 'm')->toArray();

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = [
                'month'      => Carbon::create($year, $m, 1)->format('M'),
                'attendance' => (int) ($raw[$m] ?? 0),
            ];
        }
        return $data;
    }

    private function getMonthlyIncome(int $year): array
    {
        // Replace 12 × 3 = 36 queries with 3 GROUP BY queries
        $rawTithes    = Tithe::whereYear('payment_date', $year)
            ->selectRaw('MONTH(payment_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawOfferings = Offering::whereYear('payment_date', $year)
            ->selectRaw('MONTH(payment_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawDonations = Donation::where('donation_type', 'cash')
            ->whereYear('payment_date', $year)
            ->selectRaw('MONTH(payment_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'total' => (float) ($rawTithes[$m] ?? 0)
                         + (float) ($rawOfferings[$m] ?? 0)
                         + (float) ($rawDonations[$m] ?? 0),
            ];
        }
        return $data;
    }
}
