@extends('layouts.admin')

@section('title', 'Local Secretary Dashboard')

@section('content')
<div class="mb-6">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">Welcome, {{ auth()->user()->name }}</h1>
    <p style="color: #64748b; margin-top: 0.25rem;">Local Secretary Dashboard — {{ now()->format('l, F j, Y') }}</p>
</div>

{{-- Quick Stats Row --}}
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    {{-- Total Members --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #dbeafe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ number_format($memberStats['total']) }}</div>
        <div class="stat-card-label">Total Members</div>
        <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.5rem;">
            +{{ $memberStats['new_this_month'] }} this month
        </div>
    </div>

    {{-- Active Members --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #d1fae5;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #059669;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ number_format($memberStats['active']) }}</div>
        <div class="stat-card-label">Active Members</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">
            {{ $memberStats['inactive'] }} inactive
        </div>
    </div>

    {{-- Visitors Pending Follow-up --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #fef3c7;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $visitorStats['pending_followup'] }}</div>
        <div class="stat-card-label">Pending Follow-ups</div>
        <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.5rem;">
            {{ $visitorStats['converted'] }} converted
        </div>
    </div>

    {{-- Avg Attendance --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #ede9fe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #7c3aed;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ number_format($avgAttendance, 0) }}</div>
        <div class="stat-card-label">Avg. Attendance</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">Last 4 weeks</div>
    </div>
</div>

{{-- Data Quality Alerts --}}
@if($memberAlerts['missing_phone'] > 0 || $memberAlerts['missing_email'] > 0 || $memberAlerts['missing_dob'] > 0)
<div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.75rem;">
    <svg style="width: 1.25rem; height: 1.25rem; color: #d97706; flex-shrink: 0; margin-top: 0.125rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
    </svg>
    <div>
        <p style="font-weight: 600; color: #92400e; margin-bottom: 0.25rem;">Data Quality Alerts</p>
        <ul style="list-style: disc; padding-left: 1.25rem; color: #78350f; font-size: 0.875rem; margin: 0;">
            @if($memberAlerts['missing_phone'] > 0)
            <li>{{ $memberAlerts['missing_phone'] }} member(s) missing phone number
                <a href="{{ route('admin.members.index') }}?filter=missing_phone" style="color: #2563eb; margin-left: 0.25rem;">Fix →</a>
            </li>
            @endif
            @if($memberAlerts['missing_email'] > 0)
            <li>{{ $memberAlerts['missing_email'] }} member(s) missing email address
                <a href="{{ route('admin.members.index') }}?filter=missing_email" style="color: #2563eb; margin-left: 0.25rem;">Fix →</a>
            </li>
            @endif
            @if($memberAlerts['missing_dob'] > 0)
            <li>{{ $memberAlerts['missing_dob'] }} member(s) missing date of birth
                <a href="{{ route('admin.members.index') }}?filter=missing_dob" style="color: #2563eb; margin-left: 0.25rem;">Fix →</a>
            </li>
            @endif
        </ul>
    </div>
</div>
@endif

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    {{-- Main Column --}}
    <div>

        {{-- Recent Attendance Sessions --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Recent Attendance Sessions</h3>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service</th>
                            <th>Members</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $session)
                        <tr>
                            <td>{{ $session->service_date->format('M d, Y') }}</td>
                            <td>{{ $session->serviceType->name ?? 'N/A' }}</td>
                            <td>{{ $session->total_members ?? 0 }}</td>
                            <td>{{ $session->total_attendance ?? 0 }}</td>
                            <td>
                                @if($session->status === 'open')
                                <span class="badge badge-success">Open</span>
                                @else
                                <span class="badge" style="background: #f1f5f9; color: #64748b;">Closed</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.attendance.show', $session) }}" class="btn btn-secondary btn-sm">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b;">No recent attendance sessions</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Visitors --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Recent Visitors</h3>
                <a href="{{ route('admin.visitors.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>First Visit</th>
                            <th>Phone</th>
                            <th>Follow-up</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentVisitors as $visitor)
                        <tr>
                            <td style="font-weight: 500;">{{ $visitor->first_name }} {{ $visitor->last_name }}</td>
                            <td>{{ $visitor->first_visit_date ? \Carbon\Carbon::parse($visitor->first_visit_date)->format('M d, Y') : '—' }}</td>
                            <td>{{ $visitor->phone ?? '—' }}</td>
                            <td>
                                @if($visitor->follow_up_status === 'converted')
                                <span class="badge badge-success">Converted</span>
                                @elseif($visitor->follow_up_status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                                @else
                                <span class="badge" style="background: #f1f5f9; color: #64748b;">{{ ucfirst($visitor->follow_up_status ?? '—') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">No recent visitors</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Financial Summary (read-only) --}}
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Monthly Financial Summary</h3>
                <span style="color: #64748b; font-size: 0.875rem;">{{ now()->format('F Y') }}</span>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div style="text-align: center; padding: 1rem; background: #f0fdf4; border-radius: 0.5rem;">
                        <div style="font-size: 1.125rem; font-weight: 700; color: #166534;">GH₵{{ number_format($financeStats['tithes'], 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Tithes</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: #eff6ff; border-radius: 0.5rem;">
                        <div style="font-size: 1.125rem; font-weight: 700; color: #1e40af;">GH₵{{ number_format($financeStats['offerings'], 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Offerings</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: #fdf4ff; border-radius: 0.5rem;">
                        <div style="font-size: 1.125rem; font-weight: 700; color: #86198f;">GH₵{{ number_format($financeStats['donations'], 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Donations</div>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-size: 0.875rem;">Total Income This Month</span>
                    <span style="font-size: 1.125rem; font-weight: 700; color: #1e3a5f;">GH₵{{ number_format($financeStats['total_income'], 2) }}</span>
                </div>
                <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.75rem; text-align: center;">Read-only summary. Contact the Financial Secretary for details.</p>
            </div>
        </div>

    </div>

    {{-- Sidebar Column --}}
    <div>

        {{-- Quick Actions --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.members.create') }}" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                    <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Member
                </a>
                <a href="{{ route('admin.visitors.create') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                    <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                    Register Visitor
                </a>
                <a href="{{ route('admin.attendance.create') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                    <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    New Attendance
                </a>
                <a href="{{ route('admin.sms.compose') }}" class="btn btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                    <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    Send SMS
                </a>
            </div>
        </div>

        {{-- Upcoming Birthdays --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Upcoming Birthdays</h3>
                <span style="font-size: 0.75rem; color: #64748b;">Next 30 days</span>
            </div>
            <div class="card-body" style="padding: 0;">
                @forelse($upcomingBirthdays as $member)
                @php
                    $dob  = $member->date_of_birth;
                    $next = $dob->copy()->year(now()->year);
                    if ($next->lt(now()->startOfDay())) { $next->addYear(); }
                    $isToday = $next->isToday();
                @endphp
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 9999px; background: #ede9fe; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; color: #7c3aed; flex-shrink: 0;">
                        {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.875rem; font-weight: 500; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $member->full_name }}</p>
                        <p style="font-size: 0.75rem; color: #64748b;">{{ $next->format('M d') }}</p>
                    </div>
                    @if($isToday)
                    <span class="badge badge-success">Today!</span>
                    @else
                    <span style="font-size: 0.75rem; color: #64748b;">in {{ now()->diffInDays($next) }}d</span>
                    @endif
                </div>
                @empty
                <div style="padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.875rem;">
                    No upcoming birthdays in the next 30 days.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Visitor Summary --}}
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Visitor Overview</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">Total Visitors</span>
                    <span style="font-weight: 600;">{{ $visitorStats['total'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">This Month</span>
                    <span style="font-weight: 600;">{{ $visitorStats['this_month'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">Pending Follow-up</span>
                    <span class="badge badge-warning">{{ $visitorStats['pending_followup'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span style="color: #64748b;">Converted to Members</span>
                    <span class="badge badge-success">{{ $visitorStats['converted'] }}</span>
                </div>
                <a href="{{ route('admin.visitors.index') }}?follow_up_status=pending" class="btn btn-secondary btn-sm" style="width: 100%;">
                    Follow Up Visitors
                </a>
            </div>
        </div>

    </div>
</div>

<style>
@media (max-width: 1024px) {
    div[style*="grid-template-columns: repeat(4, 1fr)"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
@media (max-width: 640px) {
    div[style*="grid-template-columns: repeat(4, 1fr)"],
    div[style*="grid-template-columns: repeat(2, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
    div[style*="grid-template-columns: repeat(3, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
