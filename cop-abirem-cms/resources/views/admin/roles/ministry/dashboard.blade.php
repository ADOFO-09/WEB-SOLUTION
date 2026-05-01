@extends('layouts.admin')

@section('title', 'Ministry Leader Dashboard')

@section('content')
<div class="mb-6">
    <div style="display: flex; justify-content: space-between; align-items: start;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">{{ $ministry->name ?? 'Ministry' }} Dashboard</h1>
            <p style="color: #64748b; margin-top: 0.25rem;">Welcome, {{ auth()->user()->name }} - Ministry Leader</p>
        </div>
        <div style="background: #dbeafe; padding: 0.5rem 1rem; border-radius: 0.5rem;">
            <span style="font-size: 0.875rem; color: #1e40af; font-weight: 500;">{{ $ministry->name ?? 'Your Ministry' }}</span>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Members -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #dbeafe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $memberStats['total'] }}</div>
        <div class="stat-card-label">Ministry Members</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
            {{ $memberStats['active'] }} active
        </div>
    </div>

    <!-- Gender Distribution -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #fce7f3;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #db2777;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $memberStats['male'] }}/{{ $memberStats['female'] }}</div>
        <div class="stat-card-label">Male / Female</div>
    </div>

    <!-- Avg Attendance -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #d1fae5;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #059669;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $avgAttendanceRate }}%</div>
        <div class="stat-card-label">Avg Attendance</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
            Last 4 weeks
        </div>
    </div>

    <!-- Follow-ups Needed -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #fef3c7;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ count($absentMembers) }}</div>
        <div class="stat-card-label">Need Follow-up</div>
        <div style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.25rem;">
            Frequently absent
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Attendance Stats -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Recent Attendance</h3>
                <a href="{{ route('admin.ministry.attendance') }}" class="btn btn-secondary btn-sm">Mark Attendance</a>
            </div>
            <div class="card-body">
                @if(count($attendanceStats) > 0)
                <div style="display: flex; align-items: flex-end; height: 180px; gap: 0.75rem; padding-bottom: 2rem;">
                    @foreach($attendanceStats as $stat)
                    <div style="flex: 1; text-align: center;">
                        <div style="position: relative;">
                            <div style="height: {{ ($stat['rate'] / 100) * 120 }}px; background: linear-gradient(180deg, #10b981 0%, #059669 100%); border-radius: 0.25rem 0.25rem 0 0; min-height: 8px;"></div>
                            <div style="position: absolute; top: -20px; left: 0; right: 0; font-size: 0.75rem; font-weight: 600; color: #059669;">{{ $stat['rate'] }}%</div>
                        </div>
                        <div style="font-size: 0.75rem; font-weight: 500; color: #374151; margin-top: 0.5rem;">{{ $stat['date'] }}</div>
                        <div style="font-size: 0.625rem; color: #64748b;">{{ $stat['present'] }}/{{ $stat['total'] }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align: center; padding: 2rem; color: #64748b;">
                    <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; opacity: 0.5;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>No attendance data for this period</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Ministry Members -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Ministry Members</h3>
                <a href="{{ route('admin.ministry.members') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ministryMembers->take(8) as $member)
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
                            <td>{{ $member->phone_primary ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $member->membership_status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                    {{ ucfirst($member->membership_status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #64748b;">No members in this ministry</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Send SMS to Ministry -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Send SMS to Ministry Members</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ministry.send-sms') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Message ({{ $ministryMembers->filter(fn($m) => !empty($m->phone_primary))->count() }} members with phone numbers)</label>
                        <textarea name="message" class="form-input" rows="3" maxlength="160" required placeholder="Enter your message (max 160 characters)..."></textarea>
                        <div class="form-hint">Characters remaining: <span id="charCount">160</span></div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        Send to All Members
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Members Needing Follow-up -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Needs Follow-up</h3>
            </div>
            <div class="card-body">
                @forelse($absentMembers as $absent)
                <div style="display: flex; align-items: center; padding: 0.75rem 0; {{ !$loop->last ? 'border-bottom: 1px solid #e2e8f0;' : '' }}">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; color: #dc2626; margin-right: 0.75rem;">
                        {{ $absent['absences'] }}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; font-size: 0.875rem;">{{ $absent['member']->first_name }} {{ $absent['member']->last_name }}</div>
                        <div style="font-size: 0.75rem; color: #dc2626;">{{ $absent['absences'] }} absences this month</div>
                    </div>
                    @if($absent['member']->phone_primary)
                    <a href="tel:{{ $absent['member']->phone_primary }}" style="padding: 0.25rem; border-radius: 0.25rem; color: #3b82f6;">
                        <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                    </a>
                    @endif
                </div>
                @empty
                <div style="text-align: center; padding: 1rem; color: #64748b;">
                    <svg style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #10b981;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p style="font-size: 0.875rem;">All members attending regularly!</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Birthdays -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Upcoming Birthdays</h3>
            </div>
            <div class="card-body">
                @forelse($upcomingBirthdays as $member)
                @php
                    $birthday = \Carbon\Carbon::parse($member->date_of_birth)->setYear(now()->year);
                    if ($birthday->isPast() && !$birthday->isToday()) {
                        $birthday->addYear();
                    }
                    $daysUntil = now()->diffInDays($birthday, false);
                @endphp
                <div style="display: flex; align-items: center; padding: 0.75rem 0; {{ !$loop->last ? 'border-bottom: 1px solid #e2e8f0;' : '' }}">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); display: flex; align-items: center; justify-content: center; margin-right: 0.75rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.87c1.355 0 2.697.055 4.024.165C17.155 8.51 18 9.473 18 10.608v2.513m-3-4.87v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-3.38a48.474 48.474 0 00-6-.37c-2.032 0-4.034.125-6 .37m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.17c0 .62-.504 1.124-1.125 1.124H4.125A1.125 1.125 0 013 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 016 13.12M12.265 3.11a.375.375 0 11-.53 0L12 2.845l.265.265zm-3 0a.375.375 0 11-.53 0L9 2.845l.265.265zm6 0a.375.375 0 11-.53 0L15 2.845l.265.265z" />
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; font-size: 0.875rem;">{{ $member->first_name }} {{ $member->last_name }}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">{{ $birthday->format('M d') }}</div>
                    </div>
                    <span class="badge {{ $daysUntil === 0 ? 'badge-success' : 'badge-info' }}">
                        {{ $daysUntil === 0 ? 'Today!' : 'In ' . $daysUntil . ' days' }}
                    </span>
                </div>
                @empty
                <p style="text-align: center; color: #64748b; padding: 1rem;">No upcoming birthdays</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.ministry.attendance') }}" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Mark Attendance
                </a>
                <a href="{{ route('admin.ministry.members') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    View Members
                </a>
                <a href="{{ route('admin.ministry.compose-sms') }}" class="btn btn-secondary" style="width: 100%;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    Send SMS
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelector('textarea[name="message"]').addEventListener('input', function() {
    document.getElementById('charCount').textContent = 160 - this.value.length;
});
</script>
@endpush

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
}
</style>
@endsection
