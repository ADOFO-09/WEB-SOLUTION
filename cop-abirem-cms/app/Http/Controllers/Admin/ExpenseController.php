<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use App\Helpers\SettingHelper;

class ExpenseController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:expenses.view', only: ['index', 'show']),
            new Middleware('permission:expenses.create', only: ['create', 'store']),
            new Middleware('permission:expenses.edit', only: ['edit', 'update', 'approve', 'reject', 'markPaid']),
            new Middleware('permission:finance.delete', only: ['destroy']),
        ];
    }

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
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $query->orderBy('created_at', 'desc');
        $expenses = $query->paginate(SettingHelper::perPage())->withQueryString();

        $stats = [
            'total_paid' => Expense::whereYear('expense_date', date('Y'))->where('status', 'paid')->sum('amount'),
            'this_month' => Expense::whereMonth('expense_date', date('m'))->whereYear('expense_date', date('Y'))->where('status', 'paid')->sum('amount'),
            'pending_count' => Expense::where('status', 'pending')->count(),
            'pending_amount' => Expense::where('status', 'pending')->sum('amount'),
            'approved_count' => Expense::where('status', 'approved')->count(),
        ];

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.finance.expenses.index', compact('expenses', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.finance.expenses.create', compact('categories'));
    }

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
        $validated['reference_number'] = Expense::generateReferenceNumber();

        $expense = Expense::create($validated);

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense request submitted. Reference: ' . $expense->reference_number);
    }

    public function show(Expense $expense)
    {
        $expense->load(['expenseCategory', 'requestedBy', 'approvedBy', 'financialYear']);
        return view('admin.finance.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'Only pending expenses can be edited.');
        }

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.finance.expenses.edit', compact('expense', 'categories'));
    }

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

    public function destroy(Expense $expense)
    {
        if (!in_array($expense->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Only pending or rejected expenses can be deleted.');
        }

        $expense->delete();
        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function approve(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'This expense cannot be approved.');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Expense approved successfully.');
    }

    public function reject(Request $request, Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->with('error', 'This expense cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $expense->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Expense rejected.');
    }

    public function markPaid(Request $request, Expense $expense)
    {
        if ($expense->status !== 'approved') {
            return back()->with('error', 'Only approved expenses can be marked as paid.');
        }

        $validated = $request->validate([
            'voucher_number' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $expense->update(array_merge(['status' => 'paid'], array_filter($validated)));

        return back()->with('success', 'Expense marked as paid.');
    }

    public function printVoucher(Expense $expense)
    {
        $expense->load(['expenseCategory', 'requestedBy', 'approvedBy']);
        return view('admin.finance.expenses.voucher', compact('expense'));
    }

    public function budgetReport(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $categories = ExpenseCategory::where('is_active', true)->get()->map(function ($cat) use ($year) {
            $cat->spent = Expense::where('expense_category_id', $cat->id)
                ->where('status', 'paid')
                ->whereYear('expense_date', $year)
                ->sum('amount');
            $cat->usage_percentage = $cat->budget_amount > 0 
                ? min(100, round(($cat->spent / $cat->budget_amount) * 100, 1)) 
                : 0;
            return $cat;
        });

        $totalBudget = $categories->sum('budget_amount');
        $totalSpent = $categories->sum('spent');

        return view('admin.finance.expenses.budget-report', compact(
            'categories', 'totalBudget', 'totalSpent', 'year'
        ));
    }
}
