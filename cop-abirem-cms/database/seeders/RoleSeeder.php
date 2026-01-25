<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'System Administrator',
                'slug' => 'admin',
                'description' => 'Full system access with complete control over all modules and configurations.',
                'is_system' => true,
            ],
            [
                'name' => 'Presiding Elder',
                'slug' => 'elder',
                'description' => 'Senior leadership role with extensive permissions across all modules except system configuration.',
                'is_system' => true,
            ],
            [
                'name' => 'Local Secretary',
                'slug' => 'secretary',
                'description' => 'Administrative role focused on member management and communication.',
                'is_system' => true,
            ],
            [
                'name' => 'Financial Secretary',
                'slug' => 'finance',
                'description' => 'Specialized role for financial operations and accounting.',
                'is_system' => true,
            ],
            [
                'name' => 'Ministry Leader',
                'slug' => 'ministry_leader',
                'description' => 'Ministry-specific access with limited system permissions.',
                'is_system' => true,
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Self-service portal access for church members.',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
