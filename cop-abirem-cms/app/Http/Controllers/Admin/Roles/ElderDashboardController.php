<?php

namespace App\Http\Controllers\Admin\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Visitor;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\Pledge;
use App\Models\Expense;
use App\Models\AttendanceSession;
use App\Models\SmsMessage;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ElderDashboardController extends Controller
{
    /**
     * Presiding Elder Dashboard
     * Full access to all modules except system settings
     */
    public function index()
    {
        $currentMonth = Carbon::now();
        $currentYear = Carbon::now()->year;

        // Member Statistics
        $memberStats = [
            'total' => Member::count(),
            'active' => Member::where('membership_status', 'active')->count(),
            'inactive' => Member::where('membership_status', 'inactive')->count(),
            'new_this_month' => Member::whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentYear)->count(),
        ];

        // Visitor Statistics
        $visitorStats = [
            'total' => Visitor::count(),
            'this_month' => Visitor::whereMonth('first_visit_date', $currentMonth->month)
                ->whereYear('first_visit_date', $currentYear)->count(),
            'pending_followup' => Visitor::where('follow_up_status', 'pending')->count(),
            'converted' => Visitor::where('follow_up_status', 'converted')->count(),
        ];

        // Financial Overview (Current Month)
        $financeStats = [
            'tithes' => Tithe::whereMonth('payment_date', $currentMonth->month)
                ->whereYear('payment_date', $currentYear)->sum('amount') ?? 0,
            'offerings' => Offering::whereMonth('payment_date', $currentMonth->month)
                ->whereYear('payment_date', $currentYear)->sum('amount') ?? 0,
            'donations' => Donation::whereMonth('payment_date', $currentMonth->month)
                ->whereYear('payment_date', $currentYear)->sum('amount') ?? 0,
            'expenses' => Expense::whereMonth('expense_date', $currentMonth->month)
                ->whereYear('expense_date', $currentYear)
                ->where('status', 'approved')->sum('amount') ?? 0,
        ];

        $financeStats['total_income'] = $financeStats['tithes'] + $financeStats['offerings'] + $financeStats['donations'];
        $financeStats['net_income'] = $financeStats['total_income'] - $financeStats['expenses'];

        // Pending Expense Approvals
        $pendingExpenses = Expense::pending()
        ->with(['expenseCategory', 'requestedBy'])
        ->latest()
        ->take(10)
        ->get();

        // Pledge Overview
        $pledgeStats = [
            'total_pledged' => Pledge::where('status', '!=', 'cancelled')->sum('total_amount') ?? 0,
            'total_paid' => Pledge::where('status', '!=', 'cancelled')->sum('amount_paid') ?? 0,
            'active_pledges' => Pledge::where('status', 'active')->count(),
            'overdue' => Pledge::where('status', 'active')
                ->where('due_date', '<', now())
                ->whereRaw('amount_paid < total_amount')
                ->count(),
        ];
        $pledgeStats['balance'] = $pledgeStats['total_pledged'] - $pledgeStats['total_paid'];

        // Recent Attendance Sessions
        $recentAttendance = AttendanceSession::with('serviceType')
            ->orderBy('service_date', 'desc')
            ->take(8)
            ->get();

        $avgAttendance = AttendanceSession::where('service_date', '>=', now()->subWeeks(4))
            ->avg('total_attendance') ?? 0;

        // Recent Activities
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Recent SMS Messages
        $recentSms = SmsMessage::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.roles.elder.dashboard', compact(
            'memberStats',
            'visitorStats',
            'financeStats',
            'pendingExpenses',
            'pledgeStats',
            'recentAttendance',
            'avgAttendance',
            'recentActivities',
            'recentSms'
        ));
    }

    /**
     * Approve an expense
     */
    public function approveExpense(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        
        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Log the activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approved',
            'model_type' => 'Expense',
            'model_id' => $expense->id,
            'description' => 'Approved expense: ' . $expense->description,
        ]);

        return back()->with('success', 'Expense approved successfully.');
    }

    /**
     * Reject an expense
     */
    public function rejectExpense(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $expense = Expense::findOrFail($id);
        
        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        // Log the activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'model_type' => 'Expense',
            'model_id' => $expense->id,
            'description' => 'Rejected expense: ' . $expense->description . '. Reason: ' . $request->reason,
        ]);

        return back()->with('success', 'Expense rejected.');
    }
}