<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\ServiceType;
use App\Models\Ministry;
use App\Models\Member;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AttendanceController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:attendance.view', only: ['index', 'show']),
            new Middleware(middleware: 'permission:attendance.create', only: ['create', 'store']),
            new Middleware(middleware: 'permission:attendance.edit', only: [
                'edit', 'update', 'mark', 'markMember', 'markVisitor', 
                'unmark', 'close', 'reopen', 'scanner', 'processScan'
            ]),
            new Middleware(middleware: 'permission:attendance.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of attendance sessions.
     */
    public function index(Request $request)
    {
        $query = AttendanceSession::with(['serviceType', 'ministry', 'createdBy'])
            ->withCount('records');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('theme', 'like', '%' . $request->search . '%')
                  ->orWhere('preacher', 'like', '%' . $request->search . '%');
            });
        }

        // Service type filter
        if ($request->filled('service_type')) {
            $query->byServiceType($request->service_type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('service_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('service_date', '<=', $request->date_to);
        }

        // Sorting
        $query->orderBy('service_date', 'desc')->orderBy('start_time', 'desc');

        $sessions = $query->paginate(15)->withQueryString();
        $serviceTypes = ServiceType::active()->orderBy('name')->get();

        // Statistics
        $stats = [
            'total_sessions' => AttendanceSession::count(),
            'open_sessions' => AttendanceSession::open()->count(),
            'this_month_sessions' => AttendanceSession::thisMonth()->count(),
            'this_month_attendance' => AttendanceSession::thisMonth()->sum('total_attendance'),
        ];

        return view('admin.attendance.index', compact('sessions', 'serviceTypes', 'stats'));
    }

    /**
     * Show the form for creating a new session.
     */
    public function create()
    {
        $serviceTypes = ServiceType::active()->orderBy('name')->get();
        $ministries = Ministry::active()->orderBy('name')->get();

        // Check for open sessions
        $openSessions = AttendanceSession::open()->with('serviceType')->get();

        return view('admin.attendance.create', compact('serviceTypes', 'ministries', 'openSessions'));
    }

    /**
     * Store a newly created session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'ministry_id' => 'nullable|exists:ministries,id',
            'service_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['status'] = 'open';
        $validated['created_by'] = auth()->id();

        $session = AttendanceSession::create($validated);

        return redirect()->route('admin.attendance.mark', $session)
            ->with('success', 'Attendance session created. You can now mark attendance.');
    }

    /**
     * Display the specified session.
     */
    public function show(AttendanceSession $attendance)
    {
        $attendance->load([
            'serviceType',
            'ministry',
            'createdBy',
            'closedBy',
            'records' => fn($q) => $q->with(['member', 'visitor', 'markedBy'])->orderBy('check_in_time'),
        ]);

        // Get statistics
        $stats = [
            'total' => $attendance->records->count(),
            'members' => $attendance->records->whereNotNull('member_id')->count(),
            'visitors' => $attendance->records->whereNotNull('visitor_id')->count(),
            'late' => $attendance->records->where('is_late', true)->count(),
            'on_time' => $attendance->records->where('is_late', false)->count(),
        ];

        return view('admin.attendance.show', compact('attendance', 'stats'));
    }

    /**
     * Show the form for editing the specified session.
     */
    public function edit(AttendanceSession $attendance)
    {
        $serviceTypes = ServiceType::active()->orderBy('name')->get();
        $ministries = Ministry::active()->orderBy('name')->get();

        return view('admin.attendance.edit', compact('attendance', 'serviceTypes', 'ministries'));
    }

    /**
     * Update the specified session.
     */
    public function update(Request $request, AttendanceSession $attendance)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'ministry_id' => 'nullable|exists:ministries,id',
            'service_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'total_children' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $attendance->update($validated);

        return redirect()->route('admin.attendance.show', $attendance)
            ->with('success', 'Session updated successfully.');
    }

    /**
     * Remove the specified session.
     */
    public function destroy(AttendanceSession $attendance)
    {
        if ($attendance->records()->count() > 0) {
            return back()->with('error', 'Cannot delete session with attendance records. Please remove records first.');
        }

        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Session deleted successfully.');
    }

    /**
     * Show mark attendance interface.
     */
    public function mark(AttendanceSession $attendance)
    {
        if (!$attendance->is_open) {
            return redirect()->route('admin.attendance.show', $attendance)
                ->with('error', 'This session is closed. Reopen it to mark attendance.');
        }

        $attendance->load(['serviceType', 'records.member', 'records.visitor']);

        // Get all active members not yet marked
        $markedMemberIds = $attendance->getAttendedMemberIds();
        $availableMembers = Member::active()
            ->whereNotIn('id', $markedMemberIds)
            ->orderBy('first_name')
            ->get();

        // Get visitors not yet marked
        $markedVisitorIds = $attendance->getAttendedVisitorIds();
        $availableVisitors = Visitor::notConverted()
            ->whereNotIn('id', $markedVisitorIds)
            ->orderBy('first_name')
            ->get();

        // Attendance records for display
        $records = $attendance->records()
            ->with(['member', 'visitor'])
            ->orderBy('check_in_time', 'desc')
            ->get();

        return view('admin.attendance.mark', compact('attendance', 'availableMembers', 'availableVisitors', 'records'));
    }

    /**
     * Mark member attendance (AJAX).
     */
    public function markMember(Request $request, AttendanceSession $attendance)
    {
        if (!$attendance->is_open) {
            return response()->json(['success' => false, 'message' => 'Session is closed'], 400);
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'method' => 'nullable|in:manual,qr_code',
        ]);

        try {
            $record = $attendance->markMemberAttendance(
                $validated['member_id'],
                $validated['method'] ?? 'manual'
            );

            $member = Member::find($validated['member_id']);

            return response()->json([
                'success' => true,
                'message' => $member->full_name . ' marked present',
                'record' => [
                    'id' => $record->id,
                    'name' => $member->full_name,
                    'member_id' => $member->member_id,
                    'check_in_time' => $record->check_in_time->format('g:i A'),
                    'is_late' => $record->is_late,
                    'type' => 'member',
                ],
                'totals' => [
                    'members' => $attendance->memberRecords()->count(),
                    'visitors' => $attendance->visitorRecords()->count(),
                    'total' => $attendance->records()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark visitor attendance (AJAX).
     */
    public function markVisitor(Request $request, AttendanceSession $attendance)
    {
        if (!$attendance->is_open) {
            return response()->json(['success' => false, 'message' => 'Session is closed'], 400);
        }

        $validated = $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
        ]);

        try {
            $record = $attendance->markVisitorAttendance($validated['visitor_id'], 'manual');
            $visitor = Visitor::find($validated['visitor_id']);

            // Record the visit
            $visitor->recordVisit($attendance->id);

            return response()->json([
                'success' => true,
                'message' => $visitor->full_name . ' (Visitor) marked present',
                'record' => [
                    'id' => $record->id,
                    'name' => $visitor->full_name,
                    'check_in_time' => $record->check_in_time->format('g:i A'),
                    'is_late' => $record->is_late,
                    'type' => 'visitor',
                ],
                'totals' => [
                    'members' => $attendance->memberRecords()->count(),
                    'visitors' => $attendance->visitorRecords()->count(),
                    'total' => $attendance->records()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Unmark attendance (AJAX).
     */
    public function unmark(Request $request, AttendanceSession $attendance)
    {
        if (!$attendance->is_open) {
            return response()->json(['success' => false, 'message' => 'Session is closed'], 400);
        }

        $validated = $request->validate([
            'record_id' => 'required|exists:attendance_records,id',
        ]);

        try {
            $attendance->unmarkAttendance($validated['record_id']);

            return response()->json([
                'success' => true,
                'message' => 'Attendance removed',
                'totals' => [
                    'members' => $attendance->memberRecords()->count(),
                    'visitors' => $attendance->visitorRecords()->count(),
                    'total' => $attendance->records()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Close session.
     */
    public function close(AttendanceSession $attendance)
    {
        $attendance->close();

        return redirect()->route('admin.attendance.show', $attendance)
            ->with('success', 'Session closed successfully.');
    }

    /**
     * Reopen session.
     */
    public function reopen(AttendanceSession $attendance)
    {
        $attendance->reopen();

        return redirect()->route('admin.attendance.mark', $attendance)
            ->with('success', 'Session reopened. You can continue marking attendance.');
    }

    /**
     * QR Code scanner interface.
     */
    public function scanner(AttendanceSession $attendance)
    {
        if (!$attendance->is_open) {
            return redirect()->route('admin.attendance.show', $attendance)
                ->with('error', 'This session is closed.');
        }

        $attendance->load('serviceType');

        return view('admin.attendance.scanner', compact('attendance'));
    }

    /**
     * Process QR code scan (AJAX).
     */
    public function processScan(Request $request, AttendanceSession $attendance)
    {
        if (!$attendance->is_open) {
            return response()->json(['success' => false, 'message' => 'Session is closed'], 400);
        }

        $validated = $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $data = json_decode($validated['qr_data'], true);

            if (!$data || !isset($data['member_id'])) {
                return response()->json(['success' => false, 'message' => 'Invalid QR code'], 400);
            }

            $member = Member::where('member_id', $data['member_id'])->first();

            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found'], 404);
            }

            // Check if already marked
            if ($attendance->isMemberMarked($member->id)) {
                return response()->json([
                    'success' => false,
                    'message' => $member->full_name . ' is already marked present',
                    'already_marked' => true,
                ], 400);
            }

            $record = $attendance->markMemberAttendance($member->id, 'qr_code');

            return response()->json([
                'success' => true,
                'message' => $member->full_name . ' marked present',
                'member' => [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'member_id' => $member->member_id,
                    'photo_url' => $member->photo_url,
                ],
                'record' => [
                    'id' => $record->id,
                    'check_in_time' => $record->check_in_time->format('g:i A'),
                    'is_late' => $record->is_late,
                ],
                'totals' => [
                    'members' => $attendance->memberRecords()->count(),
                    'visitors' => $attendance->visitorRecords()->count(),
                    'total' => $attendance->records()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}