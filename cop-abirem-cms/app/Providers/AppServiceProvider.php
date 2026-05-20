<?php

namespace App\Providers;

use App\Models\FinancialYear;
use App\Models\Permission;
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

        // Share financial years with all admin views so year dropdowns stay in sync.
        View::composer('admin.*', function ($view) {
            if (!Schema::hasTable('financial_years')) return;
            try {
                $view->with('financialYears',
                    FinancialYear::orderBy('start_date', 'desc')->get()
                );
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
