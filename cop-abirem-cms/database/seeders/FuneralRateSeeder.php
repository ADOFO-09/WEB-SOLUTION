<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FuneralRateSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\FuneralRate::firstOrCreate(
            ['effective_from' => '2024-01-01'],
            ['amount' => 10.00, 'notes' => 'Initial funeral due rate', 'created_by' => 1]
        );
        $this->command->info('✓ Funeral rate seeded: GH₵10/month from Jan 2024.');
    }
}
