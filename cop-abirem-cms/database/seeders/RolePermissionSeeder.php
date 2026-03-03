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
        // 1. SYSTEM ADMINISTRATOR - Full Access
        // ============================================
        $adminPermissions = $permissions->keys()->toArray(); // ALL permissions
        foreach ($adminPermissions as $permSlug) {
            if (isset($permissions[$permSlug])) {
                $rolePermissions[] = [
                    'role_id' => $roles['admin'],
                    'permission_id' => $permissions[$permSlug],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ============================================
        // 2. PRESIDING ELDER - Everything except system settings
        // ============================================
        $elderPermissions = [
            // Dashboard
            'dashboard.view', 'dashboard.stats',
            
            // Members - Full access
            'members.view', 'members.create', 'members.edit', 'members.delete', 'members.export',
            
            // Visitors - Full access
            'visitors.view', 'visitors.create', 'visitors.edit', 'visitors.delete', 'visitors.followup',
            
            // Attendance - Full access
            'attendance.view', 'attendance.create', 'attendance.mark', 'attendance.edit', 'attendance.delete',
            'service-types.view', 'service-types.manage',
            
            // Finance - Full access including approval
            'tithes.view', 'tithes.create', 'tithes.edit', 'tithes.delete',
            'offerings.view', 'offerings.create', 'offerings.edit', 'offerings.delete',
            'donations.view', 'donations.create', 'donations.edit', 'donations.delete',
            'pledges.view', 'pledges.create', 'pledges.edit', 'pledges.delete', 'pledges.payments',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete', 'expenses.approve',
            'finance.view', 'finance.receipts',
            
            // SMS - Full access
            'sms.view', 'sms.send', 'sms.bulk', 'sms.templates',
            
            // Reports - Full access
            'reports.view', 'reports.generate', 'reports.export', 
            'reports.financial', 'reports.membership', 'reports.attendance',
            
            // Users - View only (cannot manage system users)
            'users.view',
            
            // Settings - View logs only
            'settings.logs',
        ];
        
        foreach ($elderPermissions as $permSlug) {
            if (isset($permissions[$permSlug])) {
                $rolePermissions[] = [
                    'role_id' => $roles['elder'],
                    'permission_id' => $permissions[$permSlug],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ============================================
        // 3. LOCAL SECRETARY - Member management & communication
        // ============================================
        $secretaryPermissions = [
            // Dashboard
            'dashboard.view', 'dashboard.stats',
            
            // Members - Full access
            'members.view', 'members.create', 'members.edit', 'members.delete', 'members.export',
            
            // Visitors - Full access
            'visitors.view', 'visitors.create', 'visitors.edit', 'visitors.delete', 'visitors.followup',
            
            // Attendance - Full access
            'attendance.view', 'attendance.create', 'attendance.mark', 'attendance.edit', 'attendance.delete',
            'service-types.view',
            
            // Finance - View only (read-only)
            'finance.summary_only',
            
            // SMS - Full access
            'sms.view', 'sms.send', 'sms.bulk', 'sms.templates',
            
            // Reports - Membership and attendance only
            'reports.view', 'reports.generate', 'reports.export',
            'reports.membership', 'reports.attendance',
        ];
        
        foreach ($secretaryPermissions as $permSlug) {
            if (isset($permissions[$permSlug])) {
                $rolePermissions[] = [
                    'role_id' => $roles['secretary'],
                    'permission_id' => $permissions[$permSlug],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ============================================
        // 4. FINANCIAL SECRETARY - Finance operations only
        // ============================================
        $financePermissions = [
            // Dashboard
            'dashboard.view', 'dashboard.stats',
            
            // Members - Names only (limited access)
            'members.view_names',
            
            // Finance - Full access except approval
            'tithes.view', 'tithes.create', 'tithes.edit', 'tithes.delete',
            'offerings.view', 'offerings.create', 'offerings.edit', 'offerings.delete',
            'donations.view', 'donations.create', 'donations.edit', 'donations.delete',
            'pledges.view', 'pledges.create', 'pledges.edit', 'pledges.delete', 'pledges.payments',
            'expenses.view', 'expenses.create', 'expenses.edit', // No delete, no approve
            'finance.view', 'finance.receipts',
            
            // Reports - Financial only
            'reports.view', 'reports.generate', 'reports.export',
            'reports.financial',
        ];
        
        foreach ($financePermissions as $permSlug) {
            if (isset($permissions[$permSlug])) {
                $rolePermissions[] = [
                    'role_id' => $roles['finance'],
                    'permission_id' => $permissions[$permSlug],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ============================================
        // 5. MINISTRY LEADER - Ministry-specific access
        // ============================================
        $ministryLeaderPermissions = [
            // Dashboard
            'dashboard.view',
            
            // Members - View only within ministry
            'members.view',
            
            // Attendance - Ministry only
            'attendance.view', 'attendance.ministry_only',
            
            // SMS - Ministry only
            'sms.view', 'sms.ministry_only',
            
            // Reports - Ministry only
            'reports.view', 'reports.ministry_only',
        ];
        
        foreach ($ministryLeaderPermissions as $permSlug) {
            if (isset($permissions[$permSlug])) {
                $rolePermissions[] = [
                    'role_id' => $roles['ministry_leader'],
                    'permission_id' => $permissions[$permSlug],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ============================================
        // 6. MEMBER - Self-service portal only
        // ============================================
        $memberPermissions = [
            // Portal access only
            'portal.access',
            'portal.profile',
            'portal.contributions',
            'portal.attendance',
        ];
        
        foreach ($memberPermissions as $permSlug) {
            if (isset($permissions[$permSlug])) {
                $rolePermissions[] = [
                    'role_id' => $roles['member'],
                    'permission_id' => $permissions[$permSlug],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

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
