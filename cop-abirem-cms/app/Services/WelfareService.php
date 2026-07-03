<?php

namespace App\Services;

use App\Models\Member;
use App\Models\WelfareRate;
use App\Models\WelfareContribution;
use App\Models\WelfareBenefit;
use App\Models\WelfareBenefitExpense;
use Carbon\Carbon;

class WelfareService
{
    /**
     * Return the expected rate for a given month/year.
     * Uses the welfare_rates row with the latest effective_from <= first day of that month.
     * Returns null if no rate applies yet.
     */
    public function rateForPeriod(int $month, int $year): ?WelfareRate
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        return WelfareRate::where('effective_from', '<=', $periodStart)
            ->orderByDesc('effective_from')
            ->first();
    }

    /**
     * Calculate a member's welfare balance.
     * Walks each month from member.welfare_start_date (or earliest rate effective_from, or join date)
     * to the current month. For each month: expected += rateForPeriod. paid = sum of contributions.
     * Returns ['expected'=>float,'paid'=>float,'balance'=>float,'months'=>[...]]
     * balance > 0 means arrears, balance < 0 means credit.
     */
    public function calculateMemberBalance(Member $member): array
    {
        if (!$member->welfare_enrolled) {
            return ['expected' => 0, 'paid' => 0, 'balance' => 0, 'months' => [], 'enrolled' => false];
        }

        // Determine start month
        $startDate = $member->welfare_start_date
            ?? WelfareRate::orderBy('effective_from')->first()?->effective_from
            ?? $member->date_joined
            ?? Carbon::now()->startOfYear();

        $start = Carbon::parse($startDate)->startOfMonth();
        $now   = Carbon::now()->startOfMonth();

        if ($start->gt($now)) {
            return ['expected' => 0, 'paid' => 0, 'balance' => 0, 'months' => [], 'enrolled' => true];
        }

        $expected = 0.0;
        $months   = [];
        $current  = $start->copy();

        while ($current->lte($now)) {
            $rate = $this->rateForPeriod($current->month, $current->year);
            $monthExpected = $rate ? (float) $rate->amount : 0.0;
            $expected += $monthExpected;
            $months[] = [
                'year'     => $current->year,
                'month'    => $current->month,
                'label'    => $current->format('M Y'),
                'expected' => $monthExpected,
                'rate_id'  => $rate?->id,
            ];
            $current->addMonth();
        }

        $paid = (float) WelfareContribution::where('member_id', $member->id)->sum('amount');
        $balance = round($expected - $paid, 2);

        return [
            'enrolled' => true,
            'expected' => round($expected, 2),
            'paid'     => $paid,
            'balance'  => $balance,
            'months'   => $months,
        ];
    }

    /**
     * Summary of welfare spending within a date range.
     * Returns ['by_month'=>[...],'by_purpose'=>[...],'total_main'=>float,'total_expenses'=>float,'grand_total'=>float]
     */
    public function spendingSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = WelfareBenefit::with('welfareBenefitExpenses');
        if ($startDate) $query->where('benefit_date', '>=', $startDate);
        if ($endDate)   $query->where('benefit_date', '<=', $endDate);
        $benefits = $query->get();

        $byMonth   = [];
        $byPurpose = [];
        $totalMain = 0.0;
        $totalExp  = 0.0;

        foreach ($benefits as $b) {
            $key    = $b->benefit_date->format('Y-m');
            $label  = $b->benefit_date->format('M Y');
            $main   = (float) $b->amount;
            $other  = (float) $b->total_other_expenses;
            $total  = $main + $other;

            $byMonth[$key] = $byMonth[$key] ?? ['label' => $label, 'main' => 0, 'other' => 0, 'total' => 0, 'count' => 0];
            $byMonth[$key]['main']  += $main;
            $byMonth[$key]['other'] += $other;
            $byMonth[$key]['total'] += $total;
            $byMonth[$key]['count']++;

            $p = $b->purpose;
            $byPurpose[$p] = $byPurpose[$p] ?? ['label' => WelfareBenefit::PURPOSES[$p] ?? $p, 'main' => 0, 'other' => 0, 'total' => 0, 'count' => 0];
            $byPurpose[$p]['main']  += $main;
            $byPurpose[$p]['other'] += $other;
            $byPurpose[$p]['total'] += $total;
            $byPurpose[$p]['count']++;

            $totalMain += $main;
            $totalExp  += $other;
        }

        ksort($byMonth);

        return [
            'by_month'       => array_values($byMonth),
            'by_purpose'     => array_values($byPurpose),
            'total_main'     => round($totalMain, 2),
            'total_expenses' => round($totalExp, 2),
            'grand_total'    => round($totalMain + $totalExp, 2),
        ];
    }

    /**
     * Fund summary: total collected, total paid out (benefits), balance.
     */
    public function fundSummary(): array
    {
        $collected    = (float) WelfareContribution::sum('amount');
        $benefitMain  = (float) WelfareBenefit::sum('amount');
        $benefitOther = (float) WelfareBenefitExpense::sum('amount');
        $totalPaid    = round($benefitMain + $benefitOther, 2);

        return [
            'collected' => round($collected, 2),
            'paid_out'  => $totalPaid,
            'balance'   => round($collected - $totalPaid, 2),
        ];
    }
}
