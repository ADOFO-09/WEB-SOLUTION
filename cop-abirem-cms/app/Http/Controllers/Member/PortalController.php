<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Pledge;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PortalController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function dashboard(Request $request)
    {
        $member = $request->user()->member;
        $currentYear = now()->year;
        
        // Giving Summary
        $givingSummary = [
            'tithes_ytd' => Tithe::where('member_id', $member->id)
                ->whereYear('payment_date', $currentYear)
                ->sum('amount'),
            'offerings_ytd' => Offering::where('member_id', $member->id)
                ->whereYear('payment_date', $currentYear)
                ->sum('amount'),
            'donations_ytd' => Donation::where('member_id', $member->id)
                ->whereYear('payment_date', $currentYear)
                ->where('donation_type', 'cash')
                ->sum('amount'),
            'total_ytd' => 0,
        ];
        $givingSummary['total_ytd'] = $givingSummary['tithes_ytd'] + $givingSummary['offerings_ytd'] + $givingSummary['donations_ytd'];
        
        // Pledge Summary
        $pledgeSummary = [
            'active_pledges' => Pledge::where('member_id', $member->id)
                ->where('status', 'active')
                ->count(),
            'total_pledged' => Pledge::where('member_id', $member->id)
                ->where('status', 'active')
                ->sum('total_amount'),
            'total_paid' => Pledge::where('member_id', $member->id)
                ->where('status', 'active')
                ->sum('amount_paid'),
            'remaining' => 0,
        ];
        $pledgeSummary['remaining'] = $pledgeSummary['total_pledged'] - $pledgeSummary['total_paid'];
        
        // Attendance Summary (Last 3 months)
        $threeMonthsAgo = now()->subMonths(3);
        $attendanceCount = AttendanceRecord::where('member_id', $member->id)
            ->where('created_at', '>=', $threeMonthsAgo)
            ->count();
        
        // Recent Activity
        $recentTithes = Tithe::where('member_id', $member->id)
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get();
            
        $recentOfferings = Offering::where('member_id', $member->id)
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get();
        
        // Upcoming pledge due dates
        $upcomingPledges = Pledge::where('member_id', $member->id)
            ->where('status', 'active')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(3)
            ->get();
        
        // Monthly giving chart data (last 6 months)
        $monthlyGiving = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');
            
            $tithes = Tithe::where('member_id', $member->id)
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
                
            $offerings = Offering::where('member_id', $member->id)
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
                
            $monthlyGiving[] = [
                'month' => $date->format('M'),
                'tithes' => $tithes,
                'offerings' => $offerings,
                'total' => $tithes + $offerings,
            ];
        }
        
        return view('member.dashboard', compact(
            'member',
            'givingSummary',
            'pledgeSummary',
            'attendanceCount',
            'recentTithes',
            'recentOfferings',
            'upcomingPledges',
            'monthlyGiving'
        ));
    }
}
