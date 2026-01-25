<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Church Building Extension',
                'slug' => 'church-building-extension',
                'description' => 'Extending the main auditorium to accommodate more members',
                'target_amount' => 150000.00,
                'amount_raised' => 45000.00,
                'start_date' => '2024-01-01',
                'end_date' => '2025-12-31',
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'Youth Center Construction',
                'slug' => 'youth-center-construction',
                'description' => 'Building a dedicated center for youth activities and programs',
                'target_amount' => 80000.00,
                'amount_raised' => 12000.00,
                'start_date' => '2024-06-01',
                'end_date' => '2026-06-30',
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'Sound System Upgrade',
                'slug' => 'sound-system-upgrade',
                'description' => 'Upgrading the church sound system for better audio quality',
                'target_amount' => 15000.00,
                'amount_raised' => 15000.00,
                'start_date' => '2024-03-01',
                'end_date' => '2024-09-30',
                'status' => 'completed',
                'created_by' => 1,
            ],
        ];

        foreach ($projects as $project) {
            DB::table('projects')->insert(array_merge($project, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
