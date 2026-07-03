<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\FuneralContribution;
use App\Services\FuneralService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FuneralReportController extends Controller implements HasMiddleware
{
    public function __construct(private FuneralService $service) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:funeral.view', only: ['spending', 'exportSpending', 'fundSummary']),
        ];
    }

    public function spending(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $year      = $request->get('year');

        if ($year && !$startDate && !$endDate) {
            $startDate = $year . '-01-01';
            $endDate   = $year . '-12-31';
        }

        $summary = $this->service->spendingSummary($startDate, $endDate);

        return view('admin.finance.funeral.reports.spending', compact('summary', 'startDate', 'endDate', 'year'));
    }

    public function exportSpending(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $year      = $request->get('year');

        if ($year && !$startDate && !$endDate) {
            $startDate = $year . '-01-01';
            $endDate   = $year . '-12-31';
        }

        $summary = $this->service->spendingSummary($startDate, $endDate);

        $sym      = SettingHelper::currencySymbol();
        $period   = ($startDate && $endDate) ? "{$startDate} to {$endDate}" : 'All Time';
        $filename = 'Funeral_Spending_Report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($summary, $sym, $period) {
            $fh = fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($fh, ['Funeral Spending Report']);
            fputcsv($fh, ['Period:', $period]);
            fputcsv($fh, ['Generated:', date('d M Y H:i')]);
            fputcsv($fh, []);

            fputcsv($fh, ['SUMMARY']);
            fputcsv($fh, ['Total Amount Donated', number_format($summary['total_main'], 2)]);
            fputcsv($fh, ['Total Other Expenses', number_format($summary['total_expenses'], 2)]);
            fputcsv($fh, ['Grand Total', number_format($summary['grand_total'], 2)]);
            fputcsv($fh, []);

            fputcsv($fh, ['MONTHLY BREAKDOWN']);
            fputcsv($fh, ["Month", "Amount Donated ({$sym})", "Other Expenses ({$sym})", "Total ({$sym})", '# Benefits']);
            foreach ($summary['by_month'] as $row) {
                fputcsv($fh, [
                    $row['label'],
                    number_format($row['main'], 2),
                    number_format($row['other'], 2),
                    number_format($row['total'], 2),
                    $row['count'],
                ]);
            }
            fputcsv($fh, ['TOTAL', number_format($summary['total_main'], 2), number_format($summary['total_expenses'], 2), number_format($summary['grand_total'], 2), '']);

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function fundSummary()
    {
        $fund = $this->service->fundSummary();
        $recentContributions = FuneralContribution::with('member')
            ->orderByDesc('payment_date')
            ->limit(10)
            ->get();

        return view('admin.finance.funeral.reports.fund', compact('fund', 'recentContributions'));
    }
}
