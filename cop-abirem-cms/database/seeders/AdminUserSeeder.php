<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');

        DB::table('users')->insert([
            'name' => 'System Administrator',
            'email' => 'admin@copabirem.org',
            'password' => Hash::make('Admin@123!'),
            'role_id' => $adminRoleId,
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
