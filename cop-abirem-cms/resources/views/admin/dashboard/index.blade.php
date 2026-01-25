@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Dashboard</h1>
        <p style="color: #64748b; margin-top: 0.25rem;">Welcome back, {{ auth()->user()->name }}!</p>
    </div>
    <div style="font-size: 0.875rem; color: #64748b;">
        {{ now()->format('l, F j, Y') }}
    </div>
</div>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-4" style="gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Total Users -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #dbeafe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $quickStats['total_users'] }}</div>
        <div class="stat-card-label">Total Users</div>
    </div>

    <!-- Active Users -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #d1fae5;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #059669;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $quickStats['active_users'] }}</div>
        <div class="stat-card-label">Active Users</div>
    </div>

    <!-- Today's Logins -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #fef3c7;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $quickStats['today_logins'] }}</div>
        <div class="stat-card-label">Today's Logins</div>
    </div>

    <!-- Members (if available) -->
    @if(isset($stats['members']))
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #ede9fe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #7c3aed;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $stats['members']['total'] }}</div>
        <div class="stat-card-label">Church Members</div>
    </div>
    @else
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #f1f5f9;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #64748b;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">--</div>
        <div class="stat-card-label">Coming Soon</div>
    </div>
    @endif
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header">
        <h3 style="font-size: 1rem; font-weight: 600; color: #1e293b;">Recent Activity</h3>
        @if(auth()->user()->hasPermission('settings.logs'))
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary btn-sm">View All</a>
        @endif
    </div>
    <div class="card-body" style="padding: 0;">
        @if($recentActivity->isEmpty())
        <div style="padding: 3rem; text-align: center; color: #64748b;">
            <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #cbd5e1;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <p>No recent activity</p>
        </div>
        @else
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivity as $log)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 2rem; height: 2rem; border-radius: 9999px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; color: #64748b;">
                                    {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                                </div>
                                <span>{{ $log->user?->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $log->action }}</span>
                        </td>
                        <td style="color: #64748b;">
                            {{ $log->model_type ? class_basename($log->model_type) : '-' }}
                            @if($log->model_id)
                            #{{ $log->model_id }}
                            @endif
                        </td>
                        <td style="color: #64748b; font-size: 0.75rem;">
                            {{ $log->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Quick Links -->
<div style="margin-top: 1.5rem;">
    <h3 style="font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem;">Quick Actions</h3>
    <div class="grid grid-cols-4" style="gap: 1rem;">
        @if(auth()->user()->hasPermission('users.create'))
        <a href="{{ route('admin.users.create') }}" class="card" style="padding: 1.5rem; text-decoration: none; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background: #dbeafe; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 600; color: #1e293b;">Add User</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Create new user account</div>
                </div>
            </div>
        </a>
        @endif

        @if(auth()->user()->hasPermission('roles.view'))
        <a href="{{ route('admin.roles.index') }}" class="card" style="padding: 1.5rem; text-decoration: none; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #059669;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 600; color: #1e293b;">Manage Roles</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Configure permissions</div>
                </div>
            </div>
        </a>
        @endif

        <a href="{{ route('admin.profile.show') }}" class="card" style="padding: 1.5rem; text-decoration: none; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background: #fef3c7; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #d97706;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 600; color: #1e293b;">My Profile</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Update your info</div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.profile.password') }}" class="card" style="padding: 1.5rem; text-decoration: none; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background: #fee2e2; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #dc2626;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 600; color: #1e293b;">Security</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Change password</div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
