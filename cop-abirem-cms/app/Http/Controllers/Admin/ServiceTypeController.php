<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ServiceTypeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'permission:attendance.view', only: ['index', 'show']),
            new Middleware(middleware: 'permission:attendance.create', only: ['create', 'store']),
            new Middleware(middleware: 'permission:attendance.edit', only: ['edit', 'update']),
            new Middleware(middleware: 'permission:attendance.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of service types.
     */
    public function index(Request $request)
    {
        $query = ServiceType::withCount('attendanceSessions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $serviceTypes = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.service-types.index', compact('serviceTypes'));
    }

    /**
     * Show the form for creating a new service type.
     */
    public function create()
    {
        return view('admin.service-types.create');
    }

    /**
     * Store a newly created service type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:service_types',
            'day_of_week' => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'default_start_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        ServiceType::create($validated);

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type created successfully.');
    }

    /**
     * Display the specified service type.
     */
    public function show(ServiceType $serviceType)
    {
        $serviceType->load(['attendanceSessions' => fn($q) => $q->latest('service_date')->limit(10)]);

        $stats = [
            'total_sessions' => $serviceType->attendanceSessions()->count(),
            'total_attendance' => $serviceType->attendanceSessions()->sum('total_attendance'),
            'avg_attendance' => round($serviceType->attendanceSessions()->avg('total_attendance') ?? 0),
        ];

        return view('admin.service-types.show', compact('serviceType', 'stats'));
    }

    /**
     * Show the form for editing the specified service type.
     */
    public function edit(ServiceType $serviceType)
    {
        return view('admin.service-types.edit', compact('serviceType'));
    }

    /**
     * Update the specified service type.
     */
    public function update(Request $request, ServiceType $serviceType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('service_types')->ignore($serviceType->id)],
            'day_of_week' => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'default_start_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $serviceType->update($validated);

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type updated successfully.');
    }

    /**
     * Remove the specified service type.
     */
    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->attendanceSessions()->count() > 0) {
            return back()->with('error', 'Cannot delete service type with attendance sessions.');
        }

        $serviceType->delete();

        return redirect()->route('admin.service-types.index')
            ->with('success', 'Service type deleted successfully.');
    }
}