<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\MinistryExpense;
use App\Models\MinistryOffering;
use App\Models\MinistryWelfareBenefit;
use App\Models\MinistryWelfareBenefitExpense;
use App\Models\MinistryWelfareContribution;
use App\Models\MinistryWelfareRate;
use App\Models\SmsTemplate;
use App\Helpers\SettingHelper;
use App\Services\GiantSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MinistryFinanceController extends Controller
{
    // ── Resolve the logged-in user's ministry ─────────────────────────────

    private function getMinistry(): ?Ministry
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->member_id) {
            return null;
        }

        $ministryId = DB::table('member_ministry')
            ->where('member_id', $user->member_id)
            ->where('is_active', 1)
            ->orderByRaw("role = 'leader' DESC")
            ->value('ministry_id');

        return $ministryId ? Ministry::find($ministryId) : null;
    }

    // ── Finance Overview ──────────────────────────────────────────────────

    public function index()
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $year  = request('year', now()->year);
        $month = request('month', now()->month);

        $totalIncomeYear   = MinistryOffering::where('ministry_id', $ministry->id)->whereYear('offering_date', $year)->sum('amount');
        $totalExpenseYear  = MinistryExpense::where('ministry_id', $ministry->id)->whereYear('expense_date', $year)->sum('amount');
        $totalIncomeMonth  = MinistryOffering::where('ministry_id', $ministry->id)->whereYear('offering_date', $year)->whereMonth('offering_date', $month)->sum('amount');
        $totalExpenseMonth = MinistryExpense::where('ministry_id', $ministry->id)->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');

        $welfareIncome   = MinistryWelfareContribution::where('ministry_id', $ministry->id)->where('period_year', $year)->sum('amount');
        $welfareExpenses = MinistryWelfareBenefit::where('ministry_id', $ministry->id)->where('benefit_year', $year)->sum('amount');

        $recentOfferings = MinistryOffering::where('ministry_id', $ministry->id)->with('recordedBy')->orderByDesc('offering_date')->limit(5)->get();
        $recentExpenses  = MinistryExpense::where('ministry_id', $ministry->id)->with('recordedBy')->orderByDesc('expense_date')->limit(5)->get();

        return view('admin.roles.ministry.finance.index', compact(
            'ministry', 'year', 'month',
            'totalIncomeYear', 'totalExpenseYear',
            'totalIncomeMonth', 'totalExpenseMonth',
            'welfareIncome', 'welfareExpenses',
            'recentOfferings', 'recentExpenses'
        ));
    }

    // ── Offerings ─────────────────────────────────────────────────────────

    public function offerings(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $year  = $request->input('year', now()->year);
        $query = MinistryOffering::where('ministry_id', $ministry->id)->with('recordedBy');

        if ($request->filled('type'))  { $query->where('offering_type', $request->type); }
        if ($request->filled('month')) { $query->whereMonth('offering_date', $request->month); }
        $query->whereYear('offering_date', $year);

        $offerings  = $query->orderByDesc('offering_date')->paginate(20)->withQueryString();
        $totalShown = (clone $query)->sum('amount');
        $types      = MinistryOffering::TYPES;
        $methods    = MinistryOffering::PAYMENT_METHODS;

        return view('admin.roles.ministry.finance.offerings', compact(
            'ministry', 'offerings', 'totalShown', 'types', 'methods', 'year'
        ));
    }

    public function storeOffering(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        $validated = $request->validate([
            'offering_type'     => 'required|in:' . implode(',', array_keys(MinistryOffering::TYPES)),
            'amount'            => 'required|numeric|min:0.01',
            'offering_date'     => 'required|date|before_or_equal:today',
            'description'       => 'nullable|string|max:255',
            'payment_method'    => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'notes'             => 'nullable|string|max:500',
        ]);

        $validated['ministry_id'] = $ministry->id;
        MinistryOffering::create($validated);

        return back()->with('success', 'Offering recorded successfully.');
    }

    public function deleteOffering(int $id)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        MinistryOffering::where('ministry_id', $ministry->id)->findOrFail($id)->delete();

        return back()->with('success', 'Offering deleted.');
    }

    // ── Expenses ──────────────────────────────────────────────────────────

    public function expenses(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $year  = $request->input('year', now()->year);
        $query = MinistryExpense::where('ministry_id', $ministry->id)->with('recordedBy');

        if ($request->filled('category')) { $query->where('category', $request->category); }
        if ($request->filled('month'))    { $query->whereMonth('expense_date', $request->month); }
        $query->whereYear('expense_date', $year);

        $expenses   = $query->orderByDesc('expense_date')->paginate(20)->withQueryString();
        $totalShown = (clone $query)->sum('amount');
        $categories = MinistryExpense::CATEGORIES;
        $methods    = MinistryExpense::PAYMENT_METHODS;

        return view('admin.roles.ministry.finance.expenses', compact(
            'ministry', 'expenses', 'totalShown', 'categories', 'methods', 'year'
        ));
    }

    public function storeExpense(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        $validated = $request->validate([
            'category'          => 'required|in:' . implode(',', array_keys(MinistryExpense::CATEGORIES)),
            'amount'            => 'required|numeric|min:0.01',
            'expense_date'      => 'required|date|before_or_equal:today',
            'description'       => 'required|string|max:255',
            'paid_to'           => 'nullable|string|max:150',
            'payment_method'    => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'notes'             => 'nullable|string|max:500',
        ]);

        $validated['ministry_id'] = $ministry->id;
        MinistryExpense::create($validated);

        return back()->with('success', 'Expense recorded successfully.');
    }

    public function deleteExpense(int $id)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        MinistryExpense::where('ministry_id', $ministry->id)->findOrFail($id)->delete();

        return back()->with('success', 'Expense deleted.');
    }

    // ── Welfare Contributions ─────────────────────────────────────────────

    public function welfareContributions(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $year  = $request->input('period_year', now()->year);
        $query = MinistryWelfareContribution::where('ministry_id', $ministry->id)->with(['member', 'receivedBy']);

        if ($request->filled('member_id'))    { $query->where('member_id', $request->member_id); }
        if ($request->filled('period_month')) { $query->where('period_month', $request->period_month); }
        $query->where('period_year', $year);

        $contributions = $query->orderByDesc('payment_date')->paginate(20)->withQueryString();

        $stats = [
            'total_this_year'  => MinistryWelfareContribution::where('ministry_id', $ministry->id)->where('period_year', $year)->sum('amount'),
            'this_month_count' => MinistryWelfareContribution::where('ministry_id', $ministry->id)->where('period_year', $year)->where('period_month', now()->month)->count(),
            'unique_members'   => MinistryWelfareContribution::where('ministry_id', $ministry->id)->where('period_year', $year)->distinct('member_id')->count('member_id'),
        ];

        $members = Member::whereHas('ministries', fn($q) => $q->where('ministries.id', $ministry->id))->orderBy('first_name')->get();
        $methods = MinistryWelfareContribution::PAYMENT_METHODS;

        return view('admin.roles.ministry.finance.welfare.contributions', compact(
            'ministry', 'contributions', 'stats', 'members', 'methods', 'year'
        ));
    }

    public function storeWelfareContribution(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        $validated = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'amount'         => 'required|numeric|min:0.01',
            'period_month'   => 'required|integer|min:1|max:12',
            'period_year'    => 'required|integer|min:2000|max:2099',
            'payment_date'   => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'notes'          => 'nullable|string|max:500',
        ]);

        $validated['ministry_id'] = $ministry->id;
        $contribution = MinistryWelfareContribution::create($validated);

        $this->sendContributionSms($contribution);

        return back()->with('success', 'Welfare contribution recorded. Ref: ' . $contribution->reference_number);
    }

    public function deleteWelfareContribution(int $id)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        MinistryWelfareContribution::where('ministry_id', $ministry->id)->findOrFail($id)->delete();

        return back()->with('success', 'Contribution deleted.');
    }

    // ── Welfare Benefits ──────────────────────────────────────────────────

    public function welfareBenefits(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $query = MinistryWelfareBenefit::where('ministry_id', $ministry->id)->with(['member', 'recordedBy', 'expenses']);

        if ($request->filled('purpose')) { $query->where('purpose', $request->purpose); }
        if ($request->filled('year'))    { $query->where('benefit_year', $request->year); }

        $benefits = $query->orderByDesc('benefit_date')->paginate(20)->withQueryString();
        $purposes = MinistryWelfareBenefit::PURPOSES;

        return view('admin.roles.ministry.finance.welfare.benefits', compact(
            'ministry', 'benefits', 'purposes'
        ));
    }

    public function createWelfareBenefit()
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $members  = Member::whereHas('ministries', fn($q) => $q->where('ministries.id', $ministry->id))->orderBy('first_name')->get();
        $purposes = MinistryWelfareBenefit::PURPOSES;

        return view('admin.roles.ministry.finance.welfare.create-benefit', compact('ministry', 'members', 'purposes'));
    }

    public function storeWelfareBenefit(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        $request->validate([
            'member_id'        => 'nullable|exists:members,id',
            'beneficiary_name' => 'required|string|max:255',
            'purpose'          => 'required|in:' . implode(',', array_keys(MinistryWelfareBenefit::PURPOSES)),
            'benefit_date'     => 'required|date|before_or_equal:today',
            'amount'           => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'expenses.*.description' => 'required_with:expenses.*.amount|string|max:255',
            'expenses.*.amount'      => 'required_with:expenses.*.description|numeric|min:0.01',
        ]);

        $expenseItems = collect($request->input('expenses', []))->filter(
            fn($e) => !empty($e['description']) && !empty($e['amount'])
        );

        $benefit = null;

        DB::transaction(function () use ($request, $ministry, $expenseItems, &$benefit) {
            $benefit = MinistryWelfareBenefit::create([
                'ministry_id'      => $ministry->id,
                'member_id'        => $request->member_id,
                'beneficiary_name' => $request->beneficiary_name,
                'purpose'          => $request->purpose,
                'benefit_date'     => $request->benefit_date,
                'benefit_year'     => date('Y', strtotime($request->benefit_date)),
                'amount'           => $request->amount,
                'description'      => $request->description,
                'approved_by'      => $request->approved_by,
            ]);

            foreach ($expenseItems as $item) {
                MinistryWelfareBenefitExpense::create([
                    'ministry_welfare_benefit_id' => $benefit->id,
                    'description'                 => $item['description'],
                    'amount'                      => $item['amount'],
                ]);
            }
        });

        $this->sendBenefitSms($benefit);

        return redirect()->route('admin.ministry.finance.welfare.benefits')
            ->with('success', 'Welfare benefit recorded successfully.');
    }

    public function showWelfareBenefit(int $id)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $benefit  = MinistryWelfareBenefit::where('ministry_id', $ministry->id)
            ->with(['member', 'approvedBy', 'recordedBy', 'expenses'])
            ->findOrFail($id);
        $purposes = MinistryWelfareBenefit::PURPOSES;

        return view('admin.roles.ministry.finance.welfare.show-benefit', compact('ministry', 'benefit', 'purposes'));
    }

    public function deleteWelfareBenefit(int $id)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        MinistryWelfareBenefit::where('ministry_id', $ministry->id)->findOrFail($id)->delete();

        return redirect()->route('admin.ministry.finance.welfare.benefits')->with('success', 'Welfare benefit deleted.');
    }

    // ── Welfare Due Rates ─────────────────────────────────────────────────

    public function welfareDueRates()
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $rates = MinistryWelfareRate::where('ministry_id', $ministry->id)
            ->with('createdBy')
            ->orderByDesc('effective_from')
            ->get();

        return view('admin.roles.ministry.finance.welfare.due-rates', compact('ministry', 'rates'));
    }

    public function storeWelfareDueRate(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) return back()->with('error', 'No ministry assigned.');

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'effective_from' => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        $validated['ministry_id'] = $ministry->id;
        $validated['created_by']  = Auth::id();

        MinistryWelfareRate::create($validated);

        return back()->with('success', 'Due rate added successfully.');
    }

    // ── Welfare Balances ──────────────────────────────────────────────────

    public function welfareBalances(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $year   = $request->input('year', now()->year);
        $status = $request->input('status', 'all');

        $members = Member::whereHas('ministries', fn($q) => $q->where('ministries.id', $ministry->id))
            ->orderBy('first_name')->get();

        $balances = $members->map(function (Member $member) use ($ministry, $year) {
            $paid = MinistryWelfareContribution::where('ministry_id', $ministry->id)
                ->where('member_id', $member->id)
                ->where('period_year', $year)
                ->sum('amount');

            $expected = 0;
            for ($m = 1; $m <= 12; $m++) {
                $rate = MinistryWelfareRate::rateForPeriod($ministry->id, $m, $year);
                if ($rate) {
                    $expected += (float) $rate->amount;
                }
            }

            return [
                'member'   => $member,
                'expected' => $expected,
                'paid'     => (float) $paid,
                'balance'  => round($expected - (float) $paid, 2),
            ];
        });

        if ($status === 'arrears') {
            $balances = $balances->filter(fn($b) => $b['balance'] > 0);
        } elseif ($status === 'paid') {
            $balances = $balances->filter(fn($b) => $b['balance'] == 0);
        } elseif ($status === 'credit') {
            $balances = $balances->filter(fn($b) => $b['balance'] < 0);
        }

        return view('admin.roles.ministry.finance.welfare.balances', compact(
            'ministry', 'balances', 'year', 'status'
        ));
    }

    // ── Welfare Fund Summary ──────────────────────────────────────────────

    public function welfareFundSummary(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $year = $request->input('year', now()->year);

        $contributionsByMonth = MinistryWelfareContribution::where('ministry_id', $ministry->id)
            ->where('period_year', $year)
            ->select('period_month', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('period_month')->get()->keyBy('period_month');

        $benefitsByPurpose = MinistryWelfareBenefit::where('ministry_id', $ministry->id)
            ->where('benefit_year', $year)
            ->select('purpose', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('purpose')->get()->keyBy('purpose');

        $totalContributions = MinistryWelfareContribution::where('ministry_id', $ministry->id)->where('period_year', $year)->sum('amount');
        $totalBenefits      = MinistryWelfareBenefit::where('ministry_id', $ministry->id)->where('benefit_year', $year)->sum('amount');
        $balance            = $totalContributions - $totalBenefits;

        $recentBenefits = MinistryWelfareBenefit::where('ministry_id', $ministry->id)
            ->with(['member', 'expenses'])->orderByDesc('benefit_date')->limit(10)->get();

        $availableYears = MinistryWelfareContribution::where('ministry_id', $ministry->id)
            ->selectRaw('period_year as y')->distinct()->pluck('y')
            ->merge(MinistryWelfareBenefit::where('ministry_id', $ministry->id)->selectRaw('benefit_year as y')->distinct()->pluck('y'))
            ->unique()->sortDesc()->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        $purposes = MinistryWelfareBenefit::PURPOSES;

        return view('admin.roles.ministry.finance.welfare.fund-summary', compact(
            'ministry', 'year', 'availableYears',
            'contributionsByMonth', 'benefitsByPurpose',
            'totalContributions', 'totalBenefits', 'balance',
            'recentBenefits', 'purposes'
        ));
    }

    // ── Income & Expense Report ───────────────────────────────────────────

    public function report(Request $request)
    {
        $ministry = $this->getMinistry();
        if (!$ministry) {
            return redirect()->route('admin.ministry.dashboard')->with('error', 'No ministry assigned.');
        }

        $year = (int) $request->input('year', now()->year);

        // ── General Finance ──────────────────────────────────────────────────
        $incomeByType = MinistryOffering::where('ministry_id', $ministry->id)
            ->whereYear('offering_date', $year)
            ->select('offering_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('offering_type')->get()->keyBy('offering_type');

        $expenseByCategory = MinistryExpense::where('ministry_id', $ministry->id)
            ->whereYear('expense_date', $year)
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')->get()->keyBy('category');

        // ── Welfare Fund ─────────────────────────────────────────────────────
        $welfareBenefitsByPurpose = MinistryWelfareBenefit::where('ministry_id', $ministry->id)
            ->whereYear('benefit_date', $year)
            ->select('purpose', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('purpose')->get()->keyBy('purpose');

        // ── Monthly aggregates (4 queries using GROUP BY instead of 48 loops) ─
        $rawOfferings   = MinistryOffering::where('ministry_id', $ministry->id)
            ->whereYear('offering_date', $year)
            ->selectRaw('MONTH(offering_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawExpenses    = MinistryExpense::where('ministry_id', $ministry->id)
            ->whereYear('expense_date', $year)
            ->selectRaw('MONTH(expense_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawContribs    = MinistryWelfareContribution::where('ministry_id', $ministry->id)
            ->where('period_year', $year)
            ->selectRaw('period_month as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $rawBenefits    = MinistryWelfareBenefit::where('ministry_id', $ministry->id)
            ->whereYear('benefit_date', $year)
            ->selectRaw('MONTH(benefit_date) as m, SUM(amount) as total')
            ->groupBy('m')->pluck('total', 'm')->toArray();

        $monthlyIncome = $monthlyExpenses = $monthlyWelfareContribs = $monthlyWelfareBenefits = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyIncome[$m]          = (float) ($rawOfferings[$m]  ?? 0);
            $monthlyExpenses[$m]        = (float) ($rawExpenses[$m]   ?? 0);
            $monthlyWelfareContribs[$m] = (float) ($rawContribs[$m]   ?? 0);
            $monthlyWelfareBenefits[$m] = (float) ($rawBenefits[$m]   ?? 0);
        }

        $totalIncome          = array_sum($monthlyIncome);
        $totalExpense         = array_sum($monthlyExpenses);
        $financialBalance     = $totalIncome - $totalExpense;
        $totalWelfareContribs = array_sum($monthlyWelfareContribs);
        $totalWelfareBenefits = array_sum($monthlyWelfareBenefits);
        $welfareFundBalance   = $totalWelfareContribs - $totalWelfareBenefits;

        $memberCount = $ministry->activeMembers()->count();

        $availableYears = MinistryOffering::where('ministry_id', $ministry->id)
            ->selectRaw('YEAR(offering_date) as y')->distinct()->pluck('y')
            ->merge(MinistryExpense::where('ministry_id', $ministry->id)->selectRaw('YEAR(expense_date) as y')->distinct()->pluck('y'))
            ->merge(MinistryWelfareContribution::where('ministry_id', $ministry->id)->selectRaw('period_year as y')->distinct()->pluck('y'))
            ->merge(MinistryWelfareBenefit::where('ministry_id', $ministry->id)->selectRaw('YEAR(benefit_date) as y')->distinct()->pluck('y'))
            ->unique()->sortDesc()->values();

        if ($availableYears->isEmpty()) $availableYears = collect([now()->year]);

        $currencySymbol = SettingHelper::currencySymbol();
        $purposes       = MinistryWelfareBenefit::PURPOSES;

        return view('admin.roles.ministry.finance.report', compact(
            'ministry', 'year', 'availableYears', 'memberCount',
            'incomeByType', 'expenseByCategory',
            'welfareBenefitsByPurpose', 'purposes',
            'monthlyIncome', 'monthlyExpenses',
            'monthlyWelfareContribs', 'monthlyWelfareBenefits',
            'totalIncome', 'totalExpense', 'financialBalance',
            'totalWelfareContribs', 'totalWelfareBenefits', 'welfareFundBalance',
            'currencySymbol'
        ));
    }

    // ── Welfare Member Records ────────────────────────────────────────────

    public function welfareMembers(\Illuminate\Http\Request $request): \Illuminate\View\View
    {
        $ministry = $this->getMinistry();
        if (!$ministry) abort(403);

        $search  = trim($request->get('search', ''));
        $members = $ministry->activeMembers()
            ->when($search, fn($q) => $q->where(fn($q2) =>
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name', 'like', "%{$search}%")
                   ->orWhere('member_id', 'like', "%{$search}%")
            ))
            ->withCount([
                'ministryWelfareContributions as contrib_count' =>
                    fn($q) => $q->where('ministry_id', $ministry->id),
                'ministryWelfareBenefits as benefit_count' =>
                    fn($q) => $q->where('ministry_id', $ministry->id),
            ])
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        return view('admin.roles.ministry.finance.welfare.members',
            compact('ministry', 'members', 'search'));
    }

    public function welfareMemberShow(\Illuminate\Http\Request $request, int $memberId): \Illuminate\View\View
    {
        $ministry = $this->getMinistry();
        if (!$ministry) abort(403);

        $member = \App\Models\Member::findOrFail($memberId);

        // Rates for this ministry
        $allRates = MinistryWelfareRate::where('ministry_id', $ministry->id)
            ->orderByDesc('effective_from')->get();

        $rateFor = function (int $month, int $year) use ($allRates, $ministry): ?float {
            $cut = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
            $r   = $allRates->first(fn($r) => $r->effective_from->lte($cut));
            return $r ? (float) $r->amount : null;
        };

        $allContribs = MinistryWelfareContribution::where('ministry_id', $ministry->id)
            ->where('member_id', $member->id)
            ->get()->groupBy('period_year');

        $years = $allContribs->keys()
            ->merge([now()->year])
            ->unique()->sortDesc()->values();

        $yearlyData = [];
        foreach ($years as $yr) {
            $byMonth  = $allContribs->get($yr, collect())->keyBy('period_month');
            $expected = 0;
            $months   = [];

            for ($m = 1; $m <= 12; $m++) {
                $rate    = $rateFor($m, $yr);
                $contrib = $byMonth->get($m);
                $expected += $rate ?? 0;
                $months[$m] = [
                    'rate'    => $rate,
                    'paid'    => $contrib ? (float) $contrib->amount : null,
                    'contrib' => $contrib,
                ];
            }

            $paid    = (float) $byMonth->sum('amount');
            $balance = $expected - $paid;

            $yearlyData[] = compact('yr', 'months', 'expected', 'paid', 'balance');
        }

        $benefits = MinistryWelfareBenefit::with('expenses')
            ->where('ministry_id', $ministry->id)
            ->where('member_id', $member->id)
            ->orderByDesc('benefit_date')->get();

        $totalContributions = (float) MinistryWelfareContribution::where('ministry_id', $ministry->id)
            ->where('member_id', $member->id)->sum('amount');
        $totalBenefits = $benefits->sum(fn($b) => (float) $b->total_cost);
        $purposes      = MinistryWelfareBenefit::PURPOSES;

        return view('admin.roles.ministry.finance.welfare.member-show',
            compact('ministry', 'member', 'yearlyData', 'benefits', 'totalContributions', 'totalBenefits', 'purposes'));
    }

    // ── SMS Helpers ───────────────────────────────────────────────────────

    private function sendContributionSms(MinistryWelfareContribution $contribution): void
    {
        if (!\App\Models\Setting::get('sms_auto_welfare_confirmation', false)) {
            return;
        }

        $member = $contribution->member ?? $contribution->load('member')->member;
        $phone  = $member?->phone_primary;

        if (!$phone) {
            return;
        }

        try {
            $sms = new GiantSmsService();

            if (!$sms->isConfigured()) {
                return;
            }

            $sym          = SettingHelper::currencySymbol();
            $amount       = $sym . ' ' . number_format((float) $contribution->amount, 2);
            $period       = date('F Y', mktime(0, 0, 0, $contribution->period_month, 1, $contribution->period_year));
            $ministryName = $contribution->ministry?->name ?? 'Ministry';

            $template = SmsTemplate::where('slug', 'ministry-welfare-contribution-confirmation')
                ->where('is_active', true)->first();

            if ($template) {
                $message = $template->renderContent([
                    'member_name'      => $member->first_name,
                    'amount'           => $amount,
                    'period'           => $period,
                    'reference_number' => $contribution->reference_number,
                    'ministry_name'    => $ministryName,
                ]);
            } else {
                $message = 'Dear ' . $member->first_name . ', your ' . $ministryName
                    . ' welfare due of ' . $amount . ' for ' . $period
                    . ' has been received. Ref: ' . $contribution->reference_number
                    . '. God bless you. - ' . \App\Helpers\SettingHelper::churchName();
            }

            $sms->send($phone, $message);

        } catch (\Throwable $e) {
            Log::warning('Ministry welfare contribution SMS failed for ref '
                . $contribution->reference_number . ': ' . $e->getMessage());
        }
    }

    private function sendBenefitSms(?MinistryWelfareBenefit $benefit): void
    {
        if (!$benefit || !$benefit->member_id) {
            return;
        }

        if (!\App\Models\Setting::get('sms_auto_welfare_benefit', false)) {
            return;
        }

        $member = $benefit->member ?? $benefit->load('member')->member;
        $phone  = $member?->phone_primary;

        if (!$phone) {
            return;
        }

        try {
            $sms = new GiantSmsService();

            if (!$sms->isConfigured()) {
                return;
            }

            $sym          = SettingHelper::currencySymbol();
            $amount       = $sym . ' ' . number_format((float) $benefit->amount, 2);
            $purpose      = MinistryWelfareBenefit::PURPOSES[$benefit->purpose] ?? ucfirst($benefit->purpose);
            $date         = $benefit->benefit_date instanceof \Carbon\Carbon
                ? $benefit->benefit_date->format('d M Y')
                : date('d M Y', strtotime($benefit->benefit_date));
            $ministryName = $benefit->ministry?->name ?? 'Ministry';

            $template = SmsTemplate::where('slug', 'ministry-welfare-benefit-disbursement')
                ->where('is_active', true)->first();

            if ($template) {
                $message = $template->renderContent([
                    'member_name'   => $member->first_name,
                    'amount'        => $amount,
                    'purpose'       => $purpose,
                    'date'          => $date,
                    'ministry_name' => $ministryName,
                ]);
            } else {
                $message = 'Dear ' . $member->first_name . ', a ' . $ministryName
                    . ' welfare benefit of ' . $amount . ' for ' . $purpose
                    . ' has been disbursed to you on ' . $date
                    . '. God bless you. - ' . \App\Helpers\SettingHelper::churchName();
            }

            $sms->send($phone, $message);

        } catch (\Throwable $e) {
            Log::warning('Ministry welfare benefit SMS failed for benefit #'
                . $benefit->id . ': ' . $e->getMessage());
        }
    }
}
