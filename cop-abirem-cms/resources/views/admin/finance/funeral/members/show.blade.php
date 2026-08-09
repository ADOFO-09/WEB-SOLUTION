@extends('layouts.admin')

@section('title', 'Funeral Records — '.$member->first_name.' '.$member->last_name)

@section('content')

<!-- Member Header -->
<div style="background:#fff;border-radius:12px;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:1rem;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:52px;height:52px;background:#fce7f3;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="font-size:1.2rem;font-weight:700;color:#9d174d;">{{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}</span>
            </div>
            <div>
                <h1 style="font-size:1.2rem;font-weight:700;color:#1e3a5f;margin:0;">{{ $member->first_name }} {{ $member->last_name }}</h1>
                <div style="font-size:0.8rem;color:#64748b;margin-top:0.15rem;">
                    {{ $member->member_id }}
                    @if($member->phone_primary) &bull; {{ $member->phone_primary }} @endif
                </div>
                <div style="margin-top:0.4rem;">
                    @if($member->funeral_enrolled)
                    <span style="background:#dcfce7;color:#166534;font-size:0.7rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:12px;">
                        Funeral Enrolled{{ $member->funeral_start_date ? ' — since '.$member->funeral_start_date->format('M Y') : '' }}
                    </span>
                    @else
                    <span style="background:#fef3c7;color:#92400e;font-size:0.7rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:12px;">Not Enrolled</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="display:flex;gap:2rem;align-items:center;">
            <div style="text-align:center;">
                <div style="font-size:0.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.15rem;">All-Time Paid</div>
                <div style="font-size:1.3rem;font-weight:700;color:#166534;">{{ $currencySymbol }} {{ number_format($totalContributions,2) }}</div>
            </div>
            <div style="width:1px;height:40px;background:#e2e8f0;"></div>
            <div style="text-align:center;">
                <div style="font-size:0.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.15rem;">Support Received</div>
                <div style="font-size:1.3rem;font-weight:700;color:#7c3aed;">{{ $currencySymbol }} {{ number_format($totalBenefits,2) }}</div>
            </div>
        </div>
    </div>
</div>

<div style="margin-bottom:0.75rem;">
    <a href="{{ route('admin.funeral.members.index') }}" style="font-size:0.82rem;color:#64748b;">&larr; Back to Member Records</a>
</div>

{{-- ====== CONTRIBUTION HISTORY ====== --}}
<h2 style="font-size:1rem;font-weight:700;color:#1e3a5f;margin:0 0 0.75rem;">Contribution History by Year</h2>

@forelse($yearlyData as $yd)
@php
    $isArrear  = $yd['balance'] > 0.005;
    $isCredit  = $yd['balance'] < -0.005;
    $monLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
@endphp
<div style="background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:0.85rem;overflow:hidden;">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;padding:0.85rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
        <div style="display:flex;align-items:center;gap:0.65rem;">
            <span style="font-size:1rem;font-weight:700;color:#1e293b;">{{ $yd['yr'] }}</span>
            @if($isArrear)
            <span style="background:#fee2e2;color:#991b1b;font-size:0.68rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:12px;">In Arrears</span>
            @elseif($isCredit)
            <span style="background:#eff6ff;color:#1d4ed8;font-size:0.68rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:12px;">In Credit</span>
            @else
            <span style="background:#dcfce7;color:#166534;font-size:0.68rem;font-weight:700;padding:0.15rem 0.55rem;border-radius:12px;">Fully Paid</span>
            @endif
        </div>
        <div style="display:flex;gap:1.5rem;font-size:0.78rem;flex-wrap:wrap;">
            <span><span style="color:#94a3b8;">Expected:</span> <strong style="color:#374151;">{{ $currencySymbol }} {{ number_format($yd['expected'],2) }}</strong></span>
            <span><span style="color:#94a3b8;">Paid:</span> <strong style="color:#166534;">{{ $currencySymbol }} {{ number_format($yd['paid'],2) }}</strong></span>
            <span><span style="color:#94a3b8;">Balance:</span>
                <strong style="color:{{ $isArrear ? '#dc2626' : ($isCredit ? '#1d4ed8' : '#166534') }};">
                    @if($isArrear) &minus; @elseif($isCredit) + @endif{{ $currencySymbol }} {{ number_format(abs($yd['balance']),2) }}
                    @if($isArrear) <span style="font-weight:400;font-size:0.7rem;">(owes)</span>@elseif($isCredit) <span style="font-weight:400;font-size:0.7rem;">(credit)</span>@endif
                </strong>
            </span>
        </div>
    </div>

    <div style="padding:1rem 1.25rem;overflow-x:auto;">
        <div style="display:grid;grid-template-columns:repeat(12,1fr);gap:0.3rem;min-width:560px;">
            @foreach($monLabels as $i => $mon)
            @php
                $m       = $i + 1;
                $cell    = $yd['months'][$m];
                $isPaidM = $cell['paid'] !== null;
                $hasRate = $cell['rate'] !== null;
                $future  = $yd['yr'] == now()->year && $m > now()->month;

                if ($isPaidM)       { $bg='#dcfce7'; $fc='#166534'; $lbl=$currencySymbol.' '.number_format($cell['paid'],2); }
                elseif (!$hasRate)  { $bg='#f1f5f9'; $fc='#94a3b8'; $lbl='N/A'; }
                elseif ($future)    { $bg='#f8fafc'; $fc='#94a3b8'; $lbl='—'; }
                else                { $bg='#fee2e2'; $fc='#dc2626'; $lbl='Unpaid'; }
            @endphp
            <div style="background:{{ $bg }};border-radius:6px;padding:0.45rem 0.25rem;text-align:center;"
                 title="{{ $mon }} {{ $yd['yr'] }}: {{ $lbl }}{{ $hasRate ? ' (Rate: '.$currencySymbol.' '.number_format($cell['rate'],2).')' : '' }}">
                <div style="font-size:0.62rem;font-weight:600;color:#475569;margin-bottom:0.18rem;">{{ $mon }}</div>
                <div style="font-size:0.62rem;font-weight:700;color:{{ $fc }};overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $lbl }}</div>
            </div>
            @endforeach
        </div>
        <p style="font-size:0.68rem;color:#94a3b8;margin-top:0.6rem;margin-bottom:0;">
            Expected = due rate × months with a set rate. Hover each cell to see the rate for that period.
        </p>
    </div>
</div>
@empty
<div style="background:#fff;border-radius:10px;padding:2.5rem;text-align:center;color:#9ca3af;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:1rem;">
    No contribution records found for this member.
</div>
@endforelse

{{-- ====== BENEFITS / SUPPORT HISTORY ====== --}}
<h2 style="font-size:1rem;font-weight:700;color:#1e3a5f;margin:1.25rem 0 0.75rem;">Funeral Support Received</h2>

<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
    @if($benefits->isEmpty())
    <div style="padding:2.5rem;text-align:center;color:#9ca3af;font-size:0.875rem;">No funeral support recorded for this member.</div>
    @else
    <div style="overflow-x:auto;">
        <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th style="text-align:left;padding:0.65rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Funeral Date</th>
                    <th style="text-align:left;padding:0.65rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Deceased</th>
                    <th style="text-align:left;padding:0.65rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Venue</th>
                    <th style="text-align:left;padding:0.65rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Notes</th>
                    <th style="text-align:right;padding:0.65rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Donated</th>
                    <th style="text-align:right;padding:0.65rem 1rem;color:#64748b;font-weight:600;font-size:0.72rem;text-transform:uppercase;">Total Cost</th>
                    <th style="padding:0.65rem 1rem;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($benefits as $benefit)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:0.6rem 1rem;color:#374151;white-space:nowrap;">{{ $benefit->funeral_date->format('d M Y') }}</td>
                    <td style="padding:0.6rem 1rem;color:#1e293b;font-weight:600;">{{ $benefit->deceased_name ?? '—' }}</td>
                    <td style="padding:0.6rem 1rem;color:#64748b;">{{ $benefit->venue ?? '—' }}</td>
                    <td style="padding:0.6rem 1rem;color:#64748b;max-width:160px;">
                        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $benefit->description }}">
                            {{ $benefit->description ?: '—' }}
                        </div>
                    </td>
                    <td style="padding:0.6rem 1rem;text-align:right;color:#166534;font-weight:700;">{{ $currencySymbol }} {{ number_format($benefit->amount_donated,2) }}</td>
                    <td style="padding:0.6rem 1rem;text-align:right;color:#7c3aed;font-weight:700;">{{ $currencySymbol }} {{ number_format($benefit->total_cost,2) }}</td>
                    <td style="padding:0.6rem 1rem;text-align:right;">
                        <a href="{{ route('admin.funeral.benefits.show', $benefit) }}"
                           style="font-size:0.75rem;color:#1e3a5f;font-weight:600;text-decoration:none;border:1px solid #cbd5e1;padding:0.2rem 0.55rem;border-radius:4px;">
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc;border-top:2px solid #e2e8f0;">
                    <td colspan="5" style="padding:0.65rem 1rem;font-size:0.8rem;font-weight:700;color:#374151;">Total Support Received</td>
                    <td style="padding:0.65rem 1rem;text-align:right;font-weight:700;color:#7c3aed;">{{ $currencySymbol }} {{ number_format($totalBenefits,2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

@endsection
