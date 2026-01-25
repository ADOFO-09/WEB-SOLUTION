<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoginHistorySeeder extends Seeder
{
    public function run(): void
    {
        $history = [
            [
                'user_id' => 1,
                'login_at' => '2025-01-05 07:30:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-08 08:00:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-10 07:45:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-12 07:30:00',
                'ip_address' => '192.168.1.105',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Safari/604.1',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-14 09:00:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'failed',
                'failure_reason' => 'Invalid password',
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-14 09:02:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-15 08:15:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-17 17:30:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-19 07:30:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
            [
                'user_id' => 1,
                'login_at' => '2025-01-20 08:00:00',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
                'location' => 'Abirem, Ghana',
                'status' => 'success',
                'failure_reason' => null,
            ],
        ];

        foreach ($history as $record) {
            DB::table('login_history')->insert($record);
        }
    }
}
