<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Get all roles
        $roles = DB::table('roles')->pluck('id', 'slug');
        
        // Get all permissions
        $permissions = DB::table('permissions')->pluck('id', 'slug');

        $rolePermissions = [];

        // ============================================
        // HELPER — build role permission rows from slug arrays
        // ============================================
        $addPermissions = function (string $roleSlug, array $slugs) use (&$rolePermissions, $roles, $permissions, $now) {
            if (!isset($roles[$roleSlug])) {
                return;
            }
            foreach ($slugs as $slug) {
                if (isset($permissions[$slug])) {
                    $rolePermissions[] = [
                        'role_id'       => $roles[$roleSlug],
                        'permission_id' => $permissions[$slug],
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                }
            }
        };

        // ============================================
        // 1. SYSTEM ADMINISTRATOR — ALL permissions
        // ============================================
        $addPermissions('admin', $permissions->keys()->toArray());

        // ============================================
        // 2. PRESIDING ELDER
        //    All operations + expense approval. No system config.
        // ============================================
        $addPermissions('elder', [
            // Dashboard
            'dashboard.view', 'dashboard.stats',

            // Members — full access
            'members.view', 'members.create', 'members.edit', 'members.delete',
            'members.export', 'members.approve-updates',

            // Visitors — full access
            'visitors.view', 'visitors.create', 'visitors.edit', 'visitors.delete',
            'visitors.followup', 'visitors.convert',

            // Attendance — full access
            'attendance.view', 'attendance.create', 'attendance.mark',
            'attendance.mark-manual', 'attendance.mark-qr',
            'attendance.edit', 'attendance.delete',
            'service-types.view', 'service-types.manage',

            // Finance — view + approve (not create/edit/delete)
            'tithes.view', 'offerings.view', 'donations.view',
            'pledges.view', 'expenses.view', 'expenses.approve',
            'finance.view', 'receipts.view',

            // Communication
            'sms.view', 'sms.send', 'sms.bulk', 'sms.templates',
            'announcements.view', 'announcements.manage',

            // Ministries — view all
            'ministries.view',

            // Reports — full
            'reports.view', 'reports.generate', 'reports.export',
            'reports.financial', 'reports.membership', 'reports.attendance',

            // System — logs only
            'settings.logs',
        ]);

        // ============================================
        // 3. LOCAL SECRETARY
        //    Members, visitors, attendance, SMS, read-only finance
        // ============================================
        $addPermissions('secretary', [
            // Dashboard
            'dashboard.view', 'dashboard.stats',

            // Members — create & edit, no delete
            'members.view', 'members.create', 'members.edit', 'members.export',

            // Visitors — full access
            'visitors.view', 'visitors.create', 'visitors.edit', 'visitors.delete',
            'visitors.followup', 'visitors.convert',

            // Attendance — full except delete
            'attendance.view', 'attendance.create', 'attendance.mark',
            'attendance.mark-manual', 'attendance.mark-qr',
            'attendance.edit',
            'service-types.view',

            // Finance — read-only view
            'tithes.view', 'offerings.view', 'donations.view', 'pledges.view',
            'finance.summary_only',

            // Communication
            'sms.view', 'sms.send', 'sms.bulk', 'sms.templates',
            'announcements.view',

            // Ministries — view only
            'ministries.view',

            // Reports — membership & attendance
            'reports.view', 'reports.generate', 'reports.export',
            'reports.membership', 'reports.attendance',
        ]);

        // ============================================
        // 4. FINANCIAL SECRETARY
        //    All finance, limited members, no users/settings
        // ============================================
        $addPermissions('finance', [
            // Dashboard
            'dashboard.view', 'dashboard.stats',

            // Members — names only (limited)
            'members.view', 'members.view_names',

            // Finance — full CRUD except approve
            'tithes.view', 'tithes.create', 'tithes.edit', 'tithes.delete',
            'offerings.view', 'offerings.create', 'offerings.edit', 'offerings.delete',
            'donations.view', 'donations.create', 'donations.edit', 'donations.delete',
            'pledges.view', 'pledges.create', 'pledges.edit', 'pledges.delete', 'pledges.payments',
            'expenses.view', 'expenses.create', 'expenses.edit',
            'finance.view', 'finance.manage', 'finance.receipts',
            'receipts.view', 'receipts.issue', 'receipts.print',

            // Reports — financial only
            'reports.view', 'reports.generate', 'reports.export', 'reports.financial',
        ]);

        // ============================================
        // 5. MINISTRY LEADER
        //    Own ministry only — attendance, members, SMS
        // ============================================
        $addPermissions('ministry_leader', [
            // Dashboard
            'dashboard.view',

            // Own ministry only
            'ministry.own.view', 'ministry.own.attendance',
            'ministry.own.members', 'ministry.own.sms', 'ministry.own.reports',

            // Legacy compatibility
            'attendance.view', 'attendance.ministry_only',
            'sms.view', 'sms.ministry_only',
            'reports.view', 'reports.ministry_only',

            // Announcements — view only
            'announcements.view',
        ]);

        // ============================================
        // 6. MEMBER — Self-service portal only
        // ============================================
        $addPermissions('member', [
            // Legacy slugs
            'portal.access', 'portal.profile', 'portal.contributions', 'portal.attendance',

            // Canonical extended slugs
            'portal.dashboard', 'portal.profile.view', 'portal.profile.edit',
            'portal.contributions.view', 'portal.statements.download',
            'portal.announcements.view', 'portal.attendance.view',
        ]);

        DB::table('role_permission')->truncate();
        
        // Insert in chunks to avoid memory issues
        foreach (array_chunk($rolePermissions, 100) as $chunk) {
            DB::table('role_permission')->insert($chunk);
        }


        // Insert all role permissions
        // DB::table('role_permission')->insert($rolePermissions);

        $this->command->info('✓ ' . count($rolePermissions) . ' role-permission mappings seeded successfully.');
    }
}
