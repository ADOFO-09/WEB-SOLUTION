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
                        <th style="padding:.75rem 1rem;text-align:left;border-right:1px solid #2d4f7c;width:80px;">DATE</th>
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
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;font-style:italic;">
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;font-size:.75rem;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;font-weight:600;color:#475569;">Balance B/F</td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;border-right:1px solid #e2e8f0;"></td>
                        <td style="padding:.625rem 1rem;text-align:right;font-weight:600;font-family:monospace;">{{ number_format($broughtForward, 2) }}</td>
                    </tr>
                    @endif

                    {{-- Entries --}}
                    @forelse($ledgerEntries as $entry)
                    @php $rowTotal = $entry['tithe'] + $entry['offering'] + $entry['donation'] + $entry['special']; @endphp
                    <tr style="border-bottom:1px solid #e2e8f0;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                        <td style="padding:.5rem 1rem;border-right:1px solid #e2e8f0;font-family:monospace;font-size:.75rem;white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($entry['date'])->format('d/m') }}
                        </td>
                        <td style="padding:.5rem 1rem;border-right:1px solid #e2e8f0;">{{ $entry['particular'] }}</td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;background:{{ $entry['tithe'] > 0 ? '#f0fdf4' : '' }}">
                            {{ $entry['tithe'] > 0 ? number_format($entry['tithe'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;background:{{ $entry['offering'] > 0 ? '#eff6ff' : '' }}">
                            {{ $entry['offering'] > 0 ? number_format($entry['offering'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;background:{{ $entry['donation'] > 0 ? '#faf5ff' : '' }}">
                            {{ $entry['donation'] > 0 ? number_format($entry['donation'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;background:{{ $entry['special'] > 0 ? '#fffbeb' : '' }}">
                            {{ $entry['special'] > 0 ? number_format($entry['special'], 2) : '-' }}
                        </td>
                        <td style="padding:.5rem 1rem;text-align:right;font-family:monospace;font-weight:600;">
                            {{ number_format($rowTotal, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:3rem;text-align:center;color:#64748b;">
                            No income entries for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background:#e2e8f0;border-top:2px solid #94a3b8;font-weight:700;">
                        <td style="padding:.75rem 1rem;border-right:1px solid #cbd5e1;"></td>
                        <td style="padding:.75rem 1rem;border-right:1px solid #cbd5e1;">MONTHLY TOTAL</td>
                        <td style="padding:.75rem 1rem;text-align:right;border-right:1px solid #cbd5e1;font-family:monospace;background:#dcfce7;">{{ number_format($totals['tithe'], 2) }}</td>
                        <td style="padding:.75rem 1rem;text-align:right;border-right:1px solid #cbd5e1;font-family:monospace;background:#dbeafe;">{{ number_format($totals['offering'], 2) }}</td>
                        <td style="padding:.75rem 1rem;text-align:right;border-right:1px solid #cbd5e1;font-family:monospace;background:#ede9fe;">{{ number_format($totals['donation'], 2) }}</td>
                        <td style="padding:.75rem 1rem;text-align:right;border-right:1px solid #cbd5e1;font-family:monospace;background:#fef3c7;">{{ number_format($totals['special'], 2) }}</td>
                        <td style="padding:.75rem 1rem;text-align:right;font-family:monospace;font-size:1rem;">{{ number_format($totals['grand_total'], 2) }}</td>
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
