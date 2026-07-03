<?php

namespace App\Http\Controllers\Admin;

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
            new Middleware('permission:funeral.view', only: ['spending', 'fundSummary']),
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
