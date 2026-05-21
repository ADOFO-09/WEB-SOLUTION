<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $slugsToRemove = [
        'attendance.mark-face',   // face recognition not implemented
        'system.backup',          // duplicate of settings.backup
        'system.maintenance',     // duplicate of settings.maintenance
        'system.restore',         // no corresponding route/permission check
        'reports.finance',        // duplicate of reports.financial
        'roles.create',           // unused — RoleController has no permission middleware
        'roles.edit',             // unused — RoleController has no permission middleware
        'roles.delete',           // unused — RoleController has no permission middleware
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
        $permissions = [
            ['name' => 'Mark Attendance (Face Recognition)', 'slug' => 'attendance.mark-face',  'module' => 'attendance', 'description' => 'Mark attendance via face recognition',          'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Backup Database',                   'slug' => 'system.backup',           'module' => 'settings',   'description' => 'Create database backups',                       'created_at' => $now, 'updated_at' => $now],
            ['name' => 'System Maintenance',                'slug' => 'system.maintenance',      'module' => 'settings',   'description' => 'Perform system maintenance tasks',               'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Restore Database',                  'slug' => 'system.restore',          'module' => 'settings',   'description' => 'Restore from database backups',                 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'View Financial Reports',            'slug' => 'reports.finance',         'module' => 'reports',    'description' => 'View financial reports',                        'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Create Roles',                      'slug' => 'roles.create',            'module' => 'roles',      'description' => 'Create new roles',                              'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Edit Roles',                        'slug' => 'roles.edit',              'module' => 'roles',      'description' => 'Edit roles',                                    'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Delete Roles',                      'slug' => 'roles.delete',            'module' => 'roles',      'description' => 'Delete roles',                                  'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('permissions')->insert($permissions);
    }
};
