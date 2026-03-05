@extends('layouts.admin')

@section('title', 'Ministry Members')

@section('content')
<div class="mb-6">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">{{ $ministry->name }} - Members</h1>
            <p style="color: #64748b; margin-top: 0.25rem;">Manage members in your ministry</p>
        </div>
        <a href="{{ route('admin.ministry.dashboard') }}" class="btn btn-secondary">
            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-card-value">{{ $members->total() }}</div>
        <div class="stat-card-label">Total Members</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">{{ $members->where('gender', 'male')->count() }}</div>
        <div class="stat-card-label">Male</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">{{ $members->where('gender', 'female')->count() }}</div>
        <div class="stat-card-label">Female</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">{{ $members->where('membership_status', 'active')->count() }}</div>
        <div class="stat-card-label">Active</div>
    </div>
</div>

<!-- Members Table -->
<div class="card">
    <div class="card-header">
        <h3 style="font-weight: 600; color: #1e3a5f;">Ministry Members</h3>
        <span class="badge badge-info">{{ $members->total() }} members</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Member ID</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; color: #64748b; margin-right: 0.75rem;">
                                {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 500;">{{ $member->first_name }} {{ $member->last_name }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">{{ $member->email ?? 'No email' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family: monospace;">{{ $member->member_id }}</td>
                    <td>{{ $member->phone_primary ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $member->gender === 'male' ? 'badge-info' : 'badge-secondary' }}">
                            {{ ucfirst($member->gender ?? 'N/A') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $member->membership_status === 'active' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($member->membership_status ?? 'Unknown') }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            @if($member->phone_primary)
                            <a href="tel:{{ $member->phone_primary }}" class="btn btn-secondary btn-sm" title="Call">
                                <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </a>
                            @endif
                            <a href="{{ route('admin.members.show', $member) }}" class="btn btn-secondary btn-sm" title="View Profile">
                                <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">
                        <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; opacity: 0.5;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <p>No members found in this ministry.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
    <div class="card-footer">
        {{ $members->links() }}
    </div>
    @endif
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: repeat(4, 1fr)"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
</style>
@endsection