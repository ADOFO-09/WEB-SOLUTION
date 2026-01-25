<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Tithes
            ['name' => 'Regular Tithe', 'slug' => 'regular-tithe', 'type' => 'tithe', 'description' => 'Monthly tithe contributions'],
            
            // Offerings
            ['name' => 'Sunday Offering', 'slug' => 'sunday-offering', 'type' => 'offering', 'description' => 'Regular Sunday service offering'],
            ['name' => 'Midweek Offering', 'slug' => 'midweek-offering', 'type' => 'offering', 'description' => 'Wednesday service offering'],
            ['name' => 'Thanksgiving Offering', 'slug' => 'thanksgiving-offering', 'type' => 'offering', 'description' => 'Special thanksgiving offerings'],
            ['name' => 'Harvest Offering', 'slug' => 'harvest-offering', 'type' => 'offering', 'description' => 'Annual harvest thanksgiving'],
            ['name' => 'Mission Offering', 'slug' => 'mission-offering', 'type' => 'offering', 'description' => 'Offerings for mission work'],
            ['name' => 'Special Offering', 'slug' => 'special-offering', 'type' => 'offering', 'description' => 'Other special offerings'],
            
            // Donations
            ['name' => 'Building Fund', 'slug' => 'building-fund', 'type' => 'donation', 'description' => 'Donations for building projects'],
            ['name' => 'Welfare Fund', 'slug' => 'welfare-fund', 'type' => 'donation', 'description' => 'Donations for welfare assistance'],
            ['name' => 'Equipment Fund', 'slug' => 'equipment-fund', 'type' => 'donation', 'description' => 'Donations for equipment purchase'],
            ['name' => 'Youth Fund', 'slug' => 'youth-fund', 'type' => 'donation', 'description' => 'Donations for youth ministry'],
            ['name' => 'General Donation', 'slug' => 'general-donation', 'type' => 'donation', 'description' => 'General purpose donations'],
            
            // Pledges
            ['name' => 'Building Pledge', 'slug' => 'building-pledge', 'type' => 'pledge', 'description' => 'Pledges for building projects'],
            ['name' => 'Annual Pledge', 'slug' => 'annual-pledge', 'type' => 'pledge', 'description' => 'Annual financial commitments'],
            ['name' => 'Special Project Pledge', 'slug' => 'project-pledge', 'type' => 'pledge', 'description' => 'Pledges for special projects'],
        ];

        foreach ($categories as $category) {
            DB::table('income_categories')->insert(array_merge($category, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
