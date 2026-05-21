<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $slugsToRemove = [
        'announcements.view',
        'announcements.manage',
        'portal.announcements.view',
    ];

    public function up(): void
    {
        $ids = DB::table('permissions')
            ->whereIn('slug', $this->slugsToRemove)
            ->pluck('id');

        if ($ids->isNotEmpty()) {
            DB::table('role_permission')->whereIn('permission_id', $ids)->delete();
            DB::table('permissions')->whereIn('id', $ids)->delete();
        }
    }

    public function down(): void
    {
        $now = now();
        DB::table('permissions')->insert([
            ['name' => 'View Announcements',         'slug' => 'announcements.view',        'module' => 'communication', 'description' => 'View church announcements',           'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Announcements',        'slug' => 'announcements.manage',      'module' => 'communication', 'description' => 'Create and edit announcements',        'created_at' => $now, 'updated_at' => $now],
            ['name' => 'View Announcements (Portal)', 'slug' => 'portal.announcements.view', 'module' => 'portal',        'description' => 'View church announcements via portal', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
};
