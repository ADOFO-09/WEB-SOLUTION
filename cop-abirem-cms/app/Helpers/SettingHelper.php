<?php

namespace App\Helpers;

use App\Models\Setting;
use Carbon\Carbon;

class SettingHelper
{
    // ==========================================
    // CURRENCY
    // ==========================================

    public static function currencySymbol(): string
    {
        return Setting::get('currency_symbol', 'GH₵');
    }

    public static function currencyCode(): string
    {
        return Setting::get('currency', 'GHS');
    }

    public static function formatCurrency(float|string|null $amount): string
    {
        return self::currencySymbol() . ' ' . number_format((float) $amount, 2);
    }

    // ==========================================
    // DATE / TIME
    // ==========================================

    public static function dateFormat(): string
    {
        return Setting::get('date_format', 'd M, Y');
    }

    public static function timeFormat(): string
    {
        return Setting::get('time_format', 'h:i A');
    }

    public static function formatDate(Carbon|\DateTimeInterface|string|null $date): string
    {
        if (!$date) return '—';
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        return $date->format(self::dateFormat());
    }

    public static function formatDateTime(Carbon|\DateTimeInterface|string|null $date): string
    {
        if (!$date) return '—';
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        return $date->format(self::dateFormat() . ' ' . self::timeFormat());
    }

    // ==========================================
    // PAGINATION
    // ==========================================

    public static function perPage(int $fallback = 15): int
    {
        $value = (int) Setting::get('items_per_page', $fallback);
        return $value > 0 ? $value : $fallback;
    }

    // ==========================================
    // PAYMENT METHODS
    // ==========================================

    /**
     * Return enabled payment methods as an associative array [slug => label].
     */
    public static function paymentMethods(): array
    {
        $all = [
            'cash'          => 'Cash',
            'mobile_money'  => 'Mobile Money',
            'bank_transfer' => 'Bank Transfer',
            'cheque'        => 'Cheque',
        ];

        $enabled = array_filter(
            explode(',', Setting::get('payment_methods', 'cash,mobile_money,bank_transfer,cheque'))
        );

        if (empty($enabled)) {
            return $all;
        }

        return array_intersect_key($all, array_flip($enabled));
    }

    // ==========================================
    // ATTENDANCE
    // ==========================================

    public static function attendanceGraceMinutes(): int
    {
        return (int) Setting::get('attendance_grace_minutes', 15);
    }

    public static function attendanceBiometricEnabled(): bool
    {
        return (bool) Setting::get('attendance_biometric_enabled', true);
    }

    public static function attendanceManualEnabled(): bool
    {
        return (bool) Setting::get('attendance_manual_enabled', true);
    }

    public static function attendanceQrEnabled(): bool
    {
        return (bool) Setting::get('attendance_qr_enabled', true);
    }

    // ==========================================
    // CHURCH BRANDING
    // ==========================================

    public static function churchName(): string
    {
        return Setting::get('church_name', 'Church Management System');
    }

    public static function churchShortName(): string
    {
        $short = Setting::get('church_short_name', '');
        return $short ?: self::churchName();
    }

    public static function churchLogo(): ?string
    {
        $path = Setting::get('church_logo', '');
        if (!$path) return null;
        return \Illuminate\Support\Facades\Storage::url($path);
    }

    public static function churchSlogan(): string
    {
        return Setting::get('church_slogan', '');
    }

    public static function reportHeader(): string
    {
        return Setting::get('report_header', '');
    }

    // ==========================================
    // RECEIPT / VOUCHER PREFIXES
    // ==========================================

    public static function titheReceiptPrefix(): string
    {
        return Setting::get('tithe_receipt_prefix', 'RCT') ?: 'RCT';
    }

    public static function offeringReceiptPrefix(): string
    {
        return Setting::get('offering_receipt_prefix', 'OFR') ?: 'OFR';
    }

    public static function donationReceiptPrefix(): string
    {
        return Setting::get('donation_receipt_prefix', 'DRC') ?: 'DRC';
    }

    public static function expenseVoucherPrefix(): string
    {
        return Setting::get('expense_voucher_prefix', 'EXP') ?: 'EXP';
    }
}
