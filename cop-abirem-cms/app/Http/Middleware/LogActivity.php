<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Actions to log (POST, PUT, PATCH, DELETE).
     */
    protected array $loggableMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Routes to exclude from logging.
     */
    protected array $excludedRoutes = [
        'login',
        'logout',
        'password.*',
        'sanctum.*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (!auth()->check()) {
            return $response;
        }

        // Only log modifying requests
        if (!in_array($request->method(), $this->loggableMethods)) {
            return $response;
        }

        // Skip excluded routes
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $response;
            }
        }

        // Only log successful requests
        if ($response->isSuccessful() || $response->isRedirection()) {
            $this->logRequest($request);
        }

        return $response;
    }

    /**
     * Log the request activity.
     */
    protected function logRequest(Request $request): void
    {
        $routeName = $request->route()?->getName() ?? $request->path();
        $action = $this->getActionFromRoute($routeName);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => null,
            'model_id' => null,
            'old_values' => null,
            'new_values' => $this->sanitizeInput($request->except(['_token', '_method', 'password', 'password_confirmation'])),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Get action name from route.
     */
    protected function getActionFromRoute(string $routeName): string
    {
        // Extract action from route name (e.g., 'admin.users.store' -> 'users.store')
        $parts = explode('.', $routeName);
        
        if (count($parts) >= 2) {
            return implode('.', array_slice($parts, -2));
        }

        return $routeName;
    }

    /**
     * Sanitize input data for logging.
     */
    protected function sanitizeInput(array $input): array
    {
        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'current_password', 'token', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            unset($input[$field]);
        }

        // Limit array size
        if (count($input) > 50) {
            $input = array_slice($input, 0, 50);
            $input['_truncated'] = true;
        }

        return $input;
    }
}
