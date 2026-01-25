<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Run in this order due to foreign key dependencies.
     */
    public function run(): void
    {
        $this->call([
            // ==========================================
            // PHASE 1: Core Tables (No Dependencies)
            // ==========================================
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,  // Depends on roles and permissions
            SettingSeeder::class,
            MinistrySeeder::class,
            ServiceTypeSeeder::class,
            FinancialYearSeeder::class,
            IncomeCategorySeeder::class,
            ExpenseCategorySeeder::class,
            SmsTemplateSeeder::class,
            AssetCategorySeeder::class,
            
            // ==========================================
            // PHASE 2: Users (depends on roles; seed basic users first)
            // ==========================================
            UserSeeder::class,           // ADDED: Ensures users exist (e.g., id=1) before dependents
            AdminUserSeeder::class,      // Depends on roles; creates admin user
            ProjectSeeder::class,        // MOVED: Depends on users (created_by foreign key)
            
            // ==========================================
            // PHASE 3: Members (depends on users)
            // ==========================================
            MemberSeeder::class,
            FamilyRelationshipSeeder::class,
            MemberMinistrySeeder::class,  // Depends on members and ministries
            
            // ==========================================
            // PHASE 4: Visitors (depends on members, users)
            // ==========================================
            VisitorSeeder::class,
            VisitorVisitSeeder::class,
            FollowUpLogSeeder::class,
            
            // ==========================================
            // PHASE 5: Attendance (depends on service_types, members, users)
            // ==========================================
            AttendanceSessionSeeder::class,
            AttendanceRecordSeeder::class,
            
            // ==========================================
            // PHASE 6: Finance (depends on members, financial_years, users)
            // ==========================================
            TitheSeeder::class,
            OfferingSeeder::class,
            DonationSeeder::class,
            PledgeSeeder::class,
            PledgePaymentSeeder::class,
            ExpenseSeeder::class,
            
            // ==========================================
            // PHASE 7: SMS (depends on users, members)
            // ==========================================
            SmsMessageSeeder::class,
            
            // ==========================================
            // PHASE 8: Assets (depends on asset_categories, ministries, users)
            // ==========================================
            AssetSeeder::class,
            AssetMaintenanceSeeder::class,
            
            // ==========================================
            // PHASE 9: Logs (depends on users)
            // ==========================================
            ActivityLogSeeder::class,
            LoginHistorySeeder::class,
        ]);
    }
}