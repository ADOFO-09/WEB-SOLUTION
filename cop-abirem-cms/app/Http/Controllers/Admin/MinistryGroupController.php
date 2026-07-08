<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Models\MinistryGroup;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MinistryGroupController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ministry.groups.view',   only: ['index', 'report', 'export']),
            new Middleware('permission:ministry.groups.create', only: ['create', 'store']),
            new Middleware('permission:ministry.groups.edit',   only: ['edit', 'update']),
            new Middleware('permission:ministry.groups.delete', only: ['destroy']),
            new Middleware('permission:ministry.groups.assign', only: ['assign', 'saveAssignments']),
        ];
    }

    // ==========================================
    // AUTHORIZATION
    // ==========================================

    /**
     * Verify the requesting user has access to this ministry's groups.
     * Admins/Elders/Secretaries: unrestricted.
     * Ministry Leaders: only their own ministry.
     */
    private function ensureMinistryAccess(Ministry $ministry): void
    {
        if (auth()->user()->hasPermission('ministries.view')) {
            return;
        }

        $user = auth()->user();

        if ($ministry->leader_id && $ministry->leader_id === $user->member_id) {
            return;
        }

        $isPivotLeader = DB::table('member_ministry')
            ->where('ministry_id', $ministry->id)
            ->where('member_id', $user->member_id)
            ->where('role', 'leader')
            ->where('is_active', true)
            ->exists();

        if ($isPivotLeader) {
            return;
        }

        abort(403, 'You can only manage groups for your own ministry.');
    }

    /**
     * Ensure $group belongs to $ministry (route model binding can't enforce this).
     */
    private function ensureGroupBelongs(MinistryGroup $group, Ministry $ministry): void
    {
        if ($group->ministry_id !== $ministry->id) {
            abort(404);
        }
    }

    // ==========================================
    // INDEX
    // ==========================================

    public function index(Ministry $ministry)
    {
        $this->ensureMinistryAccess($ministry);

        $groups = $ministry->groups()
            ->withCount(['members'])
            ->with('leader')
            ->orderBy('name')
            ->get();

        $totalGrouped   = $ministry->totalGroupedMembers();
        $totalUngrouped = $ministry->totalUngroupedMembers();
        $totalMembers   = $ministry->activeMembers()->count();

        return view('admin.ministries.groups.index', compact(
            'ministry', 'groups', 'totalGrouped', 'totalUngrouped', 'totalMembers'
        ));
    }

    // ==========================================
    // CREATE / STORE
    // ==========================================

    public function create(Ministry $ministry)
    {
        $this->ensureMinistryAccess($ministry);

        $ministryMembers = $ministry->activeMembers()->orderBy('first_name')->get();

        return view('admin.ministries.groups.form', compact('ministry', 'ministryMembers'));
    }

    public function store(Request $request, Ministry $ministry)
    {
        $this->ensureMinistryAccess($ministry);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('ministry_groups')->where('ministry_id', $ministry->id),
            ],
            'description'      => 'nullable|string|max:500',
            'leader_member_id' => [
                'nullable',
                Rule::exists('member_ministry', 'member_id')
                    ->where('ministry_id', $ministry->id)
                    ->where('is_active', true),
            ],
            'is_active' => 'boolean',
        ]);

        $validated['ministry_id'] = $ministry->id;
        $validated['created_by']  = auth()->id();
        $validated['is_active']   = $request->boolean('is_active', true);

        MinistryGroup::create($validated);

        return redirect()->route('admin.ministries.groups.index', $ministry)
            ->with('success', "Group \"{$validated['name']}\" created successfully.");
    }

    // ==========================================
    // EDIT / UPDATE
    // ==========================================

    public function edit(Ministry $ministry, MinistryGroup $group)
    {
        $this->ensureMinistryAccess($ministry);
        $this->ensureGroupBelongs($group, $ministry);

        $ministryMembers = $ministry->activeMembers()->orderBy('first_name')->get();

        return view('admin.ministries.groups.form', compact('ministry', 'group', 'ministryMembers'));
    }

    public function update(Request $request, Ministry $ministry, MinistryGroup $group)
    {
        $this->ensureMinistryAccess($ministry);
        $this->ensureGroupBelongs($group, $ministry);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('ministry_groups')
                    ->where('ministry_id', $ministry->id)
                    ->ignore($group->id),
            ],
            'description'      => 'nullable|string|max:500',
            'leader_member_id' => [
                'nullable',
                Rule::exists('member_ministry', 'member_id')
                    ->where('ministry_id', $ministry->id)
                    ->where('is_active', true),
            ],
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $group->update($validated);

        return redirect()->route('admin.ministries.groups.index', $ministry)
            ->with('success', "Group \"{$group->name}\" updated successfully.");
    }

    // ==========================================
    // DESTROY
    // ==========================================

    public function destroy(Request $request, Ministry $ministry, MinistryGroup $group)
    {
        $this->ensureMinistryAccess($ministry);
        $this->ensureGroupBelongs($group, $ministry);

        $unassignedCount = DB::table('member_ministry')
            ->where('ministry_group_id', $group->id)
            ->count();

        DB::transaction(function () use ($group) {
            // Unassign members before delete (FK nullOnDelete handles this, but we do it
            // explicitly so the count message is accurate and no race can sneak through)
            DB::table('member_ministry')
                ->where('ministry_group_id', $group->id)
                ->update(['ministry_group_id' => null]);

            $group->delete();
        });

        $msg = "Group \"{$group->name}\" deleted.";
        if ($unassignedCount > 0) {
            $msg .= " {$unassignedCount} member(s) were unassigned from the group (they remain in the ministry).";
        }

        return redirect()->route('admin.ministries.groups.index', $ministry)
            ->with('success', $msg);
    }

    // ==========================================
    // ASSIGN PAGE
    // ==========================================

    public function assign(Ministry $ministry)
    {
        $this->ensureMinistryAccess($ministry);

        $ministry->load(['activeMembers' => function ($q) {
            $q->orderBy('first_name')->orderBy('last_name');
        }]);

        $groups = $ministry->activeGroups()->orderBy('name')->get();

        // Map member_id => group_id (null if unassigned)
        $currentAssignments = $ministry->activeMembers
            ->pluck('pivot.ministry_group_id', 'id')
            ->toArray();

        // Initial count per group for Alpine.js
        $groupCounts = $groups->mapWithKeys(fn ($g) => [
            $g->id => collect($currentAssignments)->filter(fn ($gid) => $gid == $g->id)->count(),
        ])->toArray();

        return view('admin.ministries.groups.assign', compact(
            'ministry', 'groups', 'currentAssignments', 'groupCounts'
        ));
    }

    public function saveAssignments(Request $request, Ministry $ministry)
    {
        $this->ensureMinistryAccess($ministry);

        $validated = $request->validate([
            'assignments'   => 'nullable|array',
            'assignments.*' => 'nullable|integer',
        ]);

        $assignments = $validated['assignments'] ?? [];

        // Collect valid member IDs for this ministry
        $validMemberIds = $ministry->activeMembers()->pluck('members.id')->flip();

        // Collect valid group IDs for this ministry
        $validGroupIds = $ministry->activeGroups()->pluck('id')->flip();

        DB::transaction(function () use ($assignments, $ministry, $validMemberIds, $validGroupIds) {
            foreach ($assignments as $memberId => $groupId) {
                $memberId = (int) $memberId;
                $groupId  = $groupId ? (int) $groupId : null;

                if (!isset($validMemberIds[$memberId])) {
                    continue;
                }

                if ($groupId !== null && !isset($validGroupIds[$groupId])) {
                    continue;
                }

                $ministry->members()->updateExistingPivot($memberId, [
                    'ministry_group_id' => $groupId,
                ]);
            }
        });

        return redirect()->route('admin.ministries.groups.assign', $ministry)
            ->with('success', 'Group assignments saved successfully.');
    }

    // ==========================================
    // REPORT
    // ==========================================

    public function report(Ministry $ministry, MinistryGroup $group)
    {
        $this->ensureMinistryAccess($ministry);
        $this->ensureGroupBelongs($group, $ministry);

        $group->load('leader');

        $members = $ministry->activeMembers()
            ->wherePivot('ministry_group_id', $group->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $allGroups = $ministry->activeGroups()->orderBy('name')->get();

        return view('admin.ministries.groups.report', compact(
            'ministry', 'group', 'members', 'allGroups'
        ));
    }

    // ==========================================
    // EXPORT (CSV)
    // ==========================================

    public function export(Ministry $ministry, MinistryGroup $group)
    {
        $this->ensureMinistryAccess($ministry);
        $this->ensureGroupBelongs($group, $ministry);

        $members = $ministry->activeMembers()
            ->wherePivot('ministry_group_id', $group->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $filename = str_replace(' ', '-', strtolower($ministry->name))
                  . '-' . str_replace(' ', '-', strtolower($group->name))
                  . '-' . date('Y-m-d') . '.csv';

        $callback = function () use ($members, $ministry, $group) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Ministry: ' . $ministry->name . ' — Group: ' . $group->name,
            ]);
            fputcsv($file, [
                'Member ID', 'First Name', 'Last Name', 'Gender',
                'Phone', 'Email', 'Role in Ministry', 'Date Joined',
            ]);

            foreach ($members as $member) {
                fputcsv($file, [
                    $member->member_id ?? $member->id,
                    $member->first_name,
                    $member->last_name,
                    ucfirst($member->gender ?? ''),
                    $member->phone_primary ?? '',
                    $member->email ?? '',
                    ucfirst(str_replace('_', ' ', $member->pivot->role ?? 'member')),
                    $member->pivot->joined_date
                        ? \Carbon\Carbon::parse($member->pivot->joined_date)->format('d M Y')
                        : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
