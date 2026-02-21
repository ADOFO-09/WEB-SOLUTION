<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Routes that should be accessible during maintenance mode.
     */
    protected array $except = [
        'login',
        'logout',
        'admin/settings/*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceMode = Setting::get('maintenance_mode', '0');

        if ($maintenanceMode == '1') {
            // Allow admins to bypass maintenance mode
            if (auth()->check() && auth()->user()->isAdmin()) {
                // Show a notice to admins that site is in maintenance mode
                session()->flash('maintenance_notice', 'The site is currently in maintenance mode. Only administrators can access the system.');
                return $next($request);
            }

            // Allow certain routes (login, etc.)
            foreach ($this->except as $pattern) {
                if ($request->is($pattern)) {
                    return $next($request);
                }
            }

            // Return maintenance page for everyone else
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
