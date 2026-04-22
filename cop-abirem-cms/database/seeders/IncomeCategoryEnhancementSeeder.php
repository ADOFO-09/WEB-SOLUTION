<?php

namespace Database\Seeders;

use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;

class IncomeCategoryEnhancementSeeder extends Seeder
{
    public function run(): void
    {
        // Update existing categories with sort_order and is_system flags
        $existing = [
            'Regular Tithe'        => ['sort_order' => 1,  'is_system' => true, 'type' => 'tithe'],
            'Sunday Offering'      => ['sort_order' => 1,  'is_system' => true, 'type' => 'offering'],
            'Midweek Offering'     => ['sort_order' => 2,  'is_system' => true, 'type' => 'offering'],
            'Thanksgiving Offering'=> ['sort_order' => 1,  'is_system' => true, 'type' => 'special'],
            'Harvest Offering'     => ['sort_order' => 2,  'is_system' => true, 'type' => 'special'],
            'Mission Offering'     => ['sort_order' => 3,  'is_system' => true, 'type' => 'special'],
            'Special Offering'     => ['sort_order' => 10, 'is_system' => true, 'type' => 'special'],
            'Building Fund'        => ['sort_order' => 1,  'is_system' => true, 'type' => 'donation'],
            'Welfare Fund'         => ['sort_order' => 2,  'is_system' => true, 'type' => 'donation'],
            'Equipment Fund'       => ['sort_order' => 3,  'is_system' => true, 'type' => 'donation'],
            'Youth Fund'           => ['sort_order' => 4,  'is_system' => true, 'type' => 'donation'],
            'General Donation'     => ['sort_order' => 5,  'is_system' => true, 'type' => 'donation'],
            'Building Pledge'      => ['sort_order' => 1,  'is_system' => true, 'type' => 'pledge'],
            'Annual Pledge'        => ['sort_order' => 2,  'is_system' => true, 'type' => 'pledge'],
            'Special Project Pledge' => ['sort_order' => 3, 'is_system' => true, 'type' => 'pledge'],
        ];

        foreach ($existing as $name => $attrs) {
            IncomeCategory::where('name', $name)->update($attrs);
        }

        // Add new church-specific categories
        $newCategories = [
            // Tithe
            ['name' => '1st Sunday Tithe',   'slug' => '1st-sunday-tithe',  'type' => 'tithe',    'sort_order' => 2],
            ['name' => '2nd Sunday Tithe',   'slug' => '2nd-sunday-tithe',  'type' => 'tithe',    'sort_order' => 3],
            ['name' => '3rd Sunday Tithe',   'slug' => '3rd-sunday-tithe',  'type' => 'tithe',    'sort_order' => 4],
            ['name' => '4th Sunday Tithe',   'slug' => '4th-sunday-tithe',  'type' => 'tithe',    'sort_order' => 5],

            // Regular Offerings
            ['name' => 'Local Offering',         'slug' => 'local-offering',        'type' => 'offering', 'sort_order' => 3],
            ['name' => 'Thank Offering',          'slug' => 'thank-offering',        'type' => 'offering', 'sort_order' => 4],
            ['name' => 'Prayer Meeting Offering', 'slug' => 'prayer-meeting-offering','type' => 'offering', 'sort_order' => 5],

            // Special / Designated Offerings
            ['name' => 'Annual Offering',           'slug' => 'annual-offering',         'type' => 'special', 'sort_order' => 4],
            ['name' => 'McKeown Missions Offering', 'slug' => 'mckeown-missions-offering','type' => 'special', 'sort_order' => 5],
            ['name' => 'Area Offering',             'slug' => 'area-offering',           'type' => 'special', 'sort_order' => 6],
            ['name' => 'District Offering',         'slug' => 'district-offering',       'type' => 'special', 'sort_order' => 7],
            ['name' => 'District Week Offering',    'slug' => 'district-week-offering',  'type' => 'special', 'sort_order' => 8],
            ['name' => 'Ministries Offering',       'slug' => 'ministries-offering',     'type' => 'special', 'sort_order' => 9],
            ['name' => 'Youth Offering',            'slug' => 'youth-offering',          'type' => 'special', 'sort_order' => 11],
            ["Women's Offering",                    'slug' => 'womens-offering',         'type' => 'special', 'sort_order' => 12],
            ["Men's Offering",                      'slug' => 'mens-offering',           'type' => 'special', 'sort_order' => 13],
            ["Children's Offering",                 'slug' => 'childrens-offering',      'type' => 'special', 'sort_order' => 14],
            ['name' => 'Easter Offering',           'slug' => 'easter-offering',         'type' => 'special', 'sort_order' => 15],
            ['name' => 'Christmas Offering',        'slug' => 'christmas-offering',      'type' => 'special', 'sort_order' => 16],
            ['name' => 'Convention Offering',       'slug' => 'convention-offering',     'type' => 'special', 'sort_order' => 17],
        ];

        foreach ($newCategories as $cat) {
            $name = $cat['name'] ?? $cat[0];
            IncomeCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name'       => $name,
                    'type'       => $cat['type'],
                    'sort_order' => $cat['sort_order'],
                    'is_system'  => true,
                    'is_active'  => true,
                ]
            );
        }
    }
}
