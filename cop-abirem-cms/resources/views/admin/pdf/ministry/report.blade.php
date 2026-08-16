@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Activity Report — ' . $year)
@section('pdf-meta', 'Annual ministry activity report for ' . $year . ' &nbsp;&bull;&nbsp; Members: ' . $memberCount)

@section('content')

@php
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$types  = \App\Models\MinistryOffering::TYPES;
$cats   = \App\Models\MinistryExpense::CATEGORIES;
@endphp

{{-- ── Summary Stats ──────────────────────────────────────────────────────── --}}
<table class="stats-wrap">
    <tr>
        <td>
            <div class="stat-label">Total Offerings</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($totalIncome, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value neg">{{ $currencySymbol }} {{ number_format($totalExpense, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Financial Balance</div>
            <div class="stat-value {{ $financialBalance >= 0 ? 'pos' : 'neg' }}">
                {{ $currencySymbol }} {{ number_format(abs($financialBalance), 2) }}
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="stat-label">Welfare Contributions</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($totalWelfareContribs, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Welfare Benefits Paid</div>
            <div class="stat-value neg">{{ $currencySymbol }} {{ number_format($totalWelfareBenefits, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Welfare Fund Balance</div>
            <div class="stat-value {{ $welfareFundBalance >= 0 ? 'pos' : 'neg' }}">
                {{ $currencySymbol }} {{ number_format(abs($welfareFundBalance), 2) }}
            </div>
        </td>
    </tr>
</table>

{{-- ── Offerings by Type ──────────────────────────────────────────────────── --}}
<div class="section-title">Offerings by Type</div>
<table class="data">
    <thead>
        <tr>
            <th>Offering Type</th>
            <th class="c">Records</th>
            <th class="r">Total ({{ $currencySymbol }})</th>
            <th class="r">% of Income</th>
        </tr>
    </thead>
    <tbody>
        @foreach($types as $key => $label)
        @php $row = $incomeByType[$key] ?? null; @endphp
        <tr>
            <td>{{ $label }}</td>
            <td class="c">{{ $row ? $row->count : '—' }}</td>
            <td class="r">{{ $row ? number_format($row->total, 2) : '—' }}</td>
            <td class="r text-mut">
                {{ ($row && $totalIncome > 0) ? number_format(($row->total / $totalIncome) * 100, 1) . '%' : '—' }}
            </td>
        </tr>
        @endforeach
        <tr class="total">
            <td>Total Offerings</td>
            <td class="c">{{ $incomeByType->sum('count') }}</td>
            <td class="r">{{ number_format($totalIncome, 2) }}</td>
            <td class="r">100%</td>
        </tr>
    </tbody>
</table>

{{-- ── Expenses by Category ───────────────────────────────────────────────── --}}
<div class="section-title">Expenses by Category</div>
<table class="data">
    <thead>
        <tr>
            <th>Category</th>
            <th class="c">Records</th>
            <th class="r">Total ({{ $currencySymbol }})</th>
            <th class="r">% of Expenses</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cats as $key => $label)
        @php $row = $expenseByCategory[$key] ?? null; @endphp
        <tr>
            <td>{{ $label }}</td>
            <td class="c">{{ $row ? $row->count : '—' }}</td>
            <td class="r">{{ $row ? number_format($row->total, 2) : '—' }}</td>
            <td class="r text-mut">
                {{ ($row && $totalExpense > 0) ? number_format(($row->total / $totalExpense) * 100, 1) . '%' : '—' }}
            </td>
        </tr>
        @endforeach
        <tr class="total">
            <td>Total Expenses</td>
            <td class="c">{{ $expenseByCategory->sum('count') }}</td>
            <td class="r">{{ number_format($totalExpense, 2) }}</td>
            <td class="r">100%</td>
        </tr>
    </tbody>
</table>

{{-- ── Welfare Benefits by Purpose ────────────────────────────────────────── --}}
<div class="section-title">Welfare Benefits by Purpose</div>
<table class="data">
    <thead>
        <tr>
            <th>Purpose</th>
            <th class="c">Cases</th>
            <th class="r">Total ({{ $currencySymbol }})</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purposes as $key => $label)
        @php $row = $welfareBenefitsByPurpose[$key] ?? null; @endphp
        <tr>
            <td>{{ $label }}</td>
            <td class="c">{{ $row ? $row->count : '—' }}</td>
            <td class="r">{{ $row ? number_format($row->total, 2) : '—' }}</td>
        </tr>
        @endforeach
        @if($totalWelfareBenefits > 0)
        <tr class="total">
            <td>Total Benefits</td>
            <td class="c">{{ $welfareBenefitsByPurpose->sum('count') }}</td>
            <td class="r">{{ number_format($totalWelfareBenefits, 2) }}</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- ── Monthly Overview ────────────────────────────────────────────────────── --}}
<div class="section-title">Monthly Overview</div>
<table class="data">
    <thead>
        <tr>
            <th>Month</th>
            <th class="r">Offerings</th>
            <th class="r">Expenses</th>
            <th class="r">Fin. Balance</th>
            <th class="r">Welfare Contribs</th>
            <th class="r">Benefits Paid</th>
            <th class="r">Welfare Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($months as $i => $label)
        @php
            $m   = $i + 1;
            $inc = $monthlyIncome[$m]          ?? 0;
            $exp = $monthlyExpenses[$m]        ?? 0;
            $wc  = $monthlyWelfareContribs[$m] ?? 0;
            $wb  = $monthlyWelfareBenefits[$m] ?? 0;
            $fb  = $inc - $exp;
            $wfb = $wc - $wb;
            $hasData = $inc || $exp || $wc || $wb;
        @endphp
        <tr style="{{ !$hasData ? 'opacity:0.4' : '' }}">
            <td>{{ $label }}</td>
            <td class="r">{{ $inc ? number_format($inc, 2) : '—' }}</td>
            <td class="r">{{ $exp ? number_format($exp, 2) : '—' }}</td>
            <td class="r {{ $fb < 0 ? 'text-neg' : ($fb > 0 ? 'text-pos' : '') }}">
                {{ $hasData ? number_format($fb, 2) : '—' }}
            </td>
            <td class="r">{{ $wc ? number_format($wc, 2) : '—' }}</td>
            <td class="r">{{ $wb ? number_format($wb, 2) : '—' }}</td>
            <td class="r {{ $wfb < 0 ? 'text-neg' : ($wfb > 0 ? 'text-pos' : '') }}">
                {{ ($wc || $wb) ? number_format($wfb, 2) : '—' }}
            </td>
        </tr>
        @endforeach
        <tr class="total">
            <td>Annual Total</td>
            <td class="r">{{ number_format($totalIncome, 2) }}</td>
            <td class="r">{{ number_format($totalExpense, 2) }}</td>
            <td class="r {{ $financialBalance < 0 ? 'text-neg' : 'text-pos' }}">{{ number_format($financialBalance, 2) }}</td>
            <td class="r">{{ number_format($totalWelfareContribs, 2) }}</td>
            <td class="r">{{ number_format($totalWelfareBenefits, 2) }}</td>
            <td class="r {{ $welfareFundBalance < 0 ? 'text-neg' : 'text-pos' }}">{{ number_format($welfareFundBalance, 2) }}</td>
        </tr>
    </tbody>
</table>

@endsection
