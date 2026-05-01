@extends('layouts.admin')

@section('title', 'Presiding Elder Dashboard')

@section('content')
<div class="mb-6">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">Welcome, Elder {{ auth()->user()->name }}</h1>
    <p style="color: #64748b; margin-top: 0.25rem;">Presiding Elder Dashboard - {{ now()->format('l, F j, Y') }}</p>
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
        <div class="stat-card-value">{{ number_format($memberStats['total']) }}</div>
        <div class="stat-card-label">Total Members</div>
        <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.5rem;">
            +{{ $memberStats['new_this_month'] }} this month
        </div>
    </div>

    <!-- Monthly Income -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #d1fae5;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #059669;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">GH₵{{ number_format($financeStats['total_income'], 2) }}</div>
        <div class="stat-card-label">Monthly Income</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">
            Net: GH₵{{ number_format($financeStats['net_income'], 2) }}
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #fef3c7;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $pendingExpenses->count() }}</div>
        <div class="stat-card-label">Pending Approvals</div>
        <div style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.5rem;">
            Requires your action
        </div>
    </div>

    <!-- Average Attendance -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #ede9fe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #7c3aed;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ number_format($avgAttendance, 0) }}</div>
        <div class="stat-card-label">Avg. Attendance</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">
            Last 4 weeks
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content Column -->
    <div class="lg:col-span-2">
        <!-- Pending Expense Approvals -->
        @if($pendingExpenses->count() > 0)
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Pending Expense Approvals</h3>
                <span class="badge badge-warning">{{ $pendingExpenses->count() }} pending</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Submitted By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingExpenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date ? $expense->expense_date->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ Str::limit($expense->description, 30) }}</td>
                            <td>{{ $expense->category->name ?? 'N/A' }}</td>
                            <td style="font-weight: 600;">GH₵{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->recordedBy->name ?? 'N/A' }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <form action="{{ route('admin.elder.expense.approve', $expense->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="showRejectModal({{ $expense->id }})">Reject</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Financial Overview -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Monthly Financial Overview</h3>
                <span style="color: #64748b; font-size: 0.875rem;">{{ now()->format('F Y') }}</span>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div style="text-align: center; padding: 1rem; background: #f0fdf4; border-radius: 0.5rem;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: #166534;">GH₵{{ number_format($financeStats['tithes'], 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Tithes</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: #eff6ff; border-radius: 0.5rem;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: #1e40af;">GH₵{{ number_format($financeStats['offerings'], 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Offerings</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: #fdf4ff; border-radius: 0.5rem;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: #86198f;">GH₵{{ number_format($financeStats['donations'], 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Donations</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: #fef2f2; border-radius: 0.5rem;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: #991b1b;">GH₵{{ number_format($financeStats['expenses'], 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Expenses</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Recent Attendance Sessions</h3>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service Type</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $session)
                        <tr>
                            <td>{{ $session->service_date->format('M d, Y') }}</td>
                            <td>{{ $session->serviceType->name ?? 'N/A' }}</td>
                            <td><span class="badge badge-success">{{ $session->total_present ?? 0 }}</span></td>
                            <td><span class="badge badge-danger">{{ $session->total_absent ?? 0 }}</span></td>
                            <td>{{ ($session->total_present ?? 0) + ($session->total_absent ?? 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b;">No recent attendance sessions</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Column -->
    <div>
        <!-- Pledge Overview -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Pledge Overview</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Total Pledged</span>
                        <span style="font-weight: 600;">GH₵{{ number_format($pledgeStats['total_pledged'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Total Paid</span>
                        <span style="font-weight: 600; color: #059669;">GH₵{{ number_format($pledgeStats['total_paid'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Balance</span>
                        <span style="font-weight: 600; color: #dc2626;">GH₵{{ number_format($pledgeStats['balance'], 2) }}</span>
                    </div>
                </div>
                <div style="background: #f1f5f9; border-radius: 9999px; height: 8px; overflow: hidden;">
                    @php
                        $percentage = $pledgeStats['total_pledged'] > 0 
                            ? ($pledgeStats['total_paid'] / $pledgeStats['total_pledged']) * 100 
                            : 0;
                    @endphp
                    <div style="background: #059669; height: 100%; width: {{ $percentage }}%;"></div>
                </div>
                <div style="text-align: center; margin-top: 0.5rem; font-size: 0.75rem; color: #64748b;">
                    {{ number_format($percentage, 1) }}% collected
                </div>
                @if($pledgeStats['overdue'] > 0)
                <div style="margin-top: 1rem; padding: 0.75rem; background: #fef2f2; border-radius: 0.5rem;">
                    <span style="color: #dc2626; font-weight: 600;">{{ $pledgeStats['overdue'] }} overdue pledges</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Visitor Stats -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Visitors</h3>
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
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Converted</span>
                    <span class="badge badge-success">{{ $visitorStats['converted'] }}</span>
                </div>
                <a href="{{ route('admin.visitors.index') }}" class="btn btn-secondary btn-sm" style="width: 100%; margin-top: 1rem;">Manage Visitors</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.members.create') }}" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Member
                </a>
                <a href="{{ route('admin.attendance.create') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    New Attendance
                </a>
                <a href="{{ route('admin.sms.compose') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    Send SMS
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary" style="width: 100%;">
                    <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5" />
                    </svg>
                    View Reports
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 0.75rem; width: 100%; max-width: 400px; padding: 1.5rem;">
        <h3 style="font-weight: 600; margin-bottom: 1rem;">Reject Expense</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Reason for Rejection</label>
                <textarea name="reason" class="form-input" rows="3" required placeholder="Enter reason for rejection..."></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="hideRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal(expenseId) {
    document.getElementById('rejectForm').action = '/admin/elder/expense/' + expenseId + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function hideRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
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
