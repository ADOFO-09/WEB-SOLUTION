<?php

namespace Database\Seeders;

use App\Models\FuneralBenefit;
use App\Models\FuneralContribution;
use App\Models\FuneralRate;
use App\Models\Member;
use App\Models\WelfareBenefit;
use App\Models\WelfareContribution;
use App\Models\WelfareRate;
use Illuminate\Database\Seeder;

/**
 * Removes all data created by WelfareDemoSeeder and FuneralDemoSeeder.
 *
 * Run: php artisan db:seed --class=WelfareFuneralDemoCleanupSeeder
 *
 * What it removes:
 *  - WelfareContribution / FuneralContribution rows with notes = 'SAMPLE DATA' (force delete)
 *  - WelfareBenefit / FuneralBenefit rows whose description starts with 'SAMPLE DATA' (+ expense lines via cascade)
 *  - WelfareRate / FuneralRate rows with notes starting with 'DEMO:'
 *  - Resets welfare_enrolled / welfare_start_date / funeral_enrolled / funeral_start_date to defaults on all members
 *
 * What it does NOT remove:
 *  - Any real welfare/funeral records (notes != 'SAMPLE DATA')
 *  - Real rates that do not have the 'DEMO:' prefix in their notes
 *  - Member records themselves
 */
class WelfareFuneralDemoCleanupSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('Cleanup seeder blocked in production.');
            return;
        }

        $this->command->info('Cleaning up welfare & funeral demo data...');

        // Contributions (force delete to bypass soft-delete)
        $wc = WelfareContribution::withTrashed()->where('notes', 'SAMPLE DATA')->forceDelete();
        $fc = FuneralContribution::withTrashed()->where('notes', 'SAMPLE DATA')->forceDelete();
        $this->command->line("  Deleted {$wc} welfare contributions, {$fc} funeral contributions.");

        // Benefits — expense lines cascade automatically
        $wb = WelfareBenefit::withTrashed()->where('description', 'like', 'SAMPLE DATA%')->forceDelete();
        $fb = FuneralBenefit::withTrashed()->where('description', 'like', 'SAMPLE DATA%')->forceDelete();
        $this->command->line("  Deleted {$wb} welfare benefits, {$fb} funeral benefits (expense lines cascaded).");

        // Demo rates
        $wr = WelfareRate::where('notes', 'like', 'DEMO:%')->delete();
        $fr = FuneralRate::where('notes', 'like', 'DEMO:%')->delete();
        $this->command->line("  Deleted {$wr} welfare rates, {$fr} funeral rates.");

        // Reset member welfare/funeral fields to clean defaults
        $updated = Member::whereNotNull('welfare_start_date')
            ->orWhereNotNull('funeral_start_date')
            ->orWhere('welfare_enrolled', false)
            ->orWhere('funeral_enrolled', false)
            ->update([
                'welfare_enrolled'   => true,
                'welfare_start_date' => null,
                'funeral_enrolled'   => true,
                'funeral_start_date' => null,
            ]);
        $this->command->line("  Reset welfare/funeral fields on {$updated} members.");

        $this->command->info('✓ Demo data removed.');
    }
}
