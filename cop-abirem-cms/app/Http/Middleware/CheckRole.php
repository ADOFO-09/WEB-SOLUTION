<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
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

        // Check if user has any of the required roles
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
