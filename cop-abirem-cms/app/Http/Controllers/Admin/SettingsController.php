<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Backup;
use App\Models\Setting;
use App\Services\GiantSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SettingsController extends Controller
{
    /**
     * Display general settings.
     */
    public function general()
    {
        $settings = Setting::getGroup('general');
        
        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'church_name' => 'required|string|max:255',
            'church_address' => 'nullable|string|max:500',
            'church_city' => 'nullable|string|max:100',
            'church_region' => 'nullable|string|max:100',
            'church_phone' => 'nullable|string|max:20',
            'church_email' => 'nullable|email|max:255',
            'church_website' => 'nullable|url|max:255',
            'pastor_name' => 'nullable|string|max:255',
            'pastor_phone' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'member_id_prefix' => 'nullable|string|max:10|alpha_num',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        return back()->with('success', 'General settings updated successfully.');
    }

    /**
     * Display financial settings.
     */
    public function financial()
    {
        $settings = Setting::getGroup('financial');
        
        return view('admin.settings.financial', compact('settings'));
    }

    /**
     * Update financial settings.
     */
    public function updateFinancial(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'fiscal_year_start' => 'required|string|max:5',
            'payment_methods' => 'nullable|array',
            'tithe_percentage' => 'nullable|numeric|min:0|max:100',
            'enable_online_giving' => 'boolean',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_number' => 'nullable|string|max:20',
            'mobile_money_name' => 'nullable|string|max:255',
            'tithe_receipt_prefix' => 'nullable|string|max:10|alpha_num',
            'offering_receipt_prefix' => 'nullable|string|max:10|alpha_num',
            'donation_receipt_prefix' => 'nullable|string|max:10|alpha_num',
            'expense_voucher_prefix' => 'nullable|string|max:10|alpha_num',
        ]);

        // Handle payment methods array
        if (isset($validated['payment_methods'])) {
            $validated['payment_methods'] = implode(',', $validated['payment_methods']);
        }

        $validated['enable_online_giving'] = $request->boolean('enable_online_giving');

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'financial');
        }

        return back()->with('success', 'Financial settings updated successfully.');
    }

    /**
     * Display SMS settings.
     */
    public function sms()
    {
        $settings = Setting::getGroup('sms');
        
        return view('admin.settings.sms', compact('settings'));
    }

    /**
     * Update SMS settings.
     */
    public function updateSms(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => 'required|string|in:arkesel,hubtel,mnotify,twilio,giantsms',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_api_secret' => 'nullable|string|max:255',
            'sms_sender_id' => 'required|string|max:11',
            'sms_balance_threshold' => 'nullable|integer|min:0',
            'sms_topup_url' => 'nullable|url|max:500',
            'enable_sms_notifications' => 'boolean',
            'sms_birthday_enabled' => 'boolean',
            'sms_birthday_template' => 'nullable|string|max:500',
            'sms_auto_tithe_confirmation' => 'boolean',
            'sms_auto_donation_confirmation' => 'boolean',
            'sms_auto_pledge_reminder' => 'boolean',
        ]);

        $validated['enable_sms_notifications'] = $request->boolean('enable_sms_notifications');
        $validated['sms_birthday_enabled'] = $request->boolean('sms_birthday_enabled');
        $validated['sms_auto_tithe_confirmation'] = $request->boolean('sms_auto_tithe_confirmation');
        $validated['sms_auto_donation_confirmation'] = $request->boolean('sms_auto_donation_confirmation');
        $validated['sms_auto_pledge_reminder'] = $request->boolean('sms_auto_pledge_reminder');

        // Encrypt credentials before storing; skip if blank (preserve existing value)
        foreach (['sms_api_key', 'sms_api_secret'] as $credKey) {
            if (empty($validated[$credKey])) {
                unset($validated[$credKey]);
            } else {
                $validated[$credKey] = Crypt::encryptString($validated[$credKey]);
            }
        }

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'sms');
        }

        return back()->with('success', 'SMS settings updated successfully.');
    }

    /**
     * Send a test SMS to verify the configured provider credentials.
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        $provider = Setting::get('sms_provider', '');

        if ($provider !== 'giantsms') {
            return back()->with('sms_test_error', 'Only GiantSMS is supported at this time.');
        }

        $service = new GiantSmsService();

        if (!$service->isConfigured()) {
            return back()->with('sms_test_error', 'SMS credentials are not configured. Save your settings first.');
        }

        $phone = $service->normalizePhone($request->input('test_phone'));

        try {
            $service->send($phone, 'Test message from COP Abirem CMS. Your SMS integration is working correctly.');
            return back()->with('sms_test_success', "Test SMS sent successfully to {$phone}.");
        } catch (\Exception $e) {
            Log::error('SMS test send failed: ' . $e->getMessage());
            return back()->with('sms_test_error', 'Send failed. Please check your SMS settings and try again.');
        }
    }

    /**
     * Check the current SMS account balance.
     */
    public function checkSmsBalance()
    {
        $provider = Setting::get('sms_provider', '');

        if ($provider !== 'giantsms') {
            return back()->with('sms_balance_error', 'Balance check is only supported for GiantSMS.');
        }

        $service = new GiantSmsService();

        if (!$service->isConfigured()) {
            return back()->with('sms_balance_error', 'SMS credentials are not configured.');
        }

        try {
            ['balance' => $balance] = $service->getBalance();
            Setting::set('sms_last_balance', (string) $balance, 'sms');
            Setting::set('sms_last_balance_at', now()->toDateTimeString(), 'sms');
            return back()->with('sms_balance', number_format($balance, 2));
        } catch (\Exception $e) {
            Log::error('SMS balance check failed: ' . $e->getMessage());
            return back()->with('sms_balance_error', 'Balance check failed. Please verify your SMS credentials.');
        }
    }

    /**
     * Manually trigger the birthday SMS command (for admin testing / on-demand runs).
     */
    public function runBirthdaySmsNow()
    {
        $exitCode = Artisan::call('sms:send-birthdays', ['--force' => true]);
        $output   = trim(Artisan::output());

        if ($exitCode === 0) {
            return back()->with('birthday_sms_success', $output ?: 'Birthday SMS run completed successfully.');
        }

        return back()->with('birthday_sms_error', $output ?: 'Birthday SMS run failed. Check Settings → SMS template and credentials.');
    }

    /**
     * Display system settings.
     */
    public function system()
    {
        $settings = Setting::getGroup('system');
        
        // Get system info
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'memory_limit' => ini_get('memory_limit'),
            'max_upload_size' => ini_get('upload_max_filesize'),
        ];
        
        return view('admin.settings.system', compact('settings', 'systemInfo'));
    }

    /**
     * Update system settings.
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'items_per_page' => 'required|integer|min:10|max:100',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
            'enable_activity_logging' => 'boolean',
            'session_timeout' => 'required|integer|min:5|max:480',
            'enable_two_factor' => 'boolean',
            'maintenance_mode' => 'boolean',
            'attendance_grace_minutes' => 'nullable|integer|min:0|max:120',
            'attendance_biometric_enabled' => 'boolean',
            'attendance_manual_enabled' => 'boolean',
            'attendance_qr_enabled' => 'boolean',
        ]);

        $validated['enable_activity_logging'] = $request->boolean('enable_activity_logging');
        $validated['enable_two_factor'] = $request->boolean('enable_two_factor');
        $validated['maintenance_mode'] = $request->boolean('maintenance_mode');
        $validated['attendance_biometric_enabled'] = $request->boolean('attendance_biometric_enabled');
        $validated['attendance_manual_enabled'] = $request->boolean('attendance_manual_enabled');
        $validated['attendance_qr_enabled'] = $request->boolean('attendance_qr_enabled');

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'system');
        }

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Display backup management page.
     */
    public function backup()
    {
        $backups = $this->getBackupFiles();
        $backupSettings = Setting::getGroup('backup');

        return view('admin.settings.backup', compact('backups', 'backupSettings'));
    }

    /**
     * Update automatic backup settings.
     */
    public function updateBackupSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_backup_enabled' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'backup_retention_days' => 'required|integer|min:1|max:365',
        ]);

        $validated['auto_backup_enabled'] = $request->boolean('auto_backup_enabled');

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'backup');
        }

        return back()->with('success', 'Backup settings updated successfully.');
    }

    /**
     * Create a new database backup.
     */
    public function createBackup()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Ensure backup directory exists
            if (!File::isDirectory(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            // Get database credentials
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Write credentials to a temp file so the password never appears
            // in the process list (--password on the CLI is visible via /proc).
            $cnfFile = tempnam(sys_get_temp_dir(), 'mysqldump_cnf_');
            file_put_contents($cnfFile, "[client]\npassword=" . $password . "\n");
            chmod($cnfFile, 0600);

            $command = sprintf(
                'mysqldump --defaults-extra-file=%s --host=%s --user=%s %s > %s',
                escapeshellarg($cnfFile),
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnVar);
            unlink($cnfFile);

            if ($returnVar !== 0) {
                // Fallback: Create a simple SQL export
                $this->createSimpleBackup($path);
            }

            // Log the backup
            ActivityLog::log('backup_created', null, null, ['filename' => $filename]);

            Backup::create([
                'filename'   => $filename,
                'file_path'  => $path,
                'file_size'  => file_exists($path) ? filesize($path) : 0,
                'type'       => 'manual',
                'status'     => 'completed',
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return back()->with('success', "Backup created successfully: {$filename}");

        } catch (\Exception $e) {
            Log::error('Database backup failed: ' . $e->getMessage());
            return back()->with('error', 'Backup failed. Please try again or check server logs.');
        }
    }

    /**
     * Download a backup file.
     */
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        return response()->download($path);
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        File::delete($path);

        return back()->with('success', 'Backup deleted successfully.');
    }

    /**
     * Restore from a backup file.
     */
    public function restoreBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        try {
            $host     = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $restored = false;
            $mysqlBin = $this->findMysqlBinary();

            if ($mysqlBin) {
                // Use proc_open to pipe file to stdin — avoids shell-redirect issues on Windows.
                $command = sprintf(
                    '%s --host=%s --user=%s --password=%s %s',
                    escapeshellarg($mysqlBin),
                    escapeshellarg($host),
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($database)
                );

                $descriptors = [
                    0 => ['file', $path, 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ];

                $process = proc_open($command, $descriptors, $pipes);

                if (is_resource($process)) {
                    $stderr = stream_get_contents($pipes[2]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    $returnVar = proc_close($process);

                    if ($returnVar === 0) {
                        $restored = true;
                    } else {
                        Log::warning('mysql CLI restore failed, falling back to PHP restore. stderr: ' . $stderr);
                    }
                }
            }

            if (!$restored) {
                $this->restoreViaPHP($path);
            }

            ActivityLog::log('backup_restored', null, null, ['filename' => $filename]);
            return back()->with('success', 'Database restored successfully from backup.');

        } catch (\Exception $e) {
            Log::error('Database restore failed: ' . $e->getMessage());
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * PHP-based restore — executes SQL statements directly via Laravel DB.
     * Used as a fallback when the mysql CLI is unavailable.
     */
    private function restoreViaPHP(string $path): void
    {
        $sql = File::get($path);
        $statements = $this->parseSqlStatements($sql);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            foreach ($statements as $statement) {
                DB::unprepared($statement);
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Split a SQL dump into individual statements.
     * Accumulates lines until one ends with a semicolon, then yields the statement.
     */
    private function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $current    = '';

        foreach (explode("\n", $sql) as $line) {
            $line = rtrim($line);

            // Skip blank lines and single-line comments
            if ($line === '' || str_starts_with($line, '--') || str_starts_with($line, '#')) {
                continue;
            }

            $current .= $line . "\n";

            if (str_ends_with(rtrim($line), ';')) {
                $statements[] = rtrim($current);
                $current = '';
            }
        }

        if (trim($current) !== '') {
            $statements[] = rtrim($current);
        }

        return $statements;
    }

    /**
     * Get list of backup files.
     */
    private function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::isDirectory($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
            return collect();
        }

        $files = File::files($backupPath);
        
        return collect($files)->map(function ($file) {
            return [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'size_raw' => $file->getSize(),
                'date' => Carbon::createFromTimestamp($file->getMTime()),
            ];
        })->sortByDesc('date')->values();
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < \count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Locate the mysql client binary, checking XAMPP paths and system PATH.
     */
    private function findMysqlBinary(): ?string
    {
        $candidates = [
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysql.exe',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
            '/opt/homebrew/bin/mysql',
            'mysql',
        ];

        foreach ($candidates as $bin) {
            if (str_contains($bin, DIRECTORY_SEPARATOR) || str_contains($bin, '/')) {
                if (file_exists($bin)) {
                    return $bin;
                }
            } else {
                $cmd = PHP_OS_FAMILY === 'Windows' ? "where {$bin} 2>nul" : "which {$bin} 2>/dev/null";
                exec($cmd, $out, $ret);
                if ($ret === 0 && !empty($out)) {
                    return trim($out[0]);
                }
            }
        }

        return null;
    }

    /**
     * Create a simple SQL backup (fallback method).
     */
    private function createSimpleBackup($path)
    {
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        $key = 'Tables_in_' . $database;
        
        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n\n";
        
        foreach ($tables as $table) {
            $tableName = $table->$key;
            
            // Get create table statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "\n\n-- Table: {$tableName}\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = collect((array) $row)->map(function ($value) {
                    if (\is_null($value)) return 'NULL';
                    return "'" . addslashes($value) . "'";
                })->implode(', ');
                
                $sql .= "INSERT INTO `{$tableName}` VALUES ({$values});\n";
            }
        }
        
        File::put($path, $sql);
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return back()->with('success', 'Application cache cleared successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());
            return back()->with('error', 'Failed to clear cache. Please try again.');
        }
    }

    /**
     * Optimize application.
     */
    public function optimize()
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return back()->with('success', 'Application optimized successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to optimize application: ' . $e->getMessage());
            return back()->with('error', 'Failed to optimize. Please try again.');
        }
    }
}
