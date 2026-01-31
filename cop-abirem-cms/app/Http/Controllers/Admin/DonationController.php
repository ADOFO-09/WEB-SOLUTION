<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Member;
use App\Models\IncomeCategory;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DonationController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:finance.view', only: ['index', 'show', 'printReceipt']),
            new Middleware(middleware: 'permission:finance.create', only: ['create', 'store']),
            new Middleware(middleware: 'permission:finance.edit', only: ['edit', 'update']),
            new Middleware(middleware: 'permission:finance.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of donations.
     */
    public function index(Request $request)
    {
        $query = Donation::with(['member', 'incomeCategory', 'project', 'recordedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%")
                  ->orWhere('donor_name', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Project filter
        if ($request->filled('project_id')) {
            $query->byProject($request->project_id);
        }

        // Type filter
        if ($request->filled('donation_type')) {
            $query->byType($request->donation_type);
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $query->orderBy('payment_date', 'desc');
        $donations = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_amount' => Donation::thisYear()->cash()->sum('amount'),
            'this_month' => Donation::thisMonth()->cash()->sum('amount'),
            'total_count' => Donation::thisYear()->count(),
            'in_kind_count' => Donation::thisYear()->inKind()->count(),
        ];

        $projects = Project::active()->orderBy('name')->get();

        return view('admin.finance.donations.index', compact('donations', 'stats', 'projects'));
    }

    /**
     * Show the form for creating a new donation.
     */
    public function create(Request $request)
    {
        $members = Member::active()->orderBy('first_name')->get();
        $categories = IncomeCategory::active()->orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();

        $selectedProject = $request->has('project_id') 
            ? Project::find($request->project_id) 
            : null;
        
        return view('admin.finance.donations.create', compact('members', 'categories', 'projects', 'selectedProject'));
    }

    /**
     * Store a newly created donation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'donor_name' => 'nullable|string|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'income_category_id' => 'nullable|exists:income_categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'donation_type' => 'required|in:cash,in_kind',
            'amount' => 'required_if:donation_type,cash|nullable|numeric|min:0',
            'in_kind_description' => 'required_if:donation_type,in_kind|nullable|string|max:500',
            'estimated_value' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required_if:donation_type,cash|nullable|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'is_anonymous' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['is_anonymous'] = $request->boolean('is_anonymous');
        $validated['recorded_by'] = auth()->id();

        // If anonymous, clear member_id and donor info
        if ($validated['is_anonymous']) {
            $validated['member_id'] = null;
            $validated['donor_name'] = null;
            $validated['donor_phone'] = null;
        }

        $donation = Donation::create($validated);

        // Update project amount if linked
        if ($donation->project_id) {
            $donation->project->updateAmountRaised();
        }

        return redirect()->route('admin.donations.show', $donation)
            ->with('success', 'Donation recorded successfully. Receipt #' . $donation->receipt_number);
    }

    /**
     * Display the specified donation.
     */
    public function show(Donation $donation)
    {
        $donation->load(['member', 'incomeCategory', 'project', 'financialYear', 'recordedBy']);
        
        return view('admin.finance.donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified donation.
     */
    public function edit(Donation $donation)
    {
        $members = Member::active()->orderBy('first_name')->get();
        $categories = IncomeCategory::active()->orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();
        
        return view('admin.finance.donations.edit', compact('donation', 'members', 'categories', 'projects'));
    }

    /**
     * Update the specified donation.
     */
    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'donor_name' => 'nullable|string|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'income_category_id' => 'nullable|exists:income_categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'donation_type' => 'required|in:cash,in_kind',
            'amount' => 'required_if:donation_type,cash|nullable|numeric|min:0',
            'in_kind_description' => 'required_if:donation_type,in_kind|nullable|string|max:500',
            'estimated_value' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required_if:donation_type,cash|nullable|in:cash,mobile_money,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'is_anonymous' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['is_anonymous'] = $request->boolean('is_anonymous');

        $oldProjectId = $donation->project_id;
        $donation->update($validated);

        // Update project amounts
        if ($oldProjectId) {
            Project::find($oldProjectId)?->updateAmountRaised();
        }
        if ($donation->project_id) {
            $donation->project->updateAmountRaised();
        }

        return redirect()->route('admin.donations.show', $donation)
            ->with('success', 'Donation updated successfully.');
    }

    /**
     * Remove the specified donation.
     */
    public function destroy(Donation $donation)
    {
        $projectId = $donation->project_id;
        $donation->delete();

        if ($projectId) {
            Project::find($projectId)?->updateAmountRaised();
        }

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }

    /**
     * Print donation receipt.
     */
    public function printReceipt(Donation $donation)
    {
        $donation->load(['member', 'project', 'recordedBy']);
        
        return view('admin.finance.donations.receipt', compact('donation'));
    }
}