<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     * 
     * This middleware ensures only users with admin panel access can access admin routes.
     * Members (role: member) can only access the member portal.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        // Check if user is locked
        if ($user->isLocked()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is temporarily locked. Please try again later.');
        }

        // Members cannot access admin panel
        if ($user->hasRole('member')) {
            return redirect()->route('member.dashboard')
                ->with('info', 'You have been redirected to the member portal.');
        }

        // Check if user must change password
        if ($user->must_change_password && !$request->routeIs('admin.password.change*')) {
            return redirect()->route('admin.password.change')
                ->with('warning', 'Please change your password to continue.');
        }

        return $next($request);
    }
}
