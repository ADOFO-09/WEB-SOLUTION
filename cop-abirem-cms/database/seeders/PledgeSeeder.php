<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PledgeSeeder extends Seeder
{
    public function run(): void
    {
        $pledges = [
            // Building Pledges
            [
                'pledge_number' => 'PL-2025-0001',
                'member_id' => 1,
                'financial_year_id' => 2,
                'income_category_id' => 13, // Building Pledge
                'project_id' => 1,
                'total_amount' => 5000.00,
                'amount_paid' => 2000.00,
                'pledge_date' => '2025-01-01',
                'due_date' => '2025-06-30',
                'payment_frequency' => 'monthly',
                'status' => 'active',
                'notes' => 'Building fund pledge for 2025',
                'created_by' => 1,
            ],
            [
                'pledge_number' => 'PL-2025-0002',
                'member_id' => 3,
                'financial_year_id' => 2,
                'income_category_id' => 13, // Building Pledge
                'project_id' => 1,
                'total_amount' => 10000.00,
                'amount_paid' => 5000.00,
                'pledge_date' => '2025-01-01',
                'due_date' => '2025-12-31',
                'payment_frequency' => 'quarterly',
                'status' => 'active',
                'notes' => 'Major building pledge commitment',
                'created_by' => 1,
            ],
            [
                'pledge_number' => 'PL-2025-0003',
                'member_id' => 4,
                'financial_year_id' => 2,
                'income_category_id' => 13, // Building Pledge
                'project_id' => 1,
                'total_amount' => 3000.00,
                'amount_paid' => 500.00,
                'pledge_date' => '2025-01-05',
                'due_date' => '2025-07-31',
                'payment_frequency' => 'monthly',
                'status' => 'active',
                'notes' => 'Building pledge',
                'created_by' => 1,
            ],
            
            // Annual Pledges
            [
                'pledge_number' => 'PL-2025-0004',
                'member_id' => 6,
                'financial_year_id' => 2,
                'income_category_id' => 14, // Annual Pledge
                'project_id' => null,
                'total_amount' => 2400.00,
                'amount_paid' => 200.00,
                'pledge_date' => '2025-01-01',
                'due_date' => '2025-12-31',
                'payment_frequency' => 'monthly',
                'status' => 'active',
                'notes' => 'Annual giving pledge',
                'created_by' => 1,
            ],
            
            // Youth Center Pledge
            [
                'pledge_number' => 'PL-2025-0005',
                'member_id' => 5,
                'financial_year_id' => 2,
                'income_category_id' => 15, // Special Project Pledge
                'project_id' => 2,
                'total_amount' => 1500.00,
                'amount_paid' => 0.00,
                'pledge_date' => '2025-01-12',
                'due_date' => '2025-09-30',
                'payment_frequency' => 'monthly',
                'status' => 'active',
                'notes' => 'Youth center project pledge',
                'created_by' => 1,
            ],
            
            // Completed Pledge (from previous year)
            [
                'pledge_number' => 'PL-2024-0010',
                'member_id' => 7,
                'financial_year_id' => 1,
                'income_category_id' => 13,
                'project_id' => 3, // Sound System (completed)
                'total_amount' => 2000.00,
                'amount_paid' => 2000.00,
                'pledge_date' => '2024-04-01',
                'due_date' => '2024-09-30',
                'payment_frequency' => 'monthly',
                'status' => 'completed',
                'notes' => 'Sound system pledge - fully paid',
                'created_by' => 1,
            ],
        ];

        foreach ($pledges as $pledge) {
            DB::table('pledges')->insert(array_merge($pledge, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
