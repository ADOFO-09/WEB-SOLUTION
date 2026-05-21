<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Helpers\SettingHelper;

class FinancialYearController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:finance.view',   only: ['index']),
            new Middleware('permission:finance.manage', only: ['create', 'store', 'edit', 'update', 'activate', 'close', 'destroy']),
        ];
    }

    /**
     * Display a listing of all financial years.
     */
    public function index()
    {
        $yearsPaginated = FinancialYear::orderByDesc('start_date')->paginate(SettingHelper::perPage());

        return view('admin.finance.financial-years.index', compact('yearsPaginated'));
    }

    /**
     * Show the form for creating a new financial year.
     */
    public function create()
    {
        return view('admin.finance.financial-years.create');
    }

    /**
     * Store a newly created financial year.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['is_active']) {
            FinancialYear::query()->update(['is_active' => false]);
        }

        FinancialYear::create($validated);

        return redirect()->route('admin.finance.years.index')
            ->with('success', 'Financial year created successfully.');
    }

    /**
     * Show the form for editing the specified financial year.
     */
    public function edit(FinancialYear $financialYear)
    {
        return view('admin.finance.financial-years.edit', compact('financialYear'));
    }

    /**
     * Update the specified financial year.
     */
    public function update(Request $request, FinancialYear $financialYear)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['is_active']) {
            FinancialYear::where('id', '!=', $financialYear->id)->update(['is_active' => false]);
        }

        $financialYear->update($validated);

        return redirect()->route('admin.finance.years.index')
            ->with('success', 'Financial year updated successfully.');
    }

    /**
     * Activate the specified financial year (deactivates all others).
     */
    public function activate(FinancialYear $financialYear)
    {
        if ($financialYear->is_closed) {
            return back()->with('error', 'Cannot activate a closed financial year.');
        }

        FinancialYear::where('id', '!=', $financialYear->id)->update(['is_active' => false]);
        $financialYear->update(['is_active' => true]);

        return redirect()->route('admin.finance.years.index')
            ->with('success', "\"{$financialYear->name}\" is now the active financial year.");
    }

    /**
     * Close the specified financial year.
     */
    public function close(FinancialYear $financialYear)
    {
        if ($financialYear->is_closed) {
            return back()->with('error', 'This financial year is already closed.');
        }

        $financialYear->close();

        return redirect()->route('admin.finance.years.index')
            ->with('success', "\"{$financialYear->name}\" has been closed.");
    }

    /**
     * Remove the specified financial year (soft delete).
     */
    public function destroy(FinancialYear $financialYear)
    {
        $titheCount    = $financialYear->tithes()->count();
        $offeringCount = $financialYear->offerings()->count();
        $donationCount = $financialYear->donations()->count();
        $pledgeCount   = $financialYear->pledges()->count();

        if ($titheCount + $offeringCount + $donationCount + $pledgeCount > 0) {
            return back()->with('error', 'Cannot delete a financial year that has linked tithes, offerings, donations, or pledges.');
        }

        $financialYear->delete();

        return redirect()->route('admin.finance.years.index')
            ->with('success', 'Financial year deleted successfully.');
    }
}
