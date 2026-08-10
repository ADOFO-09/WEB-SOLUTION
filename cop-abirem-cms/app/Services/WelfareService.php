<?php

namespace App\Services;

use App\Models\Member;
use App\Models\WelfareRate;
use App\Models\WelfareContribution;
use App\Models\WelfareBenefit;
use App\Models\WelfareBenefitExpense;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WelfareService
{
    /**
     * Find the applicable rate for a given month/year from a pre-loaded collection.
     * Uses the most recent rate whose effective_from <= first day of that month.
     */
    private function rateForPeriodFromCollection(int $month, int $year, Collection $rates): ?WelfareRate
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        return $rates
            ->filter(fn($r) => $r->effective_from->lte($periodStart))
            ->last();
    }

    /**
     * Build a member's balance array using pre-loaded rates and a pre-resolved paid amount.
     * Used by calculateAllBalances() — avoids any DB queries inside the loop.
     */
    private function buildBalance(Member $member, Collection $allRates, float $paid): array
    {
        if (!$member->welfare_enrolled) {
            return ['expected' => 0, 'paid' => 0, 'balance' => 0, 'months' => [], 'enrolled' => false];
        }

        $startDate = $member->welfare_start_date
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
     * Calculate balances for ALL enrolled members in 2 queries total (rates + contributions).
     * Returns a Collection of arrays, each merged with the member object.
     */
    public function calculateAllBalances(Collection $members): Collection
    {
        // 1 query: all rates ordered ascending so rateForPeriodFromCollection can use ->last()
        $allRates = WelfareRate::orderBy('effective_from')->get();

        // 1 query: sum contributions per member
        $memberIds = $members->pluck('id');
        $paidByMember = WelfareContribution::whereIn('member_id', $memberIds)
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
     * Calculate a single member's welfare balance (kept for individual lookups).
     * Loads rates and contribution in 2 queries.
     */
    public function calculateMemberBalance(Member $member): array
    {
        $allRates = WelfareRate::orderBy('effective_from')->get();
        $paid     = (float) WelfareContribution::where('member_id', $member->id)->sum('amount');
        return $this->buildBalance($member, $allRates, $paid);
    }

    /**
     * Summary of welfare spending within a date range.
     * Uses SQL GROUP BY — no PHP aggregation loop over rows.
     */
    public function spendingSummary(?string $startDate = null, ?string $endDate = null): array
    {
        // Totals from benefits table
        $benefitsQuery = WelfareBenefit::query()
            ->when($startDate, fn($q) => $q->where('benefit_date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('benefit_date', '<=', $endDate));

        // By month — benefits amount grouped
        $byMonthBenefits = (clone $benefitsQuery)
            ->selectRaw("DATE_FORMAT(benefit_date, '%Y-%m') as period_key,
                          DATE_FORMAT(benefit_date, '%b %Y') as label,
                          SUM(amount) as main_total,
                          COUNT(*) as cnt")
            ->groupBy('period_key', 'label')
            ->orderBy('period_key')
            ->get()
            ->keyBy('period_key');

        // By month — extra expenses amount (left join to expenses table)
        $byMonthExpenses = WelfareBenefitExpense::join('welfare_benefits', 'welfare_benefit_expenses.welfare_benefit_id', '=', 'welfare_benefits.id')
            ->when($startDate, fn($q) => $q->where('welfare_benefits.benefit_date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('welfare_benefits.benefit_date', '<=', $endDate))
            ->whereNull('welfare_benefits.deleted_at')
            ->selectRaw("DATE_FORMAT(welfare_benefits.benefit_date, '%Y-%m') as period_key, SUM(welfare_benefit_expenses.amount) as other_total")
            ->groupBy('period_key')
            ->pluck('other_total', 'period_key');

        // By purpose
        $byPurposeBenefits = (clone $benefitsQuery)
            ->selectRaw('purpose, SUM(amount) as main_total, COUNT(*) as cnt')
            ->groupBy('purpose')
            ->get()
            ->keyBy('purpose');

        $byPurposeExpenses = WelfareBenefitExpense::join('welfare_benefits', 'welfare_benefit_expenses.welfare_benefit_id', '=', 'welfare_benefits.id')
            ->when($startDate, fn($q) => $q->where('welfare_benefits.benefit_date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('welfare_benefits.benefit_date', '<=', $endDate))
            ->whereNull('welfare_benefits.deleted_at')
            ->selectRaw('welfare_benefits.purpose, SUM(welfare_benefit_expenses.amount) as other_total')
            ->groupBy('welfare_benefits.purpose')
            ->pluck('other_total', 'welfare_benefits.purpose');

        // Build by_month array
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

        // Build by_purpose array
        $byPurpose = [];
        foreach ($byPurposeBenefits as $purposeKey => $row) {
            $main  = (float) $row->main_total;
            $other = (float) ($byPurposeExpenses[$purposeKey] ?? 0);
            $byPurpose[] = [
                'label' => WelfareBenefit::PURPOSES[$purposeKey] ?? $purposeKey,
                'main'  => $main,
                'other' => $other,
                'total' => $main + $other,
                'count' => (int) $row->cnt,
            ];
        }

        return [
            'by_month'       => $byMonth,
            'by_purpose'     => $byPurpose,
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
