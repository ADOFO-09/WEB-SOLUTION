<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SettingHelper;
use App\Models\Member;
use App\Models\WelfareBenefit;
use App\Models\WelfareContribution;
use App\Models\WelfareRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WelfareMemberController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:welfare.view'),
        ];
    }

    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        $members = Member::query()
            ->where(function ($q) {
                $q->where('welfare_enrolled', true)
                  ->orWhereHas('welfareContributions');
            })
            ->when($search, fn($q) => $q->where(fn($q2) =>
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name', 'like', "%{$search}%")
                   ->orWhere('member_id', 'like', "%{$search}%")
            ))
            ->withCount(['welfareContributions', 'welfareBenefits'])
            ->withSum('welfareContributions as total_paid', 'amount')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();

        return view('admin.finance.welfare.members.index', compact('members', 'search'));
    }

    public function show(Member $member)
    {
        // Load all rates once — sorted desc so first() returns most recent applicable rate
        $allRates = WelfareRate::orderByDesc('effective_from')->get();

        $rateFor = function (int $month, int $year) use ($allRates): ?float {
            $cut = Carbon::create($year, $month, 1)->startOfDay();
            $r   = $allRates->first(fn($r) => $r->effective_from->lte($cut));
            return $r ? (float) $r->amount : null;
        };

        // Load all contributions at once
        $allContribs = WelfareContribution::where('member_id', $member->id)
            ->get()
            ->groupBy('period_year');

        $years = $allContribs->keys()
            ->merge([now()->year])
            ->unique()->sortDesc()->values();

        $yearlyData = [];
        foreach ($years as $yr) {
            $byMonth  = $allContribs->get($yr, collect())->keyBy('period_month');
            $expected = 0;
            $months   = [];

            for ($m = 1; $m <= 12; $m++) {
                $rate   = $rateFor($m, $yr);
                $contrib = $byMonth->get($m);
                $expected += $rate ?? 0;
                $months[$m] = [
                    'rate'    => $rate,
                    'paid'    => $contrib ? (float) $contrib->amount : null,
                    'contrib' => $contrib,
                ];
            }

            $paid    = (float) $byMonth->sum('amount');
            $balance = $expected - $paid; // positive = owes (arrears), negative = credit

            $yearlyData[] = compact('yr', 'months', 'expected', 'paid', 'balance');
        }

        $benefits = WelfareBenefit::with('welfareBenefitExpenses')
            ->where('member_id', $member->id)
            ->orderByDesc('benefit_date')
            ->get();

        $totalContributions = (float) WelfareContribution::where('member_id', $member->id)->sum('amount');
        $totalBenefits      = $benefits->sum(fn($b) => (float) $b->total_cost);
        $purposes           = WelfareBenefit::PURPOSES;

        return view('admin.finance.welfare.members.show',
            compact('member', 'yearlyData', 'benefits', 'totalContributions', 'totalBenefits', 'purposes'));
    }
}
