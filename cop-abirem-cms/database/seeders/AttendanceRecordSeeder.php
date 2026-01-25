<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $records = [];
        
        // Session 1 (Jan 5) - All 10 members attended
        for ($i = 1; $i <= 10; $i++) {
            $records[] = [
                'session_id' => 1,
                'member_id' => $i,
                'visitor_id' => null,
                'check_in_time' => '2025-01-05 07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'attendance_method' => 'manual',
                'is_late' => false,
                'marked_by' => 1,
            ];
        }
        
        // Session 2 (Jan 12) - 9 members attended
        for ($i = 1; $i <= 9; $i++) {
            $records[] = [
                'session_id' => 2,
                'member_id' => $i,
                'visitor_id' => null,
                'check_in_time' => '2025-01-12 07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'attendance_method' => 'manual',
                'is_late' => $i > 7,
                'marked_by' => 1,
            ];
        }
        
        // Session 3 (Jan 19) - All 10 members attended
        for ($i = 1; $i <= 10; $i++) {
            $records[] = [
                'session_id' => 3,
                'member_id' => $i,
                'visitor_id' => null,
                'check_in_time' => '2025-01-19 07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'attendance_method' => 'manual',
                'is_late' => $i > 8,
                'marked_by' => 1,
            ];
        }
        
        // Session 4 (Midweek Jan 8) - 6 members
        foreach ([1, 2, 3, 6, 7, 8] as $memberId) {
            $records[] = [
                'session_id' => 4,
                'member_id' => $memberId,
                'visitor_id' => null,
                'check_in_time' => '2025-01-08 17:' . str_pad(rand(45, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'attendance_method' => 'manual',
                'is_late' => false,
                'marked_by' => 1,
            ];
        }
        
        // Session 5 (Midweek Jan 15) - 7 members
        foreach ([1, 2, 3, 4, 6, 7, 8] as $memberId) {
            $records[] = [
                'session_id' => 5,
                'member_id' => $memberId,
                'visitor_id' => null,
                'check_in_time' => '2025-01-15 17:' . str_pad(rand(45, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'attendance_method' => 'manual',
                'is_late' => false,
                'marked_by' => 1,
            ];
        }
        
        // Session 6 (Friday Prayer Jan 10) - 5 members
        foreach ([1, 2, 7, 8, 9] as $memberId) {
            $records[] = [
                'session_id' => 6,
                'member_id' => $memberId,
                'visitor_id' => null,
                'check_in_time' => '2025-01-10 17:' . str_pad(rand(50, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'attendance_method' => 'manual',
                'is_late' => false,
                'marked_by' => 1,
            ];
        }
        
        // Session 7 (Friday Prayer Jan 17) - 6 members
        foreach ([1, 2, 4, 7, 8, 9] as $memberId) {
            $records[] = [
                'session_id' => 7,
                'member_id' => $memberId,
                'visitor_id' => null,
                'check_in_time' => '2025-01-17 17:' . str_pad(rand(50, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'attendance_method' => 'manual',
                'is_late' => false,
                'marked_by' => 1,
            ];
        }

        foreach ($records as $record) {
            DB::table('attendance_records')->insert(array_merge($record, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
