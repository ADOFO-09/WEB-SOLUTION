<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowUpLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            // Michael Osei follow-ups
            [
                'visitor_id' => 1,
                'contacted_by' => 1,
                'contact_date' => '2025-01-07',
                'contact_method' => 'phone',
                'outcome' => 'reached',
                'notes' => 'Spoke with him, very interested in the church',
                'next_follow_up_date' => '2025-01-14',
            ],
            [
                'visitor_id' => 1,
                'contacted_by' => 1,
                'contact_date' => '2025-01-14',
                'contact_method' => 'whatsapp',
                'outcome' => 'interested',
                'notes' => 'Confirmed attendance for next Sunday',
                'next_follow_up_date' => null,
            ],
            
            // Abena Darko follow-ups
            [
                'visitor_id' => 2,
                'contacted_by' => 1,
                'contact_date' => '2025-01-14',
                'contact_method' => 'phone',
                'outcome' => 'reached',
                'notes' => 'Family is settling in, will visit again',
                'next_follow_up_date' => '2025-01-21',
            ],
            
            // Daniel Amponsah - pending
            [
                'visitor_id' => 3,
                'contacted_by' => 1,
                'contact_date' => '2025-01-20',
                'contact_method' => 'phone',
                'outcome' => 'no_answer',
                'notes' => 'No answer, will try again',
                'next_follow_up_date' => '2025-01-22',
            ],
            
            // Esther Mensah follow-ups
            [
                'visitor_id' => 4,
                'contacted_by' => 1,
                'contact_date' => '2025-01-13',
                'contact_method' => 'phone',
                'outcome' => 'interested',
                'notes' => 'Very interested in membership',
                'next_follow_up_date' => '2025-01-20',
            ],
            
            // Frank Boateng follow-ups
            [
                'visitor_id' => 5,
                'contacted_by' => 1,
                'contact_date' => '2024-12-28',
                'contact_method' => 'phone',
                'outcome' => 'not_interested',
                'notes' => 'Lives too far, attends church in Nkawkaw',
                'next_follow_up_date' => null,
            ],
        ];

        foreach ($logs as $log) {
            DB::table('follow_up_logs')->insert(array_merge($log, [
                'created_at' => now(),
            ]));
        }
    }
}
