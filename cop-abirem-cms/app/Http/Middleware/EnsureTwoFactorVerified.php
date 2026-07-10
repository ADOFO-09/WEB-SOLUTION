<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in — let the auth middleware handle it
        if (!auth()->check()) {
            return $next($request);
        }

        // 2FA not enabled globally — pass through
        if (!((bool)(int) Setting::get('enable_two_factor', 0))) {
            return $next($request);
        }

        // Already passed 2FA this session
        if ($request->session()->get('two_fa_verified')) {
            return $next($request);
        }

        // User is logged in but hasn't completed 2FA — send to challenge
        return redirect()->route('two-factor.challenge');
    }
}
