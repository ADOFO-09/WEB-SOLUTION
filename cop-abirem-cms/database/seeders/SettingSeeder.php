<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'church_name', 'value' => 'The Church of Pentecost - Abirem Central Assembly', 'group' => 'general', 'type' => 'text'],
            ['key' => 'church_address', 'value' => 'Abirem, Eastern Region, Ghana', 'group' => 'general', 'type' => 'textarea'],
            ['key' => 'church_phone', 'value' => '', 'group' => 'general', 'type' => 'text'],
            ['key' => 'church_email', 'value' => '', 'group' => 'general', 'type' => 'text'],
            ['key' => 'church_website', 'value' => '', 'group' => 'general', 'type' => 'text'],
            ['key' => 'church_logo', 'value' => '', 'group' => 'general', 'type' => 'file'],
            
            // Locale Settings
            ['key' => 'currency', 'value' => 'GHS', 'group' => 'locale', 'type' => 'text'],
            ['key' => 'currency_symbol', 'value' => 'GH₵', 'group' => 'locale', 'type' => 'text'],
            ['key' => 'currency_position', 'value' => 'before', 'group' => 'locale', 'type' => 'text'],
            ['key' => 'date_format', 'value' => 'd/m/Y', 'group' => 'locale', 'type' => 'text'],
            ['key' => 'time_format', 'value' => 'H:i', 'group' => 'locale', 'type' => 'text'],
            ['key' => 'timezone', 'value' => 'Africa/Accra', 'group' => 'locale', 'type' => 'text'],
            
            // Finance Settings
            ['key' => 'fiscal_year_start_month', 'value' => '1', 'group' => 'finance', 'type' => 'number'],
            ['key' => 'tithe_receipt_prefix', 'value' => 'TT', 'group' => 'finance', 'type' => 'text'],
            ['key' => 'offering_receipt_prefix', 'value' => 'OF', 'group' => 'finance', 'type' => 'text'],
            ['key' => 'donation_receipt_prefix', 'value' => 'DN', 'group' => 'finance', 'type' => 'text'],
            ['key' => 'expense_voucher_prefix', 'value' => 'EXP', 'group' => 'finance', 'type' => 'text'],
            
            // Attendance Settings
            ['key' => 'attendance_manual_enabled', 'value' => 'true', 'group' => 'attendance', 'type' => 'boolean'],
            ['key' => 'attendance_qr_enabled', 'value' => 'true', 'group' => 'attendance', 'type' => 'boolean'],
            ['key' => 'attendance_biometric_enabled', 'value' => 'false', 'group' => 'attendance', 'type' => 'boolean'],
            ['key' => 'attendance_face_enabled', 'value' => 'false', 'group' => 'attendance', 'type' => 'boolean'],
            ['key' => 'late_arrival_minutes', 'value' => '30', 'group' => 'attendance', 'type' => 'number'],
            
            // Member Settings
            ['key' => 'member_id_prefix', 'value' => 'COP', 'group' => 'members', 'type' => 'text'],
            ['key' => 'auto_generate_member_id', 'value' => 'true', 'group' => 'members', 'type' => 'boolean'],
            ['key' => 'require_photo', 'value' => 'false', 'group' => 'members', 'type' => 'boolean'],
            
            // SMS Settings
            ['key' => 'sms_enabled', 'value' => 'true', 'group' => 'sms', 'type' => 'boolean'],
            ['key' => 'sms_auto_tithe_confirmation', 'value' => 'true', 'group' => 'sms', 'type' => 'boolean'],
            ['key' => 'sms_auto_donation_confirmation', 'value' => 'true', 'group' => 'sms', 'type' => 'boolean'],
            ['key' => 'sms_auto_pledge_reminder', 'value' => 'true', 'group' => 'sms', 'type' => 'boolean'],
            ['key' => 'sms_auto_birthday', 'value' => 'true', 'group' => 'sms', 'type' => 'boolean'],
            ['key' => 'sms_pledge_reminder_days', 'value' => '7', 'group' => 'sms', 'type' => 'number'],
            
            // Backup Settings
            ['key' => 'auto_backup_enabled', 'value' => 'true', 'group' => 'backup', 'type' => 'boolean'],
            ['key' => 'backup_frequency', 'value' => 'daily', 'group' => 'backup', 'type' => 'text'],
            ['key' => 'backup_retention_days', 'value' => '30', 'group' => 'backup', 'type' => 'number'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
