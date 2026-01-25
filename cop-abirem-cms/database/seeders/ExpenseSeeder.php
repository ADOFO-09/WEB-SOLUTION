<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            // Utilities
            [
                'reference_number' => 'EXP-2025-0001',
                'financial_year_id' => 2,
                'expense_category_id' => 1, // Electricity
                'description' => 'January 2025 Electricity Bill',
                'amount' => 450.00,
                'expense_date' => '2025-01-10',
                'payment_method' => 'mobile_money',
                'payee_name' => 'ECG',
                'payee_phone' => null,
                'voucher_number' => 'VCH-2025-0001',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-10 09:00:00',
            ],
            [
                'reference_number' => 'EXP-2025-0002',
                'financial_year_id' => 2,
                'expense_category_id' => 2, // Water
                'description' => 'January 2025 Water Bill',
                'amount' => 85.00,
                'expense_date' => '2025-01-12',
                'payment_method' => 'cash',
                'payee_name' => 'Ghana Water Company',
                'payee_phone' => null,
                'voucher_number' => 'VCH-2025-0002',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-12 10:00:00',
            ],
            
            // Maintenance
            [
                'reference_number' => 'EXP-2025-0003',
                'financial_year_id' => 2,
                'expense_category_id' => 4, // Maintenance & Repairs
                'description' => 'Repair of church generator',
                'amount' => 350.00,
                'expense_date' => '2025-01-08',
                'payment_method' => 'cash',
                'payee_name' => 'Kwame Electricals',
                'payee_phone' => '0244555666',
                'voucher_number' => 'VCH-2025-0003',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-08 14:00:00',
            ],
            
            // Stationery
            [
                'reference_number' => 'EXP-2025-0004',
                'financial_year_id' => 2,
                'expense_category_id' => 5, // Stationery & Supplies
                'description' => 'Office stationery and printing paper',
                'amount' => 180.00,
                'expense_date' => '2025-01-05',
                'payment_method' => 'cash',
                'payee_name' => 'Abirem Stationery Shop',
                'payee_phone' => '0244123789',
                'voucher_number' => 'VCH-2025-0004',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-05 11:00:00',
            ],
            
            // Transport
            [
                'reference_number' => 'EXP-2025-0005',
                'financial_year_id' => 2,
                'expense_category_id' => 6, // Transport & Fuel
                'description' => 'Fuel for church bus - Youth program transportation',
                'amount' => 250.00,
                'expense_date' => '2025-01-11',
                'payment_method' => 'cash',
                'payee_name' => 'Shell Filling Station',
                'payee_phone' => null,
                'voucher_number' => 'VCH-2025-0005',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-11 08:00:00',
            ],
            
            // Ministry Support
            [
                'reference_number' => 'EXP-2025-0006',
                'financial_year_id' => 2,
                'expense_category_id' => 7, // Ministry Support
                'description' => 'Women\'s Ministry program materials',
                'amount' => 400.00,
                'expense_date' => '2025-01-15',
                'payment_method' => 'cash',
                'payee_name' => 'Deaconess Ama Mensah',
                'payee_phone' => '0244111222',
                'voucher_number' => 'VCH-2025-0006',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-15 09:00:00',
            ],
            
            // Welfare
            [
                'reference_number' => 'EXP-2025-0007',
                'financial_year_id' => 2,
                'expense_category_id' => 8, // Welfare & Benevolence
                'description' => 'Hospital visitation support for sick member',
                'amount' => 300.00,
                'expense_date' => '2025-01-18',
                'payment_method' => 'cash',
                'payee_name' => 'Welfare Committee',
                'payee_phone' => null,
                'voucher_number' => 'VCH-2025-0007',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-18 10:00:00',
            ],
            
            // District Remittance
            [
                'reference_number' => 'EXP-2025-0008',
                'financial_year_id' => 2,
                'expense_category_id' => 9, // District Remittance
                'description' => 'January 2025 District Remittance',
                'amount' => 1000.00,
                'expense_date' => '2025-01-20',
                'payment_method' => 'bank_transfer',
                'payee_name' => 'COP Abirem District',
                'payee_phone' => null,
                'voucher_number' => 'VCH-2025-0008',
                'status' => 'paid',
                'requested_by' => 1,
                'approved_by' => 1,
                'approved_at' => '2025-01-20 09:00:00',
            ],
            
            // Pending Expense
            [
                'reference_number' => 'EXP-2025-0009',
                'financial_year_id' => 2,
                'expense_category_id' => 13, // Cleaning & Sanitation
                'description' => 'Cleaning supplies for the month',
                'amount' => 150.00,
                'expense_date' => '2025-01-21',
                'payment_method' => 'cash',
                'payee_name' => 'Abirem Supermarket',
                'payee_phone' => null,
                'voucher_number' => null,
                'status' => 'pending',
                'requested_by' => 1,
                'approved_by' => null,
                'approved_at' => null,
            ],
        ];

        foreach ($expenses as $expense) {
            DB::table('expenses')->insert(array_merge($expense, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
