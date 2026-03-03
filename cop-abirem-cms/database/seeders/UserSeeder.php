<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates test user accounts for each role.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Get role IDs
        $roles = DB::table('roles')->pluck('id', 'slug');

        $users = [
            // 1. System Administrator
            [
                'name' => 'System Administrator',
                'email' => 'admin@copabirem.org',
                'password' => Hash::make('Admin@123!'),
                'role_id' => $roles['admin'],
                'member_id' => null,
                'is_active' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 2. Presiding Elder
            [
                'name' => 'Elder Kwame Mensah',
                'email' => 'elder@copabirem.org',
                'password' => Hash::make('Elder@123!'),
                'role_id' => $roles['elder'],
                'member_id' => 1, // Link to first member (Elder Kwame Mensah)
                'is_active' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 3. Local Secretary
            [
                'name' => 'Ama Mensah',
                'email' => 'secretary@copabirem.org',
                'password' => Hash::make('Secretary@123!'),
                'role_id' => $roles['secretary'],
                'member_id' => 2, // Link to second member (Deaconess Ama Mensah)
                'is_active' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 4. Financial Secretary
            [
                'name' => 'Kofi Adjei',
                'email' => 'finance@copabirem.org',
                'password' => Hash::make('Finance@123!'),
                'role_id' => $roles['finance'],
                'member_id' => 3, // Link to third member (Kofi Adjei)
                'is_active' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 5. Ministry Leader
            [
                'name' => 'Emmanuel Frimpong',
                'email' => 'ministry@copabirem.org',
                'password' => Hash::make('Ministry@123!'),
                'role_id' => $roles['ministry_leader'],
                'member_id' => 7, // Link to Deacon Emmanuel Frimpong
                'is_active' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 6. General Member
            [
                'name' => 'Grace Appiah',
                'email' => 'member@copabirem.org',
                'password' => Hash::make('Member@123!'),
                'role_id' => $roles['member'],
                'member_id' => 10, // Link to Grace Appiah
                'is_active' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // DB::table('users')->insert($users);

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Find by email
                $userData // Update or create with this data
            );
        }

        $this->command->info('✓ ' . count($users) . ' test users seeded successfully.');
        $this->command->newLine();
        $this->command->info('=== TEST LOGIN CREDENTIALS ===');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['System Administrator', 'admin@copabirem.org', 'Admin@123!'],
                ['Presiding Elder', 'elder@copabirem.org', 'Elder@123!'],
                ['Local Secretary', 'secretary@copabirem.org', 'Secretary@123!'],
                ['Financial Secretary', 'finance@copabirem.org', 'Finance@123!'],
                ['Ministry Leader', 'ministry@copabirem.org', 'Ministry@123!'],
                ['Member', 'member@copabirem.org', 'Member@123!'],
            ]
        );
    }
}
