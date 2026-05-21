<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard', 'description' => 'Access the dashboard'],
            ['name' => 'View Dashboard Stats', 'slug' => 'dashboard.stats', 'module' => 'dashboard', 'description' => 'View dashboard statistics'],

            // Members
            ['name' => 'View Members', 'slug' => 'members.view', 'module' => 'members', 'description' => 'View member list and profiles'],
            ['name' => 'Create Members', 'slug' => 'members.create', 'module' => 'members', 'description' => 'Add new members'],
            ['name' => 'Edit Members', 'slug' => 'members.edit', 'module' => 'members', 'description' => 'Update member information'],
            ['name' => 'Delete Members', 'slug' => 'members.delete', 'module' => 'members', 'description' => 'Remove members from system'],
            ['name' => 'Export Members', 'slug' => 'members.export', 'module' => 'members', 'description' => 'Export member data'],
            ['name' => 'View Member Names Only', 'slug' => 'members.view_names', 'module' => 'members', 'description' => 'View only member names (limited access)'],

            // Visitors
            ['name' => 'View Visitors', 'slug' => 'visitors.view', 'module' => 'visitors', 'description' => 'View visitor records'],
            ['name' => 'Create Visitors', 'slug' => 'visitors.create', 'module' => 'visitors', 'description' => 'Register new visitors'],
            ['name' => 'Edit Visitors', 'slug' => 'visitors.edit', 'module' => 'visitors', 'description' => 'Update visitor information'],
            ['name' => 'Delete Visitors', 'slug' => 'visitors.delete', 'module' => 'visitors', 'description' => 'Remove visitor records'],
            ['name' => 'Follow Up Visitors', 'slug' => 'visitors.followup', 'module' => 'visitors', 'description' => 'Manage visitor follow-ups'],

            // Attendance
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'module' => 'attendance', 'description' => 'View attendance records'],
            ['name' => 'Create Attendance', 'slug' => 'attendance.create', 'module' => 'attendance', 'description' => 'Create attendance sessions'],
            ['name' => 'Mark Attendance', 'slug' => 'attendance.mark', 'module' => 'attendance', 'description' => 'Mark member attendance'],
            ['name' => 'Edit Attendance', 'slug' => 'attendance.edit', 'module' => 'attendance', 'description' => 'Modify attendance records'],
            ['name' => 'Delete Attendance', 'slug' => 'attendance.delete', 'module' => 'attendance', 'description' => 'Delete attendance records'],
            ['name' => 'Ministry Attendance Only', 'slug' => 'attendance.ministry_only', 'module' => 'attendance', 'description' => 'Mark attendance for own ministry only'],

            // Service Types
            ['name' => 'View Service Types', 'slug' => 'service-types.view', 'module' => 'service-types', 'description' => 'View service types'],
            ['name' => 'Manage Service Types', 'slug' => 'service-types.manage', 'module' => 'service-types', 'description' => 'Create, edit, delete service types'],

            // Finance - Tithes
            ['name' => 'View Tithes', 'slug' => 'tithes.view', 'module' => 'finance', 'description' => 'View tithe records'],
            ['name' => 'Create Tithes', 'slug' => 'tithes.create', 'module' => 'finance', 'description' => 'Record new tithes'],
            ['name' => 'Edit Tithes', 'slug' => 'tithes.edit', 'module' => 'finance', 'description' => 'Modify tithe records'],
            ['name' => 'Delete Tithes', 'slug' => 'tithes.delete', 'module' => 'finance', 'description' => 'Delete tithe records'],

            // Finance - Offerings
            ['name' => 'View Offerings', 'slug' => 'offerings.view', 'module' => 'finance', 'description' => 'View offering records'],
            ['name' => 'Create Offerings', 'slug' => 'offerings.create', 'module' => 'finance', 'description' => 'Record new offerings'],
            ['name' => 'Edit Offerings', 'slug' => 'offerings.edit', 'module' => 'finance', 'description' => 'Modify offering records'],
            ['name' => 'Delete Offerings', 'slug' => 'offerings.delete', 'module' => 'finance', 'description' => 'Delete offering records'],

            // Finance - Donations
            ['name' => 'View Donations', 'slug' => 'donations.view', 'module' => 'finance', 'description' => 'View donation records'],
            ['name' => 'Create Donations', 'slug' => 'donations.create', 'module' => 'finance', 'description' => 'Record new donations'],
            ['name' => 'Edit Donations', 'slug' => 'donations.edit', 'module' => 'finance', 'description' => 'Modify donation records'],
            ['name' => 'Delete Donations', 'slug' => 'donations.delete', 'module' => 'finance', 'description' => 'Delete donation records'],

            // Finance - Pledges
            ['name' => 'View Pledges', 'slug' => 'pledges.view', 'module' => 'finance', 'description' => 'View pledge records'],
            ['name' => 'Create Pledges', 'slug' => 'pledges.create', 'module' => 'finance', 'description' => 'Record new pledges'],
            ['name' => 'Edit Pledges', 'slug' => 'pledges.edit', 'module' => 'finance', 'description' => 'Modify pledge records'],
            ['name' => 'Delete Pledges', 'slug' => 'pledges.delete', 'module' => 'finance', 'description' => 'Delete pledge records'],
            ['name' => 'Record Pledge Payments', 'slug' => 'pledges.payments', 'module' => 'finance', 'description' => 'Record pledge payments'],

            // Finance - Expenses
            ['name' => 'View Expenses', 'slug' => 'expenses.view', 'module' => 'finance', 'description' => 'View expense records'],
            ['name' => 'Create Expenses', 'slug' => 'expenses.create', 'module' => 'finance', 'description' => 'Record new expenses'],
            ['name' => 'Edit Expenses', 'slug' => 'expenses.edit', 'module' => 'finance', 'description' => 'Modify expense records'],
            ['name' => 'Delete Expenses', 'slug' => 'expenses.delete', 'module' => 'finance', 'description' => 'Delete expense records'],
            ['name' => 'Approve Expenses', 'slug' => 'expenses.approve', 'module' => 'finance', 'description' => 'Approve expense requests'],

            // Finance - General
            ['name' => 'View Finance Dashboard', 'slug' => 'finance.view', 'module' => 'finance', 'description' => 'View financial dashboard and summaries'],
            ['name' => 'Manage Finance Setup', 'slug' => 'finance.manage', 'module' => 'finance', 'description' => 'Manage financial years and projects'],
            ['name' => 'Issue Receipts', 'slug' => 'finance.receipts', 'module' => 'finance', 'description' => 'Issue and print receipts'],
            ['name' => 'View Finance Summary Only', 'slug' => 'finance.summary_only', 'module' => 'finance', 'description' => 'View financial summaries (read-only)'],

            // SMS
            ['name' => 'View SMS', 'slug' => 'sms.view', 'module' => 'sms', 'description' => 'View SMS messages'],
            ['name' => 'Send SMS', 'slug' => 'sms.send', 'module' => 'sms', 'description' => 'Send SMS messages'],
            ['name' => 'Send Bulk SMS', 'slug' => 'sms.bulk', 'module' => 'sms', 'description' => 'Send bulk SMS to groups'],
            ['name' => 'Manage SMS Templates', 'slug' => 'sms.templates', 'module' => 'sms', 'description' => 'Create and manage SMS templates'],
            ['name' => 'Ministry SMS Only', 'slug' => 'sms.ministry_only', 'module' => 'sms', 'description' => 'Send SMS to own ministry only'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'View all reports'],
            ['name' => 'Generate Reports', 'slug' => 'reports.generate', 'module' => 'reports', 'description' => 'Generate various reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports', 'description' => 'Export reports to PDF/Excel'],
            ['name' => 'Financial Reports', 'slug' => 'reports.financial', 'module' => 'reports', 'description' => 'View financial reports'],
            ['name' => 'Membership Reports', 'slug' => 'reports.membership', 'module' => 'reports', 'description' => 'View membership reports'],
            ['name' => 'Attendance Reports', 'slug' => 'reports.attendance', 'module' => 'reports', 'description' => 'View attendance reports'],
            ['name' => 'Monthly Reports', 'slug' => 'reports.monthly', 'module' => 'reports', 'description' => 'Create and manage monthly statistical reports'],
            ['name' => 'Ministry Reports Only', 'slug' => 'reports.ministry_only', 'module' => 'reports', 'description' => 'View own ministry reports only'],

            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users', 'description' => 'View user accounts'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Create new user accounts'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Edit user accounts'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Delete user accounts'],
            ['name' => 'Manage Roles', 'slug' => 'users.roles', 'module' => 'users', 'description' => 'Manage user roles'],

            // Settings
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'settings', 'description' => 'View system settings'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings', 'description' => 'Configure system settings'],
            ['name' => 'View Activity Logs', 'slug' => 'settings.logs', 'module' => 'settings', 'description' => 'View system activity logs'],
            ['name' => 'Manage Backups', 'slug' => 'settings.backup', 'module' => 'settings', 'description' => 'Create and restore backups'],
            ['name' => 'System Maintenance', 'slug' => 'settings.maintenance', 'module' => 'settings', 'description' => 'System maintenance operations'],

            // Member Portal (legacy slugs — kept for backward compatibility)
            ['name' => 'Access Member Portal', 'slug' => 'portal.access', 'module' => 'portal', 'description' => 'Access member self-service portal'],
            ['name' => 'View Own Profile', 'slug' => 'portal.profile', 'module' => 'portal', 'description' => 'View own profile information'],
            ['name' => 'View Own Contributions', 'slug' => 'portal.contributions', 'module' => 'portal', 'description' => 'View own tithes, offerings, donations, pledges'],
            ['name' => 'View Own Attendance', 'slug' => 'portal.attendance', 'module' => 'portal', 'description' => 'View own attendance history'],

            // -----------------------------------------------
            // EXTENDED PERMISSIONS
            // -----------------------------------------------

            // Members - extended
            ['name' => 'Approve Member Updates', 'slug' => 'members.approve-updates', 'module' => 'members', 'description' => 'Approve member contact update requests'],

            // Visitors - extended
            ['name' => 'Convert Visitors to Members', 'slug' => 'visitors.convert', 'module' => 'visitors', 'description' => 'Convert visitors to full members'],

            // Attendance - method-specific marking
            ['name' => 'Mark Attendance (Manual)', 'slug' => 'attendance.mark-manual', 'module' => 'attendance', 'description' => 'Mark attendance manually from list'],
            ['name' => 'Mark Attendance (QR Code)', 'slug' => 'attendance.mark-qr', 'module' => 'attendance', 'description' => 'Mark attendance via QR code scan'],
            ['name' => 'Mark Attendance (Biometric)', 'slug' => 'attendance.mark-biometric', 'module' => 'attendance', 'description' => 'Mark attendance via biometric device'],

            // Finance - Receipts
            ['name' => 'View Receipts', 'slug' => 'receipts.view', 'module' => 'finance', 'description' => 'View issued receipts'],
            ['name' => 'Issue Receipts', 'slug' => 'receipts.issue', 'module' => 'finance', 'description' => 'Issue receipts for contributions'],
            ['name' => 'Print Receipts', 'slug' => 'receipts.print', 'module' => 'finance', 'description' => 'Print contribution receipts'],

            // Ministries
            ['name' => 'View All Ministries', 'slug' => 'ministries.view', 'module' => 'ministries', 'description' => 'View all ministry groups'],
            ['name' => 'Create Ministries', 'slug' => 'ministries.create', 'module' => 'ministries', 'description' => 'Create new ministry groups'],
            ['name' => 'Edit Ministries', 'slug' => 'ministries.edit', 'module' => 'ministries', 'description' => 'Edit ministry groups and manage their members'],
            ['name' => 'Delete Ministries', 'slug' => 'ministries.delete', 'module' => 'ministries', 'description' => 'Delete ministry groups'],
            ['name' => 'Manage Ministries', 'slug' => 'ministries.manage', 'module' => 'ministries', 'description' => 'Create, edit, delete ministry groups'],

            // Ministry Leader — own ministry only
            ['name' => 'View Own Ministry', 'slug' => 'ministry.own.view', 'module' => 'ministries', 'description' => 'View own ministry details'],
            ['name' => 'Own Ministry Attendance', 'slug' => 'ministry.own.attendance', 'module' => 'ministries', 'description' => 'Mark attendance for own ministry'],
            ['name' => 'Own Ministry Members', 'slug' => 'ministry.own.members', 'module' => 'ministries', 'description' => 'View members of own ministry'],
            ['name' => 'Own Ministry SMS', 'slug' => 'ministry.own.sms', 'module' => 'ministries', 'description' => 'Send SMS to own ministry members'],
            ['name' => 'Own Ministry Reports', 'slug' => 'ministry.own.reports', 'module' => 'ministries', 'description' => 'Generate reports for own ministry'],

            // Roles & Permissions management
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'users', 'description' => 'View system roles'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'users', 'description' => 'Create and edit roles'],
            ['name' => 'Assign Permissions', 'slug' => 'permissions.assign', 'module' => 'users', 'description' => 'Assign permissions to roles'],


            // Member Portal — canonical extended slugs
            ['name' => 'Access Dashboard (Portal)', 'slug' => 'portal.dashboard', 'module' => 'portal', 'description' => 'Access the member portal dashboard'],
            ['name' => 'View Own Profile (Portal)', 'slug' => 'portal.profile.view', 'module' => 'portal', 'description' => 'View own profile via portal'],
            ['name' => 'Request Profile Update', 'slug' => 'portal.profile.edit', 'module' => 'portal', 'description' => 'Request contact info updates via portal'],
            ['name' => 'View Own Contributions (Portal)', 'slug' => 'portal.contributions.view', 'module' => 'portal', 'description' => 'View own contribution history via portal'],
            ['name' => 'Download Statements', 'slug' => 'portal.statements.download', 'module' => 'portal', 'description' => 'Download contribution statements'],
            ['name' => 'View Own Attendance (Portal)', 'slug' => 'portal.attendance.view', 'module' => 'portal', 'description' => 'View own attendance history via portal'],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']], // check by unique column
                $permission
            );
        }

        // DB::table('permissions')->insert($permissions);

        $this->command->info('✓ ' . count($permissions) . ' permissions seeded successfully.');
    }
}
