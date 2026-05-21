<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CreateDatabaseBackup extends Command
{
    protected $signature   = 'backup:run {--force : Run even if auto_backup_enabled is off}';
    protected $description = 'Create a scheduled database backup';

    public function handle(): int
    {
        if (!$this->option('force') && !Setting::get('auto_backup_enabled', false)) {
            $this->line('Auto backup is disabled. Use --force to run anyway.');
            return self::SUCCESS;
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $dir      = storage_path('app/backups');
        $path     = $dir . DIRECTORY_SEPARATOR . $filename;

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        try {
            $this->createBackup($path);

            Backup::create([
                'filename'   => $filename,
                'file_path'  => $path,
                'file_size'  => file_exists($path) ? filesize($path) : 0,
                'type'       => 'scheduled',
                'status'     => 'completed',
                'created_by' => null,
                'created_at' => now(),
            ]);

            $this->pruneOldBackups($dir);

            $this->info("Backup created: {$filename}");
            Log::info("Scheduled backup created: {$filename}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Backup failed: {$e->getMessage()}");
            Log::error("Scheduled backup failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function createBackup(string $path): void
    {
        $host     = config('database.connections.mysql.host');
        $db       = config('database.connections.mysql.database');
        $user     = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $cnfFile = tempnam(sys_get_temp_dir(), 'mysqldump_cnf_');
        file_put_contents($cnfFile, "[client]\npassword=" . $password . "\n");
        chmod($cnfFile, 0600);

        $command = sprintf(
            'mysqldump --defaults-extra-file=%s --host=%s --user=%s %s > %s',
            escapeshellarg($cnfFile),
            escapeshellarg($host),
            escapeshellarg($user),
            escapeshellarg($db),
            escapeshellarg($path)
        );

        exec($command, $output, $exitCode);
        unlink($cnfFile);

        if ($exitCode !== 0 || !file_exists($path) || filesize($path) === 0) {
            // Fallback to PHP-based export
            $this->createSimpleBackup($path);
        }
    }

    private function createSimpleBackup(string $path): void
    {
        $database = config('database.connections.mysql.database');
        $tables   = DB::select('SHOW TABLES');
        $key      = 'Tables_in_' . $database;

        $sql  = "-- Scheduled Backup\n-- Generated: " . now()->toDateTimeString() . "\n\n";

        foreach ($tables as $table) {
            $tableName   = $table->$key;
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "\nDROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            foreach (DB::table($tableName)->get() as $row) {
                $values = collect((array) $row)->map(fn ($v) => is_null($v) ? 'NULL' : "'" . addslashes($v) . "'")->implode(', ');
                $sql .= "INSERT INTO `{$tableName}` VALUES ({$values});\n";
            }
        }

        File::put($path, $sql);
    }

    private function pruneOldBackups(string $dir): void
    {
        $retentionDays = (int) Setting::get('backup_retention_days', 30);
        $cutoff        = now()->subDays($retentionDays);

        collect(File::files($dir))
            ->filter(fn ($f) => \Carbon\Carbon::createFromTimestamp($f->getMTime())->lt($cutoff))
            ->each(fn ($f) => File::delete($f->getPathname()));
    }
}
