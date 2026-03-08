<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {

            // Core web routes (includes role_routes.php, member.php, auth.php via require)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Admin core — Dashboard & Profile
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            // Domain-based route files
            Route::middleware('web')
                ->group(base_path('routes/people.php'));

            Route::middleware('web')
                ->group(base_path('routes/attendance.php'));

            Route::middleware('web')
                ->group(base_path('routes/finance.php'));

            Route::middleware('web')
                ->group(base_path('routes/communication.php'));

            Route::middleware('web')
                ->group(base_path('routes/reports.php'));

            Route::middleware('web')
                ->group(base_path('routes/system.php'));
        });
    }
}