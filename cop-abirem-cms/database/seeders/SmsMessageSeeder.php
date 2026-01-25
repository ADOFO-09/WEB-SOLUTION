<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'message_type' => 'bulk',
                'category' => 'general',
                'subject' => 'New Year Service Reminder',
                'message_content' => 'Dear Church Family, you are warmly invited to our New Year Thanksgiving Service on Sunday, Jan 5th at 8:00 AM. Theme: "New Beginnings". Come expecting a blessing! - COP Abirem Central',
                'recipient_count' => 95,
                'successful_count' => 92,
                'failed_count' => 3,
                'cost' => 9.50,
                'status' => 'sent',
                'scheduled_at' => null,
                'sent_at' => '2025-01-03 18:00:00',
                'sent_by' => 1,
            ],
            [
                'message_type' => 'automated',
                'category' => 'birthday',
                'subject' => 'Birthday Wishes - January Celebrants',
                'message_content' => 'Happy Birthday! May the Lord bless you with good health, prosperity, and divine favor this new year of your life. - COP Abirem Central Family',
                'recipient_count' => 8,
                'successful_count' => 8,
                'failed_count' => 0,
                'cost' => 0.80,
                'status' => 'sent',
                'scheduled_at' => '2025-01-01 06:00:00',
                'sent_at' => '2025-01-01 06:00:00',
                'sent_by' => 1,
            ],
            [
                'message_type' => 'bulk',
                'category' => 'event',
                'subject' => 'Youth Program Announcement',
                'message_content' => 'Dear Youth, join us for our first Youth Program of the year on Saturday, Jan 11th at 3:00 PM. Theme: "Pursuing Purpose". Invite a friend! - COP Abirem Youth Ministry',
                'recipient_count' => 35,
                'successful_count' => 34,
                'failed_count' => 1,
                'cost' => 3.50,
                'status' => 'sent',
                'scheduled_at' => null,
                'sent_at' => '2025-01-09 10:00:00',
                'sent_by' => 1,
            ],
            [
                'message_type' => 'automated',
                'category' => 'reminder',
                'subject' => 'Pledge Reminder',
                'message_content' => 'Dear Member, this is a gentle reminder of your building fund pledge. Your faithfulness is appreciated. God bless you! - COP Abirem Central',
                'recipient_count' => 5,
                'successful_count' => 5,
                'failed_count' => 0,
                'cost' => 0.50,
                'status' => 'sent',
                'scheduled_at' => '2025-01-15 09:00:00',
                'sent_at' => '2025-01-15 09:00:00',
                'sent_by' => 1,
            ],
            [
                'message_type' => 'bulk',
                'category' => 'general',
                'subject' => 'Week of Prayer',
                'message_content' => 'Dear Church Family, our Week of Prayer begins Monday, Jan 20th. Join us every evening at 6:00 PM for powerful prayer sessions. Let\'s seek God together! - COP Abirem Central',
                'recipient_count' => 95,
                'successful_count' => 0,
                'failed_count' => 0,
                'cost' => 0.00,
                'status' => 'scheduled',
                'scheduled_at' => '2025-01-18 18:00:00',
                'sent_at' => null,
                'sent_by' => 1,
            ],
        ];

        foreach ($messages as $message) {
            DB::table('sms_messages')->insert(array_merge($message, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
