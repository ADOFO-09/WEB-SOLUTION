<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitorVisitSeeder extends Seeder
{
    public function run(): void
    {
        $visits = [
            // Michael Osei visits
            ['visitor_id' => 1, 'visit_date' => '2025-01-05', 'service_type_id' => 1, 'notes' => 'First visit - Sunday Worship'],
            ['visitor_id' => 1, 'visit_date' => '2025-01-12', 'service_type_id' => 1, 'notes' => 'Second visit'],
            ['visitor_id' => 1, 'visit_date' => '2025-01-19', 'service_type_id' => 1, 'notes' => 'Third visit'],
            
            // Abena Darko visits
            ['visitor_id' => 2, 'visit_date' => '2025-01-12', 'service_type_id' => 1, 'notes' => 'First visit with family'],
            ['visitor_id' => 2, 'visit_date' => '2025-01-19', 'service_type_id' => 1, 'notes' => 'Second visit'],
            
            // Daniel Amponsah visits
            ['visitor_id' => 3, 'visit_date' => '2025-01-19', 'service_type_id' => 1, 'notes' => 'First visit'],
            
            // Esther Mensah visits
            ['visitor_id' => 4, 'visit_date' => '2025-01-12', 'service_type_id' => 1, 'notes' => 'First visit'],
            ['visitor_id' => 4, 'visit_date' => '2025-01-15', 'service_type_id' => 3, 'notes' => 'Midweek service'],
            ['visitor_id' => 4, 'visit_date' => '2025-01-19', 'service_type_id' => 1, 'notes' => 'Third visit - ready to join'],
            
            // Frank Boateng visits
            ['visitor_id' => 5, 'visit_date' => '2024-12-25', 'service_type_id' => 7, 'notes' => 'Christmas special program'],
        ];

        foreach ($visits as $visit) {
            DB::table('visitor_visits')->insert(array_merge($visit, [
                'created_at' => now(),
            ]));
        }
    }
}
