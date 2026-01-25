<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $maintenance = [
            [
                'asset_id' => 2, // Drum Set
                'maintenance_type' => 'service',
                'description' => 'Regular tuning and cymbal polishing',
                'cost' => 150.00,
                'maintenance_date' => '2024-12-15',
                'next_maintenance_date' => '2025-06-15',
                'performed_by' => 'Music Ministry Team',
                'vendor' => null,
                'notes' => 'Routine maintenance',
                'created_by' => 1,
            ],
            [
                'asset_id' => 3, // Digital Mixer
                'maintenance_type' => 'inspection',
                'description' => 'Quarterly inspection and firmware update',
                'cost' => 0.00,
                'maintenance_date' => '2025-01-05',
                'next_maintenance_date' => '2025-04-05',
                'performed_by' => 'Technical Ministry',
                'vendor' => null,
                'notes' => 'All systems functioning properly',
                'created_by' => 1,
            ],
            [
                'asset_id' => 6, // Plastic Chairs
                'maintenance_type' => 'repair',
                'description' => 'Replaced 10 broken chairs',
                'cost' => 300.00,
                'maintenance_date' => '2024-11-20',
                'next_maintenance_date' => null,
                'performed_by' => null,
                'vendor' => 'Accra Furniture Works',
                'notes' => 'Chairs replaced under bulk purchase agreement',
                'created_by' => 1,
            ],
            [
                'asset_id' => 8, // Projector
                'maintenance_type' => 'cleaning',
                'description' => 'Lens cleaning and dust removal',
                'cost' => 50.00,
                'maintenance_date' => '2025-01-10',
                'next_maintenance_date' => '2025-07-10',
                'performed_by' => 'Technical Ministry',
                'vendor' => null,
                'notes' => 'Regular cleaning schedule',
                'created_by' => 1,
            ],
            [
                'asset_id' => 10, // Printer
                'maintenance_type' => 'service',
                'description' => 'Toner replacement and roller cleaning',
                'cost' => 250.00,
                'maintenance_date' => '2025-01-15',
                'next_maintenance_date' => '2025-04-15',
                'performed_by' => null,
                'vendor' => 'CompuGhana Service Center',
                'notes' => 'Replaced toner cartridge',
                'created_by' => 1,
            ],
        ];

        foreach ($maintenance as $record) {
            DB::table('asset_maintenance')->insert(array_merge($record, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
