<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use App\Models\WelfareBenefit;
use App\Models\WelfareBenefitExpense;
use App\Models\WelfareContribution;
use App\Models\WelfareRate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo seeder for Welfare module — fills realistic sample data for UI testing.
 *
 * Run:    php artisan db:seed --class=WelfareDemoSeeder
 * Remove: php artisan db:seed --class=WelfareFuneralDemoCleanupSeeder
 *
 * Or remove manually:
 *   WelfareContribution::where('notes','SAMPLE DATA')->forceDelete();
 *   WelfareBenefit::where('description','like','SAMPLE DATA%')->forceDelete();
 *   WelfareRate::where('notes','like','DEMO:%')->delete();
 *   Member::whereNotNull('welfare_start_date')->update(['welfare_start_date'=>null,'welfare_enrolled'=>true]);
 */
class WelfareDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Environment Guard ─────────────────────────────────────────────────
        if (app()->environment('production')) {
            $this->command->error('WelfareDemoSeeder is DEMO-only and must NOT run in production.');
            return;
        }

        // ── Existing Data Guard ───────────────────────────────────────────────
        if (WelfareContribution::count() > 0 || WelfareBenefit::count() > 0) {
            $this->command->warn('Existing welfare data detected — aborting to avoid mixing demo records.');
            $this->command->line('Remove first: php artisan db:seed --class=WelfareFuneralDemoCleanupSeeder');
            return;
        }

        $this->command->info('Seeding welfare demo data...');

        $user    = User::first();
        $members = Member::active()->take(7)->get();

        if ($members->count() < 5) {
            $this->command->error('Need at least 5 active members. Run member seeders first.');
            return;
        }

        // ── Rates — demonstrate the rate-change feature ───────────────────────
        // Older rate: GH₵5 from 3 years ago
        $oldRateDate     = Carbon::now()->subYears(3)->startOfYear();
        $currentRateDate = Carbon::now()->subYear()->startOfYear();

        WelfareRate::firstOrCreate(
            ['effective_from' => $oldRateDate->toDateString()],
            ['amount' => 5.00, 'notes' => 'DEMO: Older welfare rate', 'created_by' => $user->id]
        );
        // Newer rate: GH₵10 from 1 year ago (may already exist from WelfareRateSeeder — that's fine)
        WelfareRate::firstOrCreate(
            ['effective_from' => $currentRateDate->toDateString()],
            ['amount' => 10.00, 'notes' => 'DEMO: Current welfare rate', 'created_by' => $user->id]
        );

        // ── Assign welfare enrollment & start dates to members ────────────────
        // m0: enrolled from scheme start — will be FULLY PAID UP
        $members[0]->update([
            'welfare_enrolled'   => true,
            'welfare_start_date' => $oldRateDate->copy()->toDateString(),
        ]);
        // m1: enrolled 2.5 years ago — will be IN ARREARS (large)
        $members[1]->update([
            'welfare_enrolled'   => true,
            'welfare_start_date' => Carbon::now()->subYears(2)->subMonths(6)->startOfMonth()->toDateString(),
        ]);
        // m2: enrolled 1.5 years ago — IN ARREARS (moderate, only first 8 months paid)
        $members[2]->update([
            'welfare_enrolled'   => true,
            'welfare_start_date' => Carbon::now()->subYears(1)->subMonths(6)->startOfMonth()->toDateString(),
        ]);
        // m3: enrolled 6 months ago — recently enrolled, paid only 2 months
        $members[3]->update([
            'welfare_enrolled'   => true,
            'welfare_start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
        ]);
        // m4: enrolled from scheme start — will be in CREDIT (overpaid)
        $members[4]->update([
            'welfare_enrolled'   => true,
            'welfare_start_date' => $oldRateDate->copy()->toDateString(),
        ]);
        // m5: NOT ENROLLED — tests exemption display
        $members[5]->update([
            'welfare_enrolled'   => false,
            'welfare_start_date' => null,
        ]);
        // m6 (if available): enrolled 2 years ago — SPORADIC payments
        if ($members->count() >= 7) {
            $members[6]->update([
                'welfare_enrolled'   => true,
                'welfare_start_date' => Carbon::now()->subYears(2)->startOfYear()->toDateString(),
            ]);
        }

        // ── Contributions ─────────────────────────────────────────────────────
        // m0: FULLY PAID UP — every month from start to now
        $this->seedMonthlyContributions(
            $members[0], $oldRateDate->copy(), Carbon::now(), $user, 'mobile_money'
        );

        // m1: IN ARREARS — ~60% of months paid (random gaps)
        $this->seedSampledContributions(
            $members[1],
            Carbon::now()->subYears(2)->subMonths(6)->startOfMonth(),
            Carbon::now(),
            $user,
            0.60,
            'cash'
        );

        // m2: IN ARREARS — only the first 8 months paid
        $this->seedFixedContributions(
            $members[2],
            Carbon::now()->subYears(1)->subMonths(6)->startOfMonth(),
            8,
            $user,
            'mobile_money'
        );

        // m3: recently enrolled, paid 2 of 6 months
        $this->seedFixedContributions(
            $members[3],
            Carbon::now()->subMonths(6)->startOfMonth(),
            2,
            $user,
            'cash'
        );

        // m4: CREDIT — every month paid, plus one advance
        $this->seedMonthlyContributions(
            $members[4], $oldRateDate->copy(), Carbon::now(), $user, 'bank_transfer'
        );
        WelfareContribution::create([
            'member_id'      => $members[4]->id,
            'amount'         => 30.00,
            'period_month'   => Carbon::now()->addMonths(1)->month,
            'period_year'    => Carbon::now()->addMonths(1)->year,
            'payment_date'   => Carbon::now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'received_by'    => $user->id,
            'notes'          => 'SAMPLE DATA',
        ]);

        // m6: SPORADIC — some months missed
        if ($members->count() >= 7) {
            $this->seedSporadicContributions(
                $members[6],
                Carbon::now()->subYears(2)->startOfYear(),
                $user
            );
        }

        // ── Welfare Benefits with itemised expense lines ───────────────────────
        DB::transaction(function () use ($members, $user) {
            // 1. Childbirth — main GH₵200, two expense lines, ~14 months ago (last year)
            $b1 = WelfareBenefit::create([
                'member_id'       => $members[0]->id,
                'benefactor_name' => $members[0]->full_name,
                'purpose'         => 'childbirth',
                'benefit_date'    => Carbon::now()->subMonths(14)->toDateString(),
                'benefit_year'    => Carbon::now()->subMonths(14)->year,
                'amount'          => 200.00,
                'description'     => 'SAMPLE DATA — Childbirth welfare support',
                'recorded_by'     => $user->id,
            ]);
            WelfareBenefitExpense::create(['welfare_benefit_id' => $b1->id, 'description' => 'Transport',    'amount' => 30.00]);
            WelfareBenefitExpense::create(['welfare_benefit_id' => $b1->id, 'description' => 'Gift items',   'amount' => 50.00]);

            // 2. Marriage — main GH₵300, NO expense lines (tests zero-expense path)
            WelfareBenefit::create([
                'member_id'       => $members[1]->id,
                'benefactor_name' => $members[1]->full_name,
                'purpose'         => 'marriage',
                'benefit_date'    => Carbon::now()->subMonths(20)->toDateString(),
                'benefit_year'    => Carbon::now()->subMonths(20)->year,
                'amount'          => 300.00,
                'description'     => 'SAMPLE DATA — Marriage welfare support',
                'recorded_by'     => $user->id,
            ]);

            // 3. Funeral — main GH₵250, one expense line, ~8 months ago (different month)
            $b3 = WelfareBenefit::create([
                'member_id'       => $members[2]->id,
                'benefactor_name' => $members[2]->full_name,
                'purpose'         => 'funeral',
                'benefit_date'    => Carbon::now()->subMonths(8)->toDateString(),
                'benefit_year'    => Carbon::now()->subMonths(8)->year,
                'amount'          => 250.00,
                'description'     => 'SAMPLE DATA — Funeral welfare support',
                'recorded_by'     => $user->id,
            ]);
            WelfareBenefitExpense::create(['welfare_benefit_id' => $b3->id, 'description' => 'Wreath', 'amount' => 40.00]);
        });

        $this->command->info('✓ Welfare demo data seeded.');
        $this->command->line('  Members used (ids): ' . $members->pluck('id')->join(', '));
        $this->command->line('  Remove: php artisan db:seed --class=WelfareFuneralDemoCleanupSeeder');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Seed one contribution per month for every month in range at the effective rate. */
    private function seedMonthlyContributions(Member $member, Carbon $from, Carbon $to, User $user, string $method): void
    {
        $current = $from->copy()->startOfMonth();
        $end     = $to->copy()->startOfMonth();

        while ($current->lte($end)) {
            WelfareContribution::create([
                'member_id'      => $member->id,
                'amount'         => $this->rateAmountFor($current),
                'period_month'   => $current->month,
                'period_year'    => $current->year,
                'payment_date'   => $current->copy()->addDays(rand(1, 12))->toDateString(),
                'payment_method' => $method,
                'received_by'    => $user->id,
                'notes'          => 'SAMPLE DATA',
            ]);
            $current->addMonth();
        }
    }

    /** Seed contributions for a random ~$ratio fraction of the months in range. */
    private function seedSampledContributions(Member $member, Carbon $from, Carbon $to, User $user, float $ratio, string $method): void
    {
        $current = $from->copy()->startOfMonth();
        $end     = $to->copy()->startOfMonth();

        while ($current->lte($end)) {
            if ((mt_rand(1, 100) / 100) <= $ratio) {
                WelfareContribution::create([
                    'member_id'      => $member->id,
                    'amount'         => $this->rateAmountFor($current),
                    'period_month'   => $current->month,
                    'period_year'    => $current->year,
                    'payment_date'   => $current->copy()->addDays(rand(1, 18))->toDateString(),
                    'payment_method' => $method,
                    'received_by'    => $user->id,
                    'notes'          => 'SAMPLE DATA',
                ]);
            }
            $current->addMonth();
        }
    }

    /** Seed exactly $count consecutive monthly contributions starting from $from. */
    private function seedFixedContributions(Member $member, Carbon $from, int $count, User $user, string $method): void
    {
        $current = $from->copy()->startOfMonth();
        for ($i = 0; $i < $count; $i++) {
            WelfareContribution::create([
                'member_id'      => $member->id,
                'amount'         => $this->rateAmountFor($current),
                'period_month'   => $current->month,
                'period_year'    => $current->year,
                'payment_date'   => $current->copy()->addDays(rand(1, 10))->toDateString(),
                'payment_method' => $method,
                'received_by'    => $user->id,
                'notes'          => 'SAMPLE DATA',
            ]);
            $current->addMonth();
        }
    }

    /** Seed contributions with deliberate gaps (months 2, 5, 9, 12, 16 skipped). */
    private function seedSporadicContributions(Member $member, Carbon $from, User $user): void
    {
        $skipOffsets = [2, 5, 9, 12, 16, 20];
        $current     = $from->copy()->startOfMonth();
        $end         = Carbon::now()->startOfMonth();
        $i           = 0;

        while ($current->lte($end)) {
            if (!in_array($i, $skipOffsets)) {
                WelfareContribution::create([
                    'member_id'      => $member->id,
                    'amount'         => $this->rateAmountFor($current),
                    'period_month'   => $current->month,
                    'period_year'    => $current->year,
                    'payment_date'   => $current->copy()->addDays(rand(1, 20))->toDateString(),
                    'payment_method' => 'cash',
                    'received_by'    => User::first()->id,
                    'notes'          => 'SAMPLE DATA',
                ]);
            }
            $current->addMonth();
            $i++;
        }
    }

    /** Return the welfare rate amount effective for the given month. */
    private function rateAmountFor(Carbon $month): float
    {
        $rate = WelfareRate::where('effective_from', '<=', $month->copy()->startOfMonth())
            ->orderByDesc('effective_from')
            ->first();
        return $rate ? (float) $rate->amount : 10.00;
    }
}
