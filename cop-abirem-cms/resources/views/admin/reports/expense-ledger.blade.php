@extends('layouts.admin')

@section('title', 'Expense Ledger')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Expense Ledger</h1>
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
            <table style="width:100%;border-collapse:collapse;font-size:.8rem;">
                <thead>
                    <tr style="background:#7f1d1d;color:white;">
                        <th style="padding:.75rem .75rem;text-align:left;border-right:1px solid #991b1b;width:70px;">DATE</th>
                        <th style="padding:.75rem .75rem;text-align:left;border-right:1px solid #991b1b;">PARTICULARS</th>
                        <th style="padding:.75rem .5rem;text-align:right;border-right:1px solid #991b1b;width:90px;">TRANSPORT</th>
                        <th style="padding:.75rem .5rem;text-align:right;border-right:1px solid #991b1b;width:90px;">UTILITIES</th>
                        <th style="padding:.75rem .5rem;text-align:right;border-right:1px solid #991b1b;width:90px;">WELFARE</th>
                        <th style="padding:.75rem .5rem;text-align:right;border-right:1px solid #991b1b;width:90px;">CLEANING</th>
                        <th style="padding:.75rem .5rem;text-align:right;border-right:1px solid #991b1b;width:90px;">MAINT.</th>
                        <th style="padding:.75rem .5rem;text-align:right;border-right:1px solid #991b1b;width:90px;">REMIT.</th>
                        <th style="padding:.75rem .5rem;text-align:right;border-right:1px solid #991b1b;width:90px;">OTHERS</th>
                        <th style="padding:.75rem .75rem;text-align:right;width:100px;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($groupedEntries as $dateKey => $entries)
                    @php
                        $count      = $entries->count();
                        $isMulti    = $count > 1;
                        $dateLabel  = \Carbon\Carbon::parse($dateKey)->format('d/m');
                        $dayCols    = [];
                        foreach($columns as $col) {
                            $dayCols[$col] = $entries->sum($col);
                        }
                        $dayTotal = array_sum($dayCols);
                    @endphp

                    @foreach($entries as $i => $entry)
                    @php
                        $rowTotal = 0;
                        foreach($columns as $col) $rowTotal += $entry[$col];
                        $isFirst  = $i === 0;
                        $rowBg    = $isMulti ? '#fafafa' : 'white';
                    @endphp
                    <tr style="background:{{ $rowBg }};border-bottom:1px solid #f1f5f9;"
                        onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='{{ $rowBg }}'">
                        <td style="padding:.5rem .75rem;border-right:1px solid #e2e8f0;font-family:monospace;font-size:.75rem;white-space:nowrap;color:{{ $isFirst ? '#1e293b' : 'transparent' }};user-select:{{ $isFirst ? 'auto' : 'none' }};">
                            {{ $dateLabel }}
                        </td>
                        <td style="padding:.5rem .75rem;border-right:1px solid #e2e8f0;padding-left:{{ $isFirst ? '.75rem' : '1.5rem' }};">
                            {{ $entry['particular'] }}
                        </td>
                        @foreach($columns as $col)
                        <td style="padding:.5rem .5rem;text-align:right;border-right:1px solid #e2e8f0;font-family:monospace;{{ $entry[$col] > 0 ? 'background:#fef2f2;' : '' }}">
                            {{ $entry[$col] > 0 ? number_format($entry[$col], 2) : '-' }}
                        </td>
                        @endforeach
                        <td style="padding:.5rem .75rem;text-align:right;font-family:monospace;font-weight:{{ $isMulti ? '400' : '600' }};color:{{ $isMulti ? '#94a3b8' : '#1e293b' }};">
                            {{ $isMulti ? '-' : number_format($rowTotal, 2) }}
                        </td>
                    </tr>
                    @endforeach

                    {{-- Day total row — only when multiple entries share the date --}}
                    @if($isMulti)
                    <tr style="background:#fee2e2;border-bottom:2px solid #fca5a5;">
                        <td style="padding:.5rem .75rem;border-right:1px solid #fca5a5;"></td>
                        <td style="padding:.5rem .75rem;border-right:1px solid #fca5a5;font-size:.75rem;font-weight:600;color:#991b1b;padding-left:1.5rem;">
                            Day Total
                        </td>
                        @foreach($columns as $col)
                        <td style="padding:.5rem .5rem;text-align:right;border-right:1px solid #fca5a5;font-family:monospace;font-weight:600;font-size:.8rem;color:#7f1d1d;">
                            {{ $dayCols[$col] > 0 ? number_format($dayCols[$col], 2) : '-' }}
                        </td>
                        @endforeach
                        <td style="padding:.5rem .75rem;text-align:right;font-family:monospace;font-weight:700;color:#991b1b;">
                            {{ number_format($dayTotal, 2) }}
                        </td>
                    </tr>
                    @endif

                    @empty
                    <tr>
                        <td colspan="10" style="padding:3rem;text-align:center;color:#64748b;">
                            No approved expenses for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
                <tfoot>
                    <tr style="background:#7f1d1d;color:white;border-top:2px solid #450a0a;font-weight:700;">
                        <td style="padding:.875rem .75rem;border-right:1px solid #991b1b;"></td>
                        <td style="padding:.875rem .75rem;border-right:1px solid #991b1b;letter-spacing:.05em;font-size:.75rem;">MONTHLY TOTAL</td>
                        @foreach($columns as $col)
                        <td style="padding:.875rem .5rem;text-align:right;border-right:1px solid #991b1b;font-family:monospace;">{{ number_format($totals[$col], 2) }}</td>
                        @endforeach
                        <td style="padding:.875rem .75rem;text-align:right;font-family:monospace;font-size:1rem;">{{ number_format($totals['grand_total'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;">
        @php
        $summaryItems = [
            'transport'  => ['label' => 'Transport',   'bg' => '#fef2f2', 'border' => '#fca5a5', 'text' => '#991b1b'],
            'utilities'  => ['label' => 'Utilities',   'bg' => '#eff6ff', 'border' => '#93c5fd', 'text' => '#1d4ed8'],
            'welfare'    => ['label' => 'Welfare',     'bg' => '#faf5ff', 'border' => '#c4b5fd', 'text' => '#6d28d9'],
            'remittance' => ['label' => 'Remittances', 'bg' => '#fffbeb', 'border' => '#fcd34d', 'text' => '#b45309'],
        ];
        @endphp
        @foreach($summaryItems as $col => $info)
        <div style="background:{{ $info['bg'] }};border:1px solid {{ $info['border'] }};border-radius:.75rem;padding:1rem;text-align:center;">
            <div style="font-size:1.25rem;font-weight:700;color:{{ $info['text'] }};">GH₵{{ number_format($totals[$col], 2) }}</div>
            <div style="font-size:.75rem;color:{{ $info['text'] }};">{{ $info['label'] }}</div>
        </div>
        @endforeach
        <div style="background:#7f1d1d;border-radius:.75rem;padding:1rem;text-align:center;">
            <div style="font-size:1.25rem;font-weight:700;color:#fca5a5;">GH₵{{ number_format($totals['grand_total'], 2) }}</div>
            <div style="font-size:.75rem;color:#fecaca;">Total Expenses</div>
        </div>
    </div>

</div>
@endsection
