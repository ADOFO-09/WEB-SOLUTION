<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist (assuming RoleSeeder has run first)
        $adminRole = Role::where('slug', 'admin')->first();
        if (!$adminRole) {
            throw new \Exception('Admin role not found. Ensure RoleSeeder runs before UserSeeder.');
        }

        // Create a default admin user with id=1 (to resolve foreign key issues in other seeders)
        User::create([
            'id' => 1,  // Explicitly set to ensure id=1 exists
            'role_id' => $adminRole->id,
            'member_id' => null,  // No associated member initially
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),  // Change this in production!
            'is_active' => 1,
            'must_change_password' => 1,
            'last_login_at' => null,
            'last_login_ip' => null,
            'login_attempts' => 0,
            'locked_until' => null,
            'created_by' => null,  // System-created
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);

        // Optionally, create additional users (e.g., a regular user)
        $userRole = Role::where('slug', 'member')->first();  // Assuming a 'member' role exists
        if ($userRole) {
            User::create([
                'role_id' => $userRole->id,
                'member_id' => null,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'is_active' => 1,
                'must_change_password' => 0,
                'last_login_at' => null,
                'last_login_ip' => null,
                'login_attempts' => 0,
                'locked_until' => null,
                'created_by' => 1,  // Created by admin
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]);
        }
    }
}