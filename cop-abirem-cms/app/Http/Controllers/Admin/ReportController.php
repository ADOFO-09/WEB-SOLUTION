<?php

namespace App\Http\Controllers\Admin;

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
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Ministry;
use App\Models\ServiceType;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:reports.view'),
            new Middleware('permission:reports.financial', only: ['incomeStatement', 'expenseReport', 'titheReport']),
            new Middleware('permission:reports.membership', only: ['membershipReport', 'memberDirectory']),
            new Middleware('permission:reports.attendance', only: ['attendanceReport']),
        ];
    }

    /**
     * Reports index/hub.
     */
    public function index()
    {
        $user = auth()->user();

        $allCategories = [
            'financial' => [
                'permission' => 'reports.financial',
                'reports' => [
                    ['name' => 'Income Statement', 'route' => 'admin.reports.income-statement', 'description' => 'Comprehensive income and expense summary'],
                    ['name' => 'Tithe Report',     'route' => 'admin.reports.tithes',            'description' => 'Detailed tithe contributions by member'],
                    ['name' => 'Offering Report',  'route' => 'admin.reports.offerings',         'description' => 'Offering collections by category'],
                    ['name' => 'Expense Report',   'route' => 'admin.reports.expenses',          'description' => 'Expense breakdown by category'],
                    ['name' => 'Pledge Report',    'route' => 'admin.reports.pledges',           'description' => 'Active pledges and fulfillment status'],
                ],
            ],
            'membership' => [
                'permission' => 'reports.membership',
                'reports' => [
                    ['name' => 'Member Directory',      'route' => 'admin.reports.member-directory', 'description' => 'Complete member listing with contact info'],
                    ['name' => 'Membership Statistics', 'route' => 'admin.reports.membership',       'description' => 'Demographics and growth analysis'],
                    ['name' => 'Ministry Report',       'route' => 'admin.reports.ministries',       'description' => 'Ministry membership breakdown'],
                    ['name' => 'Birthday Report',       'route' => 'admin.reports.birthdays',        'description' => 'Upcoming member birthdays'],
                ],
            ],
            'attendance' => [
                'permission' => 'reports.attendance',
                'reports' => [
                    ['name' => 'Attendance Summary', 'route' => 'admin.reports.attendance', 'description' => 'Service attendance trends'],
                    ['name' => 'Visitor Report',     'route' => 'admin.reports.visitors',  'description' => 'Visitor tracking and conversion'],
                ],
            ],
        ];

        // Only include categories the user has permission to view
        $reportCategories = collect($allCategories)
            ->filter(fn($cat) => $user->hasPermission($cat['permission']))
            ->map(fn($cat) => $cat['reports'])
            ->all();

        return view('admin.reports.index', compact('reportCategories'));
    }

    /**
     * Income Statement Report.
     */
    public function incomeStatement(Request $request)
    {
        $year = (int) $request->get('year', $this->defaultYear());
        $month = $request->get('month');
        
        if ($month) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            $periodLabel = $startDate->format('F Y');
        } else {
            $startDate = Carbon::create($year, 1, 1)->startOfYear();
            $endDate = Carbon::create($year, 12, 31)->endOfYear();
            $periodLabel = "Year $year";
        }

        // Income
        $income = [
            'tithes' => Tithe::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'offerings' => Offering::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'donations' => Donation::where('donation_type', 'cash')->whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
        ];
        $income['total'] = array_sum($income);

        // Offerings by category
        $offeringsByCategory = Offering::selectRaw('income_category_id, SUM(amount) as total')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('income_category_id')
            ->with('incomeCategory')
            ->get();

        // Expenses by category
        $expensesByCategory = Expense::selectRaw('expense_category_id, SUM(amount) as total')
            ->where('status', 'paid')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('expense_category_id')
            ->with('expenseCategory')
            ->orderByDesc('total')
            ->get();

        $totalExpenses = $expensesByCategory->sum('total');
        $netIncome = $income['total'] - $totalExpenses;

        // Comparison with previous period
        if ($month) {
            $prevStart = Carbon::create($year, $month, 1)->subMonth()->startOfMonth();
            $prevEnd = Carbon::create($year, $month, 1)->subMonth()->endOfMonth();
        } else {
            $prevStart = Carbon::create($year - 1, 1, 1)->startOfYear();
            $prevEnd = Carbon::create($year - 1, 12, 31)->endOfYear();
        }
        
        $prevIncome = Tithe::whereBetween('payment_date', [$prevStart, $prevEnd])->sum('amount')
            + Offering::whereBetween('payment_date', [$prevStart, $prevEnd])->sum('amount')
            + Donation::where('donation_type', 'cash')->whereBetween('payment_date', [$prevStart, $prevEnd])->sum('amount');

        $comparison = [
            'previous_income' => $prevIncome,
            'change' => $prevIncome > 0 ? round((($income['total'] - $prevIncome) / $prevIncome) * 100, 1) : 0,
        ];

        return view('admin.reports.income-statement', compact(
            'income', 'offeringsByCategory', 'expensesByCategory', 
            'totalExpenses', 'netIncome', 'comparison',
            'periodLabel', 'year', 'month'
        ));
    }

    /**
     * Tithe Report.
     */
    public function titheReport(Request $request)
    {
        $year = (int) $request->get('year', $this->defaultYear());
        $month = $request->get('month');

        $query = Tithe::with('member')->whereYear('payment_date', $year);
        
        if ($month) {
            $query->whereMonth('payment_date', $month);
        }

        // By member summary
        $memberTithes = Tithe::selectRaw('member_id, SUM(amount) as total, COUNT(*) as payments')
            ->whereYear('payment_date', $year)
            ->when($month, fn($q) => $q->whereMonth('payment_date', $month))
            ->groupBy('member_id')
            ->with('member')
            ->orderByDesc('total')
            ->get();

        // Monthly breakdown
        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyBreakdown[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'amount' => Tithe::whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                'count' => Tithe::whereYear('payment_date', $year)->whereMonth('payment_date', $m)->distinct('member_id')->count('member_id'),
            ];
        }

        $totals = [
            'amount' => $memberTithes->sum('total'),
            'members' => $memberTithes->count(),
            'payments' => $memberTithes->sum('payments'),
        ];

        return view('admin.reports.tithes', compact(
            'memberTithes', 'monthlyBreakdown', 'totals', 'year', 'month'
        ));
    }

    /**
     * Expense Report.
     */
    public function expenseReport(Request $request)
    {
        $year = (int) $request->get('year', $this->defaultYear());
        
        // By category
        $byCategory = ExpenseCategory::withCount(['expenses as paid_count' => fn($q) => 
            $q->where('status', 'paid')->whereYear('expense_date', $year)
        ])->get()->map(function ($cat) use ($year) {
            $cat->total_spent = Expense::where('expense_category_id', $cat->id)
                ->where('status', 'paid')
                ->whereYear('expense_date', $year)
                ->sum('amount');
            $cat->budget_used = $cat->budget_amount > 0 
                ? round(($cat->total_spent / $cat->budget_amount) * 100, 1) 
                : 0;
            return $cat;
        })->sortByDesc('total_spent');

        // Monthly trend
        $monthlyTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyTrend[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'amount' => Expense::where('status', 'paid')
                    ->whereYear('expense_date', $year)
                    ->whereMonth('expense_date', $m)
                    ->sum('amount'),
            ];
        }

        $totals = [
            'budget' => $byCategory->sum('budget_amount'),
            'spent' => $byCategory->sum('total_spent'),
            'remaining' => $byCategory->sum('budget_amount') - $byCategory->sum('total_spent'),
        ];

        return view('admin.reports.expenses', compact(
            'byCategory', 'monthlyTrend', 'totals', 'year'
        ));
    }

    /**
     * Membership Statistics Report.
     */
    public function membershipReport(Request $request)
    {
        $year = (int) $request->get('year', $this->defaultYear());

        // Demographics
        $demographics = [
            'by_gender' => Member::where('membership_status', 'active')
                ->selectRaw('gender, COUNT(*) as count')
                ->groupBy('gender')
                ->pluck('count', 'gender'),
            'by_marital_status' => Member::where('membership_status', 'active')
                ->selectRaw('marital_status, COUNT(*) as count')
                ->groupBy('marital_status')
                ->pluck('count', 'marital_status'),
            'by_employment' => Member::where('membership_status', 'active')
                ->selectRaw('occupation, COUNT(*) as count')
                ->groupBy('occupation')
                ->pluck('count', 'occupation'),
        ];

        // Growth trend
        $growthTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create($year, $m, 1)->startOfMonth();
            $endDate = Carbon::create($year, $m, 1)->endOfMonth();
            
            $growthTrend[] = [
                'month' => $startDate->format('M'),
                'new_members' => Member::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_at_end' => Member::where('created_at', '<=', $endDate)->count(),
            ];
        }

        // Status breakdown
        $statusBreakdown = Member::selectRaw('membership_status, COUNT(*) as count')
            ->groupBy('membership_status')
            ->pluck('count', 'membership_status');

        $totals = [
            'total' => Member::count(),
            'active' => Member::where('membership_status', 'active')->count(),
            'new_this_year' => Member::whereYear('created_at', $year)->count(),
        ];

        return view('admin.reports.membership', compact(
            'demographics', 'growthTrend', 'statusBreakdown', 'totals', 'year'
        ));
    }

    /**
     * Member Directory (printable).
     */
    public function memberDirectory(Request $request)
    {
        $query = Member::where('membership_status', 'active')->orderBy('last_name')->orderBy('first_name');

        if ($request->filled('ministry_id')) {
            $query->whereHas('ministries', fn($q) => $q->where('ministries.id', $request->ministry_id));
        }

        $members = $query->get();
        $ministries = Ministry::active()->orderBy('name')->get();

        return view('admin.reports.member-directory', compact('members', 'ministries'));
    }

    /**
     * Attendance Report.
     */
    public function attendanceReport(Request $request)
    {
        $year = (int) $request->get('year', $this->defaultYear());
        $serviceTypeId = $request->get('service_type_id');

        $query = AttendanceSession::whereYear('service_date', $year);
        
        if ($serviceTypeId) {
            $query->where('service_type_id', $serviceTypeId);
        }

        // By service type
        $byServiceType = AttendanceSession::selectRaw('service_type_id, COUNT(*) as sessions, SUM(total_members) as members, SUM(total_visitors) as visitors')
            ->whereYear('service_date', $year)
            ->groupBy('service_type_id')
            ->with('serviceType')
            ->get();

        // Monthly trend
        $monthlyTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $sessions = AttendanceSession::whereYear('service_date', $year)
                ->whereMonth('service_date', $m)
                ->when($serviceTypeId, fn($q) => $q->where('service_type_id', $serviceTypeId));
            
            $monthlyTrend[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'sessions' => (clone $sessions)->count(),
                'members' => (clone $sessions)->sum('total_members'),
                'visitors' => (clone $sessions)->sum('total_visitors'),
                'average' => (clone $sessions)->count() > 0 
                    ? round(((clone $sessions)->sum('total_members') + (clone $sessions)->sum('total_visitors')) / (clone $sessions)->count())
                    : 0,
            ];
        }

        $serviceTypes = ServiceType::active()->orderBy('name')->get();

        $totals = [
            'sessions' => $byServiceType->sum('sessions'),
            'total_attendance' => $byServiceType->sum('members') + $byServiceType->sum('visitors'),
            'average' => $byServiceType->sum('sessions') > 0 
                ? round(($byServiceType->sum('members') + $byServiceType->sum('visitors')) / $byServiceType->sum('sessions'))
                : 0,
        ];

        return view('admin.reports.attendance', compact(
            'byServiceType', 'monthlyTrend', 'serviceTypes', 'totals', 'year', 'serviceTypeId'
        ));
    }

    /**
     * Visitor Report.
     */
    public function visitorReport(Request $request)
    {
        $year = (int) $request->get('year', $this->defaultYear());

        // Monthly visitors
        $monthlyVisitors = [];
        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create($year, $m, 1)->startOfMonth();
            $endDate = Carbon::create($year, $m, 1)->endOfMonth();
            
            $monthlyVisitors[] = [
                'month' => $startDate->format('M'),
                'new' => Visitor::whereBetween('first_visit_date', [$startDate, $endDate])->count(),
                'converted' => Visitor::whereBetween('first_visit_date', [$startDate, $endDate])
                    ->where('follow_up_status', 'converted')->count(),
            ];
        }

        // By follow-up status
        $byFollowUpStatus = Visitor::selectRaw('follow_up_status, COUNT(*) as count')
            ->groupBy('follow_up_status')
            ->pluck('count', 'follow_up_status');

        // Conversion rate
        $totalVisitors = Visitor::whereYear('first_visit_date', $year)->count();
        $convertedVisitors = Visitor::whereYear('first_visit_date', $year)->where('follow_up_status', 'converted')->count();
        $conversionRate = $totalVisitors > 0 ? round(($convertedVisitors / $totalVisitors) * 100, 1) : 0;

        $totals = [
            'total' => $totalVisitors,
            'converted' => $convertedVisitors,
            'pending' => Visitor::whereYear('first_visit_date', $year)->where('follow_up_status', 'pending')->count(),
            'conversion_rate' => $conversionRate,
        ];

        return view('admin.reports.visitors', compact(
            'monthlyVisitors', 'byFollowUpStatus', 'totals', 'year'
        ));
    }

    /**
     * Birthday Report.
     */
    public function birthdayReport(Request $request)
    {
        $month = $request->get('month', date('m'));

        $members = Member::where('membership_status', 'active')
            ->whereMonth('date_of_birth', $month)
            ->orderByRaw('DAY(date_of_birth)')
            ->get()
            ->map(function ($member) {
                $member->birthday_day = Carbon::parse($member->date_of_birth)->day;
                $member->age = Carbon::parse($member->date_of_birth)->age;
                return $member;
            });

        $monthName = Carbon::create(null, $month, 1)->format('F');

        return view('admin.reports.birthdays', compact('members', 'month', 'monthName'));
    }

    /**
     * Pledge Report.
     */
    public function pledgeReport(Request $request)
    {
        $status = $request->get('status', 'active');

        $pledges = Pledge::with(['member', 'project'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'total_pledged' => Pledge::sum('total_amount'),
            'total_paid' => Pledge::sum('amount_paid'),
            'active' => Pledge::where('status', 'active')->count(),
            'fulfilled' => Pledge::where('status', 'completed')->count(),
            'overdue' => Pledge::where('status', 'active')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->count(),
        ];

        return view('admin.reports.pledges', compact('pledges', 'summary', 'status'));
    }

    /**
     * Ministry Report.
     */
    public function ministryReport()
    {
        $ministries = Ministry::withCount(['members as active_members_count' => fn($q) => 
            $q->where('members.membership_status', 'active')
        ])->orderByDesc('active_members_count')->get();

        $totalMembers = Member::where('membership_status', 'active')->count();
        $membersInMinistries = \DB::table('member_ministry')
            ->distinct('member_id')
            ->count('member_id');

        return view('admin.reports.ministries', compact('ministries', 'totalMembers', 'membersInMinistries'));
    }

    /**
     * Offering Report.
     */
    public function offeringReport(Request $request)
    {
        $year = (int) $request->get('year', $this->defaultYear());
        $categoryId = $request->get('category_id');

        // By category
        $byCategory = IncomeCategory::where('is_active', true)->get()->map(function ($cat) use ($year) {
            $cat->total = Offering::where('income_category_id', $cat->id)
                ->whereYear('payment_date', $year)
                ->sum('amount');
            return $cat;
        })->sortByDesc('total');

        // Monthly trend
        $monthlyTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $query = Offering::whereYear('payment_date', $year)->whereMonth('payment_date', $m);
            if ($categoryId) {
                $query->where('income_category_id', $categoryId);
            }
            $monthlyTrend[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'amount' => $query->sum('amount'),
            ];
        }

        $categories = IncomeCategory::where('is_active', true)->orderBy('name')->get();

        $totals = [
            'total' => $byCategory->sum('total'),
            'categories' => $byCategory->filter(fn($c) => $c->total > 0)->count(),
        ];

        return view('admin.reports.offerings', compact(
            'byCategory', 'monthlyTrend', 'categories', 'totals', 'year', 'categoryId'
        ));
    }

    private function defaultYear(): int
    {
        $active = FinancialYear::active()->first() ?? FinancialYear::open()->orderBy('start_date', 'desc')->first();
        return $active ? $active->start_date->year : (int) date('Y');
    }
}
