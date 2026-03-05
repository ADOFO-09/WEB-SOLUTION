@extends('layouts.admin')

@section('title', 'Ministry Attendance')

@section('content')
<div class="mb-6">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">{{ $ministry->name }} - Mark Attendance</h1>
            <p style="color: #64748b; margin-top: 0.25rem;">Mark attendance for your ministry members</p>
        </div>
        <a href="{{ route('admin.ministry.dashboard') }}" class="btn btn-secondary">
            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 300px; gap: 1.5rem;">
    <!-- Main Content -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Mark Attendance</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ministry.save-attendance') }}" method="POST">
                    @csrf
                    
                    <!-- Session Selection -->
                    <div class="form-group">
                        <label class="form-label">Select Attendance Session</label>
                        <select name="session_id" class="form-select" required>
                            <option value="">-- Select a Session --</option>
                            @foreach($sessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->service_date ? $session->service_date->format('M d, Y') : $session->created_at->format('M d, Y') }} 
                                - {{ $session->serviceType->name ?? 'Service' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Members List -->
                    <div style="margin-top: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <label class="form-label" style="margin: 0;">Ministry Members ({{ $members->count() }})</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="markAll('present')">Mark All Present</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="markAll('absent')">Mark All Absent</button>
                            </div>
                        </div>

                        <div style="max-height: 500px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                            <table class="table" style="margin: 0;">
                                <thead style="position: sticky; top: 0; background: white;">
                                    <tr>
                                        <th>Member</th>
                                        <th style="text-align: center;">Present</th>
                                        <th style="text-align: center;">Absent</th>
                                        <th style="text-align: center;">Late</th>
                                        <th style="text-align: center;">Excused</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($members as $member)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center;">
                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; color: #64748b; margin-right: 0.75rem;">
                                                    {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight: 500;">{{ $member->first_name }} {{ $member->last_name }}</div>
                                                    <div style="font-size: 0.75rem; color: #64748b;">{{ $member->member_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="radio" name="attendance[{{ $member->id }}]" value="present" class="attendance-radio" checked>
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="radio" name="attendance[{{ $member->id }}]" value="absent" class="attendance-radio">
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="radio" name="attendance[{{ $member->id }}]" value="late" class="attendance-radio">
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="radio" name="attendance[{{ $member->id }}]" value="excused" class="attendance-radio">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">
                                            No members found in this ministry.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($members->count() > 0)
                    <div style="margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Save Attendance
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Quick Stats -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Quick Stats</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">Total Members</span>
                    <span style="font-weight: 600;">{{ $members->count() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #64748b;">Recent Sessions</span>
                    <span style="font-weight: 600;">{{ $sessions->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Sessions -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Recent Sessions</h3>
            </div>
            <div class="card-body">
                @forelse($sessions->take(5) as $session)
                <div style="padding: 0.75rem 0; {{ !$loop->last ? 'border-bottom: 1px solid #e2e8f0;' : '' }}">
                    <div style="font-weight: 500; font-size: 0.875rem;">{{ $session->serviceType->name ?? 'Service' }}</div>
                    <div style="font-size: 0.75rem; color: #64748b;">
                        {{ $session->service_date ? $session->service_date->format('M d, Y') : $session->created_at->format('M d, Y') }}
                    </div>
                    <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.25rem;">
                        {{ $session->total_attendance ?? 0 }} attended
                    </div>
                </div>
                @empty
                <p style="text-align: center; color: #64748b; padding: 1rem;">No recent sessions</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function markAll(status) {
    document.querySelectorAll('input[value="' + status + '"]').forEach(function(radio) {
        radio.checked = true;
    });
}
</script>

<style>
.attendance-radio {
    width: 1.25rem;
    height: 1.25rem;
    cursor: pointer;
}
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 1fr 300px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection