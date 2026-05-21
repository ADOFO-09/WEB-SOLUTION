<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Helpers\SettingHelper;

class ActivityLogController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:settings.logs', only: ['index', 'export']),
        ];
    }

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
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(SettingHelper::perPage())->withQueryString();

        $users = User::select('id', 'name')->orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'users', 'actions'));
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = ActivityLog::with('user');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            
            // Header row
            fputcsv($handle, [
                'Date/Time',
                'User',
                'Action',
                'Description',
                'Model Type',
                'Model ID',
                'IP Address',
                'User Agent',
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'System',
                    $log->action,
                    $log->description,
                    $log->model_type,
                    $log->model_id,
                    $log->ip_address,
                    $log->user_agent,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
