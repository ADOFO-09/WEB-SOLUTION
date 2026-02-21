<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberAccess
{
    /**
     * Handle an incoming request.
     * 
     * Ensures the authenticated user has a linked member profile.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has a linked member profile
        if (!$user->member_id) {
            // If user is admin, redirect to admin dashboard
            if ($user->role && in_array($user->role->slug, ['super-admin', 'admin', 'secretary', 'finance-officer', 'pastor'])) {
                return redirect()->route('admin.dashboard')
                    ->with('info', 'Your account is not linked to a member profile. Please use the admin panel.');
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account is not linked to a member profile. Please contact the church administrator.');
        }
        
        // Load the member relationship
        $user->load('member');
        
        if (!$user->member || $user->member->membership_status !== 'active') {
            return redirect()->route('login')
                ->with('error', 'Your membership is not active. Please contact the church administrator.');
        }
        
        return $next($request);
    }
}
