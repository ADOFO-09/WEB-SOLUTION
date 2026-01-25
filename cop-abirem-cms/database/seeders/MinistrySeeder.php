<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinistrySeeder extends Seeder
{
    public function run(): void
    {
        $ministries = [
            ['name' => "Men's Ministry (Deacons)", 'slug' => 'mens-ministry', 'description' => 'Ministry for adult male members', 'meeting_day' => 'Saturday', 'meeting_time' => '07:00:00'],
            ['name' => "Women's Ministry (Deaconesses)", 'slug' => 'womens-ministry', 'description' => 'Ministry for adult female members', 'meeting_day' => 'Saturday', 'meeting_time' => '07:00:00'],
            ['name' => 'Youth Ministry (PENSA/Youth)', 'slug' => 'youth-ministry', 'description' => 'Ministry for young adults and students', 'meeting_day' => 'Saturday', 'meeting_time' => '15:00:00'],
            ['name' => "Children's Ministry", 'slug' => 'childrens-ministry', 'description' => 'Ministry for children', 'meeting_day' => 'Sunday', 'meeting_time' => '08:00:00'],
            ['name' => 'Choir/Music Ministry', 'slug' => 'choir-ministry', 'description' => 'Ministry for worship and music', 'meeting_day' => 'Friday', 'meeting_time' => '18:00:00'],
            ['name' => 'Ushering Ministry', 'slug' => 'ushering-ministry', 'description' => 'Ministry for church ushers and protocol', 'meeting_day' => 'Saturday', 'meeting_time' => '09:00:00'],
            ['name' => 'Technical/Media Ministry', 'slug' => 'technical-ministry', 'description' => 'Ministry for sound, media, and technical support', 'meeting_day' => 'Saturday', 'meeting_time' => '10:00:00'],
            ['name' => 'Evangelism Ministry', 'slug' => 'evangelism-ministry', 'description' => 'Ministry for outreach and evangelism', 'meeting_day' => 'Saturday', 'meeting_time' => '06:00:00'],
            ['name' => 'Prayer Ministry', 'slug' => 'prayer-ministry', 'description' => 'Ministry for intercessory prayer', 'meeting_day' => 'Wednesday', 'meeting_time' => '05:00:00'],
            ['name' => 'Welfare Ministry', 'slug' => 'welfare-ministry', 'description' => 'Ministry for member welfare and benevolence', 'meeting_day' => null, 'meeting_time' => null],
        ];

        foreach ($ministries as $ministry) {
            DB::table('ministries')->insert(array_merge($ministry, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
