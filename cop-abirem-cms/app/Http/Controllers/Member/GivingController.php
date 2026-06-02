<?php

namespace App\Http\Controllers\Member;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\Tithe;
use App\Models\Offering;
use App\Models\Donation;
use App\Models\PledgePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GivingController extends Controller
{
    /**
     * Display giving overview.
     */
    public function index(Request $request)
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        
        // Yearly summary
        $summary = [
            'tithes' => Tithe::where('member_id', $member->id)
                ->whereYear('payment_date', $year)
                ->sum('amount'),
            'offerings' => Offering::where('member_id', $member->id)
                ->whereYear('payment_date', $year)
                ->sum('amount'),
            'donations' => Donation::where('member_id', $member->id)
                ->whereYear('payment_date', $year)
                ->where('donation_type', 'cash')
                ->sum('amount'),
        ];
        $summary['total'] = $summary['tithes'] + $summary['offerings'] + $summary['donations'];
        
        // Monthly breakdown
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $tithes = Tithe::where('member_id', $member->id)
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->sum('amount');
            $offerings = Offering::where('member_id', $member->id)
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->sum('amount');
            $donations = Donation::where('member_id', $member->id)
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->where('donation_type', 'cash')
                ->sum('amount');
                
            $monthlyData[] = [
                'month' => Carbon::create($year, $m, 1)->format('M'),
                'tithes' => $tithes,
                'offerings' => $offerings,
                'donations' => $donations,
                'total' => $tithes + $offerings + $donations,
            ];
        }
        
        // Available years for filter
        $years = range(now()->year, now()->year - 5);
        
        return view('member.giving.index', compact('summary', 'monthlyData', 'year', 'years'));
    }
    
    /**
     * Display tithe history.
     */
    public function tithes(Request $request)
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        
        $tithes = Tithe::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date', 'desc')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();
        
        $total = Tithe::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->sum('amount');
        
        $years = range(now()->year, now()->year - 5);
        
        return view('member.giving.tithes', compact('tithes', 'total', 'year', 'years'));
    }
    
    /**
     * Display offering history.
     */
    public function offerings(Request $request)
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        
        $offerings = Offering::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->with('offeringType')
            ->orderBy('payment_date', 'desc')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();
        
        $total = Offering::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->sum('amount');
        
        $years = range(now()->year, now()->year - 5);
        
        return view('member.giving.offerings', compact('offerings', 'total', 'year', 'years'));
    }
    
    /**
     * Display donation history.
     */
    public function donations(Request $request)
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        
        $donations = Donation::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->with('project')
            ->orderBy('payment_date', 'desc')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();
        
        $total = Donation::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->where('donation_type', 'cash')
            ->sum('amount');
        
        $years = range(now()->year, now()->year - 5);
        
        return view('member.giving.donations', compact('donations', 'total', 'year', 'years'));
    }
    
    /**
     * Display giving statement.
     */
    public function statement(Request $request)
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        
        $tithes = Tithe::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date')
            ->get();
            
        $offerings = Offering::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->with('offeringType')
            ->orderBy('payment_date')
            ->get();
            
        $donations = Donation::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->where('donation_type', 'cash')
            ->with('project')
            ->orderBy('payment_date')
            ->get();
        
        $totals = [
            'tithes' => $tithes->sum('amount'),
            'offerings' => $offerings->sum('amount'),
            'donations' => $donations->sum('amount'),
            'grand_total' => $tithes->sum('amount') + $offerings->sum('amount') + $donations->sum('amount'),
        ];
        
        $years = range(now()->year, now()->year - 5);
        
        return view('member.giving.statement', compact('member', 'tithes', 'offerings', 'donations', 'totals', 'year', 'years'));
    }
    
    /**
     * Download giving statement as PDF/CSV.
     */
    public function downloadStatement(Request $request): StreamedResponse
    {
        $member = $request->user()->member;
        $year = $request->input('year', now()->year);
        
        $tithes = Tithe::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date')
            ->get();
            
        $offerings = Offering::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date')
            ->get();
            
        $donations = Donation::where('member_id', $member->id)
            ->whereYear('payment_date', $year)
            ->where('donation_type', 'cash')
            ->orderBy('payment_date')
            ->get();
        
        $filename = "giving-statement-{$year}-{$member->member_id}.csv";
        
        return response()->streamDownload(function () use ($tithes, $offerings, $donations, $member, $year) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, ['GIVING STATEMENT - ' . $year]);
            fputcsv($handle, ['Member: ' . $member->full_name . ' (' . $member->member_id . ')']);
            fputcsv($handle, ['Generated: ' . now()->format(SettingHelper::dateFormat())]);
            fputcsv($handle, []);
            
            // Tithes
            fputcsv($handle, ['TITHES']);
            fputcsv($handle, ['Date', 'Reference', 'Amount', 'Payment Method']);
            foreach ($tithes as $tithe) {
                fputcsv($handle, [
                    $tithe->payment_date->format('Y-m-d'),
                    $tithe->reference_number,
                    number_format($tithe->amount, 2),
                    ucfirst($tithe->payment_method),
                ]);
            }
            fputcsv($handle, ['', '', 'Total: ' . SettingHelper::currencySymbol() . ' ' . number_format($tithes->sum('amount'), 2), '']);
            fputcsv($handle, []);
            
            // Offerings
            fputcsv($handle, ['OFFERINGS']);
            fputcsv($handle, ['Date', 'Reference', 'Amount', 'Payment Method']);
            foreach ($offerings as $offering) {
                fputcsv($handle, [
                    $offering->payment_date->format('Y-m-d'),
                    $offering->reference_number,
                    number_format($offering->amount, 2),
                    ucfirst($offering->payment_method),
                ]);
            }
            fputcsv($handle, ['', '', 'Total: ' . SettingHelper::currencySymbol() . ' ' . number_format($offerings->sum('amount'), 2), '']);
            fputcsv($handle, []);
            
            // Donations
            fputcsv($handle, ['DONATIONS']);
            fputcsv($handle, ['Date', 'Reference', 'Amount', 'Project']);
            foreach ($donations as $donation) {
                fputcsv($handle, [
                    $donation->payment_date->format('Y-m-d'),
                    $donation->receipt_number ?? $donation->reference_number,
                    number_format($donation->amount, 2),
                    $donation->project?->name ?? 'General',
                ]);
            }
            fputcsv($handle, ['', '', 'Total: ' . SettingHelper::currencySymbol() . ' ' . number_format($donations->sum('amount'), 2), '']);
            fputcsv($handle, []);
            
            // Grand Total
            $grandTotal = $tithes->sum('amount') + $offerings->sum('amount') + $donations->sum('amount');
            fputcsv($handle, ['GRAND TOTAL', '', SettingHelper::currencySymbol() . ' ' . number_format($grandTotal, 2), '']);
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Consolidated contributions report (tithes, offerings, donations, pledge payments).
     */
    public function contributionsReport(Request $request)
    {
        $member = $request->user()->member;
        $year   = (int) $request->input('year', now()->year);
        $month  = $request->input('month');

        if ($month) {
            $start       = Carbon::create($year, $month)->startOfMonth();
            $end         = Carbon::create($year, $month)->endOfMonth();
            $periodLabel = Carbon::create($year, $month)->format('F Y');
        } else {
            $start       = Carbon::create($year)->startOfYear();
            $end         = Carbon::create($year)->endOfYear();
            $periodLabel = "Year $year";
        }

        $tithes = Tithe::where('member_id', $member->id)
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date', 'desc')
            ->get();

        $offerings = Offering::where('member_id', $member->id)
            ->whereBetween('payment_date', [$start, $end])
            ->with('offeringType')
            ->orderBy('payment_date', 'desc')
            ->get();

        $donations = Donation::where('member_id', $member->id)
            ->where('donation_type', 'cash')
            ->whereBetween('payment_date', [$start, $end])
            ->with('project')
            ->orderBy('payment_date', 'desc')
            ->get();

        $pledgePayments = PledgePayment::whereHas('pledge', fn ($q) => $q->where('member_id', $member->id))
            ->whereBetween('payment_date', [$start, $end])
            ->with('pledge')
            ->orderBy('payment_date', 'desc')
            ->get();

        $totals = [
            'tithes'   => $tithes->sum('amount'),
            'offerings'=> $offerings->sum('amount'),
            'donations'=> $donations->sum('amount'),
            'pledges'  => $pledgePayments->sum('amount'),
        ];
        $totals['grand'] = array_sum($totals);

        // Previous period comparison
        if ($month) {
            $prevStart = Carbon::create($year, $month)->subMonth()->startOfMonth();
            $prevEnd   = Carbon::create($year, $month)->subMonth()->endOfMonth();
        } else {
            $prevStart = Carbon::create($year - 1)->startOfYear();
            $prevEnd   = Carbon::create($year - 1)->endOfYear();
        }

        $prevTotal = Tithe::where('member_id', $member->id)->whereBetween('payment_date', [$prevStart, $prevEnd])->sum('amount')
            + Offering::where('member_id', $member->id)->whereBetween('payment_date', [$prevStart, $prevEnd])->sum('amount')
            + Donation::where('member_id', $member->id)->where('donation_type', 'cash')->whereBetween('payment_date', [$prevStart, $prevEnd])->sum('amount')
            + PledgePayment::whereHas('pledge', fn ($q) => $q->where('member_id', $member->id))->whereBetween('payment_date', [$prevStart, $prevEnd])->sum('amount');

        $change = $prevTotal > 0
            ? round((($totals['grand'] - $prevTotal) / $prevTotal) * 100, 1)
            : null;

        // Monthly breakdown (full-year view only)
        $monthlyBreakdown = [];
        if (!$month) {
            for ($m = 1; $m <= 12; $m++) {
                $monthlyBreakdown[] = [
                    'month'     => Carbon::create($year, $m)->format('M'),
                    'tithes'    => Tithe::where('member_id', $member->id)->whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                    'offerings' => Offering::where('member_id', $member->id)->whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                    'donations' => Donation::where('member_id', $member->id)->where('donation_type', 'cash')->whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                    'pledges'   => PledgePayment::whereHas('pledge', fn ($q) => $q->where('member_id', $member->id))->whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                ];
            }
        }

        $years = range(now()->year, now()->year - 5);

        return view('member.reports.contributions', compact(
            'member', 'tithes', 'offerings', 'donations', 'pledgePayments',
            'totals', 'prevTotal', 'change', 'periodLabel',
            'year', 'month', 'years', 'monthlyBreakdown'
        ));
    }
}
