<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\FuneralContribution;
use App\Helpers\SettingHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FuneralContributionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:funeral.view', only: ['index', 'show']),
            new Middleware('permission:funeral.create', only: ['create', 'store']),
            new Middleware('permission:funeral.edit', only: ['edit', 'update']),
            new Middleware('permission:funeral.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = FuneralContribution::with(['member', 'receivedBy']);

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('period_month')) {
            $query->where('period_month', $request->period_month);
        }

        if ($request->filled('period_year')) {
            $query->where('period_year', $request->period_year);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('member_id', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderByDesc('payment_date');
        $contributions = $query->paginate(SettingHelper::perPage())->withQueryString();

        $stats = [
            'total_this_year'  => FuneralContribution::where('period_year', date('Y'))->sum('amount'),
            'this_month_count' => FuneralContribution::where('period_year', date('Y'))->where('period_month', date('n'))->count(),
            'unique_members'   => FuneralContribution::where('period_year', date('Y'))->distinct('member_id')->count('member_id'),
        ];

        $members = Member::active()->orderBy('first_name')->get();

        return view('admin.finance.funeral.contributions.index', compact('contributions', 'stats', 'members'));
    }

    public function create()
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.finance.funeral.contributions.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'amount'         => 'required|numeric|min:0.01',
            'period_month'   => 'required|integer|min:1|max:12',
            'period_year'    => 'required|integer|min:2000|max:2099',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|in:cash,mobile_money,bank_transfer,cheque',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $validated['received_by'] = auth()->user()?->id;

        $contribution = FuneralContribution::create($validated);

        return redirect()->route('admin.funeral.contributions.show', $contribution)
            ->with('success', 'Funeral due contribution recorded successfully. Ref: ' . $contribution->reference_number);
    }

    public function show(FuneralContribution $funeralContribution)
    {
        $funeralContribution->load(['member', 'receivedBy']);
        return view('admin.finance.funeral.contributions.show', compact('funeralContribution'));
    }

    public function edit(FuneralContribution $funeralContribution)
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.finance.funeral.contributions.create', compact('funeralContribution', 'members'));
    }

    public function update(Request $request, FuneralContribution $funeralContribution)
    {
        $validated = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'amount'         => 'required|numeric|min:0.01',
            'period_month'   => 'required|integer|min:1|max:12',
            'period_year'    => 'required|integer|min:2000|max:2099',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|in:cash,mobile_money,bank_transfer,cheque',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $funeralContribution->update($validated);

        return redirect()->route('admin.funeral.contributions.show', $funeralContribution)
            ->with('success', 'Funeral due contribution updated successfully.');
    }

    public function destroy(FuneralContribution $funeralContribution)
    {
        $funeralContribution->delete();

        return redirect()->route('admin.funeral.contributions.index')
            ->with('success', 'Funeral due contribution deleted successfully.');
    }
}
