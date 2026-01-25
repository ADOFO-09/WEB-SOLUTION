<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Tithe Confirmation',
                'slug' => 'tithe-confirmation',
                'category' => 'financial',
                'content' => 'Dear {member_name}, thank you for your tithe of GHS {amount} received on {date}. Receipt No: {receipt_no}. God bless you abundantly! - COP Abirem Central',
                'variables' => json_encode(['member_name', 'amount', 'date', 'receipt_no']),
            ],
            [
                'name' => 'Donation Confirmation',
                'slug' => 'donation-confirmation',
                'category' => 'financial',
                'content' => 'Dear {member_name}, thank you for your generous donation of GHS {amount} towards {purpose}. Receipt No: {receipt_no}. May God reward you! - COP Abirem Central',
                'variables' => json_encode(['member_name', 'amount', 'purpose', 'receipt_no']),
            ],
            [
                'name' => 'Pledge Reminder',
                'slug' => 'pledge-reminder',
                'category' => 'reminder',
                'content' => 'Dear {member_name}, this is a gentle reminder of your pledge of GHS {total_amount}. Balance: GHS {balance}. Due: {due_date}. Thank you for your faithfulness! - COP Abirem Central',
                'variables' => json_encode(['member_name', 'total_amount', 'balance', 'due_date']),
            ],
            [
                'name' => 'Pledge Payment Confirmation',
                'slug' => 'pledge-payment-confirmation',
                'category' => 'financial',
                'content' => 'Dear {member_name}, your pledge payment of GHS {amount} has been received. New balance: GHS {balance}. Thank you! - COP Abirem Central',
                'variables' => json_encode(['member_name', 'amount', 'balance']),
            ],
            [
                'name' => 'Birthday Greeting',
                'slug' => 'birthday-greeting',
                'category' => 'birthday',
                'content' => 'Happy Birthday, {member_name}! May the Lord bless you with good health, prosperity, and divine favor this new year of your life. Enjoy your special day! - COP Abirem Central Family',
                'variables' => json_encode(['member_name']),
            ],
            [
                'name' => 'Service Reminder',
                'slug' => 'service-reminder',
                'category' => 'event',
                'content' => 'Dear {member_name}, you are warmly invited to {service_name} on {date} at {time}. Theme: {theme}. Come expecting a blessing! - COP Abirem Central',
                'variables' => json_encode(['member_name', 'service_name', 'date', 'time', 'theme']),
            ],
            [
                'name' => 'General Announcement',
                'slug' => 'general-announcement',
                'category' => 'general',
                'content' => 'Dear Church Family, {message} - COP Abirem Central',
                'variables' => json_encode(['message']),
            ],
            [
                'name' => 'Welcome New Member',
                'slug' => 'welcome-new-member',
                'category' => 'general',
                'content' => 'Welcome to the family, {member_name}! We are delighted to have you as a member of COP Abirem Central Assembly. Your Member ID is {member_id}. God bless you!',
                'variables' => json_encode(['member_name', 'member_id']),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('sms_templates')->insert(array_merge($template, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
