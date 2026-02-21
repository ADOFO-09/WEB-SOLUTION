<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display member's attendance history.
     */
    public function index(Request $request)
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        
        $query = AttendanceRecord::where('member_id', $member->id)
            ->whereHas('session', function ($q) use ($year, $month) {
                $q->whereYear('service_date', $year);
                if ($month) {
                    $q->whereMonth('service_date', $month);
                }
            })
            ->with(['session.serviceType']);
        
        $records = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
        
        // Monthly attendance summary
        $monthlySummary = [];
        for ($m = 1; $m <= 12; $m++) {
            $count = AttendanceRecord::where('member_id', $member->id)
                ->whereHas('session', function ($q) use ($year, $m) {
                    $q->whereYear('service_date', $year)
                      ->whereMonth('service_date', $m);
                })
                ->count();
            
            $monthlySummary[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'month_num' => $m,
                'count' => $count,
            ];
        }
        
        // Yearly stats
        $stats = [
            'total_year' => AttendanceRecord::where('member_id', $member->id)
                ->whereHas('session', fn($q) => $q->whereYear('service_date', $year))
                ->count(),
            'total_sessions' => AttendanceSession::whereYear('service_date', $year)
                ->where('status', 'closed')
                ->count(),
            'this_month' => AttendanceRecord::where('member_id', $member->id)
                ->whereHas('session', function ($q) {
                    $q->whereYear('service_date', now()->year)
                      ->whereMonth('service_date', now()->month);
                })
                ->count(),
        ];
        
        if ($stats['total_sessions'] > 0) {
            $stats['attendance_rate'] = round(($stats['total_year'] / $stats['total_sessions']) * 100);
        } else {
            $stats['attendance_rate'] = 0;
        }
        
        $years = range(now()->year, now()->year - 3);
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        return view('member.attendance.index', compact(
            'records', 'monthlySummary', 'stats', 'year', 'month', 'years', 'months'
        ));
    }
}
