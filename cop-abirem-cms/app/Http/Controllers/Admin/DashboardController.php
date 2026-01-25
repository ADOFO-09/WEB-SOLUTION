<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Get statistics based on user role
        $stats = $this->getStatistics();

        // Get recent activity
        $recentActivity = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get quick stats for cards
        $quickStats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'today_logins' => DB::table('login_history')
                ->whereDate('login_at', today())
                ->where('status', 'success')
                ->count(),
        ];

        return view('admin.dashboard.index', compact('stats', 'recentActivity', 'quickStats'));
    }

    /**
     * Get statistics based on available modules.
     */
    protected function getStatistics(): array
    {
        $stats = [];

        // User stats (always available for admin roles)
        $stats['users'] = [
            'total' => User::count(),
            'active' => User::active()->count(),
            'inactive' => User::inactive()->count(),
        ];

        // Check if members table exists and get stats
        if (\Schema::hasTable('members')) {
            $stats['members'] = [
                'total' => DB::table('members')->whereNull('deleted_at')->count(),
                'active' => DB::table('members')->where('membership_status', 'active')->whereNull('deleted_at')->count(),
            ];
        }

        // Check if visitors table exists
        if (\Schema::hasTable('visitors')) {
            $stats['visitors'] = [
                'total' => DB::table('visitors')->whereNull('deleted_at')->count(),
                'pending_followup' => DB::table('visitors')->where('follow_up_status', 'pending')->whereNull('deleted_at')->count(),
            ];
        }

        return $stats;
    }
}
