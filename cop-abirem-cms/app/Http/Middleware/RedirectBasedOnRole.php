<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
            $roleSlug = $user->role->slug ?? null;

            // Only redirect if accessing the generic dashboard
            if ($request->routeIs('admin.dashboard') || $request->routeIs('dashboard')) {
                return $this->redirectToRoleDashboard($roleSlug);
            }
        }

        return $next($request);
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    protected function redirectToRoleDashboard(?string $roleSlug)
    {
        switch ($roleSlug) {
            case 'admin':
                // System Admin stays on main dashboard
                return redirect()->route('admin.dashboard');

            case 'elder':
                return redirect()->route('admin.elder.dashboard');

            case 'secretary':
                // Local Secretary uses main admin dashboard with filtered access
                return redirect()->route('admin.dashboard');

            case 'finance':
                return redirect()->route('admin.finance.dashboard');

            case 'ministry_leader':
                return redirect()->route('admin.ministry.dashboard');

            case 'member':
                return redirect()->route('member.dashboard');

            default:
                return redirect()->route('admin.dashboard');
        }
    }
}
