@extends('layouts.admin')

@section('title', 'Ministry Welfare Fund Summary')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Welfare Fund Summary</h1>
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
    <div style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div>
                <p style="font-size:0.78rem;color:#64748b;font-weight:500;">Total Collected</p>
                <p style="font-size:1.75rem;font-weight:700;color:#166534;margin-top:0.2rem;">{{ $currencySymbol }} {{ number_format($totalContributions,2) }}</p>
            </div>
            <div style="width:42px;height:42px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:#166534;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
        </div>
        <p style="font-size:0.7rem;color:#94a3b8;margin-top:0.5rem;">All welfare contributions received</p>
    </div>

    <div style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div>
                <p style="font-size:0.78rem;color:#64748b;font-weight:500;">Total Paid Out</p>
                <p style="font-size:1.75rem;font-weight:700;color:#991b1b;margin-top:0.2rem;">{{ $currencySymbol }} {{ number_format($totalBenefits,2) }}</p>
            </div>
            <div style="width:42px;height:42px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:#991b1b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
            </div>
        </div>
        <p style="font-size:0.7rem;color:#94a3b8;margin-top:0.5rem;">Benefits paid to members</p>
    </div>

    <div style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div>
                <p style="font-size:0.78rem;color:#64748b;font-weight:500;">Fund Balance</p>
                <p style="font-size:1.75rem;font-weight:700;color:{{ $balance >= 0 ? '#4338ca' : '#dc2626' }};margin-top:0.2rem;">
                    {{ $balance < 0 ? '-' : '' }}{{ $currencySymbol }} {{ number_format(abs($balance),2) }}
                </p>
            </div>
            <div style="width:42px;height:42px;background:{{ $balance >= 0 ? '#ede9fe' : '#fee2e2' }};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:{{ $balance >= 0 ? '#4338ca' : '#dc2626' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            </div>
        </div>
        <p style="font-size:0.7rem;color:#94a3b8;margin-top:0.5rem;">
            {{ $balance >= 0 ? 'Surplus — fund is healthy' : 'Deficit — contributions needed' }}
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    <!-- Monthly Contributions -->
    <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h3 style="font-size:0.95rem;font-weight:600;color:#1e3a5f;">Monthly Contributions</h3>
            <a href="{{ route('admin.ministry.finance.welfare.contributions') }}" style="font-size:0.75rem;color:#1e3a5f;">View all</a>
        </div>
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
                <tr style="border-bottom:1px solid #f8fafc;{{ $row ? '' : 'opacity:0.4;' }}">
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
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h3 style="font-size:0.95rem;font-weight:600;color:#1e3a5f;">Benefits by Purpose</h3>
            <a href="{{ route('admin.ministry.finance.welfare.benefits') }}" style="font-size:0.75rem;color:#1e3a5f;">View all</a>
        </div>
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
