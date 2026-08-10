<?php

namespace App\Providers;

use App\Helpers\SettingHelper;
use App\Models\FinancialYear;
use App\Models\Permission;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrap();

        if (Schema::hasTable('settings')) {
            try {
                $timeout = (int) Setting::get('session_timeout', 120);
                config(['session.lifetime' => $timeout]);
            } catch (\Exception) {}
        }

        // Share financial years and new-year alert with all admin views.
        // Cached for 5 minutes — financial years change rarely.
        View::composer('admin.*', function ($view) {
            if (!Schema::hasTable('financial_years')) return;
            try {
                $view->with('financialYears',
                    Cache::remember('financial_years_list', 300, fn() =>
                        FinancialYear::orderBy('start_date', 'desc')->get()
                    )
                );
                $view->with('newYearSetupNeeded',
                    Cache::remember('financial_year_covers_today', 300, fn() =>
                        !FinancialYear::coversToday()
                    )
                );
            } catch (\Exception) {}
        });

        // Share currency symbol, payment methods, and church branding with all admin views.
        // Individual Setting::get() calls are already cached via rememberForever.
        View::composer('admin.*', function ($view) {
            if (!Schema::hasTable('settings')) return;
            try {
                $view->with('currencySymbol', SettingHelper::currencySymbol());
                $view->with('paymentMethods', SettingHelper::paymentMethods());
                $view->with('churchName',     SettingHelper::churchName());
                $view->with('churchLogo',     SettingHelper::churchLogo());
            } catch (\Exception) {}
        });

        // Share currency, date/time format, and church branding with all member views.
        View::composer('member.*', function ($view) {
            if (!Schema::hasTable('settings')) return;
            try {
                $view->with('currencySymbol', SettingHelper::currencySymbol());
                $view->with('dateFormat',     SettingHelper::dateFormat());
                $view->with('timeFormat',     SettingHelper::timeFormat());
                $view->with('churchName',     SettingHelper::churchName());
                $view->with('churchLogo',     SettingHelper::churchLogo());
            } catch (\Exception) {}
        });

        $this->registerPermissionGates();
    }

    protected function registerPermissionGates(): void
    {
        if (!Schema::hasTable('permissions')) return;

        try {
            // Cached forever — cleared by php artisan cache:clear or when permissions change.
            $permissions = Cache::rememberForever('permissions.all', fn() => Permission::all());

            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }

            Gate::before(function ($user, $_ability) {
                if ($user->hasRole('admin')) {
                    return true;
                }
            });
        } catch (\Exception) {}
    }
}
