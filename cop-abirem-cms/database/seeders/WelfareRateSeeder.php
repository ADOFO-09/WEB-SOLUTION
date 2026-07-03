<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WelfareRateSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\WelfareRate::firstOrCreate(
            ['effective_from' => '2024-01-01'],
            ['amount' => 10.00, 'notes' => 'Initial welfare due rate', 'created_by' => 1]
        );
        $this->command->info('✓ Welfare rate seeded: GH₵10/month from Jan 2024.');
    }
}
