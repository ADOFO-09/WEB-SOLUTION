<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $donations = [
            // Building Fund Donations
            [
                'reference_number' => 'DN-2025-0001',
                'member_id' => 1,
                'financial_year_id' => 2,
                'income_category_id' => 8, // Building Fund
                'project_id' => 1, // Church Building Extension
                'amount' => 2000.00,
                'donation_type' => 'monetary',
                'payment_date' => '2025-01-05',
                'payment_method' => 'bank_transfer',
                'receipt_number' => 'DN-REC-2025-0001',
                'is_anonymous' => false,
                'sms_sent' => true,
                'notes' => 'Building fund contribution',
                'recorded_by' => 1,
            ],
            [
                'reference_number' => 'DN-2025-0002',
                'member_id' => 3,
                'financial_year_id' => 2,
                'income_category_id' => 8, // Building Fund
                'project_id' => 1, // Church Building Extension
                'amount' => 5000.00,
                'donation_type' => 'monetary',
                'payment_date' => '2025-01-10',
                'payment_method' => 'bank_transfer',
                'receipt_number' => 'DN-REC-2025-0002',
                'is_anonymous' => false,
                'sms_sent' => true,
                'notes' => 'Building fund - major contribution',
                'recorded_by' => 1,
            ],
            
            // Youth Fund Donations
            [
                'reference_number' => 'DN-2025-0003',
                'member_id' => 6,
                'financial_year_id' => 2,
                'income_category_id' => 11, // Youth Fund
                'project_id' => 2, // Youth Center Construction
                'amount' => 1000.00,
                'donation_type' => 'monetary',
                'payment_date' => '2025-01-12',
                'payment_method' => 'mobile_money',
                'receipt_number' => 'DN-REC-2025-0003',
                'is_anonymous' => false,
                'sms_sent' => true,
                'notes' => 'Youth center project donation',
                'recorded_by' => 1,
            ],
            
            // Welfare Fund Donations
            [
                'reference_number' => 'DN-2025-0004',
                'member_id' => 2,
                'financial_year_id' => 2,
                'income_category_id' => 9, // Welfare Fund
                'project_id' => null,
                'amount' => 500.00,
                'donation_type' => 'monetary',
                'payment_date' => '2025-01-15',
                'payment_method' => 'cash',
                'receipt_number' => 'DN-REC-2025-0004',
                'is_anonymous' => false,
                'sms_sent' => true,
                'notes' => 'Welfare fund contribution',
                'recorded_by' => 1,
            ],
            
            // In-Kind Donation
            [
                'reference_number' => 'DN-2025-0005',
                'member_id' => 7,
                'financial_year_id' => 2,
                'income_category_id' => 12, // General Donation
                'project_id' => null,
                'amount' => null,
                'donation_type' => 'in_kind',
                'in_kind_description' => '50 bags of cement for building project',
                'estimated_value' => 3500.00,
                'payment_date' => '2025-01-18',
                'payment_method' => 'in_kind',
                'receipt_number' => 'DN-REC-2025-0005',
                'is_anonymous' => false,
                'sms_sent' => true,
                'notes' => 'Cement donation for building project',
                'recorded_by' => 1,
            ],
            
            // Anonymous Donation
            [
                'reference_number' => 'DN-2025-0006',
                'member_id' => null,
                'financial_year_id' => 2,
                'income_category_id' => 8, // Building Fund
                'project_id' => 1,
                'amount' => 10000.00,
                'donation_type' => 'monetary',
                'payment_date' => '2025-01-20',
                'payment_method' => 'bank_transfer',
                'receipt_number' => 'DN-REC-2025-0006',
                'is_anonymous' => true,
                'sms_sent' => false,
                'notes' => 'Anonymous building fund donation',
                'recorded_by' => 1,
            ],
        ];

        foreach ($donations as $donation) {
            DB::table('donations')->insert(array_merge($donation, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
