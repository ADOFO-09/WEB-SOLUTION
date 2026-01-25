<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
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

        // Check if user has any of the required permissions
        if (!empty($permissions) && !$user->hasAnyPermission($permissions)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
