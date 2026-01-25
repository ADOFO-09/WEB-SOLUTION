<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamilyRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $relationships = [
            // Elder Kwame Mensah & Deaconess Ama Mensah (Spouses)
            ['member_id' => 1, 'related_member_id' => 2, 'relationship_type' => 'spouse'],
            ['member_id' => 2, 'related_member_id' => 1, 'relationship_type' => 'spouse'],
            
            // Deacon Emmanuel Frimpong & Mrs Comfort Frimpong (Spouses)
            ['member_id' => 7, 'related_member_id' => 8, 'relationship_type' => 'spouse'],
            ['member_id' => 8, 'related_member_id' => 7, 'relationship_type' => 'spouse'],
        ];

        foreach ($relationships as $relationship) {
            DB::table('family_relationships')->insert(array_merge($relationship, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
