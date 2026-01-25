<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PledgePaymentSeeder extends Seeder
{
    public function run(): void
    {
        $payments = [
            // Payments for Pledge 1 (Elder Kwame - Building)
            [
                'pledge_id' => 1,
                'reference_number' => 'PP-2025-0001',
                'amount' => 1000.00,
                'payment_date' => '2025-01-05',
                'payment_method' => 'cash',
                'receipt_number' => 'PP-REC-2025-0001',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            [
                'pledge_id' => 1,
                'reference_number' => 'PP-2025-0002',
                'amount' => 1000.00,
                'payment_date' => '2025-01-15',
                'payment_method' => 'mobile_money',
                'receipt_number' => 'PP-REC-2025-0002',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            
            // Payments for Pledge 2 (Kofi - Building)
            [
                'pledge_id' => 2,
                'reference_number' => 'PP-2025-0003',
                'amount' => 2500.00,
                'payment_date' => '2025-01-10',
                'payment_method' => 'bank_transfer',
                'receipt_number' => 'PP-REC-2025-0003',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            [
                'pledge_id' => 2,
                'reference_number' => 'PP-2025-0004',
                'amount' => 2500.00,
                'payment_date' => '2025-01-20',
                'payment_method' => 'bank_transfer',
                'receipt_number' => 'PP-REC-2025-0004',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            
            // Payments for Pledge 3 (Akosua - Building)
            [
                'pledge_id' => 3,
                'reference_number' => 'PP-2025-0005',
                'amount' => 500.00,
                'payment_date' => '2025-01-12',
                'payment_method' => 'cash',
                'receipt_number' => 'PP-REC-2025-0005',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            
            // Payments for Pledge 4 (Adwoa - Annual)
            [
                'pledge_id' => 4,
                'reference_number' => 'PP-2025-0006',
                'amount' => 200.00,
                'payment_date' => '2025-01-10',
                'payment_method' => 'mobile_money',
                'receipt_number' => 'PP-REC-2025-0006',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            
            // Completed Pledge Payments (from 2024)
            [
                'pledge_id' => 6,
                'reference_number' => 'PP-2024-0050',
                'amount' => 500.00,
                'payment_date' => '2024-05-01',
                'payment_method' => 'cash',
                'receipt_number' => 'PP-REC-2024-0050',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            [
                'pledge_id' => 6,
                'reference_number' => 'PP-2024-0051',
                'amount' => 500.00,
                'payment_date' => '2024-06-01',
                'payment_method' => 'cash',
                'receipt_number' => 'PP-REC-2024-0051',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            [
                'pledge_id' => 6,
                'reference_number' => 'PP-2024-0052',
                'amount' => 500.00,
                'payment_date' => '2024-07-01',
                'payment_method' => 'cash',
                'receipt_number' => 'PP-REC-2024-0052',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
            [
                'pledge_id' => 6,
                'reference_number' => 'PP-2024-0053',
                'amount' => 500.00,
                'payment_date' => '2024-08-01',
                'payment_method' => 'cash',
                'receipt_number' => 'PP-REC-2024-0053',
                'sms_sent' => true,
                'recorded_by' => 1,
            ],
        ];

        foreach ($payments as $payment) {
            DB::table('pledge_payments')->insert(array_merge($payment, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
