<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use App\Models\Member;
use App\Models\IncomeCategory;
use App\Models\AttendanceSession;
use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OfferingController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:offerings.view', only: ['index', 'show', 'sessionSummary']),
            new Middleware(middleware: 'permission:offerings.create', only: ['create', 'store']),
            new Middleware(middleware: 'permission:offerings.edit', only: ['edit', 'update']),
            new Middleware(middleware: 'permission:offerings.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of offerings.
     */
    public function index(Request $request)
    {
        $query = Offering::with(['member', 'incomeCategory', 'session.serviceType', 'recordedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Session filter
        if ($request->filled('session_id')) {
            $query->bySession($request->session_id);
        }

        // Anonymous filter
        if ($request->filled('anonymous')) {
            if ($request->anonymous === 'yes') {
                $query->anonymous();
            } else {
                $query->nonAnonymous();
            }
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $query->orderBy('payment_date', 'desc');
        $offerings = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_amount' => Offering::thisYear()->sum('amount'),
            'this_month' => Offering::thisMonth()->sum('amount'),
            'total_count' => Offering::thisYear()->count(),
            'anonymous_count' => Offering::thisYear()->anonymous()->count(),
        ];

        $categories = IncomeCategory::active()->orderBy('name')->get();
        $recentSessions = AttendanceSession::with('serviceType')
            ->orderBy('service_date', 'desc')
            ->limit(20)
            ->get();

        return view('admin.finance.offerings.index', compact('offerings', 'stats', 'categories', 'recentSessions'));
    }

    /**
     * Show the form for creating a new offering.
     */
    public function create(Request $request)
    {
        $members = Member::active()->orderBy('first_name')->get();
        $categories = IncomeCategory::active()->orderBy('name')->get();
        $sessions = AttendanceSession::with('serviceType')
            ->orderBy('service_date', 'desc')
            ->limit(30)
            ->get();

        $selectedSession = $request->has('session_id')
            ? AttendanceSession::find($request->session_id)
            : null;

        $pledges = Pledge::with('member')
            ->whereIn('status', ['active'])
            ->orderBy('pledge_date', 'desc')
            ->get();

        return view('admin.finance.offerings.create', compact(
            'members', 'categories', 'sessions', 'selectedSession', 'pledges'
        ));
    }

    /**
     * Store a newly created offering.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'          => 'nullable|exists:members,id',
            'income_category_id' => 'required|exists:income_categories,id',
            'session_id'         => 'nullable|exists:attendance_sessions,id',
            'pledge_id'          => 'nullable|exists:pledges,id',
            'amount'             => 'required|numeric|min:0.01',
            'payment_date'       => 'required|date',
            'payment_method'     => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference'  => 'nullable|string|max:100',
            'is_anonymous'       => 'boolean',
            'notes'              => 'nullable|string|max:500',
        ]);

        $validated['is_anonymous'] = $request->boolean('is_anonymous');
        $validated['recorded_by']  = auth()->id();

        if ($validated['is_anonymous']) {
            $validated['member_id'] = null;
            $validated['pledge_id'] = null;
        }

        $offering = Offering::create($validated);

        // Update pledge amount paid when linked to a special offering
        if ($offering->pledge_id) {
            $pledge = Pledge::find($offering->pledge_id);
            if ($pledge) {
                $pledge->increment('amount_paid', (float) $offering->amount);
                $pledge->checkAndUpdateStatus();
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Offering recorded successfully',
                'offering' => $offering->load(['member', 'incomeCategory']),
            ]);
        }

        return redirect()->route('admin.offerings.index')
            ->with('success', 'Offering recorded successfully.');
    }

    /**
     * Display the specified offering.
     */
    public function show(Offering $offering)
    {
        $offering->load(['member', 'incomeCategory', 'session.serviceType', 'financialYear', 'recordedBy']);
        
        return view('admin.finance.offerings.show', compact('offering'));
    }

    /**
     * Show the form for editing the specified offering.
     */
    public function edit(Offering $offering)
    {
        $members = Member::active()->orderBy('first_name')->get();
        $categories = IncomeCategory::active()->orderBy('name')->get();
        $sessions = AttendanceSession::with('serviceType')
            ->orderBy('service_date', 'desc')
            ->limit(30)
            ->get();
        $pledges = Pledge::with('member')
            ->whereIn('status', ['active'])
            ->orderBy('pledge_date', 'desc')
            ->get();

        return view('admin.finance.offerings.edit', compact(
            'offering', 'members', 'categories', 'sessions', 'pledges'
        ));
    }

    /**
     * Update the specified offering.
     */
    public function update(Request $request, Offering $offering)
    {
        $validated = $request->validate([
            'member_id'          => 'nullable|exists:members,id',
            'income_category_id' => 'required|exists:income_categories,id',
            'session_id'         => 'nullable|exists:attendance_sessions,id',
            'pledge_id'          => 'nullable|exists:pledges,id',
            'amount'             => 'required|numeric|min:0.01',
            'payment_date'       => 'required|date',
            'payment_method'     => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference'  => 'nullable|string|max:100',
            'is_anonymous'       => 'boolean',
            'notes'              => 'nullable|string|max:500',
        ]);

        $validated['is_anonymous'] = $request->boolean('is_anonymous');

        if ($validated['is_anonymous']) {
            $validated['member_id'] = null;
            $validated['pledge_id'] = null;
        }

        $oldPledgeId = $offering->pledge_id;
        $oldAmount   = (float) $offering->amount;

        $offering->update($validated);

        // Recalculate pledge amounts when pledge link or amount changes
        $newPledgeId = $offering->fresh()->pledge_id;

        if ($oldPledgeId && $oldPledgeId !== $newPledgeId) {
            // Pledge was removed or changed — reverse old pledge's amount
            $oldPledge = Pledge::find($oldPledgeId);
            if ($oldPledge) {
                $oldPledge->decrement('amount_paid', $oldAmount);
                $oldPledge->checkAndUpdateStatus();
            }
        }

        if ($newPledgeId) {
            $newPledge = Pledge::find($newPledgeId);
            if ($newPledge) {
                if ($oldPledgeId === $newPledgeId) {
                    // Same pledge — adjust by the difference
                    $diff = (float) $offering->fresh()->amount - $oldAmount;
                    if ($diff != 0) {
                        $newPledge->increment('amount_paid', $diff);
                        $newPledge->checkAndUpdateStatus();
                    }
                } else {
                    // New pledge linked — add full amount
                    $newPledge->increment('amount_paid', (float) $offering->fresh()->amount);
                    $newPledge->checkAndUpdateStatus();
                }
            }
        }

        return redirect()->route('admin.offerings.show', $offering)
            ->with('success', 'Offering updated successfully.');
    }

    /**
     * Remove the specified offering.
     */
    public function destroy(Offering $offering)
    {
        $offering->delete();

        return redirect()->route('admin.offerings.index')
            ->with('success', 'Offering deleted successfully.');
    }

    /**
     * Session offerings summary.
     */
    public function sessionSummary(AttendanceSession $session)
    {
        $offerings = Offering::bySession($session->id)
            ->with(['member', 'incomeCategory'])
            ->orderBy('created_at')
            ->get();

        $totalAmount = $offerings->sum('amount');
        $byCategory = $offerings->groupBy('income_category_id')
            ->map(fn($group) => [
                'name' => $group->first()->incomeCategory->name ?? 'Uncategorized',
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ]);

        return view('admin.finance.offerings.session-summary', compact(
            'session', 'offerings', 'totalAmount', 'byCategory'
        ));
    }
}