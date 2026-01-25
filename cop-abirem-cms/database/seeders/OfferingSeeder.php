<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfferingSeeder extends Seeder
{
    public function run(): void
    {
        $offerings = [
            // Sunday Offerings
            [
                'reference_number' => 'OF-2025-0001',
                'member_id' => null,
                'financial_year_id' => 2,
                'income_category_id' => 2, // Sunday Offering
                'session_id' => 1,
                'amount' => 2500.00,
                'payment_date' => '2025-01-05',
                'payment_method' => 'cash',
                'is_anonymous' => true,
                'notes' => 'Sunday Worship Service offering',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'OF-2025-0002',
                'member_id' => null,
                'financial_year_id' => 2,
                'income_category_id' => 2, // Sunday Offering
                'session_id' => 2,
                'amount' => 3200.00,
                'payment_date' => '2025-01-12',
                'payment_method' => 'cash',
                'is_anonymous' => true,
                'notes' => 'Sunday Worship Service offering',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'OF-2025-0003',
                'member_id' => null,
                'financial_year_id' => 2,
                'income_category_id' => 2, // Sunday Offering
                'session_id' => 3,
                'amount' => 2800.00,
                'payment_date' => '2025-01-19',
                'payment_method' => 'cash',
                'is_anonymous' => true,
                'notes' => 'Sunday Worship Service offering',
                'recorded_by' => 1,
            ],
            
            // Midweek Offerings
            [
                'reference_number' => 'OF-2025-0004',
                'member_id' => null,
                'financial_year_id' => 2,
                'income_category_id' => 3, // Midweek Offering
                'session_id' => 4,
                'amount' => 850.00,
                'payment_date' => '2025-01-08',
                'payment_method' => 'cash',
                'is_anonymous' => true,
                'notes' => 'Midweek Bible Study offering',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'OF-2025-0005',
                'member_id' => null,
                'financial_year_id' => 2,
                'income_category_id' => 3, // Midweek Offering
                'session_id' => 5,
                'amount' => 920.00,
                'payment_date' => '2025-01-15',
                'payment_method' => 'cash',
                'is_anonymous' => true,
                'notes' => 'Midweek Bible Study offering',
                'recorded_by' => 1,
            ],
            
            // Thanksgiving Offerings (Named)
            [
                'reference_number' => 'OF-2025-0006',
                'member_id' => 1,
                'financial_year_id' => 2,
                'income_category_id' => 4, // Thanksgiving Offering
                'session_id' => 1,
                'amount' => 200.00,
                'payment_date' => '2025-01-05',
                'payment_method' => 'cash',
                'is_anonymous' => false,
                'notes' => 'New Year Thanksgiving',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'OF-2025-0007',
                'member_id' => 4,
                'financial_year_id' => 2,
                'income_category_id' => 4, // Thanksgiving Offering
                'session_id' => 2,
                'amount' => 500.00,
                'payment_date' => '2025-01-12',
                'payment_method' => 'cash',
                'is_anonymous' => false,
                'notes' => 'Birthday Thanksgiving',
                'recorded_by' => 1,
            ],
        ];

        foreach ($offerings as $offering) {
            DB::table('offerings')->insert(array_merge($offering, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
