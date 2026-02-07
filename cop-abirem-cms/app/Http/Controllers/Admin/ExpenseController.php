<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpenseController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:finance.view', only: [
                'index', 'show', 'printVoucher', 'budgetReport'
            ]),
            new Middleware(middleware: 'permission:finance.create', only: ['create', 'store']),
            new Middleware(middleware: 'permission:finance.edit', only: [
                'edit', 'update', 'approve', 'reject', 'markPaid'
            ]),
            new Middleware(middleware: 'permission:finance.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['expenseCategory', 'requestedBy', 'approvedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('payee_name', 'like', "%{$search}%")
                  ->orWhere('voucher_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $query->orderBy('created_at', 'desc');
        $expenses = $query->paginate(20)->withQueryString();

        $stats = [
            'total_paid' => Expense::thisYear()->paid()->sum('amount'),
            'this_month' => Expense::thisMonth()->paid()->sum('amount'),
            'pending_count' => Expense::pending()->count(),
            'pending_amount' => Expense::pending()->sum('amount'),
            'approved_count' => Expense::approved()->count(),
        ];

        $categories = ExpenseCategory::active()->orderBy('name')->get();

        return view('admin.finance.expenses.index', compact('expenses', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $categories = ExpenseCategory::active()->orderBy('name')->get();
        return view('admin.finance.expenses.create', compact('categories'));
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payee_name' => 'required|string|max:255',
            'payee_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['status'] = 'pending';
        $validated['requested_by'] = auth()->id();

        $expense = Expense::create($validated);

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense request submitted. Reference: ' . $expense->reference_number);
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $expense->load(['expenseCategory', 'requestedBy', 'approvedBy', 'financialYear']);
        return view('admin.finance.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Only pending expenses can be edited.');
        }

        $categories = ExpenseCategory::active()->orderBy('name')->get();
        return view('admin.finance.expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Only pending expenses can be edited.');
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payee_name' => 'required|string|max:255',
            'payee_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $expense->update($validated);

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        if (!in_array($expense->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Only pending or rejected expenses can be deleted.');
        }

        $expense->delete();
        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Approve an expense.
     */
    public function approve(Expense $expense)
    {
        if (!$expense->can_approve) {
            return back()->with('error', 'This expense cannot be approved.');
        }

        $expense->approve();

        return back()->with('success', 'Expense approved successfully.');
    }

    /**
     * Reject an expense.
     */
    public function reject(Request $request, Expense $expense)
    {
        if (!$expense->can_approve) {
            return back()->with('error', 'This expense cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $expense->reject($validated['rejection_reason']);

        return back()->with('success', 'Expense rejected.');
    }

    /**
     * Mark expense as paid.
     */
    public function markPaid(Request $request, Expense $expense)
    {
        if (!$expense->can_pay) {
            return back()->with('error', 'Only approved expenses can be marked as paid.');
        }

        $validated = $request->validate([
            'voucher_number' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $expense->markAsPaid($validated);

        return back()->with('success', 'Expense marked as paid. Voucher: ' . ($validated['voucher_number'] ?? 'N/A'));
    }

    /**
     * Print expense voucher.
     */
    public function printVoucher(Expense $expense)
    {
        $expense->load(['expenseCategory', 'requestedBy', 'approvedBy']);
        return view('admin.finance.expenses.voucher', compact('expense'));
    }

    /**
     * Budget report.
     */
    public function budgetReport(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $categories = ExpenseCategory::active()
            ->withCount(['expenses as paid_expenses_count' => fn($q) => $q->paid()->whereYear('expense_date', $year)])
            ->get()
            ->map(function ($cat) use ($year) {
                $cat->spent = $cat->getSpentAmount($year);
                $cat->usage_percentage = $cat->getBudgetUsedPercentage($year);
                return $cat;
            });

        $totalBudget = $categories->sum('budget_amount');
        $totalSpent = $categories->sum('spent');

        return view('admin.finance.expenses.budget-report', compact(
            'categories', 'totalBudget', 'totalSpent', 'year'
        ));
    }
}