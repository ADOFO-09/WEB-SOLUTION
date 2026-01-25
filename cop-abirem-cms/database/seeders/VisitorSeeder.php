<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        $visitors = [
            [
                'first_name' => 'Michael',
                'last_name' => 'Osei',
                'phone' => '0248889999',
                'email' => 'michael.osei@email.com',
                'address' => 'Kade Town',
                'first_visit_date' => '2025-01-05',
                'referral_source' => 'member',
                'referred_by_member_id' => 1,
                'prayer_request' => 'Pray for my job search',
                'follow_up_status' => 'contacted',
                'notes' => 'Interested in joining the youth ministry',
                'created_by' => 1,
            ],
            [
                'first_name' => 'Abena',
                'last_name' => 'Darko',
                'phone' => '0557776655',
                'email' => null,
                'address' => 'Akim Oda',
                'first_visit_date' => '2025-01-12',
                'referral_source' => 'member',
                'referred_by_member_id' => 2,
                'prayer_request' => null,
                'follow_up_status' => 'interested',
                'notes' => 'Came with family, looking for a church home',
                'created_by' => 1,
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Amponsah',
                'phone' => '0203334455',
                'email' => 'daniel.amp@email.com',
                'address' => 'Abirem',
                'first_visit_date' => '2025-01-19',
                'referral_source' => 'walk_in',
                'referred_by_member_id' => null,
                'prayer_request' => 'Family healing',
                'follow_up_status' => 'pending',
                'notes' => 'Walked in, looking for a church home',
                'created_by' => 1,
            ],
            [
                'first_name' => 'Esther',
                'last_name' => 'Mensah',
                'phone' => '0244998877',
                'email' => 'esther.m@email.com',
                'address' => 'Abirem New Site',
                'first_visit_date' => '2025-01-12',
                'referral_source' => 'social_media',
                'referred_by_member_id' => null,
                'prayer_request' => null,
                'follow_up_status' => 'converted',
                'notes' => 'Found us on Facebook',
                'converted_to_member_id' => null,
                'created_by' => 1,
            ],
            [
                'first_name' => 'Frank',
                'last_name' => 'Boateng',
                'phone' => '0201122334',
                'email' => null,
                'address' => 'Nkawkaw',
                'first_visit_date' => '2024-12-25',
                'referral_source' => 'event',
                'referred_by_member_id' => null,
                'prayer_request' => 'Business breakthrough',
                'follow_up_status' => 'not_interested',
                'notes' => 'Attended Christmas service, lives too far',
                'created_by' => 1,
            ],
        ];

        foreach ($visitors as $visitor) {
            DB::table('visitors')->insert(array_merge($visitor, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
