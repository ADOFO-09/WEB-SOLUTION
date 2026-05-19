<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MinistryController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:ministries.view', only: ['index', 'show']),
            new Middleware(middleware: 'permission:ministries.create', only: ['create', 'store']),
            new Middleware(middleware: 'permission:ministries.edit', only: ['edit', 'update', 'members']),
            new Middleware(middleware: 'permission:ministries.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of ministries.
     */
    public function index(Request $request)
    {
        $query = Ministry::withCount('activeMembers')->with('leader');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $ministries = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.ministries.index', compact('ministries'));
    }

    /**
     * Show the form for creating a new ministry.
     */
    public function create()
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.ministries.create', compact('members'));
    }

    /**
     * Store a newly created ministry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:ministries',
            'description' => 'nullable|string|max:500',
            'type' => 'nullable|string|in:' . implode(',', array_keys(Ministry::TYPES)),
            'leader_id' => 'nullable|exists:members,id',
            'meeting_day' => 'nullable|string|max:20',
            'meeting_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $ministry = Ministry::create($validated);

        // If leader is assigned, add them to ministry as leader
        if ($ministry->leader_id) {
            $ministry->addMember(Member::find($ministry->leader_id), 'leader');
        }

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministry created successfully.');
    }

    /**
     * Display the specified ministry.
     */
    public function show(Ministry $ministry)
    {
        $ministry->load(['leader', 'activeMembers' => function ($q) {
            $q->orderByPivot('role', 'desc')->orderBy('first_name');
        }]);

        return view('admin.ministries.show', compact('ministry'));
    }

    /**
     * Show the form for editing the specified ministry.
     */
    public function edit(Ministry $ministry)
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.ministries.edit', compact('ministry', 'members'));
    }

    /**
     * Update the specified ministry.
     */
    public function update(Request $request, Ministry $ministry)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('ministries')->ignore($ministry->id)],
            'description' => 'nullable|string|max:500',
            'type' => 'nullable|string|in:' . implode(',', array_keys(Ministry::TYPES)),
            'leader_id' => 'nullable|exists:members,id',
            'meeting_day' => 'nullable|string|max:20',
            'meeting_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle leader change
        $oldLeaderId = $ministry->leader_id;
        $newLeaderId = $validated['leader_id'];

        $ministry->update($validated);

        // Update leader roles
        if ($oldLeaderId !== $newLeaderId) {
            // Demote old leader
            if ($oldLeaderId) {
                $ministry->members()->updateExistingPivot($oldLeaderId, ['role' => 'member']);
            }
            // Promote new leader
            if ($newLeaderId) {
                $member = Member::find($newLeaderId);
                if ($ministry->activeMembers->contains($member)) {
                    $ministry->members()->updateExistingPivot($newLeaderId, ['role' => 'leader']);
                } else {
                    $ministry->addMember($member, 'leader');
                }
            }
        }

        return redirect()->route('admin.ministries.show', $ministry)
            ->with('success', 'Ministry updated successfully.');
    }

    /**
     * Remove the specified ministry.
     */
    public function destroy(Ministry $ministry)
    {
        try {
            $ministry->delete();
            return redirect()->route('admin.ministries.index')
                ->with('success', 'Ministry deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete ministry: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete ministry. Please try again.');
        }
    }

    /**
     * Manage ministry members.
     */
    public function members(Request $request, Ministry $ministry)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'role' => 'required|in:member,leader,assistant_leader',
            ]);

            $member = Member::find($validated['member_id']);
            
            // Check if already a member
            if ($ministry->activeMembers->contains($member)) {
                // Update role
                $ministry->members()->updateExistingPivot($member->id, [
                    'role' => $validated['role'],
                ]);
                $message = 'Member role updated.';
            } else {
                // Add member
                $ministry->addMember($member, $validated['role']);
                $message = 'Member added to ministry.';
            }

            return back()->with('success', $message);
        }

        if ($request->isMethod('delete')) {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
            ]);

            $ministry->removeMember(Member::find($validated['member_id']));

            return back()->with('success', 'Member removed from ministry.');
        }

        $ministry->load(['activeMembers' => function ($q) {
            $q->orderByPivot('role', 'desc')->orderBy('first_name');
        }]);

        $availableMembers = Member::active()
            ->whereNotIn('id', $ministry->activeMembers->pluck('id'))
            ->orderBy('first_name')
            ->get();

        return view('admin.ministries.members', compact('ministry', 'availableMembers'));
    }
}