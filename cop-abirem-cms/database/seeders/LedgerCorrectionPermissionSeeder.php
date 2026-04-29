<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class LedgerCorrectionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Corrections',   'slug' => 'corrections.view',    'module' => 'corrections', 'description' => 'View ledger corrections and audit history'],
            ['name' => 'Void Entries',        'slug' => 'corrections.void',    'module' => 'corrections', 'description' => 'Void financial ledger entries'],
            ['name' => 'Restore Entries',     'slug' => 'corrections.restore', 'module' => 'corrections', 'description' => 'Restore voided entries to active'],
            ['name' => 'Create Adjustments',  'slug' => 'corrections.adjust',  'module' => 'corrections', 'description' => 'Create adjustment entries for existing records'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['slug' => $perm['slug']], $perm);
        }

        // Assign to admin (all), elder (view+restore), finance (all corrections)
        $slugMap = [
            'admin'   => ['corrections.view', 'corrections.void', 'corrections.restore', 'corrections.adjust'],
            'elder'   => ['corrections.view', 'corrections.restore'],
            'finance' => ['corrections.view', 'corrections.void', 'corrections.restore', 'corrections.adjust'],
        ];

        foreach ($slugMap as $roleSlug => $permSlugs) {
            $role = Role::where('slug', $roleSlug)->first();
            if (!$role) continue;
            $permIds = Permission::whereIn('slug', $permSlugs)->pluck('id');
            $role->permissions()->syncWithoutDetaching($permIds);
        }

        $this->command->info('✓ Ledger correction permissions seeded.');
    }
}
