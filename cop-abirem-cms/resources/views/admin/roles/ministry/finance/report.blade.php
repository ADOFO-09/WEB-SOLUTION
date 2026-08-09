@extends('layouts.admin')
@section('title', $ministry->name.' — Activity Report '.$year)

@section('content')

{{-- Header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.4rem;font-weight:700;color:#1e3a5f;margin:0;">{{ $ministry->name }} — Activity Report</h1>
        <p style="color:#64748b;margin-top:0.25rem;font-size:0.875rem;">
            Annual summary of financial and welfare activities &bull; {{ $memberCount }} active member{{ $memberCount === 1 ? '' : 's' }}
        </p>
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;">
            <label style="font-size:0.82rem;color:#64748b;">Year:</label>
            <select name="year" class="form-select" style="width:auto;" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.ministry.finance.index') }}" class="btn btn-secondary">← Finance Home</a>
    </div>
</div>

{{-- ====== SECTION 1: FINANCIAL SUMMARY ====== --}}
<p style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin:0 0 0.6rem;">General Finance — {{ $year }}</p>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#dcfce7;">
            <svg style="width:1.4rem;height:1.4rem;color:#16a34a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </div>
        <div class="stat-card-value" style="color:#16a34a;">{{ $currencySymbol }} {{ number_format($totalIncome,2) }}</div>
        <div class="stat-card-label">Total Offerings</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#fee2e2;">
            <svg style="width:1.4rem;height:1.4rem;color:#dc2626;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5z"/></svg>
        </div>
        <div class="stat-card-value" style="color:#dc2626;">{{ $currencySymbol }} {{ number_format($totalExpense,2) }}</div>
        <div class="stat-card-label">Total Expenses</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:{{ $financialBalance >= 0 ? '#dcfce7' : '#fee2e2' }};">
            <svg style="width:1.4rem;height:1.4rem;color:{{ $financialBalance >= 0 ? '#16a34a' : '#dc2626' }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
        </div>
        <div class="stat-card-value" style="color:{{ $financialBalance >= 0 ? '#16a34a' : '#dc2626' }};">
            {{ $financialBalance < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($financialBalance),2) }}
        </div>
        <div class="stat-card-label">{{ $financialBalance >= 0 ? 'Financial Balance' : 'Financial Deficit' }}</div>
    </div>
</div>

{{-- ====== SECTION 2: WELFARE FUND SUMMARY ====== --}}
<p style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin:0 0 0.6rem;">Welfare Fund — {{ $year }}</p>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#eff6ff;">
            <svg style="width:1.4rem;height:1.4rem;color:#2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        </div>
        <div class="stat-card-value" style="color:#2563eb;">{{ $currencySymbol }} {{ number_format($totalWelfareContribs,2) }}</div>
        <div class="stat-card-label">Contributions Collected</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:#f5f3ff;">
            <svg style="width:1.4rem;height:1.4rem;color:#7c3aed;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
        </div>
        <div class="stat-card-value" style="color:#7c3aed;">{{ $currencySymbol }} {{ number_format($totalWelfareBenefits,2) }}</div>
        <div class="stat-card-label">Benefits Disbursed</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:{{ $welfareFundBalance >= 0 ? '#dcfce7' : '#fef3c7' }};">
            <svg style="width:1.4rem;height:1.4rem;color:{{ $welfareFundBalance >= 0 ? '#16a34a' : '#d97706' }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
        </div>
        <div class="stat-card-value" style="color:{{ $welfareFundBalance >= 0 ? '#16a34a' : '#d97706' }};">
            {{ $welfareFundBalance < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($welfareFundBalance),2) }}
        </div>
        <div class="stat-card-label">{{ $welfareFundBalance >= 0 ? 'Welfare Fund Balance' : 'Welfare Fund Deficit' }}</div>
    </div>
</div>

{{-- ====== SECTION 3: BREAKDOWNS (side by side) ====== --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">

    {{-- Offerings by type --}}
    <div class="card">
        <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Offerings by Type</h3></div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th style="text-align:center;">Count</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\MinistryOffering::TYPES as $key => $label)
                    @php $row = $incomeByType[$key] ?? null; @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="text-align:center;color:#64748b;">{{ $row ? $row->count : '—' }}</td>
                        <td style="text-align:right;font-weight:{{ $row ? '600' : '400' }};color:{{ $row ? '#16a34a' : '#94a3b8' }};">
                            {{ $row ? $currencySymbol.' '.number_format($row->total,2) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                        <td style="font-weight:700;">Total</td>
                        <td></td>
                        <td style="text-align:right;font-weight:700;color:#16a34a;">{{ $currencySymbol }} {{ number_format($totalIncome,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Expenses by category --}}
    <div class="card">
        <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Expenses by Category</h3></div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th style="text-align:center;">Count</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\MinistryExpense::CATEGORIES as $key => $label)
                    @php $row = $expenseByCategory[$key] ?? null; @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="text-align:center;color:#64748b;">{{ $row ? $row->count : '—' }}</td>
                        <td style="text-align:right;font-weight:{{ $row ? '600' : '400' }};color:{{ $row ? '#dc2626' : '#94a3b8' }};">
                            {{ $row ? $currencySymbol.' '.number_format($row->total,2) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                        <td style="font-weight:700;">Total</td>
                        <td></td>
                        <td style="text-align:right;font-weight:700;color:#dc2626;">{{ $currencySymbol }} {{ number_format($totalExpense,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Welfare benefits by purpose --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Welfare Benefits by Purpose</h3></div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead>
                <tr>
                    <th>Purpose</th>
                    <th style="text-align:center;">Cases</th>
                    <th style="text-align:right;">Amount Disbursed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purposes as $key => $label)
                @php $row = $welfareBenefitsByPurpose[$key] ?? null; @endphp
                <tr>
                    <td>
                        <span style="background:#f5f3ff;color:#7c3aed;font-size:0.72rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:4px;">
                            {{ $label }}
                        </span>
                    </td>
                    <td style="text-align:center;color:#64748b;">{{ $row ? $row->count : '—' }}</td>
                    <td style="text-align:right;font-weight:{{ $row ? '600' : '400' }};color:{{ $row ? '#7c3aed' : '#94a3b8' }};">
                        {{ $row ? $currencySymbol.' '.number_format($row->total,2) : '—' }}
                    </td>
                </tr>
                @endforeach
                <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                    <td style="font-weight:700;">Total</td>
                    <td></td>
                    <td style="text-align:right;font-weight:700;color:#7c3aed;">{{ $currencySymbol }} {{ number_format($totalWelfareBenefits,2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ====== SECTION 4: MONTHLY OVERVIEW ====== --}}
<div class="card">
    <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Monthly Overview — {{ $year }}</h3></div>
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table class="table" style="min-width:700px;">
            <thead>
                <tr>
                    <th>Month</th>
                    <th style="text-align:right;">Offerings</th>
                    <th style="text-align:right;">Expenses</th>
                    <th style="text-align:right;">Fin. Balance</th>
                    <th style="text-align:right;">Welfare Contribs</th>
                    <th style="text-align:right;">Benefits Paid</th>
                    <th style="text-align:right;">Welfare Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    $fmt = fn($v) => $v > 0 ? $currencySymbol.' '.number_format($v,2) : '—';
                @endphp
                @foreach($months as $i => $mn)
                @php
                    $m    = $i + 1;
                    $inc  = $monthlyIncome[$m];
                    $exp  = $monthlyExpenses[$m];
                    $wc   = $monthlyWelfareContribs[$m];
                    $wb   = $monthlyWelfareBenefits[$m];
                    $fbal = $inc - $exp;
                    $wbal = $wc - $wb;
                    $hasActivity = $inc + $exp + $wc + $wb > 0;
                @endphp
                <tr style="{{ !$hasActivity ? 'opacity:0.45;' : '' }}">
                    <td style="font-weight:500;">{{ $mn }}</td>
                    <td style="text-align:right;color:{{ $inc > 0 ? '#16a34a' : '#94a3b8' }};font-weight:{{ $inc > 0 ? '600' : '400' }};">{{ $fmt($inc) }}</td>
                    <td style="text-align:right;color:{{ $exp > 0 ? '#dc2626' : '#94a3b8' }};font-weight:{{ $exp > 0 ? '600' : '400' }};">{{ $fmt($exp) }}</td>
                    <td style="text-align:right;font-weight:600;color:{{ ($inc+$exp) > 0 ? ($fbal >= 0 ? '#16a34a' : '#dc2626') : '#94a3b8' }};">
                        @if($inc + $exp > 0)
                            {{ $fbal < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($fbal),2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td style="text-align:right;color:{{ $wc > 0 ? '#2563eb' : '#94a3b8' }};font-weight:{{ $wc > 0 ? '600' : '400' }};">{{ $fmt($wc) }}</td>
                    <td style="text-align:right;color:{{ $wb > 0 ? '#7c3aed' : '#94a3b8' }};font-weight:{{ $wb > 0 ? '600' : '400' }};">{{ $fmt($wb) }}</td>
                    <td style="text-align:right;font-weight:600;color:{{ ($wc+$wb) > 0 ? ($wbal >= 0 ? '#16a34a' : '#d97706') : '#94a3b8' }};">
                        @if($wc + $wb > 0)
                            {{ $wbal < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($wbal),2) }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;font-weight:700;">
                    <td>Total</td>
                    <td style="text-align:right;color:#16a34a;">{{ $currencySymbol }} {{ number_format($totalIncome,2) }}</td>
                    <td style="text-align:right;color:#dc2626;">{{ $currencySymbol }} {{ number_format($totalExpense,2) }}</td>
                    <td style="text-align:right;color:{{ $financialBalance >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ $financialBalance < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($financialBalance),2) }}
                    </td>
                    <td style="text-align:right;color:#2563eb;">{{ $currencySymbol }} {{ number_format($totalWelfareContribs,2) }}</td>
                    <td style="text-align:right;color:#7c3aed;">{{ $currencySymbol }} {{ number_format($totalWelfareBenefits,2) }}</td>
                    <td style="text-align:right;color:{{ $welfareFundBalance >= 0 ? '#16a34a' : '#d97706' }};">
                        {{ $welfareFundBalance < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($welfareFundBalance),2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
