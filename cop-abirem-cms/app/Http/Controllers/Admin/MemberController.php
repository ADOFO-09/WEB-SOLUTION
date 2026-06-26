<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\FamilyRelationship;
use App\Models\Setting;
use App\Models\SmsTemplate;
use App\Services\GiantSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\SettingHelper;

class MemberController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:members.view', only: ['index', 'show', 'showQrCode', 'printCard']),
            new Middleware(middleware: 'permission:members.create', only: ['create', 'store']),
            new Middleware(middleware: 'permission:members.edit', only: ['edit', 'update', 'familyRelationships']),
            new Middleware(middleware: 'permission:members.delete', only: ['destroy']),
            new Middleware(middleware: 'permission:members.export', only: ['export']),
        ];
    }

    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $query = Member::with(['createdBy', 'activeMinistries']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }

        // Ministry filter
        if ($request->filled('ministry')) {
            $query->inMinistry($request->ministry);
        }

        // Marital status filter
        if ($request->filled('marital_status')) {
            $query->byMaritalStatus($request->marital_status);
        }

        // Sorting — whitelist columns to prevent column injection
        $allowedSorts = ['first_name', 'last_name', 'date_joined', 'created_at', 'membership_status', 'gender'];
        $allowedDirections = ['asc', 'desc'];
        $sortField     = in_array($request->get('sort'), $allowedSorts) ? $request->get('sort') : 'created_at';
        $sortDirection = in_array($request->get('direction'), $allowedDirections) ? $request->get('direction') : 'desc';
        $query->orderBy($sortField, $sortDirection);

        $members = $query->paginate(SettingHelper::perPage())->withQueryString();
        $ministries = Ministry::active()->orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => Member::count(),
            'active' => Member::active()->count(),
            'inactive' => Member::inactive()->count(),
            'male' => Member::active()->byGender('male')->count(),
            'female' => Member::active()->byGender('female')->count(),
            'birthdays_this_month' => Member::active()->birthdayThisMonth()->count(),
        ];

        return view('admin.members.index', compact('members', 'ministries', 'stats'));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        $ministries = Ministry::active()->orderBy('name')->get();
        $memberId = Member::generateMemberId();
        
        return view('admin.members.create', compact('ministries', 'memberId'));
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string|max:20|unique:members',
            'title' => 'nullable|in:Mr,Mrs,Miss,Elder,Deacon,Deaconess,Pastor,Evangelist,Prophet,Apostle',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'email' => 'nullable|email|max:255|unique:members',
            'phone_primary' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'employer' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'date_joined' => 'required|date',
            'baptism_date' => 'nullable|date',
            'baptism_type' => 'required|in:water,holy_spirit,both,none',
            'membership_status' => 'required|in:active,inactive,transferred_out,transferred_in,deceased',
            'previous_church' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'ministries' => 'nullable|array',
            'ministries.*' => 'exists:ministries,id',
            'fingerprint_template_1' => 'nullable|string',
            'fingerprint_template_2' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $validated['photo_path'] = $request->file('photo')->store('members/photos', 'local');
            }

            // Handle biometric enrollment during creation
            if (!empty($validated['fingerprint_template_1'])) {
                // Duplicate fingerprint guard — same as BiometricController::enroll()
                $hash1 = hash('sha256', $validated['fingerprint_template_1']);
                $conflict = Member::where('fingerprint_hash_1', $hash1)->first()
                         ?? Member::where('fingerprint_hash_2', $hash1)->first();

                if ($conflict) {
                    DB::rollBack();
                    return back()->withErrors([
                        'fingerprint_template_1' => 'Primary fingerprint is already enrolled for '
                            . $conflict->full_name . '. Each member must use their own unique finger.',
                    ])->withInput();
                }

                $hash2 = !empty($validated['fingerprint_template_2'])
                    ? hash('sha256', $validated['fingerprint_template_2'])
                    : null;

                if ($hash2) {
                    $conflict2 = Member::where('fingerprint_hash_1', $hash2)->first()
                              ?? Member::where('fingerprint_hash_2', $hash2)->first();

                    if ($conflict2) {
                        DB::rollBack();
                        return back()->withErrors([
                            'fingerprint_template_2' => 'Backup fingerprint is already enrolled for '
                                . $conflict2->full_name . '. Each member must use their own unique finger.',
                        ])->withInput();
                    }
                }

                $validated['fingerprint_hash_1']    = $hash1;
                $validated['fingerprint_hash_2']    = $hash2;
                $validated['biometric_enrolled']    = true;
                $validated['biometric_enrolled_at'] = now();
            } else {
                unset($validated['fingerprint_template_1'], $validated['fingerprint_template_2']);
            }

            $validated['created_by'] = auth()->id();

            $member = Member::create($validated);

            // Assign ministries
            if (!empty($validated['ministries'])) {
                foreach ($validated['ministries'] as $ministryId) {
                    $member->ministries()->attach($ministryId, [
                        'role' => 'member',
                        'joined_date' => now()->toDateString(),
                        'is_active' => true,
                    ]);
                }
            }

            // Generate QR Code
            $this->generateQrCode($member);

            DB::commit();

            $this->sendWelcomeSms($member);

            $successMsg = 'Member registered successfully.';
            if ($member->biometric_enrolled) {
                $successMsg .= ' Biometric fingerprint enrolled.';
            }

            return redirect()->route('admin.members.show', $member)
                ->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to register member: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to register member. Please try again.');
        }
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member)
    {
        $member->load([
            'createdBy',
            'updatedBy',
            'activeMinistries',
            'familyRelationships.relatedMember',
            'tithes' => fn($q) => $q->latest()->limit(10),
            'attendanceRecords' => fn($q) => $q->with('session')->latest()->limit(10),
        ]);

        // Calculate statistics
        $stats = [
            'total_tithes' => $member->getTotalTithes(date('Y')),
            'total_offerings' => $member->getTotalOfferings(date('Y')),
            'attendance_rate' => $member->getAttendanceRate(90),
        ];

        return view('admin.members.show', compact('member', 'stats'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member)
    {
        $member->load('activeMinistries');
        $ministries = Ministry::active()->orderBy('name')->get();
        $allMembers = Member::where('id', '!=', $member->id)
            ->active()
            ->orderBy('first_name')
            ->get();

        return view('admin.members.edit', compact('member', 'ministries', 'allMembers'));
    }

    /**
     * Update the specified member.
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'title' => 'nullable|in:Mr,Mrs,Miss,Elder,Deacon,Deaconess,Pastor,Evangelist,Prophet,Apostle',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('members')->ignore($member->id)],
            'phone_primary' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'employer' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'date_joined' => 'required|date',
            'baptism_date' => 'nullable|date',
            'baptism_type' => 'required|in:water,holy_spirit,both,none',
            'membership_status' => 'required|in:active,inactive,transferred_out,transferred_in,deceased',
            'previous_church' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'ministries' => 'nullable|array',
            'ministries.*' => 'exists:ministries,id',
        ]);

        DB::beginTransaction();

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($member->photo_path) {
                    Storage::disk('local')->delete($member->photo_path);
                }
                $validated['photo_path'] = $request->file('photo')->store('members/photos', 'local');
            }

            $validated['updated_by'] = auth()->id();
            
            $member->update($validated);

            // Update ministry assignments
            if (isset($validated['ministries'])) {
                // Get current active ministries
                $currentMinistries = $member->activeMinistries->pluck('id')->toArray();
                $newMinistries = $validated['ministries'];

                // Remove from ministries no longer selected
                $toRemove = array_diff($currentMinistries, $newMinistries);
                foreach ($toRemove as $ministryId) {
                    $member->ministries()->updateExistingPivot($ministryId, [
                        'is_active' => false,
                        'left_date' => now()->toDateString(),
                    ]);
                }

                // Add to new ministries
                $toAdd = array_diff($newMinistries, $currentMinistries);
                foreach ($toAdd as $ministryId) {
                    $member->ministries()->syncWithoutDetaching([
                        $ministryId => [
                            'role' => 'member',
                            'joined_date' => now()->toDateString(),
                            'is_active' => true,
                        ]
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.members.show', $member)
                ->with('success', 'Member updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update member: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update member. Please try again.');
        }
    }

    /**
     * Remove the specified member (soft delete).
     */
    public function destroy(Member $member)
    {
        try {
            $member->delete();
            return redirect()->route('admin.members.index')
                ->with('success', 'Member deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete member: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete member. Please try again.');
        }
    }

    /**
     * Generate QR Code for member using SVG format (no Imagick required).
     */
    public function generateQrCode(Member $member): string
    {
        $qrData = json_encode($member->getQrCodeData());
        
        // Use SVG format - no Imagick extension required
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($qrData);

        $filename = 'members/qrcodes/' . $member->member_id . '.svg';
        Storage::disk('public')->put($filename, $qrCode);

        // Update member's QR code path
        $member->update(['qr_code_path' => $filename]);

        return $filename;
    }

    /**
     * Show QR Code for member.
     */
    public function showQrCode(Member $member)
    {
        // Regenerate QR code if it doesn't exist
        if (!$member->qr_code_path || !Storage::disk('public')->exists($member->qr_code_path)) {
            $this->generateQrCode($member);
            $member->refresh();
        }

        return view('admin.members.qrcode', compact('member'));
    }

    /**
     * Download the member's QR code as an SVG file.
     */
    public function downloadQr(Member $member)
    {
        if (!$member->qr_code_path || !Storage::disk('public')->exists($member->qr_code_path)) {
            $this->generateQrCode($member);
            $member->refresh();
        }

        $path     = Storage::disk('public')->path($member->qr_code_path);
        $filename = $member->member_id . '-qrcode.svg';

        return response()->download($path, $filename, ['Content-Type' => 'image/svg+xml']);
    }

    /**
     * Regenerate QR Code for member.
     */
    public function regenerateQrCode(Member $member)
    {
        // Delete old QR code if exists
        if ($member->qr_code_path && Storage::disk('public')->exists($member->qr_code_path)) {
            Storage::disk('public')->delete($member->qr_code_path);
        }

        $this->generateQrCode($member);

        return back()->with('success', 'QR Code regenerated successfully.');
    }

    /**
     * Print member card.
     */
    public function printCard(Member $member)
    {
        // Ensure QR code exists
        if (!$member->qr_code_path || !Storage::disk('public')->exists($member->qr_code_path)) {
            $this->generateQrCode($member);
            $member->refresh();
        }

        return view('admin.members.card', compact('member'));
    }

    /**
     * Export members to Excel/CSV.
     */
    public function export(Request $request)
    {
        $query = Member::with('activeMinistries');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }
        if ($request->filled('ministry')) {
            $query->inMinistry($request->ministry);
        }

        $members = $query->orderBy('first_name')->get();

        // Generate CSV
        $filename = 'members_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($members) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Member ID', 'Title', 'First Name', 'Middle Name', 'Last Name',
                'Date of Birth', 'Age', 'Gender', 'Marital Status',
                'Phone (Primary)', 'Phone (Secondary)', 'Email',
                'Address', 'City', 'Region',
                'Occupation', 'Employer',
                'Date Joined', 'Baptism Type', 'Status',
                'Ministries'
            ]);

            foreach ($members as $member) {
                fputcsv($file, [
                    $member->member_id,
                    $member->title,
                    $member->first_name,
                    $member->middle_name,
                    $member->last_name,
                    $member->date_of_birth?->format('Y-m-d'),
                    $member->age,
                    ucfirst($member->gender),
                    ucfirst($member->marital_status),
                    $member->phone_primary,
                    $member->phone_secondary,
                    $member->email,
                    $member->address,
                    $member->city,
                    $member->region,
                    $member->occupation,
                    $member->employer,
                    $member->date_joined?->format('Y-m-d'),
                    ucfirst(str_replace('_', ' ', $member->baptism_type)),
                    ucfirst(str_replace('_', ' ', $member->membership_status)),
                    $member->activeMinistries->pluck('name')->implode(', '),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Manage family relationships.
     */
    public function familyRelationships(Request $request, Member $member)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'related_member_id' => 'required|exists:members,id|different:member_id',
                'relationship_type' => 'required|in:spouse,child,parent,sibling',
            ]);

            FamilyRelationship::createBidirectional(
                $member->id,
                $validated['related_member_id'],
                $validated['relationship_type']
            );

            return back()->with('success', 'Family relationship added successfully.');
        }

        if ($request->isMethod('delete')) {
            $validated = $request->validate([
                'related_member_id' => 'required|exists:members,id',
            ]);

            FamilyRelationship::removeBidirectional($member->id, $validated['related_member_id']);

            return back()->with('success', 'Family relationship removed.');
        }

        $member->load('familyRelationships.relatedMember');
        $availableMembers = Member::where('id', '!=', $member->id)
            ->active()
            ->orderBy('first_name')
            ->get();

        return view('admin.members.family', compact('member', 'availableMembers'));
    }

    /**
     * Send a welcome SMS to a newly registered member.
     */
    private function sendWelcomeSms(Member $member): void
    {
        $phone = $member->phone_primary;

        if (!$phone) {
            return;
        }

        try {
            $sms = new GiantSmsService();

            if (!$sms->isConfigured()) {
                return;
            }

            $template = SmsTemplate::where('slug', 'welcome-new-member')->where('is_active', true)->first();

            if ($template) {
                $message = $template->renderContent([
                    'member_name' => $member->first_name,
                    'member_id'   => $member->member_id,
                ]);
            } else {
                $churchName = \App\Helpers\SettingHelper::churchShortName();
                $message = 'Dear ' . $member->first_name . ', welcome to ' . $churchName . '!'
                    . ' Your member ID is ' . $member->member_id . '.'
                    . ' We are glad to have you. God bless you! - ' . $churchName;
            }

            $sms->send($phone, $message);

        } catch (\Throwable $e) {
            Log::warning('Welcome SMS failed for member #' . $member->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Serve member photo from private disk (authenticated).
     */
    public function photo(Member $member)
    {
        if (!$member->photo_path || !Storage::disk('local')->exists($member->photo_path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('local')->path($member->photo_path),
            ['Cache-Control' => 'private, max-age=3600']
        );
    }
}