<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $serviceTypes = [
            ['name' => 'Sunday Worship Service', 'slug' => 'sunday-worship', 'day_of_week' => 'sunday', 'default_start_time' => '08:00:00', 'description' => 'Main Sunday worship service'],
            ['name' => 'Second Service', 'slug' => 'second-service', 'day_of_week' => 'sunday', 'default_start_time' => '10:30:00', 'description' => 'Second Sunday service'],
            ['name' => 'Midweek Bible Study', 'slug' => 'bible-study', 'day_of_week' => 'wednesday', 'default_start_time' => '18:00:00', 'description' => 'Wednesday Bible study and teaching'],
            ['name' => 'Friday Prayer Meeting', 'slug' => 'friday-prayer', 'day_of_week' => 'friday', 'default_start_time' => '18:00:00', 'description' => 'Friday evening prayer meeting'],
            ['name' => 'All-Night Prayer', 'slug' => 'all-night', 'day_of_week' => 'friday', 'default_start_time' => '21:00:00', 'description' => 'Monthly all-night prayer service'],
            ['name' => 'Ministry Meeting', 'slug' => 'ministry-meeting', 'day_of_week' => 'any', 'default_start_time' => null, 'description' => 'Various ministry group meetings'],
            ['name' => 'Special Program', 'slug' => 'special-program', 'day_of_week' => 'any', 'default_start_time' => null, 'description' => 'Special church programs and events'],
            ['name' => 'Communion Service', 'slug' => 'communion', 'day_of_week' => 'sunday', 'default_start_time' => '08:00:00', 'description' => 'Monthly communion service'],
            ['name' => 'Thanksgiving Service', 'slug' => 'thanksgiving', 'day_of_week' => 'sunday', 'default_start_time' => '08:00:00', 'description' => 'Thanksgiving and harvest services'],
        ];

        foreach ($serviceTypes as $type) {
            DB::table('service_types')->insert(array_merge($type, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
