<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Pledge;
use App\Models\Expense;
use App\Models\ServiceType;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceDashboardController extends Controller
{
    /**
     * Financial Secretary Dashboard
     * Focused on financial operations and accounting
     */
    public function index()
    {
        $currentMonth = Carbon::now();
        $currentYear = Carbon::now()->year;

        // Today's Collections
        $todayStats = [
            'tithes' => Tithe::whereDate('tithe_date', today())->sum('amount') ?? 0,
            'offerings' => Offering::whereDate('offering_date', today())->sum('amount') ?? 0,
            'donations' => Donation::whereDate('donation_date', today())->sum('amount') ?? 0,
        ];
        $todayStats['total'] = $todayStats['tithes'] + $todayStats['offerings'] + $todayStats['donations'];

        // This Week's Collections
        $weekStart = now()->startOfWeek();
        $weekStats = [
            'tithes' => Tithe::whereBetween('tithe_date', [$weekStart, now()])->sum('amount') ?? 0,
            'offerings' => Offering::whereBetween('offering_date', [$weekStart, now()])->sum('amount') ?? 0,
            'donations' => Donation::whereBetween('donation_date', [$weekStart, now()])->sum('amount') ?? 0,
        ];
        $weekStats['total'] = $weekStats['tithes'] + $weekStats['offerings'] + $weekStats['donations'];

        // This Month's Summary
        $monthStats = [
            'tithes' => Tithe::whereMonth('tithe_date', $currentMonth->month)
                ->whereYear('tithe_date', $currentYear)->sum('amount') ?? 0,
            'offerings' => Offering::whereMonth('offering_date', $currentMonth->month)
                ->whereYear('offering_date', $currentYear)->sum('amount') ?? 0,
            'donations' => Donation::whereMonth('donation_date', $currentMonth->month)
                ->whereYear('donation_date', $currentYear)->sum('amount') ?? 0,
            'expenses' => Expense::whereMonth('expense_date', $currentMonth->month)
                ->whereYear('expense_date', $currentYear)
                ->where('status', 'approved')->sum('amount') ?? 0,
        ];
        $monthStats['total_income'] = $monthStats['tithes'] + $monthStats['offerings'] + $monthStats['donations'];
        $monthStats['net'] = $monthStats['total_income'] - $monthStats['expenses'];

        // Year to Date Summary
        $ytdStats = [
            'tithes' => Tithe::whereYear('tithe_date', $currentYear)->sum('amount') ?? 0,
            'offerings' => Offering::whereYear('offering_date', $currentYear)->sum('amount') ?? 0,
            'donations' => Donation::whereYear('donation_date', $currentYear)->sum('amount') ?? 0,
            'expenses' => Expense::whereYear('expense_date', $currentYear)
                ->where('status', 'approved')->sum('amount') ?? 0,
        ];
        $ytdStats['total_income'] = $ytdStats['tithes'] + $ytdStats['offerings'] + $ytdStats['donations'];
        $ytdStats['net'] = $ytdStats['total_income'] - $ytdStats['expenses'];

        // Pledge Summary
        $pledgeStats = [
            'active_pledges' => Pledge::where('status', 'active')->count(),
            'total_pledged' => Pledge::where('status', '!=', 'cancelled')->sum('amount') ?? 0,
            'total_paid' => Pledge::where('status', '!=', 'cancelled')->sum('amount_paid') ?? 0,
            'overdue_count' => Pledge::where('status', 'active')
                ->where('end_date', '<', now())
                ->whereRaw('amount_paid < amount')->count(),
        ];
        $pledgeStats['balance'] = $pledgeStats['total_pledged'] - $pledgeStats['total_paid'];
        $pledgeStats['collection_rate'] = $pledgeStats['total_pledged'] > 0 
            ? round(($pledgeStats['total_paid'] / $pledgeStats['total_pledged']) * 100, 1) 
            : 0;

        // Recent Transactions
        $recentTithes = Tithe::with('member')
            ->orderBy('created_at', 'desc')
            ->take(5)->get();

        $recentOfferings = Offering::with('serviceType')
            ->orderBy('created_at', 'desc')
            ->take(5)->get();

        $recentDonations = Donation::with('member')
            ->orderBy('created_at', 'desc')
            ->take(5)->get();

        // Pending Expenses (awaiting approval)
        $pendingExpenses = Expense::where('status', 'pending')
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->take(10)->get();

        // Top Tithe Contributors (This Month)
        $topTithers = Tithe::select('member_id', DB::raw('SUM(amount) as total'))
            ->whereMonth('tithe_date', $currentMonth->month)
            ->whereYear('tithe_date', $currentYear)
            ->groupBy('member_id')
            ->orderByDesc('total')
            ->with('member:id,first_name,last_name,member_id')
            ->take(10)->get();

        // Monthly Trend (Last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyTrend[] = [
                'month' => $month->format('M'),
                'year' => $month->format('Y'),
                'tithes' => Tithe::whereMonth('tithe_date', $month->month)
                    ->whereYear('tithe_date', $month->year)->sum('amount') ?? 0,
                'offerings' => Offering::whereMonth('offering_date', $month->month)
                    ->whereYear('offering_date', $month->year)->sum('amount') ?? 0,
                'donations' => Donation::whereMonth('donation_date', $month->month)
                    ->whereYear('donation_date', $month->year)->sum('amount') ?? 0,
            ];
        }

        // For Quick Entry Forms
        $members = Member::select('id', 'first_name', 'last_name', 'member_id')
            ->where('membership_status', 'active')
            ->orderBy('first_name')->get();
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $expenseCategories = ExpenseCategory::where('is_active', true)->get();

        return view('admin.roles.finance.dashboard', compact(
            'todayStats',
            'weekStats',
            'monthStats',
            'ytdStats',
            'pledgeStats',
            'recentTithes',
            'recentOfferings',
            'recentDonations',
            'pendingExpenses',
            'topTithers',
            'monthlyTrend',
            'members',
            'serviceTypes',
            'expenseCategories'
        ));
    }

    /**
     * Quick Record Tithe
     */
    public function quickTithe(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'tithe_date' => 'required|date',
            'payment_method' => 'required|in:cash,cheque,mobile_money,bank_transfer',
        ]);

        $receiptNumber = 'TT-' . date('Ymd') . '-' . str_pad(
            Tithe::whereDate('created_at', today())->count() + 1, 
            4, '0', STR_PAD_LEFT
        );

        Tithe::create([
            'member_id' => $validated['member_id'],
            'amount' => $validated['amount'],
            'tithe_date' => $validated['tithe_date'],
            'payment_method' => $validated['payment_method'],
            'receipt_number' => $receiptNumber,
            'recorded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Tithe of GH₵' . number_format($validated['amount'], 2) . ' recorded. Receipt: ' . $receiptNumber);
    }

    /**
     * Quick Record Offering
     */
    public function quickOffering(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'offering_date' => 'required|date',
            'service_type_id' => 'nullable|exists:service_types,id',
            'offering_type' => 'required|string',
        ]);

        $receiptNumber = 'OF-' . date('Ymd') . '-' . str_pad(
            Offering::whereDate('created_at', today())->count() + 1, 
            4, '0', STR_PAD_LEFT
        );

        Offering::create([
            'amount' => $validated['amount'],
            'offering_date' => $validated['offering_date'],
            'service_type_id' => $validated['service_type_id'],
            'offering_type' => $validated['offering_type'],
            'receipt_number' => $receiptNumber,
            'recorded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Offering of GH₵' . number_format($validated['amount'], 2) . ' recorded. Receipt: ' . $receiptNumber);
    }

    /**
     * Quick Record Expense
     */
    public function quickExpense(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'required|string|max:500',
            'payment_method' => 'required|in:cash,cheque,mobile_money,bank_transfer',
        ]);

        $voucherNumber = 'EXP-' . date('Ymd') . '-' . str_pad(
            Expense::whereDate('created_at', today())->count() + 1, 
            4, '0', STR_PAD_LEFT
        );

        Expense::create([
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'voucher_number' => $voucherNumber,
            'status' => 'pending', // Requires approval
            'recorded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Expense of GH₵' . number_format($validated['amount'], 2) . ' submitted for approval. Voucher: ' . $voucherNumber);
    }
}