<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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

        // Redirect based on user role
        return redirect()->intended($this->redirectPathForRole());
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
        $user = auth()->user();
        $roleSlug = $user->role->slug ?? null;

        switch ($roleSlug) {
            case 'admin':
                return route('admin.dashboard');

            case 'elder':
                return route('admin.elder.dashboard');

            case 'secretary':
                return route('admin.dashboard');

            case 'finance':
                return route('admin.finance.dashboard');

            case 'ministry_leader':
                return route('admin.ministry.dashboard');

            case 'member':
                return route('member.dashboard');

            default:
                return route('admin.dashboard');
        }
    }
}
