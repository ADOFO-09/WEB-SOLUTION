<?php

use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Activate the Laravel scheduler by adding a single cron entry on the server:
|   * * * * * cd /path/to/cop-abirem-cms && php artisan schedule:run >> /dev/null 2>&1
|
| XAMPP (Windows): Task Scheduler → every minute →
|   php C:\xampp\htdocs\WEB-SOLUTION\cop-abirem-cms\artisan schedule:run
|
*/

// Send birthday SMS greetings every day at 06:00
Schedule::command('sms:send-birthdays')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/birthday-sms.log'));

// Scheduled database backup — only runs when auto_backup_enabled is on
if (Schema::hasTable('settings') && Setting::get('auto_backup_enabled', false)) {
    $frequency = Setting::get('backup_frequency', 'daily');
    $job = Schedule::command('backup:run')
        ->withoutOverlapping()
        ->runInBackground()
        ->appendOutputTo(storage_path('logs/backup.log'));

    match ($frequency) {
        'weekly'  => $job->weekly()->at('02:00'),
        'monthly' => $job->monthly()->at('02:00'),
        default   => $job->dailyAt('02:00'),
    };
}
