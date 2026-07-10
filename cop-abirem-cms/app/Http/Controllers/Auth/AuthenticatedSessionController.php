<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        LoginHistory::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'success',
        ]);

        // If 2FA is enabled, send code and redirect to the challenge page
        if ((bool)(int) Setting::get('enable_two_factor', 0)) {
            TwoFactorController::send($request, $user);
            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(RoleHelper::getDashboardUrl($user));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the post-login redirect path based on user role.
     */
    protected function redirectPathForRole(): string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return RoleHelper::getDashboardUrl($user);
    }
}
