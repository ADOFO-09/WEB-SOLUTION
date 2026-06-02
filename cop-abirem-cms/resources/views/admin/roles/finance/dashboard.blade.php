@extends('layouts.admin')

@section('title', 'Financial Secretary Dashboard')

@section('content')
<div class="mb-6">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">Financial Secretary Dashboard</h1>
    <p style="color: #64748b; margin-top: 0.25rem;">Welcome, {{ auth()->user()->name }} - {{ now()->format('l, F j, Y') }}</p>
</div>

<!-- Today's Summary -->
<div style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem; color: white;">
    <h2 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; opacity: 0.9;">Today's Collections</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <div style="font-size: 1.75rem; font-weight: 700;">{{ $currencySymbol }}{{ number_format($todayStats['tithes'], 2) }}</div>
            <div style="font-size: 0.75rem; opacity: 0.7;">Tithes</div>
        </div>
        <div>
            <div style="font-size: 1.75rem; font-weight: 700;">{{ $currencySymbol }}{{ number_format($todayStats['offerings'], 2) }}</div>
            <div style="font-size: 0.75rem; opacity: 0.7;">Offerings</div>
        </div>
        <div>
            <div style="font-size: 1.75rem; font-weight: 700;">{{ $currencySymbol }}{{ number_format($todayStats['donations'], 2) }}</div>
            <div style="font-size: 0.75rem; opacity: 0.7;">Donations</div>
        </div>
        <div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #fbbf24;">{{ $currencySymbol }}{{ number_format($todayStats['total'], 2) }}</div>
            <div style="font-size: 0.75rem; opacity: 0.7;">Total Today</div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <!-- This Week -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #dbeafe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $currencySymbol }}{{ number_format($weekStats['total'], 2) }}</div>
        <div class="stat-card-label">This Week</div>
    </div>

    <!-- This Month -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #d1fae5;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #059669;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $currencySymbol }}{{ number_format($monthStats['total_income'], 2) }}</div>
        <div class="stat-card-label">This Month</div>
        <div style="font-size: 0.75rem; color: {{ $monthStats['net'] >= 0 ? '#10b981' : '#ef4444' }}; margin-top: 0.25rem;">
            Net: {{ $currencySymbol }}{{ number_format($monthStats['net'], 2) }}
        </div>
    </div>

    <!-- Year to Date -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #ede9fe;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #7c3aed;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $currencySymbol }}{{ number_format($ytdStats['total_income'], 2) }}</div>
        <div class="stat-card-label">Year to Date</div>
    </div>

    <!-- Pledge Collection Rate -->
    <div class="stat-card">
        <div class="stat-card-icon" style="background: #fef3c7;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-value">{{ $pledgeStats['collection_rate'] }}%</div>
        <div class="stat-card-label">Pledge Collection</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
            {{ $pledgeStats['active_pledges'] }} active pledges
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Quick Entry: Session Tithe -->
    <div class="card">
        <div class="card-header">
            <h3 style="font-weight: 600; color: #1e3a5f;">Record Session Tithe</h3>
            <span style="font-size: 0.75rem; color: #64748b;">Total tithe for a service</span>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:.5rem;padding:.75rem;margin-bottom:1rem;font-size:.875rem;color:#991b1b;">
                {{ session('error') }}
            </div>
            @endif
            <form action="{{ route('admin.tithes.session.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Service / Session</label>
                    <select name="attendance_session_id" class="form-select" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ old('attendance_session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->serviceType->name ?? 'Service' }} — {{ $session->service_date->format('D, M d, Y') }}
                            @if($session->status === 'open') (Open) @endif
                        </option>
                        @endforeach
                    </select>
                    @if($sessions->isEmpty())
                    <p style="font-size: 0.75rem; color: #d97706; margin-top: 0.25rem;">
                        No sessions found. <a href="{{ route('admin.attendance.create') }}" style="text-decoration: underline;">Create one</a>.
                    </p>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Tithe Particular</label>
                    <select name="income_category_id" class="form-select">
                        <option value="">— Select Particular (optional) —</option>
                        @foreach($titheCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('income_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">
                        Determines the label shown in the Income Ledger (e.g. "1st Sunday Tithe")
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Total Amount ({{ $currencySymbol }})</label>
                        <input type="number" name="amount" class="form-input" step="0.01" min="0.01" required
                               placeholder="0.00" value="{{ old('amount') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            @include('admin.partials.payment-method-options')
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <input type="text" name="notes" class="form-input" placeholder="Any notes about this collection..."
                           value="{{ old('notes') }}">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Record Session Tithe</button>
            </form>
        </div>
    </div>

    <!-- Quick Entry: Offering -->
    <div class="card">
        <div class="card-header">
            <h3 style="font-weight: 600; color: #1e3a5f;">Quick Record Offering</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finance.quick-offering') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Offering Particular</label>
                    <select name="income_category_id" class="form-select" required>
                        <option value="">— Select particular —</option>
                        @php
                        $typeLabels = ['offering' => 'Regular Offerings', 'special' => 'Special Offerings', 'donation' => 'Donations'];
                        @endphp
                        @foreach($typeLabels as $type => $label)
                        @if(isset($offeringCategories[$type]) && $offeringCategories[$type]->count())
                        <optgroup label="{{ $label }}">
                            @foreach($offeringCategories[$type] as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Amount ({{ $currencySymbol }})</label>
                        <input type="number" name="amount" class="form-input" step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" name="payment_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                            @include('admin.partials.payment-method-options')
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Record Offering</button>
            </form>
        </div>
    </div>
</div>

<!-- Quick Entry: Expense -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 style="font-weight: 600; color: #1e3a5f;">Record Expense</h3>
        <span style="font-size: 0.75rem; color: #64748b;">Submitted for approval — reflects on Expense Ledger once approved</span>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.finance.quick-expense') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label">Expense Category</label>
                    <select name="expense_category_id" class="form-select" required>
                        <option value="">— Select category —</option>
                        @foreach($expenseCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">
                        Determines the column in the Expense Ledger
                    </p>
                </div>
                <div class="form-group">
                    <label class="form-label">Payee Name</label>
                    <input type="text" name="payee_name" class="form-input" required
                           placeholder="Who is being paid?" value="{{ old('payee_name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-input" required
                           placeholder="Brief description of expense" value="{{ old('description') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label">Amount ({{ $currencySymbol }})</label>
                    <input type="number" name="amount" class="form-input" step="0.01" min="0.01" required
                           placeholder="0.00" value="{{ old('amount') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="expense_date" class="form-input"
                           value="{{ old('expense_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                            @include('admin.partials.payment-method-options')
                    </select>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <p style="font-size: 0.75rem; color: #d97706;">
                    ⚠ Expense will be submitted as <strong>pending</strong> — it appears on the Expense Ledger only after approval.
                </p>
                <button type="submit" class="btn btn-primary">Submit Expense for Approval</button>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Recent Transactions -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Recent Transactions</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Receipt</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTithes as $tithe)
                        <tr>
                            <td><span class="badge badge-success">Tithe</span></td>
                            <td>{{ $tithe->tithe_date ? $tithe->tithe_date->format('M d') : 'N/A' }}</td>
                            <td>{{ $tithe->member->first_name ?? '' }} {{ $tithe->member->last_name ?? 'Anonymous' }}</td>
                            <td style="font-family: monospace; font-size: 0.75rem;">{{ $tithe->receipt_number }}</td>
                            <td style="font-weight: 600;">{{ $currencySymbol }}{{ number_format($tithe->amount, 2) }}</td>
                        </tr>
                        @endforeach
                        @foreach($recentOfferings as $offering)
                        <tr>
                            <td><span class="badge badge-info">Offering</span></td>
                            <td>{{ $offering->offering_date ? $offering->offering_date->format('M d') : 'N/A' }}</td>
                            <td>{{ $offering->serviceType->name ?? $offering->offering_type ?? 'General' }}</td>
                            <td style="font-family: monospace; font-size: 0.75rem;">{{ $offering->receipt_number }}</td>
                            <td style="font-weight: 600;">{{ $currencySymbol }}{{ number_format($offering->amount, 2) }}</td>
                        </tr>
                        @endforeach
                        @foreach($recentDonations as $donation)
                        <tr>
                            <td><span class="badge badge-secondary">Donation</span></td>
                            <td>{{ $donation->donation_date ? $donation->donation_date->format('M d') : 'N/A' }}</td>
                            <td>{{ $donation->member->first_name ?? '' }} {{ $donation->member->last_name ?? 'Anonymous' }}</td>
                            <td style="font-family: monospace; font-size: 0.75rem;">{{ $donation->receipt_number }}</td>
                            <td style="font-weight: 600;">{{ $currencySymbol }}{{ number_format($donation->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">6-Month Income Trend</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: flex-end; height: 200px; gap: 1rem;">
                    @foreach($monthlyTrend as $month)
                    @php
                        $total = $month['tithes'] + $month['offerings'] + $month['donations'];
                        $maxValue = max(array_map(fn($m) => $m['tithes'] + $m['offerings'] + $m['donations'], $monthlyTrend));
                        $height = $maxValue > 0 ? ($total / $maxValue) * 150 : 0;
                    @endphp
                    <div style="flex: 1; text-align: center;">
                        <div style="height: {{ $height }}px; background: linear-gradient(180deg, #3b82f6 0%, #1e40af 100%); border-radius: 0.25rem 0.25rem 0 0; margin-bottom: 0.5rem; min-height: 4px;"></div>
                        <div style="font-size: 0.75rem; font-weight: 600; color: #374151;">{{ $month['month'] }}</div>
                        <div style="font-size: 0.625rem; color: #64748b;">{{ $currencySymbol }}{{ number_format($total/1000, 1) }}k</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Pending Expenses -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Pending Expenses</h3>
                @if($pendingExpenses->count() > 0)
                <span class="badge badge-warning">{{ $pendingExpenses->count() }}</span>
                @endif
            </div>
            <div class="card-body">
                @forelse($pendingExpenses as $expense)
                <div style="padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div style="font-weight: 500; font-size: 0.875rem;">{{ Str::limit($expense->description, 25) }}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">{{ $expense->expenseCategory->name ?? 'Uncategorized' }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: #1e3a5f;">{{ $currencySymbol }}{{ number_format($expense->amount, 2) }}</div>
                            <span class="badge badge-warning" style="font-size: 0.625rem;">Pending</span>
                        </div>
                    </div>
                </div>
                @empty
                <p style="text-align: center; color: #64748b; padding: 1rem;">No pending expenses</p>
                @endforelse
            </div>
        </div>

        <!-- Top Contributors -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Top Tithers</h3>
                <span style="font-size: 0.75rem; color: #64748b;">This Month</span>
            </div>
            <div class="card-body">
                @forelse($topTithers as $index => $tither)
                <div style="display: flex; align-items: center; padding: 0.5rem 0; {{ !$loop->last ? 'border-bottom: 1px solid #e2e8f0;' : '' }}">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: {{ $index < 3 ? '#fbbf24' : '#e2e8f0' }}; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; color: {{ $index < 3 ? '#78350f' : '#64748b' }}; margin-right: 0.75rem;">
                        {{ $index + 1 }}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 0.875rem; font-weight: 500;">{{ $tither->member->first_name ?? 'N/A' }} {{ $tither->member->last_name ?? '' }}</div>
                    </div>
                    <div style="font-weight: 600; color: #059669;">{{ $currencySymbol }}{{ number_format($tither->total, 2) }}</div>
                </div>
                @empty
                <p style="text-align: center; color: #64748b;">No tithes this month</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-weight: 600; color: #1e3a5f;">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.tithes.session.create') }}" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem;">Record Session Tithe (Total)</a>
                {{-- Individual tithes per session: pick from recent sessions --}}
                @if($sessions->isNotEmpty())
                <div style="margin-bottom: 0.5rem;">
                    <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">Individual Tithes by Session</div>
                    <select id="individual-session-select" class="form-select" style="font-size: 0.8rem;"
                            onchange="if(this.value) window.location.href='{{ url('admin/tithes/session') }}/' + this.value + '/individual'">
                        <option value="">— Pick a session —</option>
                        @foreach($sessions as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->serviceType->name ?? 'Service' }} — {{ $s->service_date->format('D, M d') }}
                            @if($s->status === 'open') ●@endif
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <a href="{{ route('admin.tithes.index') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">View All Tithes</a>
                <a href="{{ route('admin.pledges.index') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">Manage Pledges</a>
                <a href="{{ route('admin.expenses.create') }}" class="btn btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">New Expense</a>
                <a href="{{ route('admin.reports.income-statement') }}" class="btn btn-primary" style="width: 100%;">Financial Report</a>
            </div>
        </div>
    </div>
</div>

@endsection
