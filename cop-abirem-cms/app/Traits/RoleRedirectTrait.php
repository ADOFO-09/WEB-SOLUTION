<?php

namespace App\Http\Traits;

trait RoleRedirectTrait
{
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
