<?php

namespace App\Providers;

use App\Helpers\SettingHelper;
use App\Models\FinancialYear;
use App\Models\Permission;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap pagination styling
        Paginator::useBootstrap();

        // Apply session timeout from settings
        if (Schema::hasTable('settings')) {
            try {
                $timeout = (int) Setting::get('session_timeout', 120);
                config(['session.lifetime' => $timeout]);
            } catch (\Exception $e) {}
        }

        // Share financial years with all admin views so year dropdowns stay in sync.
        View::composer('admin.*', function ($view) {
            if (!Schema::hasTable('financial_years')) return;
            try {
                $view->with('financialYears',
                    FinancialYear::orderBy('start_date', 'desc')->get()
                );
            } catch (\Exception $e) {}
        });

        // Share currency symbol and payment methods with all admin views.
        View::composer('admin.*', function ($view) {
            if (!Schema::hasTable('settings')) return;
            try {
                $view->with('currencySymbol', SettingHelper::currencySymbol());
                $view->with('paymentMethods', SettingHelper::paymentMethods());
            } catch (\Exception $e) {}
        });

        // Share currency, date/time format, and church name with all member views.
        View::composer('member.*', function ($view) {
            if (!Schema::hasTable('settings')) return;
            try {
                $view->with('currencySymbol', SettingHelper::currencySymbol());
                $view->with('dateFormat', SettingHelper::dateFormat());
                $view->with('timeFormat', SettingHelper::timeFormat());
                $view->with('churchName', Setting::get('church_name', 'Church of Pentecost - Abirem'));
            } catch (\Exception $e) {}
        });

        // Register permission gates
        $this->registerPermissionGates();
    }

    /**
     * Register gates for all permissions.
     */
    protected function registerPermissionGates(): void
    {
        // Skip if permissions table doesn't exist yet (during migrations)
        if (!Schema::hasTable('permissions')) {
            return;
        }

        try {
            // Get all permissions
            $permissions = Permission::all();

            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }

            // Define a super admin gate
            Gate::before(function ($user, $ability) {
                if ($user->hasRole('admin')) {
                    return true;
                }
            });
        } catch (\Exception $e) {
            // Silently fail if database isn't ready
        }
    }
}
