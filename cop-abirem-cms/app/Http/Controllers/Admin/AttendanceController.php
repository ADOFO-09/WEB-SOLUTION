<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\Visitor;
use App\Models\ServiceType;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;

class AttendanceController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:attendance.view', only: ['index', 'show']),
            new Middleware('permission:attendance.create', only: ['create', 'store']),
            new Middleware('permission:attendance.mark', only: ['markAttendance', 'storeAttendance', 'scanner', 'processScan']),
            new Middleware('permission:attendance.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of attendance sessions.
     */
    public function index(Request $request)
    {
        $query = AttendanceSession::with(['serviceType', 'createdBy']);

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->filled('date_from')) {
            $query->where('service_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('service_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderBy('service_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $serviceTypes = ServiceType::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total_sessions' => AttendanceSession::count(),
            'open_sessions' => AttendanceSession::where('status', 'open')->count(),
            'this_month_sessions' => AttendanceSession::whereMonth('service_date', now()->month)
                ->whereYear('service_date', now()->year)->count(),
            'this_month_attendance' => AttendanceRecord::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        return view('admin.attendance.index', compact('sessions', 'serviceTypes', 'stats'));
    }

    /**
     * Show the form for creating a new attendance session.
     */
    public function create()
    {
        $serviceTypes = ServiceType::where('is_active', true)->orderBy('name')->get();
        $openSessions = AttendanceSession::where('status', 'open')
            ->with('serviceType')
            ->orderBy('service_date', 'desc')
            ->get();
        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();
            
        return view('admin.attendance.create', compact('serviceTypes', 'openSessions', 'ministries'));
    }

    /**
     * Store a newly created attendance session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'service_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'open';

        $session = AttendanceSession::create($validated);

        return redirect()->route('admin.attendance.mark', $session)
            ->with('success', 'Attendance session created. You can now mark attendance.');
    }

    /**
     * Display the specified attendance session.
     */
    public function show(AttendanceSession $session)
    {
        $session->load(['serviceType', 'createdBy', 'records.member', 'records.visitor']);

        $memberRecords = $session->records()->whereNotNull('member_id')->with('member')->get();
        $visitorRecords = $session->records()->whereNotNull('visitor_id')->with('visitor')->get();
        
        $stats = [
            'total' => $memberRecords->count() + $visitorRecords->count(),
            'members' => $memberRecords->count(),
            'visitors' => $visitorRecords->count(),
            'children' => $session->total_children ?? 0,
            'late' => $session->records()->where('is_late', true)->count(),
        ];
        
        $attendance = $session;

        return view('admin.attendance.show', compact('attendance', 'memberRecords', 'visitorRecords', 'stats'));
    }

    /**
     * Show the attendance marking form.
     */
    public function markAttendance(AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return redirect()->route('admin.attendance.show', $session)
                ->with('error', 'This session is closed. Attendance cannot be marked.');
        }

        $session->load(['serviceType', 'records.member', 'records.visitor']);

        // Get members not yet marked
        $markedMemberIds = $session->records()->whereNotNull('member_id')->pluck('member_id')->toArray();
        $availableMembers = Member::where('membership_status', 'active')
            ->when(count($markedMemberIds) > 0, fn($q) => $q->whereNotIn('id', $markedMemberIds))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Get visitors not yet marked (only unconverted visitors)
        $markedVisitorIds = $session->records()->whereNotNull('visitor_id')->pluck('visitor_id')->toArray();
        $availableVisitors = Visitor::where('follow_up_status', '!=', 'converted')
            ->when(count($markedVisitorIds) > 0, fn($q) => $q->whereNotIn('id', $markedVisitorIds))
            ->orderBy('first_name')
            ->get();

        // Already marked records
        $records = $session->records;

        // Pass as $attendance to match view expectations
        $attendance = $session;

        return view('admin.attendance.mark', compact(
            'attendance', 'availableMembers', 'availableVisitors', 'records'
        ));
    }

    /**
     * Store attendance records.
     */
    public function storeAttendance(Request $request, AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return back()->with('error', 'This session is closed.');
        }

        $validated = $request->validate([
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:members,id',
            'visitor_ids' => 'nullable|array',
            'visitor_ids.*' => 'exists:visitors,id',
        ]);

        $addedCount = 0;

        // Add member attendance
        if (!empty($validated['member_ids'])) {
            foreach ($validated['member_ids'] as $memberId) {
                $exists = $session->records()->where('member_id', $memberId)->exists();
                if (!$exists) {
                    $session->records()->create([
                        'member_id' => $memberId,
                        'check_in_time' => now(),
                        'check_in_method' => 'manual',
                        'marked_by' => auth()->id(),
                    ]);
                    $addedCount++;
                }
            }
        }

        // Add visitor attendance
        if (!empty($validated['visitor_ids'])) {
            foreach ($validated['visitor_ids'] as $visitorId) {
                $exists = $session->records()->where('visitor_id', $visitorId)->exists();
                if (!$exists) {
                    $session->records()->create([
                        'visitor_id' => $visitorId,
                        'check_in_time' => now(),
                        'check_in_method' => 'manual',
                        'marked_by' => auth()->id(),
                    ]);
                    $addedCount++;
                }
            }
        }

        // Update session totals
        $session->update([
            'total_members' => $session->records()->whereNotNull('member_id')->count(),
            'total_visitors' => $session->records()->whereNotNull('visitor_id')->count(),
        ]);

        return back()->with('success', "{$addedCount} attendance record(s) added.");
    }

    /**
     * Show QR scanner interface.
     */
    public function scanner(AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return redirect()->route('admin.attendance.show', $session)
                ->with('error', 'This session is closed.');
        }

        $session->load('serviceType');

        $attendance = $session;

        return view('admin.attendance.scanner', compact('attendance'));
    }

    /**
     * Process QR code scan.
     */
    public function processScan(Request $request, AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Session is closed.'], 400);
        }

        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Find member by QR code (member_id)
        $member = Member::where('member_id', $validated['qr_code'])->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found with this QR code.',
            ], 404);
        }

        // Check if already marked
        $exists = $session->records()->where('member_id', $member->id)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => "{$member->full_name} is already marked present.",
                'member' => $member,
            ], 409);
        }

        // Create record
        $session->records()->create([
            'member_id' => $member->id,
            'check_in_time' => now(),
            'check_in_method' => 'qr_scan',
            'marked_by' => auth()->id(),
        ]);

        // Update totals
        $session->update([
            'total_members' => $session->records()->whereNotNull('member_id')->count(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$member->full_name} marked present!",
            'member' => $member,
            'total_members' => $session->total_members,
        ]);
    }

    /**
     * Close an attendance session.
     */
    public function close(AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return back()->with('error', 'Session is already closed.');
        }

        $session->update([
            'status' => 'closed',
            'end_time' => now()->format('H:i:s'),
            'total_members' => $session->records()->whereNotNull('member_id')->count(),
            'total_visitors' => $session->records()->whereNotNull('visitor_id')->count(),
        ]);

        return redirect()->route('admin.attendance.show', $session)
            ->with('success', 'Attendance session closed successfully.');
    }

    /**
     * Reopen a closed attendance session.
     */
    public function reopen(AttendanceSession $session)
    {
        if ($session->status === 'open') {
            return back()->with('error', 'Session is already open.');
        }

        $session->update([
            'status' => 'open',
        ]);

        return redirect()->route('admin.attendance.mark', $session)
            ->with('success', 'Attendance session reopened. You can continue marking attendance.');
    }

    /**
     * Remove the specified attendance session.
     */
    public function destroy(AttendanceSession $session)
    {
        if ($session->records()->count() > 0) {
            return back()->with('error', 'Cannot delete session with attendance records. Close it instead.');
        }

        $session->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance session deleted.');
    }

    /**
     * Remove a single attendance record.
     */
    public function removeRecord(AttendanceSession $session, AttendanceRecord $record)
    {
        if ($session->status === 'closed') {
            return back()->with('error', 'Cannot modify closed session.');
        }

        $record->delete();

        // Update totals
        $session->update([
            'total_members' => $session->records()->whereNotNull('member_id')->count(),
            'total_visitors' => $session->records()->whereNotNull('visitor_id')->count(),
        ]);

        return back()->with('success', 'Attendance record removed.');
    }

    /**
     * Mark a member present (AJAX).
     */
    public function markMember(Request $request, AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Session is closed.'], 400);
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        // Check if already marked
        if ($session->records()->where('member_id', $validated['member_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Member already marked present.'], 409);
        }

        $member = Member::find($validated['member_id']);

        $record = $session->records()->create([
            'member_id' => $validated['member_id'],
            'check_in_time' => now(),
            'check_in_method' => 'manual',
            'marked_by' => auth()->id(),
        ]);

        // Update totals
        $totalMembers = $session->records()->whereNotNull('member_id')->count();
        $totalVisitors = $session->records()->whereNotNull('visitor_id')->count();
        $session->update([
            'total_members' => $totalMembers,
            'total_visitors' => $totalVisitors,
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$member->full_name} marked present!",
            'record' => [
                'id' => $record->id,
                'name' => $member->full_name,
                'type' => 'member',
                'check_in_time' => $record->check_in_time->format('g:i A'),
                'is_late' => false,
            ],
            'totals' => [
                'total' => $totalMembers + $totalVisitors,
                'members' => $totalMembers,
                'visitors' => $totalVisitors,
            ],
        ]);
    }

    /**
     * Mark a visitor present (AJAX).
     */
    public function markVisitor(Request $request, AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Session is closed.'], 400);
        }

        $validated = $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
        ]);

        // Check if already marked
        if ($session->records()->where('visitor_id', $validated['visitor_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Visitor already marked present.'], 409);
        }

        $visitor = Visitor::find($validated['visitor_id']);

        $record = $session->records()->create([
            'visitor_id' => $validated['visitor_id'],
            'check_in_time' => now(),
            'check_in_method' => 'manual',
            'marked_by' => auth()->id(),
        ]);

        // Update totals
        $totalMembers = $session->records()->whereNotNull('member_id')->count();
        $totalVisitors = $session->records()->whereNotNull('visitor_id')->count();
        $session->update([
            'total_members' => $totalMembers,
            'total_visitors' => $totalVisitors,
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$visitor->first_name} {$visitor->last_name} marked present!",
            'record' => [
                'id' => $record->id,
                'name' => "{$visitor->first_name} {$visitor->last_name}",
                'type' => 'visitor',
                'check_in_time' => $record->check_in_time->format('g:i A'),
                'is_late' => false,
            ],
            'totals' => [
                'total' => $totalMembers + $totalVisitors,
                'members' => $totalMembers,
                'visitors' => $totalVisitors,
            ],
        ]);
    }

    /**
     * Unmark attendance record (AJAX).
     */
    public function unmark(Request $request, AttendanceSession $session)
    {
        if ($session->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Session is closed.'], 400);
        }

        $validated = $request->validate([
            'record_id' => 'required|exists:attendance_records,id',
        ]);

        $record = AttendanceRecord::find($validated['record_id']);

        if ($record->session_id !== $session->id) {
            return response()->json(['success' => false, 'message' => 'Record does not belong to this session.'], 400);
        }

        $record->delete();

        // Update totals
        $totalMembers = $session->records()->whereNotNull('member_id')->count();
        $totalVisitors = $session->records()->whereNotNull('visitor_id')->count();
        $session->update([
            'total_members' => $totalMembers,
            'total_visitors' => $totalVisitors,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance removed.',
            'totals' => [
                'total' => $totalMembers + $totalVisitors,
                'members' => $totalMembers,
                'visitors' => $totalVisitors,
            ],
        ]);
    }
}
