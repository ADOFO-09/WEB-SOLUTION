<?php

use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\MemberAccess;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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
        //
    })->create();
