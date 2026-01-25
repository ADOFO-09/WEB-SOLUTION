<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Musical Instruments', 'slug' => 'musical-instruments', 'description' => 'Drums, keyboards, guitars, etc.', 'depreciation_rate' => 10.00, 'depreciation_method' => 'straight_line', 'useful_life_years' => 10],
            ['name' => 'Sound Equipment', 'slug' => 'sound-equipment', 'description' => 'Speakers, amplifiers, mixers, microphones', 'depreciation_rate' => 15.00, 'depreciation_method' => 'straight_line', 'useful_life_years' => 7],
            ['name' => 'Furniture', 'slug' => 'furniture', 'description' => 'Chairs, tables, pulpit, altars', 'depreciation_rate' => 10.00, 'depreciation_method' => 'straight_line', 'useful_life_years' => 10],
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Computers, projectors, TVs, printers', 'depreciation_rate' => 20.00, 'depreciation_method' => 'straight_line', 'useful_life_years' => 5],
            ['name' => 'Vehicles', 'slug' => 'vehicles', 'description' => 'Church buses, cars', 'depreciation_rate' => 15.00, 'depreciation_method' => 'declining_balance', 'useful_life_years' => 8],
            ['name' => 'Kitchen Equipment', 'slug' => 'kitchen-equipment', 'description' => 'Cookers, refrigerators, utensils', 'depreciation_rate' => 15.00, 'depreciation_method' => 'straight_line', 'useful_life_years' => 7],
            ['name' => 'Lighting', 'slug' => 'lighting', 'description' => 'Stage lights, ceiling lights, fixtures', 'depreciation_rate' => 15.00, 'depreciation_method' => 'straight_line', 'useful_life_years' => 7],
            ['name' => 'Office Equipment', 'slug' => 'office-equipment', 'description' => 'Desks, filing cabinets, office chairs', 'depreciation_rate' => 10.00, 'depreciation_method' => 'straight_line', 'useful_life_years' => 10],
        ];

        foreach ($categories as $category) {
            DB::table('asset_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
