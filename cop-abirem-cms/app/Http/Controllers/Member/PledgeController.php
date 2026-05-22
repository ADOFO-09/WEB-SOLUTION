<?php

namespace App\Http\Controllers\Member;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\Pledge;
use App\Models\PledgePayment;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
    /**
     * Display member's pledges.
     */
    public function index(Request $request)
    {
        $member = $request->user()->member;
        $status = $request->input('status', 'all');
        
        $query = Pledge::where('member_id', $member->id)
            ->with(['project', 'incomeCategory']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $pledges = $query->orderBy('created_at', 'desc')
            ->paginate(SettingHelper::perPage())
            ->withQueryString();
        
        $summary = [
            'total_pledged' => Pledge::where('member_id', $member->id)->sum('total_amount'),
            'total_paid' => Pledge::where('member_id', $member->id)->sum('amount_paid'),
            'active_count' => Pledge::where('member_id', $member->id)->where('status', 'active')->count(),
            'completed_count' => Pledge::where('member_id', $member->id)->where('status', 'completed')->count(),
        ];
        $summary['remaining'] = $summary['total_pledged'] - $summary['total_paid'];
        
        return view('member.pledges.index', compact('pledges', 'summary', 'status'));
    }
    
    /**
     * Display pledge details with payment history.
     */
    public function show(Request $request, Pledge $pledge)
    {
        $member = $request->user()->member;
        
        // Ensure pledge belongs to member
        if ($pledge->member_id !== $member->id) {
            abort(403, 'Unauthorized access to pledge.');
        }
        
        $pledge->load(['project', 'incomeCategory', 'payments']);
        
        $payments = PledgePayment::where('pledge_id', $pledge->id)
            ->orderBy('payment_date', 'desc')
            ->get();
        
        $progressPercent = $pledge->total_amount > 0 
            ? min(100, round(($pledge->amount_paid / $pledge->total_amount) * 100))
            : 0;
        
        return view('member.pledges.show', compact('pledge', 'payments', 'progressPercent'));
    }
}
