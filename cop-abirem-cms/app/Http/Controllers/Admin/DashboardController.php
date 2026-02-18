<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            'admin.access',
        ];
    }

    /**
     * Display the main dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Date ranges
        $startDate = match($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::create($year, $month, 1)->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::create($year, 1, 1)->startOfYear(),
            default => Carbon::create($year, $month, 1)->startOfMonth(),
        };
        $endDate = match($period) {
            'week' => Carbon::now()->endOfWeek(),
            'month' => Carbon::create($year, $month, 1)->endOfMonth(),
            'quarter' => Carbon::now()->endOfQuarter(),
            'year' => Carbon::create($year, 12, 31)->endOfYear(),
            default => Carbon::create($year, $month, 1)->endOfMonth(),
        };

        // Member Statistics
        $memberStats = [
            'total' => Member::count(),
            'active' => Member::where('membership_status', 'active')->count(),
            'new_this_period' => Member::whereBetween('created_at', [$startDate, $endDate])->count(),
            'by_gender' => Member::selectRaw('gender, COUNT(*) as count')
                ->groupBy('gender')
                ->pluck('count', 'gender')
                ->toArray(),
        ];

        // Visitor Statistics
        $visitorStats = [
            'total' => Visitor::count(),
            'new_this_period' => Visitor::whereBetween('first_visit_date', [$startDate, $endDate])->count(),
            'converted' => Visitor::where('follow_up_status', 'converted')->count(),
            'pending_followup' => Visitor::where('follow_up_status', 'pending')->count(),
        ];

        // Attendance Statistics
        $attendanceStats = [
            'sessions_this_period' => AttendanceSession::whereBetween('service_date', [$startDate, $endDate])->count(),
            'total_attendance' => AttendanceRecord::whereHas('session', fn($q) => 
                $q->whereBetween('service_date', [$startDate, $endDate])
            )->count(),
            'average_attendance' => $this->getAverageAttendance($startDate, $endDate),
            'last_sunday' => $this->getLastSundayAttendance(),
        ];

        // Financial Statistics
        $financeStats = [
            'tithes' => Tithe::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'offerings' => Offering::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'donations' => Donation::where('donation_type', 'cash')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'total_income' => 0, // Will calculate
            'expenses_paid' => Expense::where('status', 'paid')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount'),
            'pledges_active' => Pledge::where('status', 'active')
                ->selectRaw('SUM(total_amount - amount_paid) as remaining')
                ->value('remaining') ?? 0,
        ];
        $financeStats['total_income'] = $financeStats['tithes'] + $financeStats['offerings'] + $financeStats['donations'];
        $financeStats['net_income'] = $financeStats['total_income'] - $financeStats['expenses_paid'];

        // Recent Activity
        $recentMembers = Member::latest()->take(5)->get();
        $recentVisitors = Visitor::latest()->take(5)->get();
        $pendingExpenses = Expense::where('status', 'pending')->latest()->take(5)->get();

        // Charts Data
        $charts = [
            'attendance_trend' => $this->getAttendanceTrend($year),
            'income_breakdown' => [
                'Tithes' => $financeStats['tithes'],
                'Offerings' => $financeStats['offerings'],
                'Donations' => $financeStats['donations'],
            ],
            'monthly_income' => $this->getMonthlyIncome($year),
        ];

        // Quick Stats for Cards
        $quickStats = [
            ['label' => 'Total Members', 'value' => number_format($memberStats['total']), 'icon' => 'users', 'color' => 'blue'],
            ['label' => 'This Period Attendance', 'value' => number_format($attendanceStats['total_attendance']), 'icon' => 'calendar', 'color' => 'green'],
            ['label' => 'Total Income', 'value' => 'GH₵ ' . number_format($financeStats['total_income'], 2), 'icon' => 'currency', 'color' => 'emerald'],
            ['label' => 'Pending Expenses', 'value' => Expense::pending()->count(), 'icon' => 'receipt', 'color' => 'yellow'],
        ];

        return view('admin.dashboard.index', compact(
            'memberStats', 'visitorStats', 'attendanceStats', 'financeStats',
            'recentMembers', 'recentVisitors', 'pendingExpenses',
            'charts', 'quickStats', 'period', 'year', 'month'
        ));
    }

    /**
     * Get finance dashboard with detailed breakdowns.
     */
    public function finance(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Monthly breakdown
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create($year, $m, 1)->startOfMonth();
            $endDate = Carbon::create($year, $m, 1)->endOfMonth();
            
            $monthlyData[] = [
                'month' => $startDate->format('M'),
                'tithes' => Tithe::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
                'offerings' => Offering::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
                'donations' => Donation::where('donation_type', 'cash')->whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
                'expenses' => Expense::where('status', 'paid')->whereBetween('expense_date', [$startDate, $endDate])->sum('amount'),
            ];
        }

        // Top tithe contributors
        $topTithers = Tithe::selectRaw('member_id, SUM(amount) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('member_id')
            ->orderByDesc('total')
            ->with('member')
            ->take(10)
            ->get();

        // Expense by category
        $expensesByCategory = Expense::selectRaw('expense_category_id, SUM(amount) as total')
            ->where('status', 'paid')
            ->whereYear('expense_date', $year)
            ->groupBy('expense_category_id')
            ->with('expenseCategory')
            ->orderByDesc('total')
            ->get();

        // Year totals
        $yearTotals = [
            'tithes' => Tithe::whereYear('payment_date', $year)->sum('amount'),
            'offerings' => Offering::whereYear('payment_date', $year)->sum('amount'),
            'donations' => Donation::where('donation_type', 'cash')->whereYear('payment_date', $year)->sum('amount'),
            'expenses' => Expense::where('status', 'paid')->whereYear('expense_date', $year)->sum('amount'),
        ];
        $yearTotals['total_income'] = $yearTotals['tithes'] + $yearTotals['offerings'] + $yearTotals['donations'];
        $yearTotals['net'] = $yearTotals['total_income'] - $yearTotals['expenses'];

        return view('admin.dashboard.finance', compact(
            'monthlyData', 'topTithers', 'expensesByCategory', 'yearTotals', 'year'
        ));
    }

    /**
     * Get attendance dashboard.
     */
    public function attendance(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Sessions by service type
        $sessionsByType = AttendanceSession::selectRaw('service_type_id, COUNT(*) as sessions, SUM(total_members + total_visitors) as total_attendance')
            ->whereYear('service_date', $year)
            ->groupBy('service_type_id')
            ->with('serviceType')
            ->get();

        // Monthly attendance trend
        $monthlyAttendance = [];
        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create($year, $m, 1)->startOfMonth();
            $endDate = Carbon::create($year, $m, 1)->endOfMonth();
            
            $sessions = AttendanceSession::whereBetween('service_date', [$startDate, $endDate]);
            $monthlyAttendance[] = [
                'month' => $startDate->format('M'),
                'sessions' => $sessions->count(),
                'members' => (clone $sessions)->sum('total_members'),
                'visitors' => (clone $sessions)->sum('total_visitors'),
            ];
        }

        // Recent sessions
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
        $sessions = AttendanceSession::whereBetween('service_date', [$startDate, $endDate])->get();
        if ($sessions->isEmpty()) return 0;
        return (int) round($sessions->avg(fn($s) => $s->total_members + $s->total_visitors));
    }

    private function getLastSundayAttendance(): ?array
    {
        $lastSunday = Carbon::now()->previous(Carbon::SUNDAY);
        $session = AttendanceSession::whereDate('service_date', $lastSunday)->first();
        
        if (!$session) return null;
        
        return [
            'date' => $lastSunday->format('M d, Y'),
            'members' => $session->total_members,
            'visitors' => $session->total_visitors,
            'total' => $session->total_members + $session->total_visitors,
        ];
    }

    private function getAttendanceTrend($year): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $total = AttendanceSession::whereYear('service_date', $year)
                ->whereMonth('service_date', $m)
                ->sum(\DB::raw('total_members + total_visitors'));
            $data[] = ['month' => Carbon::create($year, $m, 1)->format('M'), 'attendance' => $total];
        }
        return $data;
    }

    private function getMonthlyIncome($year): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create($year, $m, 1)->startOfMonth();
            $endDate = Carbon::create($year, $m, 1)->endOfMonth();
            
            $tithes = Tithe::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
            $offerings = Offering::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
            $donations = Donation::where('donation_type', 'cash')->whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
            
            $data[] = [
                'month' => $startDate->format('M'),
                'total' => $tithes + $offerings + $donations,
            ];
        }
        return $data;
    }
}
