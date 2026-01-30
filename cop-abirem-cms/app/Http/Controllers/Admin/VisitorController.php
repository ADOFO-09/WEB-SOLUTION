<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\FollowUpLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VisitorController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:visitors.view', only: ['index', 'show']),
            new Middleware(middleware: 'permission:visitors.create', only: ['create', 'store', 'recordVisit']),
            new Middleware(middleware: 'permission:visitors.edit', only: ['edit', 'update', 'addFollowUp', 'showConvertForm', 'convert']),
            new Middleware(middleware: 'permission:visitors.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of visitors.
     */
    public function index(Request $request)
    {
        $query = Visitor::with(['referredBy', 'createdBy'])
            ->withCount('visits');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Follow-up status filter
        if ($request->filled('follow_up_status')) {
            $query->byFollowUpStatus($request->follow_up_status);
        }

        // Referral source filter
        if ($request->filled('referral_source')) {
            $query->byReferralSource($request->referral_source);
        }

        // Conversion status filter
        if ($request->filled('converted')) {
            if ($request->converted === 'yes') {
                $query->converted();
            } else {
                $query->notConverted();
            }
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('first_visit_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('first_visit_date', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'first_visit_date');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $visitors = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Visitor::count(),
            'this_month' => Visitor::whereMonth('first_visit_date', now()->month)->count(),
            'pending_followup' => Visitor::pendingFollowUp()->count(),
            'converted' => Visitor::converted()->count(),
        ];

        return view('admin.visitors.index', compact('visitors', 'stats'));
    }

    /**
     * Show the form for creating a new visitor.
     */
    public function create()
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.visitors.create', compact('members'));
    }

    /**
     * Store a newly created visitor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'first_visit_date' => 'required|date',
            'referral_source' => 'required|in:member,walk_in,social_media,event,flyer,other',
            'referred_by_member_id' => 'nullable|exists:members,id',
            'prayer_request' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['follow_up_status'] = 'pending';
        $validated['created_by'] = auth()->id();

        $visitor = Visitor::create($validated);

        // Record first visit
        $visitor->recordVisit();

        return redirect()->route('admin.visitors.show', $visitor)
            ->with('success', 'Visitor registered successfully.');
    }

    /**
     * Display the specified visitor.
     */
    public function show(Visitor $visitor)
    {
        $visitor->load([
            'referredBy',
            'convertedToMember',
            'createdBy',
            'visits' => fn($q) => $q->with('session.serviceType')->latest('visit_date'),
            'followUpLogs' => fn($q) => $q->with('contactedBy')->latest('contact_date'),
        ]);

        return view('admin.visitors.show', compact('visitor'));
    }

    /**
     * Show the form for editing the specified visitor.
     */
    public function edit(Visitor $visitor)
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.visitors.edit', compact('visitor', 'members'));
    }

    /**
     * Update the specified visitor.
     */
    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'first_visit_date' => 'required|date',
            'referral_source' => 'required|in:member,walk_in,social_media,event,flyer,other',
            'referred_by_member_id' => 'nullable|exists:members,id',
            'prayer_request' => 'nullable|string|max:1000',
            'follow_up_status' => 'required|in:pending,in_progress,completed,not_interested,converted',
            'notes' => 'nullable|string|max:1000',
        ]);

        $visitor->update($validated);

        return redirect()->route('admin.visitors.show', $visitor)
            ->with('success', 'Visitor updated successfully.');
    }

    /**
     * Remove the specified visitor.
     */
    public function destroy(Visitor $visitor)
    {
        if ($visitor->isConverted()) {
            return back()->with('error', 'Cannot delete a converted visitor.');
        }

        $visitor->delete();

        return redirect()->route('admin.visitors.index')
            ->with('success', 'Visitor deleted successfully.');
    }

    /**
     * Add a follow-up log.
     */
    public function addFollowUp(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'contact_method' => 'required|in:phone,sms,email,visit,in_person',
            'outcome' => 'required|in:reached,no_answer,interested,not_interested,callback_requested,wrong_number',
            'notes' => 'nullable|string|max:1000',
            'next_action' => 'nullable|string|max:255',
            'next_action_date' => 'nullable|date|after_or_equal:today',
        ]);

        $visitor->addFollowUpLog(
            $validated['contact_method'],
            $validated['outcome'],
            $validated['notes'],
            auth()->id()
        );

        // Update follow-up status based on outcome
        $newStatus = match ($validated['outcome']) {
            'not_interested' => 'not_interested',
            'interested' => 'in_progress',
            default => $visitor->follow_up_status,
        };

        if ($validated['outcome'] === 'reached' && $visitor->follow_up_status === 'pending') {
            $newStatus = 'in_progress';
        }

        $visitor->update(['follow_up_status' => $newStatus]);

        return back()->with('success', 'Follow-up recorded successfully.');
    }

    /**
     * Show convert to member form.
     */
    public function showConvertForm(Visitor $visitor)
    {
        if (!$visitor->canBeConverted()) {
            return back()->with('error', 'This visitor cannot be converted to a member.');
        }

        $memberId = Member::generateMemberId();
        $ministries = Ministry::active()->orderBy('name')->get();

        return view('admin.visitors.convert', compact('visitor', 'memberId', 'ministries'));
    }

    /**
     * Convert visitor to member.
     */
    public function convert(Request $request, Visitor $visitor)
    {
        if (!$visitor->canBeConverted()) {
            return back()->with('error', 'This visitor cannot be converted to a member.');
        }

        $validated = $request->validate([
            'member_id' => 'required|string|max:20|unique:members',
            'title' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'phone_primary' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'date_joined' => 'required|date',
            'baptism_type' => 'required|in:water,holy_spirit,both,none',
            'membership_status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();

        try {
            $member = $visitor->convertToMember($validated);

            DB::commit();

            return redirect()->route('admin.members.show', $member)
                ->with('success', 'Visitor successfully converted to member.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to convert visitor: ' . $e->getMessage());
        }
    }

    /**
     * Record a new visit.
     */
    public function recordVisit(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'session_id' => 'nullable|exists:attendance_sessions,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $visitor->recordVisit($validated['session_id'] ?? null, $validated['notes'] ?? null);

        return back()->with('success', 'Visit recorded successfully.');
    }
}