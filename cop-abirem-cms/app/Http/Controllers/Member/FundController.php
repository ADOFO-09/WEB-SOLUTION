<?php

namespace App\Http\Controllers\Member;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\WelfareContribution;
use App\Models\FuneralContribution;
use App\Models\WelfareBenefit;
use App\Models\FuneralBenefit;
use Illuminate\Http\Request;

class FundController extends Controller
{
    public function welfare(Request $request)
    {
        $member = $request->user()->member;
        $year   = (int) $request->input('year', now()->year);

        $contributions = WelfareContribution::where('member_id', $member->id)
            ->where('period_year', $year)
            ->orderByDesc('period_month')
            ->orderByDesc('payment_date')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();

        $totalPaid = WelfareContribution::where('member_id', $member->id)
            ->where('period_year', $year)
            ->sum('amount');

        $allTimePaid = WelfareContribution::where('member_id', $member->id)->sum('amount');

        $benefits = WelfareBenefit::where('member_id', $member->id)
            ->orderByDesc('benefit_date')
            ->get();

        $totalBenefits = $benefits->sum('amount');

        $years = range(now()->year, now()->year - 5);

        return view('member.welfare.index', compact(
            'member', 'contributions', 'totalPaid', 'allTimePaid',
            'benefits', 'totalBenefits', 'year', 'years'
        ));
    }

    public function funeral(Request $request)
    {
        $member = $request->user()->member;
        $year   = (int) $request->input('year', now()->year);

        $contributions = FuneralContribution::where('member_id', $member->id)
            ->where('period_year', $year)
            ->orderByDesc('period_month')
            ->orderByDesc('payment_date')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();

        $totalPaid = FuneralContribution::where('member_id', $member->id)
            ->where('period_year', $year)
            ->sum('amount');

        $allTimePaid = FuneralContribution::where('member_id', $member->id)->sum('amount');

        $benefits = FuneralBenefit::where('member_id', $member->id)
            ->orderByDesc('funeral_date')
            ->get();

        $totalBenefits = $benefits->sum('amount_donated');

        $years = range(now()->year, now()->year - 5);

        return view('member.funeral.index', compact(
            'member', 'contributions', 'totalPaid', 'allTimePaid',
            'benefits', 'totalBenefits', 'year', 'years'
        ));
    }
}
