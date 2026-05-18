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

        $data    = $this->buildLedger($month, $year);
        $months  = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::createFromDate($year, $m, 1)->format('F');
        }

        return view('admin.reports.expense-ledger', array_merge($data, compact('month', 'year', 'months')));
    }

    public function export(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);
        $label = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F_Y');

        $data     = $this->buildLedger($month, $year);
        $columns  = ['transport', 'utilities', 'welfare', 'cleaning', 'maintenance', 'remittance', 'others'];
        $colLabels = ['TRANSPORT', 'UTILITIES', 'WELFARE', 'CLEANING', 'MAINT.', 'REMIT.', 'OTHERS'];

        $filename = "Expense_Ledger_{$label}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $columns, $colLabels, $month, $year) {
            $fh = fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            $monthLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');
            fputcsv($fh, ["COP ABIREM — Expense Ledger — {$monthLabel}"]);
            fputcsv($fh, []);

            fputcsv($fh, array_merge(['DATE', 'PARTICULARS'], $colLabels, ['TOTAL']));

            foreach ($data['groupedEntries'] as $dateKey => $entries) {
                $count   = $entries->count();
                $isMulti = $count > 1;
                $dateLabel = \Carbon\Carbon::parse($dateKey)->format('d/m/Y');

                foreach ($entries as $i => $entry) {
                    $rowTotal = 0;
                    foreach ($columns as $col) $rowTotal += $entry[$col];
                    $isVoided = ($entry['ledger_status'] ?? 'active') === 'voided';
                    $particular = $entry['particular'];
                    if ($isVoided) $particular .= ' [VOID]';
                    if ($entry['is_adjustment']) $particular .= ' [ADJ]';

                    $row = [$i === 0 ? $dateLabel : '', $particular];
                    foreach ($columns as $col) {
                        $row[] = $entry[$col] > 0 ? number_format($entry[$col], 2) : '';
                    }
                    $row[] = $isMulti ? '' : number_format($rowTotal, 2);
                    fputcsv($fh, $row);
                }

                if ($isMulti) {
                    $dayCols = [];
                    foreach ($columns as $col) {
                        $dayCols[$col] = $entries->sum($col);
                    }
                    $dayTotal = array_sum($dayCols);
                    $row = ['', 'Day Total'];
                    foreach ($columns as $col) {
                        $row[] = $dayCols[$col] > 0 ? number_format($dayCols[$col], 2) : '';
                    }
                    $row[] = number_format($dayTotal, 2);
                    fputcsv($fh, $row);
                }
            }

            $t = $data['totals'];
            fputcsv($fh, []);
            $row = ['', 'MONTHLY TOTAL'];
            foreach ($columns as $col) {
                $row[] = number_format($t[$col], 2);
            }
            $row[] = number_format($t['grand_total'], 2);
            fputcsv($fh, $row);

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildLedger(int $month, int $year): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $expenses = Expense::with('expenseCategory')
            ->whereIn('status', ['approved', 'paid'])
            ->whereBetween('expense_date', [$start, $end])
            ->orderBy('expense_date')
            ->get();

        $ledgerEntries = $expenses->filter(fn($e) => !$e->isAdjusted())->map(function ($expense) {
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

        $groupedEntries = $ledgerEntries
            ->sortBy('date')
            ->groupBy(fn($e) => Carbon::parse($e['date'])->format('Y-m-d'));

        $columns = ['transport', 'utilities', 'welfare', 'cleaning', 'maintenance', 'remittance', 'others'];
        $totals  = [];
        foreach ($columns as $col) {
            $totals[$col] = $ledgerEntries->sum($col);
        }
        $totals['grand_total'] = array_sum($totals);

        return compact('groupedEntries', 'totals', 'columns');
    }
}
