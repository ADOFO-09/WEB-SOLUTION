<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', "%{$request->model_type}%");
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in values
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_SEARCH(old_values, 'all', ?) IS NOT NULL", ["%{$search}%"])
                  ->orWhereRaw("JSON_SEARCH(new_values, 'all', ?) IS NOT NULL", ["%{$search}%"]);
            });
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name']);
        
        // Get unique actions
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();
        
        // Get unique model types
        $modelTypes = ActivityLog::whereNotNull('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(fn($type) => class_basename($type))
            ->unique()
            ->sort()
            ->values();

        return view('admin.activity-logs.index', compact('logs', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Display the specified log entry.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        
        return view('admin.activity-logs.show', compact('activityLog'));
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Date/Time', 'User', 'Action', 'Model', 'Model ID', 'IP Address']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->model_type ? class_basename($log->model_type) : '-',
                    $log->model_id ?? '-',
                    $log->ip_address ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
