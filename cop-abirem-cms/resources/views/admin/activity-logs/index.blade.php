@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Activity Logs</h1>
    <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="btn btn-secondary">Export CSV</a>
</div>
@endsection

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="min-width: 150px;">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 150px;">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
            </div>
            <div style="min-width: 150px;">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Model</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size: 0.875rem; color: #64748b; white-space: nowrap;">{{ $log->created_at->format('M j, Y g:i A') }}</td>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td><span class="badge badge-info">{{ $log->action }}</span></td>
                    <td style="color: #64748b;">{{ $log->model_type ? class_basename($log->model_type) : '-' }} {{ $log->model_id ? '#'.$log->model_id : '' }}</td>
                    <td style="font-size: 0.875rem; color: #64748b;">{{ $log->ip_address ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: #64748b;">No activity logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="card-body" style="border-top: 1px solid #e2e8f0;">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
