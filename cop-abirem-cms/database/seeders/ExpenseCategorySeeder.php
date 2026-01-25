<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electricity', 'slug' => 'electricity', 'description' => 'Monthly electricity bills', 'budget_amount' => 500.00],
            ['name' => 'Water', 'slug' => 'water', 'description' => 'Monthly water bills', 'budget_amount' => 100.00],
            ['name' => 'Internet & Communication', 'slug' => 'internet-communication', 'description' => 'Internet and phone bills', 'budget_amount' => 200.00],
            ['name' => 'Maintenance & Repairs', 'slug' => 'maintenance-repairs', 'description' => 'Building and equipment maintenance', 'budget_amount' => 300.00],
            ['name' => 'Stationery & Supplies', 'slug' => 'stationery-supplies', 'description' => 'Office supplies and stationery', 'budget_amount' => 150.00],
            ['name' => 'Transport & Fuel', 'slug' => 'transport-fuel', 'description' => 'Transportation and fuel costs', 'budget_amount' => 400.00],
            ['name' => 'Ministry Support', 'slug' => 'ministry-support', 'description' => 'Support for various ministries', 'budget_amount' => 500.00],
            ['name' => 'Welfare & Benevolence', 'slug' => 'welfare-benevolence', 'description' => 'Member welfare and assistance', 'budget_amount' => 600.00],
            ['name' => 'District Remittance', 'slug' => 'district-remittance', 'description' => 'Monthly remittance to district', 'budget_amount' => 1000.00],
            ['name' => 'Area Remittance', 'slug' => 'area-remittance', 'description' => 'Remittance to area office', 'budget_amount' => 500.00],
            ['name' => 'Events & Programs', 'slug' => 'events-programs', 'description' => 'Special events and programs', 'budget_amount' => 800.00],
            ['name' => 'Equipment Purchase', 'slug' => 'equipment-purchase', 'description' => 'Purchase of equipment', 'budget_amount' => 1000.00],
            ['name' => 'Cleaning & Sanitation', 'slug' => 'cleaning-sanitation', 'description' => 'Cleaning supplies and services', 'budget_amount' => 200.00],
            ['name' => 'Security', 'slug' => 'security', 'description' => 'Security services and equipment', 'budget_amount' => 300.00],
            ['name' => 'Miscellaneous', 'slug' => 'miscellaneous', 'description' => 'Other expenses', 'budget_amount' => 200.00],
        ];

        foreach ($categories as $category) {
            DB::table('expense_categories')->insert(array_merge($category, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
