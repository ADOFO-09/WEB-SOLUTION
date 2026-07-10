<?php

use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Http\Middleware\MemberAccess;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\SecurityHeaders;
use App\Helpers\RoleHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        $middleware->alias([
            // Access guards
            'admin.access'      => AdminAccess::class,
            'member.access'     => MemberAccess::class,
            'two_fa'            => EnsureTwoFactorVerified::class,
            'role.redirect'     => \App\Http\Middleware\RedirectBasedOnRole::class,

            // Role & permission checks (custom)
            'check.role'        => CheckRole::class,
            'check.permission'  => CheckPermission::class,

            // Spatie middleware aliases (kept for compatibility)
            'permission'        => PermissionMiddleware::class,
            'role'              => RoleMiddleware::class,
            'role_or_permission'=> RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (TokenMismatchException $_e, $_request) {
            return redirect()->route('login')
                ->with('error', 'Your session has expired. Please sign in again.');
        });

        // Redirect permission-denied (403) to the user's own dashboard instead of an error page
        $redirect403 = function (\Illuminate\Http\Request $request, string $message = '') {
            if ($request->expectsJson()) {
                return null; // let the default JSON 403 response through for API/AJAX
            }
            $user  = auth()->user();
            $route = $user ? RoleHelper::getDashboardRoute($user) : 'login';
            $msg   = $message ?: 'You do not have permission to access that page.';
            return redirect()->route($route)->with('error', $msg);
        };

        $exceptions->renderable(function (HttpException $e, \Illuminate\Http\Request $request) use ($redirect403) {
            if ($e->getStatusCode() === 403) {
                return $redirect403($request, $e->getMessage());
            }
        });

        $exceptions->renderable(function (UnauthorizedException $e, \Illuminate\Http\Request $request) use ($redirect403) {
            return $redirect403($request, 'You do not have permission to access that page.');
        });
    })->create();
