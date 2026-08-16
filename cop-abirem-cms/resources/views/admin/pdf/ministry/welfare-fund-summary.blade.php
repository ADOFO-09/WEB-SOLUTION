@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Welfare Fund Summary — ' . $year)
@section('pdf-meta', 'Annual fund overview for ' . $year)

@section('content')

@php
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
@endphp

<table class="stats-wrap">
    <tr>
        <td>
            <div class="stat-label">Total Contributions</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($totalContributions, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Total Benefits Paid</div>
            <div class="stat-value neg">{{ $currencySymbol }} {{ number_format($totalBenefits, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Net Fund Balance</div>
            <div class="stat-value {{ $balance >= 0 ? 'pos' : 'neg' }}">
                {{ $currencySymbol }} {{ number_format(abs($balance), 2) }}
                @if($balance < 0) <span style="font-size:8pt;color:#dc2626">(deficit)</span>@endif
            </div>
        </td>
    </tr>
</table>

<div class="section-title">Monthly Contributions</div>
<table class="data">
    <thead>
        <tr>
            <th>Month</th>
            <th class="c">Records</th>
            <th class="r">Amount ({{ $currencySymbol }})</th>
        </tr>
    </thead>
    <tbody>
        @foreach($months as $i => $label)
        @php $m = $i + 1; $row = $contributionsByMonth[$m] ?? null; @endphp
        <tr>
            <td>{{ $label }} {{ $year }}</td>
            <td class="c">{{ $row ? $row->count : '—' }}</td>
            <td class="r">{{ $row ? number_format($row->total, 2) : '—' }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td>Total</td>
            <td class="c">{{ $contributionsByMonth->sum('count') }}</td>
            <td class="r">{{ number_format($totalContributions, 2) }}</td>
        </tr>
    </tbody>
</table>

<div class="section-title">Benefits by Purpose</div>
<table class="data">
    <thead>
        <tr>
            <th>Purpose</th>
            <th class="c">Cases</th>
            <th class="r">Amount Disbursed ({{ $currencySymbol }})</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purposes as $key => $label)
        @php $row = $benefitsByPurpose[$key] ?? null; @endphp
        <tr>
            <td>{{ $label }}</td>
            <td class="c">{{ $row ? $row->count : '—' }}</td>
            <td class="r">{{ $row ? number_format($row->total, 2) : '—' }}</td>
        </tr>
        @empty
        <tr class="empty"><td colspan="3">No benefit categories defined.</td></tr>
        @endforelse
        @if($totalBenefits > 0)
        <tr class="total">
            <td>Total Benefits</td>
            <td class="c">{{ $benefitsByPurpose->sum('count') }}</td>
            <td class="r">{{ number_format($totalBenefits, 2) }}</td>
        </tr>
        @endif
    </tbody>
</table>

@if($recentBenefits->count())
<div class="section-title">Recent Disbursements</div>
<table class="data">
    <thead>
        <tr>
            <th>Date</th>
            <th>Beneficiary</th>
            <th>Purpose</th>
            <th class="r">Amount ({{ $currencySymbol }})</th>
        </tr>
    </thead>
    <tbody>
        @foreach($recentBenefits as $b)
        <tr>
            <td class="c">{{ \Carbon\Carbon::parse($b->benefit_date)->format('d M Y') }}</td>
            <td>{{ $b->beneficiary_name }}</td>
            <td>{{ $purposes[$b->purpose] ?? ucfirst($b->purpose) }}</td>
            <td class="r">{{ number_format($b->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@endsection
