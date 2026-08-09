@extends('layouts.admin')
@section('title', 'Ministry Finance')

@section('content')
<div class="mb-6" style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">{{ $ministry->name }} — Finance</h1>
        <p style="color:#64748b;margin-top:0.25rem;">Overview of income and expenses</p>
    </div>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        <a href="{{ route('admin.ministry.finance.offerings') }}" class="btn btn-primary">+ Record Offering</a>
        <a href="{{ route('admin.ministry.finance.expenses') }}" class="btn btn-secondary">+ Record Expense</a>
        <a href="{{ route('admin.ministry.finance.report') }}" class="btn btn-secondary">Reports</a>
    </div>
</div>

{{-- Year picker --}}
<form method="GET" style="margin-bottom:1.5rem;display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
    <select name="year" class="form-select" style="width:auto;" onchange="this.form.submit()">
        @for($y = now()->year; $y >= now()->year - 5; $y--)
            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
        @endfor
    </select>
    <select name="month" class="form-select" style="width:auto;" onchange="this.form.submit()">
        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $mn)
            <option value="{{ $i+1 }}" @selected($i+1 == $month)>{{ $mn }}</option>
        @endforeach
    </select>
</form>

{{-- Stats row --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#dcfce7;">
            <svg style="width:1.4rem;height:1.4rem;color:#16a34a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </div>
        <div class="stat-card-value">GH₵ {{ number_format($totalIncomeMonth, 2) }}</div>
        <div class="stat-card-label">Income This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#fee2e2;">
            <svg style="width:1.4rem;height:1.4rem;color:#dc2626;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z"/></svg>
        </div>
        <div class="stat-card-value">GH₵ {{ number_format($totalExpenseMonth, 2) }}</div>
        <div class="stat-card-label">Expenses This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#dbeafe;">
            <svg style="width:1.4rem;height:1.4rem;color:#2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
        </div>
        <div class="stat-card-value">GH₵ {{ number_format($totalIncomeYear, 2) }}</div>
        <div class="stat-card-label">Total Income {{ $year }}</div>
    </div>
    <div class="stat-card">
        @php $balanceYear = $totalIncomeYear - $totalExpenseYear; @endphp
        <div class="stat-card-icon" style="background:{{ $balanceYear >= 0 ? '#dcfce7' : '#fee2e2' }};">
            <svg style="width:1.4rem;height:1.4rem;color:{{ $balanceYear >= 0 ? '#16a34a' : '#dc2626' }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z"/></svg>
        </div>
        <div class="stat-card-value" style="color:{{ $balanceYear >= 0 ? '#16a34a' : '#dc2626' }};">GH₵ {{ number_format(abs($balanceYear), 2) }}</div>
        <div class="stat-card-label">{{ $balanceYear >= 0 ? 'Net Balance' : 'Net Deficit' }} {{ $year }}</div>
    </div>
</div>

{{-- Welfare summary --}}
<div class="card mb-6">
    <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Welfare Summary — {{ $year }}</h3></div>
    <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
        <div style="text-align:center;padding:1rem;background:#f0fdf4;border-radius:0.75rem;">
            <div style="font-size:0.8rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">Welfare Contributions Received</div>
            <div style="font-size:1.5rem;font-weight:700;color:#16a34a;">GH₵ {{ number_format($welfareIncome, 2) }}</div>
        </div>
        <div style="text-align:center;padding:1rem;background:#fef2f2;border-radius:0.75rem;">
            <div style="font-size:0.8rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">Welfare Paid Out</div>
            <div style="font-size:1.5rem;font-weight:700;color:#dc2626;">GH₵ {{ number_format($welfareExpenses, 2) }}</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    {{-- Recent Offerings --}}
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-weight:600;color:#1e3a5f;">Recent Offerings</h3>
            <a href="{{ route('admin.ministry.finance.offerings') }}" style="font-size:0.8rem;color:#2563eb;">View all →</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($recentOfferings as $o)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 1.25rem;border-bottom:1px solid #f1f5f9;">
                <div>
                    <div style="font-size:0.875rem;font-weight:500;color:#1e293b;">{{ \App\Models\MinistryOffering::TYPES[$o->offering_type] ?? $o->offering_type }}</div>
                    <div style="font-size:0.75rem;color:#64748b;">{{ $o->offering_date->format('M d, Y') }} · {{ $o->reference_number }}</div>
                </div>
                <span style="font-weight:600;color:#16a34a;">GH₵ {{ number_format($o->amount, 2) }}</span>
            </div>
            @empty
            <div style="padding:1.5rem;text-align:center;color:#94a3b8;font-size:0.875rem;">No offerings recorded yet</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Expenses --}}
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-weight:600;color:#1e3a5f;">Recent Expenses</h3>
            <a href="{{ route('admin.ministry.finance.expenses') }}" style="font-size:0.8rem;color:#2563eb;">View all →</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($recentExpenses as $e)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 1.25rem;border-bottom:1px solid #f1f5f9;">
                <div>
                    <div style="font-size:0.875rem;font-weight:500;color:#1e293b;">{{ $e->description }}</div>
                    <div style="font-size:0.75rem;color:#64748b;">{{ $e->expense_date->format('M d, Y') }} · {{ \App\Models\MinistryExpense::CATEGORIES[$e->category] ?? $e->category }}</div>
                </div>
                <span style="font-weight:600;color:#dc2626;">GH₵ {{ number_format($e->amount, 2) }}</span>
            </div>
            @empty
            <div style="padding:1.5rem;text-align:center;color:#94a3b8;font-size:0.875rem;">No expenses recorded yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
