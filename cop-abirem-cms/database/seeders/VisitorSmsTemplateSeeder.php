<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitorSmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name'      => 'Visitor Welcome',
                'slug'      => 'visitor-welcome',
                'category'  => 'visitor',
                'content'   => 'Dear {name}, welcome to {church_name}! We are glad you visited us on {date}. We hope to see you again. God bless you! - {church_short_name}',
                'variables' => json_encode(['name', 'date']),
            ],
            [
                'name'      => 'Visitor Follow-up',
                'slug'      => 'visitor-followup',
                'category'  => 'visitor',
                'content'   => 'Dear {name}, greetings from {church_short_name}! We are following up on your visit with us. {notes}We would love to see you again. God bless you! - {church_short_name}',
                'variables' => json_encode(['name', 'notes']),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('sms_templates')->updateOrInsert(
                ['slug' => $template['slug']],
                array_merge($template, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
