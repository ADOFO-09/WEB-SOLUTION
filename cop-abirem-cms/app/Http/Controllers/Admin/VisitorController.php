<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\FollowUpLog;
use App\Models\ServiceType;
use App\Models\SmsTemplate;
use App\Services\GiantSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Helpers\SettingHelper;

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

        // Sorting — whitelist columns to prevent column injection
        $allowedSorts = ['first_name', 'last_name', 'first_visit_date', 'created_at', 'follow_up_status'];
        $allowedDirections = ['asc', 'desc'];
        $sortField     = in_array($request->get('sort'), $allowedSorts) ? $request->get('sort') : 'first_visit_date';
        $sortDirection = in_array($request->get('direction'), $allowedDirections) ? $request->get('direction') : 'desc';
        $query->orderBy($sortField, $sortDirection);

        $visitors = $query->paginate(SettingHelper::perPage())->withQueryString();

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

        // Send welcome SMS
        $this->sendWelcomeSms($visitor);

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
            'visits' => fn($q) => $q->with('serviceType')->latest('visit_date'),
            'followUpLogs' => fn($q) => $q->with('contactedBy')->latest('contact_date'),
        ]);

        $serviceTypes = ServiceType::where('is_active', true)->orderBy('name')->get();

        return view('admin.visitors.show', compact('visitor', 'serviceTypes'));
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
            'follow_up_status' => 'required|in:pending,contacted,interested,not_interested,converted',
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
            'contact_method'     => 'required|in:phone,sms,email,visit,whatsapp',
            'outcome'            => 'required|in:reached,no_answer,callback,interested,not_interested',
            'notes'              => 'nullable|string|max:1000',
            'next_follow_up_date' => 'nullable|date|after_or_equal:today',
        ]);

        $visitor->followUpLogs()->create([
            'contact_date'       => now(),
            'contact_method'     => $validated['contact_method'],
            'outcome'            => $validated['outcome'],
            'notes'              => $validated['notes'] ?? null,
            'next_follow_up_date' => $validated['next_follow_up_date'] ?? null,
            'contacted_by'       => auth()->id(),
        ]);

        // Never change the status of an already-converted visitor
        if (!$visitor->isConverted()) {
            $newStatus = match ($validated['outcome']) {
                'not_interested' => 'not_interested',
                'interested'     => 'interested',
                'reached'        => $visitor->follow_up_status === 'pending' ? 'contacted' : $visitor->follow_up_status,
                default          => $visitor->follow_up_status,
            };
            $visitor->update(['follow_up_status' => $newStatus]);
        }

        // Send SMS if the chosen contact method is SMS
        if ($validated['contact_method'] === 'sms') {
            $this->sendFollowUpSms($visitor, $validated['notes'] ?? null);
        }

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
            // Re-check inside the transaction with a row lock to prevent double-submission
            $locked = Visitor::lockForUpdate()->find($visitor->id);
            if (!$locked->canBeConverted()) {
                DB::rollBack();
                return back()->with('error', 'This visitor has already been converted to a member.');
            }

            $member = $locked->convertToMember($validated);

            DB::commit();

            return redirect()->route('admin.members.show', $member)
                ->with('success', 'Visitor successfully converted to member.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to convert visitor: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to convert visitor. Please try again.');
        }
    }

    /**
     * Record a new visit.
     */
    public function recordVisit(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'service_type_id' => 'nullable|exists:service_types,id',
            'visit_date'      => 'nullable|date',
            'notes'           => 'nullable|string|max:500',
        ]);

        $visitor->visits()->create([
            'visit_date'      => $validated['visit_date'] ?? now()->toDateString(),
            'service_type_id' => $validated['service_type_id'] ?? null,
            'notes'           => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Visit recorded successfully.');
    }

    /**
     * Send a welcome SMS to a newly registered visitor.
     */
    private function sendWelcomeSms(Visitor $visitor): void
    {
        if (empty($visitor->phone)) {
            return;
        }

        try {
            $sms = new GiantSmsService();
            if (!$sms->isConfigured()) {
                return;
            }

            $template = SmsTemplate::where('slug', 'visitor-welcome')->where('is_active', true)->first();

            if ($template) {
                $message = $template->renderContent([
                    'name' => $visitor->first_name,
                    'date' => $visitor->first_visit_date->format('d M Y'),
                ]);
            } else {
                $churchName = \App\Helpers\SettingHelper::churchShortName();
                $message = 'Dear ' . $visitor->first_name . ', welcome to ' . $churchName . '!'
                    . ' We are glad you visited us on ' . $visitor->first_visit_date->format('d M Y')
                    . '. We hope to see you again. God bless you! - ' . $churchName;
            }

            $sms->send($visitor->phone, $message);

        } catch (\Throwable $e) {
            Log::warning('Visitor welcome SMS failed for visitor #' . $visitor->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Send a follow-up SMS to a visitor.
     */
    private function sendFollowUpSms(Visitor $visitor, ?string $notes): void
    {
        if (empty($visitor->phone)) {
            return;
        }

        try {
            $sms = new GiantSmsService();
            if (!$sms->isConfigured()) {
                return;
            }

            $template = SmsTemplate::where('slug', 'visitor-followup')->where('is_active', true)->first();

            $notesLine = $notes ? trim($notes) . ' ' : '';

            if ($template) {
                $message = $template->renderContent([
                    'name'  => $visitor->first_name,
                    'notes' => $notesLine,
                ]);
            } else {
                $churchName = \App\Helpers\SettingHelper::churchShortName();
                $message = 'Dear ' . $visitor->first_name . ', greetings from ' . $churchName . '!'
                    . ' We are following up on your visit with us. '
                    . $notesLine
                    . 'We would love to see you again. God bless you! - ' . $churchName;
            }

            $sms->send($visitor->phone, $message);

        } catch (\Throwable $e) {
            Log::warning('Visitor follow-up SMS failed for visitor #' . $visitor->id . ': ' . $e->getMessage());
        }
    }
}