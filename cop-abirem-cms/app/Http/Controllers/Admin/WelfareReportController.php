<?php

namespace App\Http\Controllers\Admin;

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
            new Middleware('permission:welfare.view', only: ['balances', 'spending', 'fundSummary']),
        ];
    }

    public function balances(Request $request)
    {
        $members = Member::where('welfare_enrolled', true)
            ->orderBy('first_name')
            ->get();

        $balances = $members->map(function ($member) {
            $data = $this->service->calculateMemberBalance($member);
            return array_merge(['member' => $member], $data);
        });

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
