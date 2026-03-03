<?php

namespace Database\Seeders;

use App\Models\Role; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            [
                'name' => 'System Administrator',
                'slug' => 'admin',
                'description' => 'Full system access with complete control over all modules and configurations.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Presiding Elder',
                'slug' => 'elder',
                'description' => 'Senior leadership role with extensive permissions across all modules except system configuration.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Local Secretary',
                'slug' => 'secretary',
                'description' => 'Administrative role focused on member management and communication.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Financial Secretary',
                'slug' => 'finance',
                'description' => 'Specialized role for financial operations and accounting.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Ministry Leader',
                'slug' => 'ministry_leader',
                'description' => 'Ministry-specific access with limited system permissions.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Self-service portal access for church members.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']], // check by unique column
                $role
            );
        }
        

        $this->command->info('✓ 6 roles seeded successfully.');
        
    }
}
