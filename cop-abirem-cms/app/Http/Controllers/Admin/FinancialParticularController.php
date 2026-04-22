<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Offering;
use App\Models\Tithe;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FinancialParticularController extends Controller
{
    public function index()
    {
        $incomeByType = IncomeCategory::withCount(['tithes', 'offerings', 'donations'])
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        $expenseCategories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->get();

        return view('admin.finance.particulars.index', compact('incomeByType', 'expenseCategories'));
    }

    // =========================================
    // INCOME PARTICULARS
    // =========================================

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:income_categories,name',
            'type'        => 'required|in:tithe,offering,donation,special,pledge,other',
            'description' => 'nullable|string|max:500',
        ]);

        IncomeCategory::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'type'        => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_active'   => true,
            'is_system'   => false,
            'sort_order'  => 99,
        ]);

        return back()->with('success', "Income particular '{$validated['name']}' added.");
    }

    public function toggleIncome(IncomeCategory $category)
    {
        if ($category->is_system) {
            return back()->with('error', "'{$category->name}' is a system category and cannot be toggled.");
        }
        $category->update(['is_active' => !$category->is_active]);
        $state = $category->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "'{$category->name}' {$state}.");
    }

    public function destroyIncome(IncomeCategory $category)
    {
        if ($category->is_system) {
            return back()->with('error', "Cannot delete '{$category->name}': this is a system category. Disable it instead.");
        }

        $linked = Tithe::where('income_category_id', $category->id)->count()
                + Offering::where('income_category_id', $category->id)->count()
                + Donation::where('income_category_id', $category->id)->count();

        if ($linked > 0) {
            return back()->with('error', "Cannot delete '{$category->name}': {$linked} financial record(s) are linked to it. Reassign them first, or disable this category instead.");
        }

        $name = $category->name;
        $category->delete();
        return back()->with('success', "'{$name}' deleted successfully.");
    }

    // =========================================
    // EXPENSE CATEGORIES
    // =========================================

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:expense_categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        ExpenseCategory::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', "Expense category '{$validated['name']}' added.");
    }

    public function toggleExpense(ExpenseCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        $state = $category->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "'{$category->name}' {$state}.");
    }

    public function destroyExpense(ExpenseCategory $category)
    {
        $linked = Expense::where('expense_category_id', $category->id)->count();

        if ($linked > 0) {
            return back()->with('error', "Cannot delete '{$category->name}': {$linked} expense(s) are using this category. Reassign or delete those expenses first, or disable this category instead.");
        }

        $name = $category->name;
        $category->delete();
        return back()->with('success', "'{$name}' deleted successfully.");
    }

    // =========================================
    // AJAX: add income category on the fly
    // =========================================

    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:tithe,offering,donation,special,pledge,other',
        ]);

        $category = IncomeCategory::firstOrCreate(
            ['slug' => Str::slug($validated['name'])],
            [
                'name'       => $validated['name'],
                'type'       => $validated['type'],
                'is_active'  => true,
                'is_system'  => false,
                'sort_order' => 99,
            ]
        );

        return response()->json(['id' => $category->id, 'name' => $category->name]);
    }
}
