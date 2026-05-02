<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of all projects.
     */
    public function index()
    {
        $projects = Project::withCount(['donations', 'pledges'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.finance.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('admin.finance.projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'status'        => 'required|in:planned,active,completed,cancelled',
        ]);

        $validated['slug']       = Str::slug($validated['name']);
        $validated['created_by'] = auth()->id();

        Project::create($validated);

        return redirect()->route('admin.finance.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $donationCount = $project->donations()->count();
        $pledgeCount   = $project->pledges()->count();
        $raised        = $project->donations()->sum('amount');

        $recentDonations = $project->donations()
            ->with('member')
            ->latest('payment_date')
            ->limit(10)
            ->get();

        $recentPledges = $project->pledges()
            ->with('member')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.finance.projects.show', compact(
            'project',
            'donationCount',
            'pledgeCount',
            'raised',
            'recentDonations',
            'recentPledges'
        ));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        return view('admin.finance.projects.edit', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'status'        => 'required|in:planned,active,completed,cancelled',
        ]);

        if ($project->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $project->update($validated);

        return redirect()->route('admin.finance.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project (soft delete).
     */
    public function destroy(Project $project)
    {
        if ($project->donations()->count() > 0 || $project->pledges()->count() > 0) {
            return back()->with('error', 'Cannot delete a project that has linked donations or pledges.');
        }

        $project->delete();

        return redirect()->route('admin.finance.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
