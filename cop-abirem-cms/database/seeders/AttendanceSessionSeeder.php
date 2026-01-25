<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            // January 2025 Sunday Services
            [
                'service_type_id' => 1,
                'ministry_id' => null,
                'service_date' => '2025-01-05',
                'start_time' => '08:00:00',
                'end_time' => '11:30:00',
                'theme' => 'New Year Thanksgiving',
                'preacher' => 'Pastor James Mensah',
                'total_members' => 85,
                'total_visitors' => 5,
                'total_children' => 25,
                'total_attendance' => 115,
                'status' => 'closed',
                'created_by' => 1,
                'closed_by' => 1,
                'closed_at' => '2025-01-05 12:00:00',
            ],
            [
                'service_type_id' => 1,
                'ministry_id' => null,
                'service_date' => '2025-01-12',
                'start_time' => '08:00:00',
                'end_time' => '11:00:00',
                'theme' => 'Walking in Divine Purpose',
                'preacher' => 'Elder Kwame Mensah',
                'total_members' => 92,
                'total_visitors' => 8,
                'total_children' => 28,
                'total_attendance' => 128,
                'status' => 'closed',
                'created_by' => 1,
                'closed_by' => 1,
                'closed_at' => '2025-01-12 12:00:00',
            ],
            [
                'service_type_id' => 1,
                'ministry_id' => null,
                'service_date' => '2025-01-19',
                'start_time' => '08:00:00',
                'end_time' => '11:15:00',
                'theme' => 'Faith that Moves Mountains',
                'preacher' => 'Pastor James Mensah',
                'total_members' => 88,
                'total_visitors' => 6,
                'total_children' => 30,
                'total_attendance' => 124,
                'status' => 'closed',
                'created_by' => 1,
                'closed_by' => 1,
                'closed_at' => '2025-01-19 12:00:00',
            ],
            
            // Midweek Services
            [
                'service_type_id' => 3,
                'ministry_id' => null,
                'service_date' => '2025-01-08',
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'theme' => 'Bible Study: Book of Romans',
                'preacher' => 'Elder Kwame Mensah',
                'total_members' => 45,
                'total_visitors' => 2,
                'total_children' => 10,
                'total_attendance' => 57,
                'status' => 'closed',
                'created_by' => 1,
                'closed_by' => 1,
                'closed_at' => '2025-01-08 20:30:00',
            ],
            [
                'service_type_id' => 3,
                'ministry_id' => null,
                'service_date' => '2025-01-15',
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'theme' => 'Bible Study: Book of Romans (Cont.)',
                'preacher' => 'Deacon Emmanuel Frimpong',
                'total_members' => 48,
                'total_visitors' => 3,
                'total_children' => 8,
                'total_attendance' => 59,
                'status' => 'closed',
                'created_by' => 1,
                'closed_by' => 1,
                'closed_at' => '2025-01-15 20:30:00',
            ],
            
            // Friday Prayer Meetings
            [
                'service_type_id' => 4,
                'ministry_id' => null,
                'service_date' => '2025-01-10',
                'start_time' => '18:00:00',
                'end_time' => '20:30:00',
                'theme' => 'Prayers for the Nation',
                'preacher' => null,
                'total_members' => 35,
                'total_visitors' => 1,
                'total_children' => 5,
                'total_attendance' => 41,
                'status' => 'closed',
                'created_by' => 1,
                'closed_by' => 1,
                'closed_at' => '2025-01-10 21:00:00',
            ],
            [
                'service_type_id' => 4,
                'ministry_id' => null,
                'service_date' => '2025-01-17',
                'start_time' => '18:00:00',
                'end_time' => '20:30:00',
                'theme' => 'Prayers for Families',
                'preacher' => null,
                'total_members' => 38,
                'total_visitors' => 0,
                'total_children' => 6,
                'total_attendance' => 44,
                'status' => 'closed',
                'created_by' => 1,
                'closed_by' => 1,
                'closed_at' => '2025-01-17 21:00:00',
            ],
        ];

        foreach ($sessions as $session) {
            DB::table('attendance_sessions')->insert(array_merge($session, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
