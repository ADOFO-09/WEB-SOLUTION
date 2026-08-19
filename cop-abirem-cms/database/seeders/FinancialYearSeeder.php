<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialYearSeeder extends Seeder
{
    public function run(): void
    {
        $current  = (int) date('Y');
        $previous = $current - 1;

        $years = [
            [
                'name'       => 'FY ' . $previous,
                'start_date' => $previous . '-01-01',
                'end_date'   => $previous . '-12-31',
                'is_active'  => false,
                'is_closed'  => true,
            ],
            [
                'name'       => 'FY ' . $current,
                'start_date' => $current . '-01-01',
                'end_date'   => $current . '-12-31',
                'is_active'  => true,
                'is_closed'  => false,
            ],
        ];

        foreach ($years as $year) {
            DB::table('financial_years')->insertOrIgnore(array_merge($year, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
