<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users', 'description' => 'Can view user list and details'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Can create new user accounts'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Can edit user accounts'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Can delete user accounts'],

            // Role Management
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'roles', 'description' => 'Can view roles and permissions'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'module' => 'roles', 'description' => 'Can create new roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'module' => 'roles', 'description' => 'Can edit roles and permissions'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'module' => 'roles', 'description' => 'Can delete roles'],

            // Member Management
            ['name' => 'View Members', 'slug' => 'members.view', 'module' => 'members', 'description' => 'Can view member list and profiles'],
            ['name' => 'Create Members', 'slug' => 'members.create', 'module' => 'members', 'description' => 'Can register new members'],
            ['name' => 'Edit Members', 'slug' => 'members.edit', 'module' => 'members', 'description' => 'Can edit member profiles'],
            ['name' => 'Delete Members', 'slug' => 'members.delete', 'module' => 'members', 'description' => 'Can delete members'],
            ['name' => 'Export Members', 'slug' => 'members.export', 'module' => 'members', 'description' => 'Can export member data'],

            // Visitor Management
            ['name' => 'View Visitors', 'slug' => 'visitors.view', 'module' => 'visitors', 'description' => 'Can view visitor list'],
            ['name' => 'Create Visitors', 'slug' => 'visitors.create', 'module' => 'visitors', 'description' => 'Can register visitors'],
            ['name' => 'Edit Visitors', 'slug' => 'visitors.edit', 'module' => 'visitors', 'description' => 'Can edit visitor records'],
            ['name' => 'Delete Visitors', 'slug' => 'visitors.delete', 'module' => 'visitors', 'description' => 'Can delete visitors'],
            ['name' => 'Convert Visitors', 'slug' => 'visitors.convert', 'module' => 'visitors', 'description' => 'Can convert visitor to member'],

            // Ministry Management
            ['name' => 'View Ministries', 'slug' => 'ministries.view', 'module' => 'ministries', 'description' => 'Can view ministries'],
            ['name' => 'Create Ministries', 'slug' => 'ministries.create', 'module' => 'ministries', 'description' => 'Can create ministries'],
            ['name' => 'Edit Ministries', 'slug' => 'ministries.edit', 'module' => 'ministries', 'description' => 'Can edit ministries'],
            ['name' => 'Delete Ministries', 'slug' => 'ministries.delete', 'module' => 'ministries', 'description' => 'Can delete ministries'],

            // Attendance
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'module' => 'attendance', 'description' => 'Can view attendance records'],
            ['name' => 'Mark Attendance', 'slug' => 'attendance.mark', 'module' => 'attendance', 'description' => 'Can mark attendance'],
            ['name' => 'Manage Sessions', 'slug' => 'attendance.sessions', 'module' => 'attendance', 'description' => 'Can create/close sessions'],
            ['name' => 'Delete Attendance', 'slug' => 'attendance.delete', 'module' => 'attendance', 'description' => 'Can delete attendance records'],

            // Finance
            ['name' => 'View Finance', 'slug' => 'finance.view', 'module' => 'finance', 'description' => 'Can view financial records'],
            ['name' => 'Record Tithe', 'slug' => 'finance.tithe', 'module' => 'finance', 'description' => 'Can record tithe payments'],
            ['name' => 'Record Offering', 'slug' => 'finance.offering', 'module' => 'finance', 'description' => 'Can record offerings'],
            ['name' => 'Record Donation', 'slug' => 'finance.donation', 'module' => 'finance', 'description' => 'Can record donations'],
            ['name' => 'Manage Pledges', 'slug' => 'finance.pledge', 'module' => 'finance', 'description' => 'Can create and manage pledges'],
            ['name' => 'Record Expense', 'slug' => 'finance.expense', 'module' => 'finance', 'description' => 'Can record expenses'],
            ['name' => 'Approve Expense', 'slug' => 'finance.approve', 'module' => 'finance', 'description' => 'Can approve/reject expenses'],
            ['name' => 'Delete Finance Records', 'slug' => 'finance.delete', 'module' => 'finance', 'description' => 'Can delete financial records'],
            ['name' => 'Manage Financial Years', 'slug' => 'finance.years', 'module' => 'finance', 'description' => 'Can manage financial years'],

            // SMS
            ['name' => 'Send SMS', 'slug' => 'sms.send', 'module' => 'sms', 'description' => 'Can send SMS messages'],
            ['name' => 'View SMS Reports', 'slug' => 'sms.reports', 'module' => 'sms', 'description' => 'Can view SMS history/reports'],
            ['name' => 'Manage SMS Templates', 'slug' => 'sms.templates', 'module' => 'sms', 'description' => 'Can manage SMS templates'],
            ['name' => 'Configure SMS', 'slug' => 'sms.configure', 'module' => 'sms', 'description' => 'Can configure SMS settings'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'Can view reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports', 'description' => 'Can export reports'],
            ['name' => 'View Financial Reports', 'slug' => 'reports.finance', 'module' => 'reports', 'description' => 'Can view financial reports'],

            // Assets
            ['name' => 'View Assets', 'slug' => 'assets.view', 'module' => 'assets', 'description' => 'Can view assets'],
            ['name' => 'Create Assets', 'slug' => 'assets.create', 'module' => 'assets', 'description' => 'Can register assets'],
            ['name' => 'Edit Assets', 'slug' => 'assets.edit', 'module' => 'assets', 'description' => 'Can edit assets'],
            ['name' => 'Delete Assets', 'slug' => 'assets.delete', 'module' => 'assets', 'description' => 'Can delete assets'],

            // Settings
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings', 'description' => 'Can change system settings'],
            ['name' => 'Manage Backups', 'slug' => 'settings.backup', 'module' => 'settings', 'description' => 'Can backup/restore database'],
            ['name' => 'View Activity Logs', 'slug' => 'settings.logs', 'module' => 'settings', 'description' => 'Can view activity logs'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
