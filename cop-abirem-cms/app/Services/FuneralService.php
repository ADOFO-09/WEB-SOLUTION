<?php

namespace App\Services;

use App\Models\Member;
use App\Models\FuneralRate;
use App\Models\FuneralContribution;
use App\Models\FuneralBenefit;
use App\Models\FuneralBenefitExpense;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FuneralService
{
    /**
     * Find the applicable rate for a given month/year from a pre-loaded collection.
     */
    private function rateForPeriodFromCollection(int $month, int $year, Collection $rates): ?FuneralRate
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        return $rates
            ->filter(fn($r) => $r->effective_from->lte($periodStart))
            ->last();
    }

    /**
     * Build a member's balance array using pre-loaded rates and a pre-resolved paid amount.
     */
    private function buildBalance(Member $member, Collection $allRates, float $paid): array
    {
        if (!$member->funeral_enrolled) {
            return ['expected' => 0, 'paid' => 0, 'balance' => 0, 'months' => [], 'enrolled' => false];
        }

        $startDate = $member->funeral_start_date
            ?? $allRates->first()?->effective_from
            ?? $member->date_joined
            ?? Carbon::now()->startOfYear();

        $start = Carbon::parse($startDate)->startOfMonth();
        $now   = Carbon::now()->startOfMonth();

        if ($start->gt($now)) {
            return ['expected' => 0, 'paid' => $paid, 'balance' => round(-$paid, 2), 'months' => [], 'enrolled' => true];
        }

        $expected = 0.0;
        $months   = [];
        $current  = $start->copy();

        while ($current->lte($now)) {
            $rate          = $this->rateForPeriodFromCollection($current->month, $current->year, $allRates);
            $monthExpected = $rate ? (float) $rate->amount : 0.0;
            $expected     += $monthExpected;
            $months[]      = [
                'year'     => $current->year,
                'month'    => $current->month,
                'label'    => $current->format('M Y'),
                'expected' => $monthExpected,
                'rate_id'  => $rate?->id,
            ];
            $current->addMonth();
        }

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
     * Calculate balances for ALL enrolled members in 2 queries total.
     */
    public function calculateAllBalances(Collection $members): Collection
    {
        $allRates = FuneralRate::orderBy('effective_from')->get();

        $memberIds    = $members->pluck('id');
        $paidByMember = FuneralContribution::whereIn('member_id', $memberIds)
            ->select('member_id', DB::raw('SUM(amount) as total'))
            ->groupBy('member_id')
            ->pluck('total', 'member_id');

        return $members->map(function (Member $member) use ($allRates, $paidByMember) {
            $paid = (float) ($paidByMember[$member->id] ?? 0);
            $data = $this->buildBalance($member, $allRates, $paid);
            return array_merge(['member' => $member], $data);
        });
    }

    /**
     * Calculate a single member's funeral dues balance (kept for individual lookups).
     */
    public function calculateMemberBalance(Member $member): array
    {
        $allRates = FuneralRate::orderBy('effective_from')->get();
        $paid     = (float) FuneralContribution::where('member_id', $member->id)->sum('amount');
        return $this->buildBalance($member, $allRates, $paid);
    }

    /**
     * Summary of funeral spending within a date range using SQL GROUP BY.
     */
    public function spendingSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $benefitsQuery = FuneralBenefit::query()
            ->when($startDate, fn($q) => $q->where('funeral_date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('funeral_date', '<=', $endDate));

        $byMonthBenefits = (clone $benefitsQuery)
            ->selectRaw("DATE_FORMAT(funeral_date, '%Y-%m') as period_key,
                          DATE_FORMAT(funeral_date, '%b %Y') as label,
                          SUM(amount_donated) as main_total,
                          COUNT(*) as cnt")
            ->groupBy('period_key', 'label')
            ->orderBy('period_key')
            ->get()
            ->keyBy('period_key');

        $byMonthExpenses = FuneralBenefitExpense::join('funeral_benefits', 'funeral_benefit_expenses.funeral_benefit_id', '=', 'funeral_benefits.id')
            ->when($startDate, fn($q) => $q->where('funeral_benefits.funeral_date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('funeral_benefits.funeral_date', '<=', $endDate))
            ->whereNull('funeral_benefits.deleted_at')
            ->selectRaw("DATE_FORMAT(funeral_benefits.funeral_date, '%Y-%m') as period_key, SUM(funeral_benefit_expenses.amount) as other_total")
            ->groupBy('period_key')
            ->pluck('other_total', 'period_key');

        $byMonth   = [];
        $totalMain = 0.0;
        $totalExp  = 0.0;

        foreach ($byMonthBenefits as $key => $row) {
            $main  = (float) $row->main_total;
            $other = (float) ($byMonthExpenses[$key] ?? 0);
            $byMonth[] = [
                'label' => $row->label,
                'main'  => $main,
                'other' => $other,
                'total' => $main + $other,
                'count' => (int) $row->cnt,
            ];
            $totalMain += $main;
            $totalExp  += $other;
        }

        return [
            'by_month'       => $byMonth,
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
        $collected    = (float) FuneralContribution::sum('amount');
        $benefitMain  = (float) FuneralBenefit::sum('amount_donated');
        $benefitOther = (float) FuneralBenefitExpense::sum('amount');
        $totalPaid    = round($benefitMain + $benefitOther, 2);

        return [
            'collected' => round($collected, 2),
            'paid_out'  => $totalPaid,
            'balance'   => round($collected - $totalPaid, 2),
        ];
    }
}
