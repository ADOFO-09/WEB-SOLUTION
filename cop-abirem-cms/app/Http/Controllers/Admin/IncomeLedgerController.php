<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Offering;
use App\Models\PledgePayment;
use App\Models\Tithe;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncomeLedgerController extends Controller
{
    public function export(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);
        $label = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F_Y');

        $data = $this->buildLedger($month, $year);

        $filename = "Income_Ledger_{$label}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $month, $year) {
            $fh = fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            $monthLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');
            fputcsv($fh, ["COP ABIREM — Income Ledger — {$monthLabel}"]);
            fputcsv($fh, []);

            fputcsv($fh, ['DATE', 'PARTICULARS', 'TITHE', 'OFFERING', 'DONATION', 'PLEDGE', 'SPECIAL', 'TOTAL']);

            if ($data['broughtForward'] > 0) {
                fputcsv($fh, ['', 'Balance B/F', '', '', '', '', '', number_format($data['broughtForward'], 2)]);
            }

            foreach ($data['groupedEntries'] as $dateKey => $entries) {
                $count   = $entries->count();
                $isMulti = $count > 1;
                $dateLabel = \Carbon\Carbon::parse($dateKey)->format('d/m/Y');

                foreach ($entries as $i => $entry) {
                    $rowTotal = $entry['tithe'] + $entry['offering'] + $entry['donation'] + ($entry['pledge'] ?? 0) + $entry['special'];
                    $isVoided = ($entry['ledger_status'] ?? 'active') === 'voided';
                    $particular = $entry['particular'];
                    if ($isVoided) $particular .= ' [VOID]';
                    if ($entry['is_adjustment']) $particular .= ' [ADJ]';

                    fputcsv($fh, [
                        $i === 0 ? $dateLabel : '',
                        $particular,
                        $entry['tithe'] > 0   ? number_format($entry['tithe'],     2) : '',
                        $entry['offering'] > 0 ? number_format($entry['offering'],  2) : '',
                        $entry['donation'] > 0 ? number_format($entry['donation'],  2) : '',
                        ($entry['pledge'] ?? 0) > 0 ? number_format($entry['pledge'], 2) : '',
                        $entry['special'] > 0  ? number_format($entry['special'],   2) : '',
                        $isMulti ? '' : number_format($rowTotal, 2),
                    ]);
                }

                if ($isMulti) {
                    $dayTithe    = $entries->sum('tithe');
                    $dayOffering = $entries->sum('offering');
                    $dayDonation = $entries->sum('donation');
                    $dayPledge   = $entries->sum('pledge');
                    $daySpecial  = $entries->sum('special');
                    $dayTotal    = $dayTithe + $dayOffering + $dayDonation + $dayPledge + $daySpecial;
                    fputcsv($fh, [
                        '', 'Day Total',
                        $dayTithe    > 0 ? number_format($dayTithe,    2) : '',
                        $dayOffering > 0 ? number_format($dayOffering, 2) : '',
                        $dayDonation > 0 ? number_format($dayDonation, 2) : '',
                        $dayPledge   > 0 ? number_format($dayPledge,   2) : '',
                        $daySpecial  > 0 ? number_format($daySpecial,  2) : '',
                        number_format($dayTotal, 2),
                    ]);
                }
            }

            $t = $data['totals'];
            fputcsv($fh, []);
            fputcsv($fh, [
                '', 'MONTHLY TOTAL',
                number_format($t['tithe'],    2),
                number_format($t['offering'], 2),
                number_format($t['donation'], 2),
                number_format($t['pledge'],   2),
                number_format($t['special'],  2),
                number_format($t['grand_total'], 2),
            ]);
            fputcsv($fh, ['', 'BALANCE C/F', '', '', '', '', '', number_format($data['carriedForward'], 2)]);

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $data   = $this->buildLedger($month, $year);
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::createFromDate($year, $m, 1)->format('F');
        }

        return view('admin.reports.income-ledger', array_merge($data, compact('month', 'year', 'months')));
    }

    private function buildLedger(int $month, int $year): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $tithes = Tithe::with('incomeCategory', 'member')
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get();

        $offerings = Offering::with('incomeCategory')
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get();

        $donations = Donation::with('member')
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get();

        $pledgePayments = PledgePayment::with(['pledge.member'])
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get();

        $ledgerEntries = collect();

        foreach ($tithes as $tithe) {
            if ($tithe->isAdjusted()) continue;
            $voided = $tithe->isVoided();
            $ledgerEntries->push([
                'date'          => $tithe->payment_date,
                'particular'    => $tithe->particular_name,
                'tithe'         => $voided ? 0.0 : (float) $tithe->amount,
                'offering'      => 0.0,
                'donation'      => 0.0,
                'pledge'        => 0.0,
                'special'       => 0.0,
                'reference'     => $tithe->receipt_number ?? $tithe->reference_number,
                'ledger_status' => $tithe->ledger_status ?? 'active',
                'is_adjustment' => (bool) $tithe->is_adjustment,
            ]);
        }

        foreach ($offerings as $offering) {
            if ($offering->isAdjusted()) continue;
            $voided    = $offering->isVoided();
            $type      = $offering->incomeCategory?->type ?? 'offering';
            $isSpecial = $type === 'special';
            $ledgerEntries->push([
                'date'          => $offering->payment_date,
                'particular'    => $offering->particular_name,
                'tithe'         => 0.0,
                'offering'      => (!$voided && !$isSpecial) ? (float) $offering->amount : 0.0,
                'donation'      => 0.0,
                'pledge'        => 0.0,
                'special'       => (!$voided && $isSpecial) ? (float) $offering->amount : 0.0,
                'reference'     => $offering->reference_number,
                'ledger_status' => $offering->ledger_status ?? 'active',
                'is_adjustment' => (bool) $offering->is_adjustment,
            ]);
        }

        foreach ($donations as $donation) {
            if ($donation->isAdjusted()) continue;
            $voided = $donation->isVoided();
            $ledgerEntries->push([
                'date'          => $donation->payment_date,
                'particular'    => $donation->purpose ?? 'Donation',
                'tithe'         => 0.0,
                'offering'      => 0.0,
                'donation'      => $voided ? 0.0 : (float) $donation->amount,
                'pledge'        => 0.0,
                'special'       => 0.0,
                'reference'     => $donation->reference_number,
                'ledger_status' => $donation->ledger_status ?? 'active',
                'is_adjustment' => (bool) $donation->is_adjustment,
            ]);
        }

        foreach ($pledgePayments as $payment) {
            $pledge     = $payment->pledge;
            $particular = ($pledge->purpose ?? 'Pledge Payment')
                        . ($pledge->member ? ' — ' . $pledge->member->full_name : '');
            $ledgerEntries->push([
                'date'          => $payment->payment_date,
                'particular'    => $particular,
                'tithe'         => 0.0,
                'offering'      => 0.0,
                'donation'      => 0.0,
                'pledge'        => (float) $payment->amount,
                'special'       => 0.0,
                'reference'     => $payment->receipt_number ?? $payment->reference_number,
                'ledger_status' => 'active',
                'is_adjustment' => false,
            ]);
        }

        $groupedEntries = $ledgerEntries
            ->sortBy('date')
            ->groupBy(fn($e) => Carbon::parse($e['date'])->format('Y-m-d'));

        $totals = [
            'tithe'       => $ledgerEntries->sum('tithe'),
            'offering'    => $ledgerEntries->sum('offering'),
            'donation'    => $ledgerEntries->sum('donation'),
            'pledge'      => $ledgerEntries->sum('pledge'),
            'special'     => $ledgerEntries->sum('special'),
            'grand_total' => 0.0,
        ];
        $totals['grand_total'] = $totals['tithe'] + $totals['offering'] + $totals['donation'] + $totals['pledge'] + $totals['special'];

        $broughtForward = $this->getTotalBefore($start);
        $carriedForward = $broughtForward + $totals['grand_total'];

        return compact('groupedEntries', 'totals', 'broughtForward', 'carriedForward');
    }

    private function getTotalBefore(Carbon $date): float
    {
        // Only count entries that are effectively active (not voided, not superseded by an adjustment)
        $activeOnly = fn($q) => $q->where(function ($sub) {
            $sub->where('ledger_status', 'active')->orWhereNull('ledger_status');
        });

        $tithes        = Tithe::where('payment_date', '<', $date->toDateString())->where($activeOnly)->sum('amount');
        $offerings     = Offering::where('payment_date', '<', $date->toDateString())->where($activeOnly)->sum('amount');
        $donations     = Donation::where('payment_date', '<', $date->toDateString())->where($activeOnly)->sum('amount');
        $pledgePayments = PledgePayment::where('payment_date', '<', $date->toDateString())->sum('amount');

        return (float) $tithes + (float) $offerings + (float) $donations + (float) $pledgePayments;
    }
}
