<?php

namespace App\Helpers;

use App\Models\User;

class RoleHelper
{
    // =========================================
    // ROLE SLUG CONSTANTS
    // =========================================

    const SUPER_ADMIN     = 'super-admin';
    const ADMIN           = 'admin';
    const ELDER           = 'elder';
    const PASTOR          = 'pastor';          // alias for elder
    const SECRETARY       = 'secretary';
    const FINANCE         = 'finance';
    const FINANCE_OFFICER = 'finance-officer'; // alias for finance
    const MINISTRY_LEADER = 'ministry_leader';
    const MEMBER          = 'member';

    /**
     * All slugs that may access the admin panel.
     */
    const ADMIN_PANEL_SLUGS = [
        'super-admin',
        'admin',
        'elder',
        'pastor',
        'secretary',
        'finance',
        'finance-officer',
        'ministry_leader',
    ];

    // =========================================
    // ROLE DETECTION
    // =========================================

    public static function getRoleSlug(User $user): ?string
    {
        return $user->role->slug ?? null;
    }

    public static function getRoleName(User $user): string
    {
        return $user->role->name ?? 'Unknown';
    }

    /**
     * System Administrator — full access including system config.
     */
    public static function isSystemAdmin(User $user): bool
    {
        return in_array(self::getRoleSlug($user), [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * Presiding Elder — all operations, expense approval, no system config.
     */
    public static function isElder(User $user): bool
    {
        return in_array(self::getRoleSlug($user), [self::ELDER, self::PASTOR]);
    }

    /**
     * Local Secretary — members, visitors, attendance, SMS, read-only finance.
     */
    public static function isSecretary(User $user): bool
    {
        return self::getRoleSlug($user) === self::SECRETARY;
    }

    /**
     * Financial Secretary — all finance modules, limited member access.
     */
    public static function isFinance(User $user): bool
    {
        return in_array(self::getRoleSlug($user), [self::FINANCE, self::FINANCE_OFFICER]);
    }

    /**
     * Ministry Leader — own ministry only (attendance, members, SMS).
     */
    public static function isMinistryLeader(User $user): bool
    {
        return self::getRoleSlug($user) === self::MINISTRY_LEADER;
    }

    /**
     * Member — portal access only (profile, contributions, attendance history).
     */
    public static function isMember(User $user): bool
    {
        return self::getRoleSlug($user) === self::MEMBER;
    }

    /**
     * Any role that can access the admin panel (not a plain member).
     */
    public static function isAdminPanelUser(User $user): bool
    {
        return in_array(self::getRoleSlug($user), self::ADMIN_PANEL_SLUGS);
    }

    /**
     * Check if user has any of the given role slugs.
     */
    public static function hasAnyRole(User $user, array $roleSlugs): bool
    {
        return in_array(self::getRoleSlug($user), $roleSlugs);
    }

    // =========================================
    // DASHBOARD ROUTING
    // =========================================

    /**
     * Return the named route for the user's role-specific dashboard.
     * Used by RedirectBasedOnRole middleware with redirect()->route().
     */
    public static function getDashboardRoute(User $user): string
    {
        $slug = self::getRoleSlug($user);

        return match (true) {
            in_array($slug, [self::ELDER, self::PASTOR])              => 'admin.elder.dashboard',
            in_array($slug, [self::FINANCE, self::FINANCE_OFFICER])   => 'admin.finance.dashboard',
            $slug === self::MINISTRY_LEADER                            => 'admin.ministry.dashboard',
            $slug === self::SECRETARY                                  => 'admin.secretary.dashboard',
            $slug === self::MEMBER                                     => 'member.dashboard',
            default                                                    => 'admin.dashboard',
        };
    }

    /**
     * Return the resolved URL for the user's role-specific dashboard.
     * Used in Blade templates where a href URL is needed.
     * Accepts an optional User; falls back to the authenticated user.
     */
    public static function getDashboardUrl(?User $user = null): string
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return route('login');
        }

        return route(self::getDashboardRoute($user));
    }
}
