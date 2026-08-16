@extends('admin.pdf.ministry-layout')

@section('pdf-title', 'Expenses — ' . $year)
@section('pdf-meta',
    ($filterMonth ? \Carbon\Carbon::create($year, $filterMonth, 1)->format('F') . ' ' : '') . $year
    . ($filterCategory ? ' &nbsp;&bull;&nbsp; Category: ' . ($categories[$filterCategory] ?? $filterCategory) : '')
    . ' &nbsp;&bull;&nbsp; Records: ' . $expenses->count()
)

@section('content')

@php
$grandTotal = $expenses->sum('amount');
@endphp

<table class="stats-wrap">
    <tr>
        <td style="width:50%">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">{{ $currencySymbol }} {{ number_format($grandTotal, 2) }}</div>
        </td>
        <td style="width:50%">
            <div class="stat-label">Number of Records</div>
            <div class="stat-value">{{ $expenses->count() }}</div>
        </td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Category</th>
            <th>Description</th>
            <th>Paid To</th>
            <th>Payment</th>
            <th class="r">Amount ({{ $currencySymbol }})</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $expense)
        <tr>
            <td class="c">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
            <td>{{ $expense->reference_number }}</td>
            <td>{{ $categories[$expense->category] ?? ucfirst($expense->category) }}</td>
            <td>{{ $expense->description }}</td>
            <td>{{ $expense->paid_to ?: '—' }}</td>
            <td class="c">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
            <td class="r">{{ number_format($expense->amount, 2) }}</td>
            <td>{{ $expense->recordedBy?->name ?? '—' }}</td>
        </tr>
        @empty
        <tr class="empty"><td colspan="8">No expenses found for the selected period.</td></tr>
        @endforelse
        @if($expenses->count())
        <tr class="total">
            <td colspan="6" class="r">Grand Total</td>
            <td class="r">{{ number_format($grandTotal, 2) }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection
