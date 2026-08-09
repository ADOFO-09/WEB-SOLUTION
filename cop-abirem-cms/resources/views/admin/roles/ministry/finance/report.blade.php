@extends('layouts.admin')
@section('title', 'Ministry Finance Report')

@section('content')
<div class="mb-6" style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">{{ $ministry->name }} — Finance Report</h1>
        <p style="color:#64748b;margin-top:0.25rem;">Annual income & expense summary</p>
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;">
            <select name="year" class="form-select" style="width:auto;" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.ministry.finance.index') }}" class="btn btn-secondary">← Finance Home</a>
    </div>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#dcfce7;">
            <svg style="width:1.4rem;height:1.4rem;color:#16a34a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </div>
        <div class="stat-card-value" style="color:#16a34a;">GH₵ {{ number_format($totalIncome, 2) }}</div>
        <div class="stat-card-label">Total Income {{ $year }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#fee2e2;">
            <svg style="width:1.4rem;height:1.4rem;color:#dc2626;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z"/></svg>
        </div>
        <div class="stat-card-value" style="color:#dc2626;">GH₵ {{ number_format($totalExpense, 2) }}</div>
        <div class="stat-card-label">Total Expenses {{ $year }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:{{ $balance >= 0 ? '#dcfce7' : '#fee2e2' }};">
            <svg style="width:1.4rem;height:1.4rem;color:{{ $balance >= 0 ? '#16a34a' : '#dc2626' }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
        </div>
        <div class="stat-card-value" style="color:{{ $balance >= 0 ? '#16a34a' : '#dc2626' }};">
            {{ $balance < 0 ? '-' : '' }}GH₵ {{ number_format(abs($balance), 2) }}
        </div>
        <div class="stat-card-label">{{ $balance >= 0 ? 'Net Balance' : 'Net Deficit' }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">

    {{-- Income breakdown --}}
    <div class="card">
        <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Income Breakdown</h3></div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Transactions</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\MinistryOffering::TYPES as $key => $label)
                    @php $row = $incomeByType[$key] ?? null; @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="color:#64748b;">{{ $row ? $row->count : 0 }}</td>
                        <td style="text-align:right;font-weight:{{ $row ? '600' : '400' }};color:{{ $row ? '#16a34a' : '#94a3b8' }};">
                            GH₵ {{ number_format($row ? $row->total : 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                    <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                        <td style="font-weight:700;">Total</td>
                        <td></td>
                        <td style="text-align:right;font-weight:700;color:#16a34a;">GH₵ {{ number_format($totalIncome, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Expense breakdown --}}
    <div class="card">
        <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Expense Breakdown</h3></div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Transactions</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\MinistryExpense::CATEGORIES as $key => $label)
                    @php $row = $expenseByCategory[$key] ?? null; @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="color:#64748b;">{{ $row ? $row->count : 0 }}</td>
                        <td style="text-align:right;font-weight:{{ $row ? '600' : '400' }};color:{{ $row ? '#dc2626' : '#94a3b8' }};">
                            GH₵ {{ number_format($row ? $row->total : 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                    <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                        <td style="font-weight:700;">Total</td>
                        <td></td>
                        <td style="text-align:right;font-weight:700;color:#dc2626;">GH₵ {{ number_format($totalExpense, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Monthly table --}}
<div class="card">
    <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Monthly Summary — {{ $year }}</h3></div>
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th style="text-align:right;">Income</th>
                    <th style="text-align:right;">Expenses</th>
                    <th style="text-align:right;">Balance</th>
                    <th>Visual</th>
                </tr>
            </thead>
            <tbody>
                @php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                @foreach($months as $i => $mn)
                @php
                    $inc  = $monthlyIncome[$i+1]   ?? 0;
                    $exp  = $monthlyExpenses[$i+1]  ?? 0;
                    $bal  = $inc - $exp;
                    $max  = max(array_merge($monthlyIncome, $monthlyExpenses, [1]));
                    $incW = $max > 0 ? round(($inc / $max) * 100) : 0;
                    $expW = $max > 0 ? round(($exp / $max) * 100) : 0;
                @endphp
                <tr>
                    <td style="font-weight:500;">{{ $mn }}</td>
                    <td style="text-align:right;color:#16a34a;font-weight:{{ $inc > 0 ? '600' : '400' }};">
                        {{ $inc > 0 ? 'GH₵ ' . number_format($inc, 2) : '—' }}
                    </td>
                    <td style="text-align:right;color:#dc2626;font-weight:{{ $exp > 0 ? '600' : '400' }};">
                        {{ $exp > 0 ? 'GH₵ ' . number_format($exp, 2) : '—' }}
                    </td>
                    <td style="text-align:right;font-weight:600;color:{{ $bal >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ ($inc > 0 || $exp > 0) ? (($bal < 0 ? '-' : '') . 'GH₵ ' . number_format(abs($bal), 2)) : '—' }}
                    </td>
                    <td style="min-width:140px;">
                        @if($inc > 0 || $exp > 0)
                        <div style="display:flex;flex-direction:column;gap:3px;">
                            <div style="display:flex;align-items:center;gap:4px;">
                                <div style="width:{{ $incW }}%;min-width:4px;height:6px;background:#16a34a;border-radius:3px;"></div>
                            </div>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <div style="width:{{ $expW }}%;min-width:4px;height:6px;background:#dc2626;border-radius:3px;"></div>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                    <td style="font-weight:700;">Total</td>
                    <td style="text-align:right;font-weight:700;color:#16a34a;">GH₵ {{ number_format($totalIncome, 2) }}</td>
                    <td style="text-align:right;font-weight:700;color:#dc2626;">GH₵ {{ number_format($totalExpense, 2) }}</td>
                    <td style="text-align:right;font-weight:700;color:{{ $balance >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ $balance < 0 ? '-' : '' }}GH₵ {{ number_format(abs($balance), 2) }}
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
