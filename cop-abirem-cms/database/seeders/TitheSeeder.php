<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TitheSeeder extends Seeder
{
    public function run(): void
    {
        $tithes = [
            // January 2025 Tithes
            [
                'reference_number' => 'TT-2025-0001',
                'member_id' => 1,
                'financial_year_id' => 2,
                'amount' => 500.00,
                'payment_date' => '2025-01-05',
                'payment_method' => 'cash',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0001',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0002',
                'member_id' => 2,
                'financial_year_id' => 2,
                'amount' => 350.00,
                'payment_date' => '2025-01-05',
                'payment_method' => 'mobile_money',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0002',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0003',
                'member_id' => 3,
                'financial_year_id' => 2,
                'amount' => 800.00,
                'payment_date' => '2025-01-05',
                'payment_method' => 'mobile_money',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0003',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0004',
                'member_id' => 4,
                'financial_year_id' => 2,
                'amount' => 450.00,
                'payment_date' => '2025-01-12',
                'payment_method' => 'cash',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0004',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0005',
                'member_id' => 5,
                'financial_year_id' => 2,
                'amount' => 150.00,
                'payment_date' => '2025-01-12',
                'payment_method' => 'mobile_money',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0005',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0006',
                'member_id' => 6,
                'financial_year_id' => 2,
                'amount' => 600.00,
                'payment_date' => '2025-01-12',
                'payment_method' => 'bank_transfer',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0006',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0007',
                'member_id' => 7,
                'financial_year_id' => 2,
                'amount' => 400.00,
                'payment_date' => '2025-01-19',
                'payment_method' => 'cash',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0007',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0008',
                'member_id' => 8,
                'financial_year_id' => 2,
                'amount' => 200.00,
                'payment_date' => '2025-01-19',
                'payment_method' => 'cash',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0008',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0009',
                'member_id' => 9,
                'financial_year_id' => 2,
                'amount' => 300.00,
                'payment_date' => '2025-01-19',
                'payment_method' => 'mobile_money',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0009',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'TT-2025-0010',
                'member_id' => 10,
                'financial_year_id' => 2,
                'amount' => 100.00,
                'payment_date' => '2025-01-19',
                'payment_method' => 'cash',
                'month_for' => '2025-01-01',
                'receipt_number' => 'TT-REC-2025-0010',
                'recorded_by' => 1,
            ],
        ];

        foreach ($tithes as $tithe) {
            DB::table('tithes')->insert(array_merge($tithe, [
                'sms_sent' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
