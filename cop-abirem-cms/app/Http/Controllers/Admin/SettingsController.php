<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Backup;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
            'enable_sms_notifications' => 'boolean',
            'sms_birthday_enabled' => 'boolean',
            'sms_birthday_template' => 'nullable|string|max:500',
        ]);

        $validated['enable_sms_notifications'] = $request->boolean('enable_sms_notifications');
        $validated['sms_birthday_enabled'] = $request->boolean('sms_birthday_enabled');

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'sms');
        }

        return back()->with('success', 'SMS settings updated successfully.');
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
        ]);

        $validated['enable_activity_logging'] = $request->boolean('enable_activity_logging');
        $validated['enable_two_factor'] = $request->boolean('enable_two_factor');
        $validated['maintenance_mode'] = $request->boolean('maintenance_mode');

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
        
        return view('admin.settings.backup', compact('backups'));
    }

    /**
     * Create a new database backup.
     */
    public function createBackup(Request $request)
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

            // Create backup using mysqldump
            $command = sprintf(
                'mysqldump --host=%s --user=%s --password=%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnVar);

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
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
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
    public function restoreBackup(Request $request, $filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        try {
            // Get database credentials
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Restore using mysql command
            $command = sprintf(
                'mysql --host=%s --user=%s --password=%s %s < %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('MySQL restore command failed.');
            }

            // Log the restore
            ActivityLog::log('backup_restored', null, null, ['filename' => $filename]);

            return back()->with('success', 'Database restored successfully from backup.');

        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
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
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
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
                    if (is_null($value)) return 'NULL';
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
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
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
            return back()->with('error', 'Failed to optimize: ' . $e->getMessage());
        }
    }
}
