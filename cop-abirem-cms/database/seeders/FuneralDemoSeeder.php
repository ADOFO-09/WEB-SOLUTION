<?php

namespace Database\Seeders;

use App\Models\FuneralBenefit;
use App\Models\FuneralBenefitExpense;
use App\Models\FuneralContribution;
use App\Models\FuneralRate;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo seeder for Funeral Dues module — fills realistic sample data for UI testing.
 *
 * Run:    php artisan db:seed --class=FuneralDemoSeeder
 * Remove: php artisan db:seed --class=WelfareFuneralDemoCleanupSeeder
 *
 * Or remove manually:
 *   FuneralContribution::where('notes','SAMPLE DATA')->forceDelete();
 *   FuneralBenefit::where('description','like','SAMPLE DATA%')->forceDelete();
 *   FuneralRate::where('notes','like','DEMO:%')->delete();
 *   Member::whereNotNull('funeral_start_date')->update(['funeral_start_date'=>null,'funeral_enrolled'=>true]);
 */
class FuneralDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Environment Guard ─────────────────────────────────────────────────
        if (app()->environment('production')) {
            $this->command->error('FuneralDemoSeeder is DEMO-only and must NOT run in production.');
            return;
        }

        // ── Existing Data Guard ───────────────────────────────────────────────
        if (FuneralContribution::count() > 0 || FuneralBenefit::count() > 0) {
            $this->command->warn('Existing funeral data detected — aborting to avoid mixing demo records.');
            $this->command->line('Remove first: php artisan db:seed --class=WelfareFuneralDemoCleanupSeeder');
            return;
        }

        $this->command->info('Seeding funeral demo data...');

        $user    = User::first();
        $members = Member::active()->take(7)->get();

        if ($members->count() < 5) {
            $this->command->error('Need at least 5 active members. Run member seeders first.');
            return;
        }

        // ── Rates — demonstrate the rate-change feature ───────────────────────
        $oldRateDate     = Carbon::now()->subYears(3)->startOfYear();
        $currentRateDate = Carbon::now()->subYear()->startOfYear();

        // Older rate: GH₵3 from 3 years ago
        FuneralRate::firstOrCreate(
            ['effective_from' => $oldRateDate->toDateString()],
            ['amount' => 3.00, 'notes' => 'DEMO: Older funeral rate', 'created_by' => $user->id]
        );
        // Current rate: GH₵5 from 1 year ago (may already exist from FuneralRateSeeder)
        FuneralRate::firstOrCreate(
            ['effective_from' => $currentRateDate->toDateString()],
            ['amount' => 5.00, 'notes' => 'DEMO: Current funeral rate', 'created_by' => $user->id]
        );

        // ── Assign funeral enrollment & start dates to members ────────────────
        // m0: enrolled from scheme start — FULLY PAID UP
        $members[0]->update([
            'funeral_enrolled'   => true,
            'funeral_start_date' => $oldRateDate->copy()->toDateString(),
        ]);
        // m1: enrolled 2.5 years ago — IN ARREARS
        $members[1]->update([
            'funeral_enrolled'   => true,
            'funeral_start_date' => Carbon::now()->subYears(2)->subMonths(6)->startOfMonth()->toDateString(),
        ]);
        // m2: enrolled 1.5 years ago — moderate arrears
        $members[2]->update([
            'funeral_enrolled'   => true,
            'funeral_start_date' => Carbon::now()->subYears(1)->subMonths(6)->startOfMonth()->toDateString(),
        ]);
        // m3: enrolled 6 months ago — recently enrolled, small arrears
        $members[3]->update([
            'funeral_enrolled'   => true,
            'funeral_start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
        ]);
        // m4: enrolled from start — CREDIT (overpaid)
        $members[4]->update([
            'funeral_enrolled'   => true,
            'funeral_start_date' => $oldRateDate->copy()->toDateString(),
        ]);
        // m5: NOT ENROLLED
        $members[5]->update([
            'funeral_enrolled'   => false,
            'funeral_start_date' => null,
        ]);
        // m6 (if available): SPORADIC
        if ($members->count() >= 7) {
            $members[6]->update([
                'funeral_enrolled'   => true,
                'funeral_start_date' => Carbon::now()->subYears(2)->startOfYear()->toDateString(),
            ]);
        }

        // ── Contributions ─────────────────────────────────────────────────────
        // m0: FULLY PAID UP
        $this->seedMonthlyContributions($members[0], $oldRateDate->copy(), Carbon::now(), $user, 'mobile_money');

        // m1: IN ARREARS (~60% paid)
        $this->seedSampledContributions(
            $members[1],
            Carbon::now()->subYears(2)->subMonths(6)->startOfMonth(),
            Carbon::now(),
            $user,
            0.60,
            'cash'
        );

        // m2: first 8 months only
        $this->seedFixedContributions(
            $members[2],
            Carbon::now()->subYears(1)->subMonths(6)->startOfMonth(),
            8,
            $user,
            'cash'
        );

        // m3: recently enrolled, 2 of 6 paid
        $this->seedFixedContributions(
            $members[3],
            Carbon::now()->subMonths(6)->startOfMonth(),
            2,
            $user,
            'mobile_money'
        );

        // m4: CREDIT — all months + advance payment
        $this->seedMonthlyContributions($members[4], $oldRateDate->copy(), Carbon::now(), $user, 'bank_transfer');
        FuneralContribution::create([
            'member_id'      => $members[4]->id,
            'amount'         => 15.00,
            'period_month'   => Carbon::now()->addMonths(1)->month,
            'period_year'    => Carbon::now()->addMonths(1)->year,
            'payment_date'   => Carbon::now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'received_by'    => $user->id,
            'notes'          => 'SAMPLE DATA',
        ]);

        // m6: SPORADIC
        if ($members->count() >= 7) {
            $this->seedSporadicContributions($members[6], Carbon::now()->subYears(2)->startOfYear(), $user);
        }

        // ── Funeral Benefits with itemised expense lines ───────────────────────
        // Benefits span TWO calendar years so monthly/annual spending reports both show data.
        DB::transaction(function () use ($members, $user) {
            // 1. Full benefit — GH₵500 + 3 expense lines (~20 months ago = last year)
            $b1 = FuneralBenefit::create([
                'member_id'       => $members[0]->id,
                'benefactor_name' => $members[0]->full_name,
                'deceased_name'   => 'Kofi Mensah (SAMPLE)',
                'funeral_date'    => Carbon::now()->subMonths(20)->toDateString(),
                'funeral_year'    => Carbon::now()->subMonths(20)->year,
                'venue'           => 'Abirem Community Centre',
                'amount_donated'  => 500.00,
                'description'     => 'SAMPLE DATA — Full funeral benefit with all expenses',
                'recorded_by'     => $user->id,
            ]);
            FuneralBenefitExpense::create(['funeral_benefit_id' => $b1->id, 'description' => 'Transport',      'amount' => 60.00]);
            FuneralBenefitExpense::create(['funeral_benefit_id' => $b1->id, 'description' => 'Refreshments',   'amount' => 80.00]);
            FuneralBenefitExpense::create(['funeral_benefit_id' => $b1->id, 'description' => 'Canopy hire',    'amount' => 100.00]);

            // 2. Mid-range benefit — GH₵400 + 1 expense line (~8 months ago = this year)
            $b2 = FuneralBenefit::create([
                'member_id'       => $members[1]->id,
                'benefactor_name' => $members[1]->full_name,
                'deceased_name'   => 'Abena Owusu (SAMPLE)',
                'funeral_date'    => Carbon::now()->subMonths(8)->toDateString(),
                'funeral_year'    => Carbon::now()->subMonths(8)->year,
                'venue'           => 'Abirem Presbyterian Church',
                'amount_donated'  => 400.00,
                'description'     => 'SAMPLE DATA — Funeral benefit with single expense',
                'recorded_by'     => $user->id,
            ]);
            FuneralBenefitExpense::create(['funeral_benefit_id' => $b2->id, 'description' => 'Transport', 'amount' => 50.00]);

            // 3. Basic benefit — GH₵300, NO expense lines (~4 months ago = this year)
            FuneralBenefit::create([
                'member_id'       => $members[2]->id,
                'benefactor_name' => $members[2]->full_name,
                'deceased_name'   => 'Yaw Asante (SAMPLE)',
                'funeral_date'    => Carbon::now()->subMonths(4)->toDateString(),
                'funeral_year'    => Carbon::now()->subMonths(4)->year,
                'venue'           => null,
                'amount_donated'  => 300.00,
                'description'     => 'SAMPLE DATA — Funeral benefit with no other expenses',
                'recorded_by'     => $user->id,
            ]);
        });

        $this->command->info('✓ Funeral demo data seeded.');
        $this->command->line('  Members used (ids): ' . $members->pluck('id')->join(', '));
        $this->command->line('  Remove: php artisan db:seed --class=WelfareFuneralDemoCleanupSeeder');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function seedMonthlyContributions(Member $member, Carbon $from, Carbon $to, User $user, string $method): void
    {
        $current = $from->copy()->startOfMonth();
        $end     = $to->copy()->startOfMonth();

        while ($current->lte($end)) {
            FuneralContribution::create([
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

    private function seedSampledContributions(Member $member, Carbon $from, Carbon $to, User $user, float $ratio, string $method): void
    {
        $current = $from->copy()->startOfMonth();
        $end     = $to->copy()->startOfMonth();

        while ($current->lte($end)) {
            if ((mt_rand(1, 100) / 100) <= $ratio) {
                FuneralContribution::create([
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

    private function seedFixedContributions(Member $member, Carbon $from, int $count, User $user, string $method): void
    {
        $current = $from->copy()->startOfMonth();
        for ($i = 0; $i < $count; $i++) {
            FuneralContribution::create([
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

    private function seedSporadicContributions(Member $member, Carbon $from, User $user): void
    {
        $skipOffsets = [3, 6, 10, 14, 17, 21];
        $current     = $from->copy()->startOfMonth();
        $end         = Carbon::now()->startOfMonth();
        $i           = 0;

        while ($current->lte($end)) {
            if (!in_array($i, $skipOffsets)) {
                FuneralContribution::create([
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

    private function rateAmountFor(Carbon $month): float
    {
        $rate = FuneralRate::where('effective_from', '<=', $month->copy()->startOfMonth())
            ->orderByDesc('effective_from')
            ->first();
        return $rate ? (float) $rate->amount : 5.00;
    }
}
