<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Member;
use App\Models\Offering;
use App\Models\Tithe;
use App\Models\Visitor;
use Carbon\Carbon;

class StaffHomeController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $now        = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd   = $now->copy()->endOfMonth();

        $data = compact('user', 'now');

        // Members
        if ($user->hasPermission('members.view')) {
            $data['memberStats'] = [
                'total'          => Member::count(),
                'active'         => Member::where('membership_status', 'active')->count(),
                'new_this_month' => Member::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Visitors
        if ($user->hasPermission('visitors.view')) {
            $data['visitorStats'] = [
                'total'           => Visitor::count(),
                'pending_followup'=> Visitor::where('follow_up_status', 'pending')->count(),
                'new_this_month'  => Visitor::whereBetween('first_visit_date', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Attendance
        if ($user->hasPermission('attendance.view')) {
            $data['attendanceStats'] = [
                'sessions_this_month' => AttendanceSession::whereBetween('service_date', [$monthStart, $monthEnd])->count(),
                'last_session'        => AttendanceSession::orderByDesc('service_date')->first(),
            ];
        }

        // Tithes
        if ($user->hasPermission('tithes.view')) {
            $data['tithesStats'] = [
                'this_month' => Tithe::whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount'),
                'count'      => Tithe::whereBetween('payment_date', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Offerings
        if ($user->hasPermission('offerings.view')) {
            $data['offeringsStats'] = [
                'this_month' => Offering::whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount'),
                'count'      => Offering::whereBetween('payment_date', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Donations
        if ($user->hasPermission('donations.view')) {
            $data['donationsStats'] = [
                'this_month' => Donation::where('donation_type', 'cash')->whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount'),
                'count'      => Donation::where('donation_type', 'cash')->whereBetween('payment_date', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Expenses
        if ($user->hasPermission('expenses.view')) {
            $data['expenseStats'] = [
                'pending'    => Expense::where('status', 'pending')->count(),
                'this_month' => Expense::where('status', 'paid')->whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount'),
            ];
        }

        return view('admin.home', $data);
    }
}
