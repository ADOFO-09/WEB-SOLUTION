<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\WelfareContribution;
use App\Services\WelfareService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WelfareReportController extends Controller implements HasMiddleware
{
    public function __construct(private WelfareService $service) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:welfare.view', only: ['balances', 'spending', 'exportSpending', 'fundSummary']),
        ];
    }

    public function balances(Request $request)
    {
        $members = Member::where('welfare_enrolled', true)
            ->orderBy('first_name')
            ->get();

        $balances = $this->service->calculateAllBalances($members);

        // Filter by status
        $status = $request->get('status', 'all');
        if ($status === 'arrears') {
            $balances = $balances->filter(fn($b) => ($b['balance'] ?? 0) > 0);
        } elseif ($status === 'credit') {
            $balances = $balances->filter(fn($b) => ($b['balance'] ?? 0) < 0);
        } elseif ($status === 'paid') {
            $balances = $balances->filter(fn($b) => ($b['balance'] ?? 0) === 0.0 || ($b['balance'] ?? 0) === 0);
        }

        return view('admin.finance.welfare.reports.balances', compact('balances', 'status'));
    }

    public function spending(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $year      = $request->get('year');
        $purpose   = $request->get('purpose');

        if ($year && !$startDate && !$endDate) {
            $startDate = $year . '-01-01';
            $endDate   = $year . '-12-31';
        }

        $summary = $this->service->spendingSummary($startDate, $endDate);

        // Filter by purpose if provided
        if ($purpose) {
            $summary['by_purpose'] = array_values(array_filter($summary['by_purpose'], fn($p) => ($p['label'] === \App\Models\WelfareBenefit::PURPOSES[$purpose] ?? $purpose)));
        }

        return view('admin.finance.welfare.reports.spending', compact('summary', 'startDate', 'endDate', 'year', 'purpose'));
    }

    public function exportSpending(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $year      = $request->get('year');
        $purpose   = $request->get('purpose');

        if ($year && !$startDate && !$endDate) {
            $startDate = $year . '-01-01';
            $endDate   = $year . '-12-31';
        }

        $summary = $this->service->spendingSummary($startDate, $endDate);

        if ($purpose) {
            $summary['by_purpose'] = array_values(array_filter($summary['by_purpose'], fn($p) => ($p['label'] === \App\Models\WelfareBenefit::PURPOSES[$purpose] ?? $purpose)));
        }

        $sym      = SettingHelper::currencySymbol();
        $period   = ($startDate && $endDate) ? "{$startDate} to {$endDate}" : 'All Time';
        $filename = 'Welfare_Spending_Report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($summary, $sym, $period) {
            $fh = fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($fh, ['Welfare Spending Report']);
            fputcsv($fh, ['Period:', $period]);
            fputcsv($fh, ['Generated:', date('d M Y H:i')]);
            fputcsv($fh, []);

            fputcsv($fh, ['SUMMARY']);
            fputcsv($fh, ['Total Main Benefits', number_format($summary['total_main'], 2)]);
            fputcsv($fh, ['Total Other Expenses', number_format($summary['total_expenses'], 2)]);
            fputcsv($fh, ['Grand Total', number_format($summary['grand_total'], 2)]);
            fputcsv($fh, []);

            fputcsv($fh, ['MONTHLY BREAKDOWN']);
            fputcsv($fh, ["Month", "Main Benefits ({$sym})", "Other Expenses ({$sym})", "Total ({$sym})", '# Benefits']);
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
            fputcsv($fh, []);

            fputcsv($fh, ['BY PURPOSE']);
            fputcsv($fh, ["Purpose", "Main Benefits ({$sym})", "Other Expenses ({$sym})", "Total ({$sym})", '# Benefits']);
            foreach ($summary['by_purpose'] as $row) {
                fputcsv($fh, [
                    $row['label'],
                    number_format($row['main'], 2),
                    number_format($row['other'], 2),
                    number_format($row['total'], 2),
                    $row['count'],
                ]);
            }

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function fundSummary()
    {
        $fund = $this->service->fundSummary();
        $recentContributions = WelfareContribution::with('member')
            ->orderByDesc('payment_date')
            ->limit(10)
            ->get();

        return view('admin.finance.welfare.reports.fund', compact('fund', 'recentContributions'));
    }
}
