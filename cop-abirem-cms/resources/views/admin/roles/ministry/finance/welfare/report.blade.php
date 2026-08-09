@extends('layouts.admin')

@section('title', 'Ministry Welfare Report')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Welfare Fund Report</h1>
        <p style="color:#64748b;margin-top:0.25rem;">{{ $ministry->name }} &mdash; {{ $year }}</p>
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;">
            <select name="year" onchange="this.form.submit()" style="padding:0.4rem 0.7rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.85rem;">
                @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.ministry.finance.index') }}" style="font-size:0.85rem;color:#64748b;">&larr; Finance Overview</a>
    </div>
</div>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);border-left:4px solid #22c55e;">
        <div style="font-size:0.72rem;text-transform:uppercase;color:#64748b;letter-spacing:0.06em;">Total Contributions</div>
        <div style="font-size:1.6rem;font-weight:700;color:#166534;margin-top:0.25rem;">{{ $currencySymbol }} {{ number_format($totalContributions,2) }}</div>
    </div>
    <div style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);border-left:4px solid #ef4444;">
        <div style="font-size:0.72rem;text-transform:uppercase;color:#64748b;letter-spacing:0.06em;">Total Benefits Paid</div>
        <div style="font-size:1.6rem;font-weight:700;color:#991b1b;margin-top:0.25rem;">{{ $currencySymbol }} {{ number_format($totalBenefits,2) }}</div>
    </div>
    <div style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);border-left:4px solid {{ $balance >= 0 ? '#6366f1' : '#f59e0b' }};">
        <div style="font-size:0.72rem;text-transform:uppercase;color:#64748b;letter-spacing:0.06em;">Fund Balance</div>
        <div style="font-size:1.6rem;font-weight:700;color:{{ $balance >= 0 ? '#4338ca' : '#92400e' }};margin-top:0.25rem;">{{ $currencySymbol }} {{ number_format(abs($balance),2) }} {{ $balance < 0 ? '(deficit)' : '' }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    <!-- Monthly Contributions -->
    <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <h3 style="font-size:0.95rem;font-weight:600;color:#1e3a5f;margin-bottom:1rem;">Monthly Contributions</h3>
        <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="text-align:left;padding:0.4rem 0.5rem;color:#64748b;font-weight:600;">Month</th>
                    <th style="text-align:center;padding:0.4rem 0.5rem;color:#64748b;font-weight:600;">Count</th>
                    <th style="text-align:right;padding:0.4rem 0.5rem;color:#64748b;font-weight:600;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach(range(1,12) as $m)
                @php $row = $contributionsByMonth[$m] ?? null; @endphp
                <tr style="border-bottom:1px solid #f8fafc;{{ $row ? '' : 'opacity:0.45;' }}">
                    <td style="padding:0.4rem 0.5rem;color:#374151;">{{ date('F', mktime(0,0,0,$m,1)) }}</td>
                    <td style="padding:0.4rem 0.5rem;color:#64748b;text-align:center;">{{ $row?->count ?? '—' }}</td>
                    <td style="padding:0.4rem 0.5rem;color:#166534;font-weight:600;text-align:right;">
                        {{ $row ? $currencySymbol.' '.number_format($row->total,2) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Benefits by Purpose -->
    <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <h3 style="font-size:0.95rem;font-weight:600;color:#1e3a5f;margin-bottom:1rem;">Benefits by Purpose</h3>
        @if($benefitsByPurpose->isEmpty())
        <p style="color:#9ca3af;font-size:0.85rem;text-align:center;padding:2rem 0;">No benefits recorded for {{ $year }}.</p>
        @else
        <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="text-align:left;padding:0.4rem 0.5rem;color:#64748b;font-weight:600;">Purpose</th>
                    <th style="text-align:center;padding:0.4rem 0.5rem;color:#64748b;font-weight:600;">Count</th>
                    <th style="text-align:right;padding:0.4rem 0.5rem;color:#64748b;font-weight:600;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purposes as $key => $label)
                @php $row = $benefitsByPurpose[$key] ?? null; @endphp
                @if($row)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:0.4rem 0.5rem;">
                        <span style="background:#eff6ff;color:#1d4ed8;padding:0.15rem 0.5rem;border-radius:4px;font-size:0.72rem;font-weight:600;">{{ $label }}</span>
                    </td>
                    <td style="padding:0.4rem 0.5rem;color:#64748b;text-align:center;">{{ $row->count }}</td>
                    <td style="padding:0.4rem 0.5rem;color:#991b1b;font-weight:600;text-align:right;">{{ $currencySymbol }} {{ number_format($row->total,2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

<!-- Recent Benefits -->
@if($recentBenefits->isNotEmpty())
<div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
    <h3 style="font-size:0.95rem;font-weight:600;color:#1e3a5f;margin-bottom:1rem;">Recent Benefits</h3>
    <div style="overflow-x:auto;">
        <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="text-align:left;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">Beneficiary</th>
                    <th style="text-align:left;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">Purpose</th>
                    <th style="text-align:center;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">Date</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">Amount</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:#64748b;font-weight:600;">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBenefits as $benefit)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:0.5rem 0.75rem;">
                        <a href="{{ route('admin.ministry.finance.welfare.benefits.show', $benefit->id) }}" style="color:#1e3a5f;font-weight:600;text-decoration:none;">
                            {{ $benefit->beneficiary_name }}
                        </a>
                    </td>
                    <td style="padding:0.5rem 0.75rem;">
                        <span style="background:#eff6ff;color:#1d4ed8;padding:0.15rem 0.5rem;border-radius:4px;font-size:0.72rem;font-weight:600;">
                            {{ $purposes[$benefit->purpose] ?? ucfirst($benefit->purpose) }}
                        </span>
                    </td>
                    <td style="padding:0.5rem 0.75rem;color:#475569;text-align:center;">{{ $benefit->benefit_date->format('d M Y') }}</td>
                    <td style="padding:0.5rem 0.75rem;color:#166534;font-weight:600;text-align:right;">{{ $currencySymbol }} {{ number_format($benefit->amount,2) }}</td>
                    <td style="padding:0.5rem 0.75rem;color:#7c3aed;font-weight:700;text-align:right;">{{ $currencySymbol }} {{ number_format($benefit->total_cost,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
