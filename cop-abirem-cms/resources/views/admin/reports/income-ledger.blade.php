@extends('layouts.admin')

@section('title', 'Income Ledger')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Income Ledger</h1>
            <p style="color:#64748b;">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</p>
        </div>
        <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
            <select name="month" class="form-select" style="width:auto;">
                @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" class="form-select" style="width:auto;">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary">View</button>
        </form>
    </div>

    {{-- Ledger Table --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
                <thead>
                    <tr style="background:#1e3a5f;color:white;">
                        <th style="padding:.75rem 1rem;text-align:left;border-right:1px solid #2d4f7c;width:70px;">DATE</th>
                        <th style="padding:.75rem 1rem;text-align:left;border-right:1px solid #2d4f7c;">PARTICULARS</th>
                        <th style="padding:.75rem 1rem;text-align:right;border-right:1px solid #2d4f7c;background:#14532d;width:120px;">TITHE</th>
                        <th style="padding:.75rem 1rem;text-align:right;border-right:1px solid #2d4f7c;background:#1e3a8a;width:120px;">OFFERING</th>
                        <th style="padding:.75rem 1rem;text-align:right;border-right:1px solid #2d4f7c;background:#4c1d95;width:120px;">DONATION</th>
                        <th style="padding:.75rem 1rem;text-align:right;border-right:1px solid #2d4f7c;background:#78350f;width:120px;">SPECIAL</th>
                        <th style="padding:.75rem 1rem;text-align:right;width:130px;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Balance Brought Forward --}}
                    @if($broughtForward > 0)
                    <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;font-style:italic;">
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;font-weight:600;color:#475569;">Balance B/F</td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;text-align:right;font-weight:600;font-family:monospace;">{{ number_format($broughtForward, 2) }}</td>
                    </tr>
                    @endif

                    {{-- Grouped entries --}}
                    @forelse($groupedEntries as $dateKey => $entries)
                    @php
                        $count       = $entries->count();
                        $isMulti     = $count > 1;
                        $dayTithe    = $entries->sum('tithe');
                        $dayOffering = $entries->sum('offering');
                        $dayDonation = $entries->sum('donation');
                        $daySpecial  = $entries->sum('special');
                        $dayTotal    = $dayTithe + $dayOffering + $dayDonation + $daySpecial;
                        $dateLabel   = \Carbon\Carbon::parse($dateKey)->format('d/m');
                    @endphp

                    @foreach($entries as $i => $entry)
                    @php
                        $rowTotal    = $entry['tithe'] + $entry['offering'] + $entry['donation'] + $entry['special'];
                        $isFirst     = $i === 0;
                        $isVoided    = ($entry['ledger_status'] ?? 'active') === 'voided';
                        $isAdj       = (bool)($entry['is_adjustment'] ?? false);
                        $rowBg       = $isVoided ? '#fef2f2' : ($isAdj ? '#eff6ff' : ($isMulti ? '#fafafa' : 'white'));
                        $hoverBg     = $isVoided ? '#fee2e2' : ($isAdj ? '#dbeafe' : '#f0f9ff');
                        $amountStyle = $isVoided ? 'text-decoration:line-through;color:#fca5a5;' : '';
                    @endphp
                    <tr style="background:{{ $rowBg }};border-bottom:1px solid #f1f5f9;{{ $isVoided ? 'opacity:.7;' : '' }}"
                        onmouseover="this.style.background='{{ $hoverBg }}'" onmouseout="this.style.background='{{ $rowBg }}'">
                        <td style="padding:.5rem 1rem;border-right:1px solid #e2e8f0;font-family:monospace;font-size:.75rem;white-space:nowrap;color:{{ $isFirst ? '#1e293b' : 'transparent' }};user-select:{{ $isFirst ? 'auto' : 'none' }};">
                            {{ $dateLabel }}
                        </td>
                        <td style="padding:.5rem 1rem;border-right:1px solid #e2e8f0;padding-left:{{ $isFirst ? '1rem' : '1.75rem' }};">
                            {{ $entry['particular'] }}
                            @if($isVoided)
                                <span style="display:inline-block;padding:.1rem .3rem;background:#fee2e2;color:#b91c1c;font-size:.65rem;font-weight:700;border-radius:.2rem;margin-left:.3rem;">VOID</span>
                            @elseif($isAdj)
                                <span style="display:inline-block;padding:.1rem .3rem;background:#dbeafe;color:#1d4ed8;font-size:.65rem;font-weight:700;border-radius:.2rem;margin-left:.3rem;">+ADJ</span>
                            @endif
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;{{ $amountStyle }}background:{{ (!$isVoided && $entry['tithe'] > 0) ? '#f0fdf4' : ($isVoided ? '#fef2f2' : '') }}">
                            {{ $entry['tithe'] > 0 ? number_format($entry['tithe'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;{{ $amountStyle }}background:{{ (!$isVoided && $entry['offering'] > 0) ? '#eff6ff' : ($isVoided ? '#fef2f2' : '') }}">
                            {{ $entry['offering'] > 0 ? number_format($entry['offering'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;{{ $amountStyle }}background:{{ (!$isVoided && $entry['donation'] > 0) ? '#faf5ff' : ($isVoided ? '#fef2f2' : '') }}">
                            {{ $entry['donation'] > 0 ? number_format($entry['donation'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;{{ $amountStyle }}background:{{ (!$isVoided && $entry['special'] > 0) ? '#fffbeb' : ($isVoided ? '#fef2f2' : '') }}">
                            {{ $entry['special'] > 0 ? number_format($entry['special'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;font-family:monospace;font-weight:{{ $isMulti ? '400' : '600' }};color:{{ $isVoided ? '#fca5a5' : ($isMulti ? '#94a3b8' : '#1e293b') }};{{ $isVoided ? 'text-decoration:line-through;' : '' }}">
                            {{ $isMulti ? '-' : number_format($rowTotal, 2) }}
                        </td>
                    </tr>
                    @endforeach

                    {{-- Day total row — only when multiple entries share the date --}}
                    @if($isMulti)
                    <tr style="background:#e0f2fe;border-bottom:2px solid #7dd3fc;">
                        <td style="padding:.5rem 1rem;border-right:1px solid #bae6fd;"></td>
                        <td style="padding:.5rem 1rem;border-right:1px solid #bae6fd;font-size:.75rem;font-weight:600;color:#0369a1;padding-left:1.75rem;">
                            Day Total
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #bae6fd;font-family:monospace;font-weight:600;font-size:.8rem;color:#15803d;">
                            {{ $dayTithe > 0 ? number_format($dayTithe, 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #bae6fd;font-family:monospace;font-weight:600;font-size:.8rem;color:#1d4ed8;">
                            {{ $dayOffering > 0 ? number_format($dayOffering, 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #bae6fd;font-family:monospace;font-weight:600;font-size:.8rem;color:#6d28d9;">
                            {{ $dayDonation > 0 ? number_format($dayDonation, 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #bae6fd;font-family:monospace;font-weight:600;font-size:.8rem;color:#b45309;">
                            {{ $daySpecial > 0 ? number_format($daySpecial, 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;font-family:monospace;font-weight:700;color:#0369a1;">
                            {{ number_format($dayTotal, 2) }}
                        </td>
                    </tr>
                    @endif

                    @empty
                    <tr>
                        <td colspan="7" style="padding:3rem;text-align:center;color:#64748b;">
                            No income entries for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
                <tfoot>
                    <tr style="background:#1e3a5f;color:white;border-top:2px solid #0f172a;font-weight:700;">
                        <td style="padding:.875rem 1rem;border-right:1px solid #2d4f7c;"></td>
                        <td style="padding:.875rem 1rem;border-right:1px solid #2d4f7c;letter-spacing:.05em;font-size:.8rem;">MONTHLY TOTAL</td>
                        <td style="padding:.875rem 1rem;text-align:right;border-right:1px solid #2d4f7c;font-family:monospace;background:#14532d;">{{ number_format($totals['tithe'], 2) }}</td>
                        <td style="padding:.875rem 1rem;text-align:right;border-right:1px solid #2d4f7c;font-family:monospace;background:#1e3a8a;">{{ number_format($totals['offering'], 2) }}</td>
                        <td style="padding:.875rem 1rem;text-align:right;border-right:1px solid #2d4f7c;font-family:monospace;background:#4c1d95;">{{ number_format($totals['donation'], 2) }}</td>
                        <td style="padding:.875rem 1rem;text-align:right;border-right:1px solid #2d4f7c;font-family:monospace;background:#78350f;">{{ number_format($totals['special'], 2) }}</td>
                        <td style="padding:.875rem 1rem;text-align:right;font-family:monospace;font-size:1rem;">{{ number_format($totals['grand_total'], 2) }}</td>
                    </tr>
                    <tr style="background:#bbf7d0;font-weight:700;border-top:1px solid #86efac;">
                        <td style="padding:.75rem 1rem;border-right:1px solid #86efac;" colspan="2">BALANCE C/F</td>
                        <td style="padding:.75rem 1rem;" colspan="4"></td>
                        <td style="padding:.75rem 1rem;text-align:right;font-family:monospace;font-size:1rem;color:#166534;">{{ number_format($carriedForward, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;">
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:.75rem;padding:1rem;text-align:center;">
            <div style="font-size:1.25rem;font-weight:700;color:#15803d;">GH₵{{ number_format($totals['tithe'], 2) }}</div>
            <div style="font-size:.75rem;color:#16a34a;">Tithes</div>
        </div>
        <div style="background:#eff6ff;border:1px solid #93c5fd;border-radius:.75rem;padding:1rem;text-align:center;">
            <div style="font-size:1.25rem;font-weight:700;color:#1d4ed8;">GH₵{{ number_format($totals['offering'], 2) }}</div>
            <div style="font-size:.75rem;color:#2563eb;">Offerings</div>
        </div>
        <div style="background:#faf5ff;border:1px solid #c4b5fd;border-radius:.75rem;padding:1rem;text-align:center;">
            <div style="font-size:1.25rem;font-weight:700;color:#6d28d9;">GH₵{{ number_format($totals['donation'], 2) }}</div>
            <div style="font-size:.75rem;color:#7c3aed;">Donations</div>
        </div>
        <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:.75rem;padding:1rem;text-align:center;">
            <div style="font-size:1.25rem;font-weight:700;color:#b45309;">GH₵{{ number_format($totals['special'], 2) }}</div>
            <div style="font-size:.75rem;color:#d97706;">Special</div>
        </div>
        <div style="background:#1e3a5f;border-radius:.75rem;padding:1rem;text-align:center;">
            <div style="font-size:1.25rem;font-weight:700;color:#fbbf24;">GH₵{{ number_format($totals['grand_total'], 2) }}</div>
            <div style="font-size:.75rem;color:#94a3b8;">Grand Total</div>
        </div>
    </div>

</div>
@endsection
