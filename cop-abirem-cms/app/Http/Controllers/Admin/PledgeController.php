<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pledge;
use App\Models\PledgePayment;
use App\Models\Member;
use App\Models\IncomeCategory;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PledgeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:pledges.view', only: ['index', 'show', 'overdueReport']),
            new Middleware(middleware: 'permission:pledges.create', only: ['create', 'store', 'recordPayment']),
            new Middleware(middleware: 'permission:pledges.edit', only: ['edit', 'update', 'cancel']),
            new Middleware(middleware: 'permission:pledges.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of pledges.
     */
    public function index(Request $request)
    {
        $query = Pledge::with(['member', 'project', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pledge_number', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Project filter
        if ($request->filled('project_id')) {
            $query->byProject($request->project_id);
        }

        // Member filter
        if ($request->filled('member_id')) {
            $query->byMember($request->member_id);
        }

        $query->orderBy('created_at', 'desc');
        $pledges = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_pledged' => Pledge::active()->sum('total_amount'),
            'total_paid' => Pledge::active()->sum('amount_paid'),
            'active_count' => Pledge::active()->count(),
            'overdue_count' => Pledge::overdue()->count(),
            'fulfilled_count' => Pledge::completed()->count(),
        ];

        $projects = Project::active()->orderBy('name')->get();
        $members = Member::active()->orderBy('first_name')->get();

        return view('admin.finance.pledges.index', compact('pledges', 'stats', 'projects', 'members'));
    }

    /**
     * Show the form for creating a new pledge.
     */
    public function create(Request $request)
    {
        $members = Member::active()->orderBy('first_name')->get();
        $categories = IncomeCategory::active()->orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();

        $selectedMember = $request->has('member_id') ? Member::find($request->member_id) : null;
        $selectedProject = $request->has('project_id') ? Project::find($request->project_id) : null;
        
        return view('admin.finance.pledges.create', compact(
            'members', 'categories', 'projects', 'selectedMember', 'selectedProject'
        ));
    }

    /**
     * Store a newly created pledge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'income_category_id' => 'nullable|exists:income_categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'purpose' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:1',
            'pledge_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:pledge_date',
            'payment_frequency' => 'required|in:one_time,weekly,monthly,quarterly,annually',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['status'] = 'active';
        $validated['amount_paid'] = 0;
        $validated['created_by'] = auth()->id();

        $pledge = Pledge::create($validated);

        return redirect()->route('admin.pledges.show', $pledge)
            ->with('success', 'Pledge created successfully. Pledge #' . $pledge->pledge_number);
    }

    /**
     * Display the specified pledge.
     */
    public function show(Pledge $pledge)
    {
        $pledge->load([
            'member', 
            'incomeCategory', 
            'project', 
            'financialYear', 
            'createdBy',
            'payments' => fn($q) => $q->with('recordedBy')->orderBy('payment_date', 'desc'),
        ]);
        
        return view('admin.finance.pledges.show', compact('pledge'));
    }

    /**
     * Show the form for editing the specified pledge.
     */
    public function edit(Pledge $pledge)
    {
        if ($pledge->status !== 'active') {
            return back()->with('error', 'Only active pledges can be edited.');
        }

        $members = Member::active()->orderBy('first_name')->get();
        $categories = IncomeCategory::active()->orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();
        
        return view('admin.finance.pledges.edit', compact('pledge', 'members', 'categories', 'projects'));
    }

    /**
     * Update the specified pledge.
     */
    public function update(Request $request, Pledge $pledge)
    {
        if ($pledge->status !== 'active') {
            return back()->with('error', 'Only active pledges can be edited.');
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'income_category_id' => 'nullable|exists:income_categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'purpose' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:' . $pledge->amount_paid,
            'pledge_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:pledge_date',
            'payment_frequency' => 'required|in:one_time,weekly,monthly,quarterly,annually',
            'notes' => 'nullable|string|max:500',
        ]);

        $pledge->update($validated);

        return redirect()->route('admin.pledges.show', $pledge)
            ->with('success', 'Pledge updated successfully.');
    }

    /**
     * Remove the specified pledge.
     */
    public function destroy(Pledge $pledge)
    {
        if ($pledge->payments()->count() > 0) {
            return back()->with('error', 'Cannot delete pledge with payments. Cancel it instead.');
        }

        $pledge->delete();

        return redirect()->route('admin.pledges.index')
            ->with('success', 'Pledge deleted successfully.');
    }

    /**
     * Record a payment for the pledge.
     */
    public function recordPayment(Request $request, Pledge $pledge)
    {
        if ($pledge->status !== 'active') {
            return back()->with('error', 'Can only record payments for active pledges.');
        }

        $maxAmount = $pledge->balance;
        
        $validated = $request->validate([
            'amount' => "required|numeric|min:0.01|max:{$maxAmount}",
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment = $pledge->recordPayment($validated['amount'], $validated);

        // Update project if linked
        if ($pledge->project_id) {
            $pledge->project->updateAmountRaised();
        }

        return back()->with('success', 'Payment of GH₵ ' . number_format($validated['amount'], 2) . ' recorded. Receipt #' . $payment->receipt_number);
    }

    /**
     * Cancel a pledge.
     */
    public function cancel(Request $request, Pledge $pledge)
    {
        if ($pledge->status !== 'active') {
            return back()->with('error', 'Only active pledges can be cancelled.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $pledge->cancel($validated['reason']);

        return redirect()->route('admin.pledges.show', $pledge)
            ->with('success', 'Pledge has been cancelled.');
    }

    /**
     * Show overdue pledges report.
     */
    public function overdueReport()
    {
        $pledges = Pledge::overdue()
            ->with(['member', 'project'])
            ->orderBy('due_date')
            ->get();

        $totalOverdue = $pledges->sum('balance');

        return view('admin.finance.pledges.overdue-report', compact('pledges', 'totalOverdue'));
    }
}