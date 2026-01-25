<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialYearSeeder extends Seeder
{
    public function run(): void
    {
        $years = [
            [
                'name' => 'FY 2024',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'is_active' => false,
                'is_closed' => true,
            ],
            [
                'name' => 'FY 2025',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'is_active' => true,
                'is_closed' => false,
            ],
        ];

        foreach ($years as $year) {
            DB::table('financial_years')->insert(array_merge($year, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
