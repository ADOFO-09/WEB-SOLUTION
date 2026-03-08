<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\RoleHelper;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     * Redirects users to their role-specific dashboard after login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($request->routeIs('admin.dashboard') || $request->routeIs('dashboard')) {
                $route = RoleHelper::getDashboardRoute($user);

                // Only redirect away if the user has a role-specific dashboard
                if ($route !== 'admin.dashboard') {
                    return redirect()->route($route);
                }
            }
        }

        return $next($request);
    }
}
