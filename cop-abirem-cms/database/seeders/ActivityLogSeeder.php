<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            [
                'user_id' => 1,
                'action' => 'login',
                'model_type' => 'App\\Models\\User',
                'model_id' => 1,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-05 07:30:00',
            ],
            [
                'user_id' => 1,
                'action' => 'create',
                'model_type' => 'App\\Models\\AttendanceSession',
                'model_id' => 1,
                'old_values' => null,
                'new_values' => json_encode(['service_date' => '2025-01-05', 'service_type_id' => 1]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-05 07:45:00',
            ],
            [
                'user_id' => 1,
                'action' => 'create',
                'model_type' => 'App\\Models\\Tithe',
                'model_id' => 1,
                'old_values' => null,
                'new_values' => json_encode(['member_id' => 1, 'amount' => 500.00]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-05 10:15:00',
            ],
            [
                'user_id' => 1,
                'action' => 'update',
                'model_type' => 'App\\Models\\AttendanceSession',
                'model_id' => 1,
                'old_values' => json_encode(['status' => 'open']),
                'new_values' => json_encode(['status' => 'closed', 'total_attendance' => 115]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-05 12:00:00',
            ],
            [
                'user_id' => 1,
                'action' => 'create',
                'model_type' => 'App\\Models\\Expense',
                'model_id' => 1,
                'old_values' => null,
                'new_values' => json_encode(['description' => 'January 2025 Electricity Bill', 'amount' => 450.00]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-10 09:30:00',
            ],
            [
                'user_id' => 1,
                'action' => 'update',
                'model_type' => 'App\\Models\\Expense',
                'model_id' => 1,
                'old_values' => json_encode(['status' => 'pending']),
                'new_values' => json_encode(['status' => 'paid', 'approved_by' => 1]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-10 10:00:00',
            ],
            [
                'user_id' => 1,
                'action' => 'create',
                'model_type' => 'App\\Models\\Member',
                'model_id' => 10,
                'old_values' => null,
                'new_values' => json_encode(['first_name' => 'Grace', 'last_name' => 'Appiah', 'member_id' => 'COP-2024-00010']),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-12 11:00:00',
            ],
            [
                'user_id' => 1,
                'action' => 'send_sms',
                'model_type' => 'App\\Models\\SmsMessage',
                'model_id' => 1,
                'old_values' => null,
                'new_values' => json_encode(['recipient_count' => 95, 'subject' => 'New Year Service Reminder']),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-03 18:00:00',
            ],
            [
                'user_id' => 1,
                'action' => 'create',
                'model_type' => 'App\\Models\\Visitor',
                'model_id' => 3,
                'old_values' => null,
                'new_values' => json_encode(['first_name' => 'Daniel', 'last_name' => 'Amponsah']),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-19 10:30:00',
            ],
            [
                'user_id' => 1,
                'action' => 'logout',
                'model_type' => 'App\\Models\\User',
                'model_id' => 1,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => '2025-01-19 17:00:00',
            ],
        ];

        foreach ($logs as $log) {
            DB::table('activity_logs')->insert($log);
        }
    }
}
