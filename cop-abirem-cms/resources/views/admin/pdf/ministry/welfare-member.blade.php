@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Welfare Statement — ' . $member->full_name)
@section('pdf-meta', $member->member_id . ' &nbsp;&bull;&nbsp; ' . $ministry->name)

@section('content')

@php
$months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
           7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
@endphp

<table class="stats-wrap" style="margin-bottom:14px">
    <tr>
        <td>
            <div class="stat-label">Total Contributions</div>
            <div class="stat-value pos">{{ $currencySymbol }} {{ number_format($totalContributions, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Total Benefits Received</div>
            <div class="stat-value neg">{{ $currencySymbol }} {{ number_format($totalBenefits, 2) }}</div>
        </td>
        <td>
            <div class="stat-label">Net Position</div>
            @php $net = $totalContributions - $totalBenefits; @endphp
            <div class="stat-value {{ $net >= 0 ? 'pos' : 'neg' }}">
                {{ $currencySymbol }} {{ number_format(abs($net), 2) }}
            </div>
        </td>
    </tr>
</table>

@foreach($yearlyData as $yd)
<div class="section-title">{{ $yd['yr'] }} — Expected: {{ $currencySymbol }} {{ number_format($yd['expected'], 2) }} &nbsp;|&nbsp; Paid: {{ $currencySymbol }} {{ number_format($yd['paid'], 2) }} &nbsp;|&nbsp; Balance: {{ $currencySymbol }} {{ number_format(abs($yd['balance']), 2) }}{{ $yd['balance'] < 0 ? ' (cr)' : '' }}</div>
<table class="data">
    <thead>
        <tr>
            <th>Month</th>
            <th class="r">Rate ({{ $currencySymbol }})</th>
            <th class="r">Paid ({{ $currencySymbol }})</th>
            <th class="c">Status</th>
            <th>Reference</th>
        </tr>
    </thead>
    <tbody>
        @foreach($yd['months'] as $m => $row)
        <tr>
            <td>{{ $months[$m] }} {{ $yd['yr'] }}</td>
            <td class="r">{{ $row['rate'] !== null ? number_format($row['rate'], 2) : '—' }}</td>
            <td class="r">{{ $row['paid'] !== null ? number_format($row['paid'], 2) : '—' }}</td>
            <td class="c">
                @if($row['rate'] === null)
                    <span class="text-mut">—</span>
                @elseif($row['paid'] === null)
                    <span class="badge badge-r">Unpaid</span>
                @elseif($row['paid'] >= $row['rate'])
                    <span class="badge badge-g">Paid</span>
                @else
                    <span class="badge badge-y">Partial</span>
                @endif
            </td>
            <td class="text-mut">{{ $row['contrib']?->reference_number ?? '—' }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td>Yearly Total</td>
            <td class="r">{{ number_format($yd['expected'], 2) }}</td>
            <td class="r">{{ number_format($yd['paid'], 2) }}</td>
            <td class="c">
                @if($yd['balance'] > 0)
                    <span class="badge badge-r">Arrears</span>
                @elseif($yd['balance'] < 0)
                    <span class="badge badge-b">Credit</span>
                @else
                    <span class="badge badge-g">Clear</span>
                @endif
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
@endforeach

@if($benefits->count())
<div class="section-title">Benefits Received</div>
<table class="data">
    <thead>
        <tr>
            <th>Date</th>
            <th>Purpose</th>
            <th>Description</th>
            <th>Approved By</th>
            <th class="r">Amount ({{ $currencySymbol }})</th>
        </tr>
    </thead>
    <tbody>
        @foreach($benefits as $b)
        <tr>
            <td class="c">{{ \Carbon\Carbon::parse($b->benefit_date)->format('d M Y') }}</td>
            <td>{{ $purposes[$b->purpose] ?? ucfirst($b->purpose) }}</td>
            <td>{{ $b->description ?: '—' }}</td>
            <td>{{ $b->approved_by ?: '—' }}</td>
            <td class="r">{{ number_format($b->amount, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="4" class="r">Total Benefits</td>
            <td class="r">{{ number_format($totalBenefits, 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@endsection
