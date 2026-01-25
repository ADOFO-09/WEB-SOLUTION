@extends('layouts.admin')

@section('title', 'View User')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>User Details</h1>
    <div style="display: flex; gap: 0.5rem;">
        @if(auth()->user()->hasPermission('users.edit'))
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit User</a>
        @endif
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-3" style="gap: 1.5rem;">
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header"><h3 style="font-weight: 600;">Profile Information</h3></div>
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="width: 4rem; height: 4rem; border-radius: 9999px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.5rem;">{{ substr($user->name, 0, 1) }}</div>
                    <div>
                        <div style="font-size: 1.25rem; font-weight: 700;">{{ $user->name }}</div>
                        <div style="color: #64748b;">{{ $user->email }}</div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div><span style="color: #64748b; font-size: 0.875rem;">Role</span><div style="font-weight: 500;">{{ $user->role->name }}</div></div>
                    <div><span style="color: #64748b; font-size: 0.875rem;">Status</span><div>@if($user->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-warning">Inactive</span>@endif</div></div>
                    <div><span style="color: #64748b; font-size: 0.875rem;">Created</span><div>{{ $user->created_at->format('M j, Y g:i A') }}</div></div>
                    <div><span style="color: #64748b; font-size: 0.875rem;">Last Login</span><div>{{ $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never' }}</div></div>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header"><h3 style="font-weight: 600;">Recent Activity</h3></div>
            <div class="card-body" style="padding: 0;">
                @if($activityLogs->isEmpty())
                <div style="padding: 2rem; text-align: center; color: #64748b;">No activity recorded</div>
                @else
                <table class="table">
                    <thead><tr><th>Action</th><th>Details</th><th>Time</th></tr></thead>
                    <tbody>
                        @foreach($activityLogs->take(10) as $log)
                        <tr>
                            <td><span class="badge badge-info">{{ $log->action }}</span></td>
                            <td style="color: #64748b;">{{ $log->model_type ? class_basename($log->model_type) : '-' }}</td>
                            <td style="color: #64748b; font-size: 0.875rem;">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
    
    <div>
        <div class="card">
            <div class="card-header"><h3 style="font-weight: 600;">Login History</h3></div>
            <div class="card-body" style="padding: 0;">
                @forelse($user->loginHistory as $login)
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="badge {{ $login->status === 'success' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($login->status) }}</span>
                        <span style="font-size: 0.75rem; color: #64748b;">{{ $login->login_at->diffForHumans() }}</span>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">{{ $login->ip_address }}</div>
                </div>
                @empty
                <div style="padding: 2rem; text-align: center; color: #64748b;">No login history</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
