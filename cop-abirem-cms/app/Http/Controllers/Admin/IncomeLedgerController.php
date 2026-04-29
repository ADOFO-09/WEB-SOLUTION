<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Offering;
use App\Models\Tithe;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncomeLedgerController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

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

        $ledgerEntries = collect();

        foreach ($tithes as $tithe) {
            $voided = $tithe->isVoided();
            $ledgerEntries->push([
                'date'          => $tithe->payment_date,
                'particular'    => $tithe->particular_name,
                'tithe'         => $voided ? 0.0 : (float) $tithe->amount,
                'offering'      => 0.0,
                'donation'      => 0.0,
                'special'       => 0.0,
                'reference'     => $tithe->receipt_number ?? $tithe->reference_number,
                'ledger_status' => $tithe->ledger_status ?? 'active',
                'is_adjustment' => (bool) $tithe->is_adjustment,
            ]);
        }

        foreach ($offerings as $offering) {
            $voided    = $offering->isVoided();
            $type      = $offering->incomeCategory?->type ?? 'offering';
            $isSpecial = $type === 'special';
            $ledgerEntries->push([
                'date'          => $offering->payment_date,
                'particular'    => $offering->particular_name,
                'tithe'         => 0.0,
                'offering'      => (!$voided && !$isSpecial) ? (float) $offering->amount : 0.0,
                'donation'      => 0.0,
                'special'       => (!$voided && $isSpecial) ? (float) $offering->amount : 0.0,
                'reference'     => $offering->reference_number,
                'ledger_status' => $offering->ledger_status ?? 'active',
                'is_adjustment' => (bool) $offering->is_adjustment,
            ]);
        }

        foreach ($donations as $donation) {
            $voided = $donation->isVoided();
            $ledgerEntries->push([
                'date'          => $donation->payment_date,
                'particular'    => $donation->purpose ?? 'Donation',
                'tithe'         => 0.0,
                'offering'      => 0.0,
                'donation'      => $voided ? 0.0 : (float) $donation->amount,
                'special'       => 0.0,
                'reference'     => $donation->reference_number,
                'ledger_status' => $donation->ledger_status ?? 'active',
                'is_adjustment' => (bool) $donation->is_adjustment,
            ]);
        }

        // Sort then group by date
        $groupedEntries = $ledgerEntries
            ->sortBy('date')
            ->groupBy(fn($e) => Carbon::parse($e['date'])->format('Y-m-d'));

        $totals = [
            'tithe'       => $ledgerEntries->sum('tithe'),
            'offering'    => $ledgerEntries->sum('offering'),
            'donation'    => $ledgerEntries->sum('donation'),
            'special'     => $ledgerEntries->sum('special'),
            'grand_total' => 0.0,
        ];
        $totals['grand_total'] = $totals['tithe'] + $totals['offering'] + $totals['donation'] + $totals['special'];

        $broughtForward = $this->getTotalBefore($start);
        $carriedForward = $broughtForward + $totals['grand_total'];

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::createFromDate($year, $m, 1)->format('F');
        }

        return view('admin.reports.income-ledger', compact(
            'groupedEntries', 'totals', 'month', 'year',
            'broughtForward', 'carriedForward', 'months'
        ));
    }

    private function getTotalBefore(Carbon $date): float
    {
        $tithes    = Tithe::where('payment_date', '<', $date->toDateString())->sum('amount');
        $offerings = Offering::where('payment_date', '<', $date->toDateString())->sum('amount');
        $donations = Donation::where('payment_date', '<', $date->toDateString())->sum('amount');

        return (float) $tithes + (float) $offerings + (float) $donations;
    }
}
