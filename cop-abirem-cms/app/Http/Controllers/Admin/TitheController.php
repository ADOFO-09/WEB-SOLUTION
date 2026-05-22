<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tithe;
use App\Models\Member;
use App\Models\FinancialYear;
use App\Models\AttendanceSession;
use App\Models\SmsTemplate;
use App\Services\GiantSmsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use App\Helpers\SettingHelper;

class TitheController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:tithes.view', only: [
                'index', 'show', 'memberHistory', 'printReceipt', 'monthlyReport'
            ]),
            new Middleware(middleware: 'permission:tithes.create', only: ['create', 'store', 'createForSession', 'storeForSession']),
            new Middleware(middleware: 'permission:tithes.edit', only: ['edit', 'update']),
            new Middleware(middleware: 'permission:tithes.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of tithes.
     */
    public function index(Request $request)
    {
        $query = Tithe::with(['member', 'recordedBy', 'attendanceSession.serviceType']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('member_id', 'like', "%{$search}%");
                  });
            });
        }

        // Member filter
        if ($request->filled('member_id')) {
            $query->byMember($request->member_id);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->byPaymentMethod($request->payment_method);
        }

        // Month filter
        if ($request->filled('month')) {
            $query->whereMonth('month_for', $request->month);
        }

        // Year filter
        if ($request->filled('year')) {
            $query->whereYear('month_for', $request->year);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $query->orderBy('payment_date', 'desc');
        $tithes = $query->paginate(SettingHelper::perPage())->withQueryString();

        // Statistics
        $stats = [
            'total_amount' => Tithe::thisYear()->sum('amount'),
            'this_month' => Tithe::thisMonth()->sum('amount'),
            'total_count' => Tithe::thisYear()->count(),
            'unique_members' => Tithe::thisYear()->distinct('member_id')->count('member_id'),
        ];

        $members = Member::active()->orderBy('first_name')->get();

        return view('admin.finance.tithes.index', compact('tithes', 'stats', 'members'));
    }

    /**
     * Show the form for creating a new tithe.
     */
    public function create(Request $request)
    {
        $members = Member::active()->orderBy('first_name')->get();
        $selectedMember = $request->has('member_id') ? Member::find($request->member_id) : null;
        
        return view('admin.finance.tithes.create', compact('members', 'selectedMember'));
    }

    /**
     * Store a newly created tithe.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'month_for' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['recorded_by'] = auth()->id();

        $tithe = Tithe::create($validated);

        // Send SMS receipt to member
        $this->sendTitheSms($tithe);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tithe recorded successfully',
                'tithe' => $tithe->load('member'),
            ]);
        }

        return redirect()->route('admin.tithes.show', $tithe)
            ->with('success', 'Tithe recorded successfully. Receipt #' . $tithe->receipt_number);
    }

    /**
     * Display the specified tithe.
     */
    public function show(Tithe $tithe)
    {
        $tithe->load(['member', 'financialYear', 'recordedBy', 'attendanceSession.serviceType']);

        return view('admin.finance.tithes.show', compact('tithe'));
    }

    /**
     * Show the form for editing the specified tithe.
     */
    public function edit(Tithe $tithe)
    {
        $members = Member::active()->orderBy('first_name')->get();
        
        return view('admin.finance.tithes.edit', compact('tithe', 'members'));
    }

    /**
     * Update the specified tithe.
     */
    public function update(Request $request, Tithe $tithe)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'month_for' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $tithe->update($validated);

        return redirect()->route('admin.tithes.show', $tithe)
            ->with('success', 'Tithe updated successfully.');
    }

    /**
     * Remove the specified tithe.
     */
    public function destroy(Tithe $tithe)
    {
        $tithe->delete();

        return redirect()->route('admin.tithes.index')
            ->with('success', 'Tithe deleted successfully.');
    }

    /**
     * Show member tithe history.
     */
    public function memberHistory(Member $member, Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $tithes = Tithe::byMember($member->id)
            ->whereYear('payment_date', $year)
            ->orderBy('month_for', 'desc')
            ->get();

        $yearlyTotal = $tithes->sum('amount');
        $monthlyBreakdown = $tithes->groupBy(fn($t) => $t->month_for->format('F'))
            ->map(fn($group) => $group->sum('amount'));

        $years = Tithe::byMember($member->id)
            ->selectRaw('YEAR(payment_date) as year')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->reverse();

        return view('admin.finance.tithes.member-history', compact(
            'member', 'tithes', 'yearlyTotal', 'monthlyBreakdown', 'year', 'years'
        ));
    }

    /**
     * Print tithe receipt.
     */
    public function printReceipt(Tithe $tithe)
    {
        $tithe->load(['member', 'recordedBy', 'attendanceSession.serviceType']);

        return view('admin.finance.tithes.receipt', compact('tithe'));
    }

    /**
     * Show the form for recording tithes for an attendance session.
     */
    public function createForSession()
    {
        $sessions = AttendanceSession::with('serviceType')
            ->orderBy('service_date', 'desc')
            ->limit(30)
            ->get();

        return view('admin.finance.tithes.create-session', compact('sessions'));
    }

    /**
     * Store a single bulk tithe amount for an attendance session.
     */
    public function storeForSession(Request $request)
    {
        $request->validate([
            'attendance_session_id' => 'required|exists:attendance_sessions,id',
            'income_category_id'    => 'nullable|exists:income_categories,id',
            'amount'                => 'required|numeric|min:0.01',
            'payment_method'        => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'notes'                 => 'nullable|string|max:500',
        ]);

        $session = AttendanceSession::findOrFail($request->attendance_session_id);

        // Prevent duplicate session tithe
        $existing = Tithe::where('attendance_session_id', $session->id)
            ->where('collection_type', 'session')
            ->first();

        if ($existing) {
            return back()
                ->with('error', 'A session tithe has already been recorded for this service. Edit the existing record instead.')
                ->withInput();
        }

        Tithe::create([
            'member_id'             => null,
            'attendance_session_id' => $session->id,
            'income_category_id'    => $request->income_category_id ?: null,
            'collection_type'       => 'session',
            'amount'                => $request->amount,
            'payment_date'          => $session->service_date,
            'payment_method'        => $request->payment_method,
            'month_for'             => $session->service_date->startOfMonth()->format('Y-m-d'),
            'notes'                 => $request->notes,
            'recorded_by'           => auth()->id(),
        ]);

        $serviceName = $session->serviceType->name ?? 'Service';
        $sessionDate = $session->service_date->format('M d, Y');

        return redirect()->route('admin.tithes.index')
            ->with('success', "Session tithe of GH₵" . number_format($request->amount, 2) . " recorded for {$serviceName} on {$sessionDate}.");
    }

    /**
     * Send an SMS receipt to the member after a tithe is recorded.
     * Silently skips if SMS is not configured, member has no phone, or it's a session collection.
     */
    private function sendTitheSms(Tithe $tithe): void
    {
        if (!\App\Models\Setting::get('sms_auto_tithe_confirmation', false)) {
            return;
        }

        // Session-level tithes have no individual member
        if (!$tithe->member_id) {
            return;
        }

        $member = $tithe->member ?? $tithe->load('member')->member;
        $phone  = $member?->phone_primary;

        if (!$phone) {
            return;
        }

        try {
            $sms = new GiantSmsService();

            if (!$sms->isConfigured()) {
                return;
            }

            $template = SmsTemplate::where('slug', 'tithe-confirmation')->where('is_active', true)->first();

            if ($template) {
                $message = $template->renderContent([
                    'member_name' => $member->first_name,
                    'amount'      => 'GH' . "\u{20B5}" . number_format((float) $tithe->amount, 2),
                    'date'        => $tithe->month_for->format('F Y'),
                    'receipt_no'  => $tithe->receipt_number,
                ]);
            } else {
                $message = 'Dear ' . $member->first_name . ', your tithe of GH'
                    . "\u{20B5}" . number_format((float) $tithe->amount, 2)
                    . ' for ' . $tithe->month_for->format('F Y')
                    . ' has been received. Receipt #' . $tithe->receipt_number
                    . '. Thank you. God bless you.';
            }

            $sms->send($phone, $message);

            $tithe->update(['sms_sent' => true]);

        } catch (\Throwable $e) {
            // Never let an SMS failure block the tithe record from being saved
            Log::warning('Tithe SMS failed for tithe #' . $tithe->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Monthly tithe report.
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $tithes = Tithe::with('member')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->orderBy('member_id')
            ->get();

        $totalAmount = $tithes->sum('amount');
        $byPaymentMethod = $tithes->groupBy('payment_method')
            ->map(fn($group) => ['count' => $group->count(), 'total' => $group->sum('amount')]);

        return view('admin.finance.tithes.monthly-report', compact(
            'tithes', 'totalAmount', 'byPaymentMethod', 'month', 'year'
        ));
    }
}