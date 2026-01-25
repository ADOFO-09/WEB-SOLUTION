<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberMinistrySeeder extends Seeder
{
    public function run(): void
    {
        $memberMinistries = [
            // Elder Kwame - Men's Ministry Leader
            ['member_id' => 1, 'ministry_id' => 1, 'role' => 'leader', 'joined_date' => '2010-01-01', 'is_active' => true],
            
            // Deaconess Ama - Women's Ministry Leader
            ['member_id' => 2, 'ministry_id' => 2, 'role' => 'leader', 'joined_date' => '2012-01-01', 'is_active' => true],
            
            // Kofi - Youth Ministry, Technical Ministry
            ['member_id' => 3, 'ministry_id' => 3, 'role' => 'member', 'joined_date' => '2018-02-01', 'is_active' => true],
            ['member_id' => 3, 'ministry_id' => 7, 'role' => 'leader', 'joined_date' => '2019-06-01', 'is_active' => true],
            
            // Akosua - Women's Ministry, Welfare Ministry
            ['member_id' => 4, 'ministry_id' => 2, 'role' => 'member', 'joined_date' => '2016-01-01', 'is_active' => true],
            ['member_id' => 4, 'ministry_id' => 10, 'role' => 'assistant_leader', 'joined_date' => '2020-01-01', 'is_active' => true],
            
            // Yaw - Youth Ministry, Choir
            ['member_id' => 5, 'ministry_id' => 3, 'role' => 'member', 'joined_date' => '2020-02-01', 'is_active' => true],
            ['member_id' => 5, 'ministry_id' => 5, 'role' => 'member', 'joined_date' => '2020-03-01', 'is_active' => true],
            
            // Adwoa - Youth Ministry, Ushering
            ['member_id' => 6, 'ministry_id' => 3, 'role' => 'assistant_leader', 'joined_date' => '2019-07-01', 'is_active' => true],
            ['member_id' => 6, 'ministry_id' => 6, 'role' => 'member', 'joined_date' => '2019-08-01', 'is_active' => true],
            
            // Deacon Emmanuel - Men's Ministry, Evangelism
            ['member_id' => 7, 'ministry_id' => 1, 'role' => 'assistant_leader', 'joined_date' => '2010-01-01', 'is_active' => true],
            ['member_id' => 7, 'ministry_id' => 8, 'role' => 'leader', 'joined_date' => '2015-01-01', 'is_active' => true],
            
            // Comfort - Women's Ministry, Prayer Ministry
            ['member_id' => 8, 'ministry_id' => 2, 'role' => 'member', 'joined_date' => '2009-01-01', 'is_active' => true],
            ['member_id' => 8, 'ministry_id' => 9, 'role' => 'leader', 'joined_date' => '2018-01-01', 'is_active' => true],
            
            // Samuel - Men's Ministry, Technical Ministry
            ['member_id' => 9, 'ministry_id' => 1, 'role' => 'member', 'joined_date' => '2021-03-01', 'is_active' => true],
            ['member_id' => 9, 'ministry_id' => 7, 'role' => 'member', 'joined_date' => '2021-04-01', 'is_active' => true],
            
            // Grace - Youth Ministry, Choir
            ['member_id' => 10, 'ministry_id' => 3, 'role' => 'member', 'joined_date' => '2022-09-01', 'is_active' => true],
            ['member_id' => 10, 'ministry_id' => 5, 'role' => 'member', 'joined_date' => '2022-10-01', 'is_active' => true],
        ];

        foreach ($memberMinistries as $mm) {
            DB::table('member_ministry')->insert(array_merge($mm, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
