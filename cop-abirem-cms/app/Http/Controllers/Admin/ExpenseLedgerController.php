<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseLedgerController extends Controller
{
    private const CATEGORY_MAP = [
        'Transport & Fuel'         => 'transport',
        'Electricity'              => 'utilities',
        'Water'                    => 'utilities',
        'Internet & Communication' => 'utilities',
        'Welfare & Benevolence'    => 'welfare',
        'Cleaning & Sanitation'    => 'cleaning',
        'Maintenance & Repairs'    => 'maintenance',
        'Equipment Purchase'       => 'maintenance',
        'District Remittance'      => 'remittance',
        'Area Remittance'          => 'remittance',
        'Ministry Support'         => 'others',
        'Events & Programs'        => 'others',
        'Stationery & Supplies'    => 'others',
        'Security'                 => 'others',
        'Miscellaneous'            => 'others',
    ];

    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $expenses = Expense::with('expenseCategory')
            ->whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$start, $end])
            ->orderBy('expense_date')
            ->get();

        $ledgerEntries = $expenses->map(function ($expense) {
            $catName = $expense->expenseCategory?->name ?? 'Miscellaneous';
            $col     = self::CATEGORY_MAP[$catName] ?? 'others';
            $voided  = $expense->isVoided();
            return [
                'date'          => $expense->expense_date,
                'particular'    => $expense->particular_name,
                'transport'     => (!$voided && $col === 'transport')   ? (float) $expense->amount : 0.0,
                'utilities'     => (!$voided && $col === 'utilities')   ? (float) $expense->amount : 0.0,
                'welfare'       => (!$voided && $col === 'welfare')     ? (float) $expense->amount : 0.0,
                'cleaning'      => (!$voided && $col === 'cleaning')    ? (float) $expense->amount : 0.0,
                'maintenance'   => (!$voided && $col === 'maintenance') ? (float) $expense->amount : 0.0,
                'remittance'    => (!$voided && $col === 'remittance')  ? (float) $expense->amount : 0.0,
                'others'        => (!$voided && $col === 'others')      ? (float) $expense->amount : 0.0,
                'reference'     => $expense->voucher_number ?? $expense->reference_number,
                'ledger_status' => $expense->ledger_status ?? 'active',
                'is_adjustment' => (bool) $expense->is_adjustment,
            ];
        });

        // Sort then group by date
        $groupedEntries = $ledgerEntries
            ->sortBy('date')
            ->groupBy(fn($e) => Carbon::parse($e['date'])->format('Y-m-d'));

        $columns = ['transport', 'utilities', 'welfare', 'cleaning', 'maintenance', 'remittance', 'others'];
        $totals  = [];
        foreach ($columns as $col) {
            $totals[$col] = $ledgerEntries->sum($col);
        }
        $totals['grand_total'] = array_sum($totals);

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::createFromDate($year, $m, 1)->format('F');
        }

        return view('admin.reports.expense-ledger', compact(
            'groupedEntries', 'totals', 'month', 'year', 'months', 'columns'
        ));
    }
}
