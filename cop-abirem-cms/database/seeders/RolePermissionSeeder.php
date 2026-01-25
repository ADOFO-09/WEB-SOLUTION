<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'slug');
        $permissions = DB::table('permissions')->pluck('id', 'slug');

        $rolePermissions = [
            // Admin - ALL permissions
            'admin' => $permissions->keys()->toArray(),

            // Elder
            'elder' => [
                'users.view',
                'members.view', 'members.create', 'members.edit', 'members.export',
                'visitors.view', 'visitors.create', 'visitors.edit', 'visitors.convert',
                'ministries.view', 'ministries.create', 'ministries.edit',
                'attendance.view', 'attendance.mark', 'attendance.sessions',
                'finance.view', 'finance.tithe', 'finance.offering', 'finance.donation', 'finance.pledge', 'finance.expense', 'finance.approve',
                'sms.send', 'sms.reports', 'sms.templates',
                'reports.view', 'reports.export', 'reports.finance',
                'assets.view', 'assets.create', 'assets.edit',
            ],

            // Secretary
            'secretary' => [
                'members.view', 'members.create', 'members.edit', 'members.export',
                'visitors.view', 'visitors.create', 'visitors.edit', 'visitors.convert',
                'ministries.view',
                'attendance.view', 'attendance.mark', 'attendance.sessions',
                'finance.view',
                'sms.send', 'sms.reports',
                'reports.view', 'reports.export',
            ],

            // Finance
            'finance' => [
                'members.view',
                'finance.view', 'finance.tithe', 'finance.offering', 'finance.donation', 'finance.pledge', 'finance.expense',
                'sms.send',
                'reports.view', 'reports.export', 'reports.finance',
            ],

            // Ministry Leader
            'ministry_leader' => [
                'members.view',
                'attendance.view', 'attendance.mark',
                'sms.send',
                'reports.view',
            ],

            // Member - Portal access only (no admin permissions)
            'member' => [],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            if (!isset($roles[$roleSlug])) continue;
            
            $roleId = $roles[$roleSlug];
            
            foreach ($permissionSlugs as $permSlug) {
                if (!isset($permissions[$permSlug])) continue;
                
                DB::table('role_permission')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissions[$permSlug],
                    'created_at' => now(),
                ]);
            }
        }
    }
}
