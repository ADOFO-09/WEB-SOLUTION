<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\WelfareBenefit;
use App\Models\WelfareBenefitExpense;
use App\Helpers\SettingHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class WelfareBenefitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:welfare.benefits.manage', only: ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = WelfareBenefit::with(['member', 'recordedBy', 'welfareBenefitExpenses']);

        if ($request->filled('year')) {
            $query->byYear($request->year);
        }

        if ($request->filled('purpose')) {
            $query->byPurpose($request->purpose);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('benefactor_name', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderByDesc('benefit_date');
        $benefits = $query->paginate(SettingHelper::perPage())->withQueryString();
        $purposes = WelfareBenefit::PURPOSES;

        return view('admin.finance.welfare.benefits.index', compact('benefits', 'purposes'));
    }

    public function create()
    {
        $members  = Member::active()->orderBy('first_name')->get();
        $purposes = WelfareBenefit::PURPOSES;
        return view('admin.finance.welfare.benefits.create', compact('members', 'purposes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'       => 'nullable|exists:members,id',
            'benefactor_name' => 'required|string|max:255',
            'purpose'         => 'required|in:marriage,funeral,childbirth,other',
            'benefit_date'    => 'required|date',
            'amount'          => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'approved_by'     => 'nullable|exists:users,id',
        ]);

        $validated['benefit_year'] = date('Y', strtotime($validated['benefit_date']));
        $validated['recorded_by']  = auth()->user()?->id;

        // Validate expense lines
        $expenses = [];
        foreach ($request->input('expenses', []) as $item) {
            if (empty($item['description']) && empty($item['amount'])) {
                continue; // skip fully empty rows
            }
            $expenses[] = $item;
        }

        $request->validate([
            'expenses.*.description' => 'required|string|max:255',
            'expenses.*.amount'      => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($validated, $expenses) {
            $benefit = WelfareBenefit::create($validated);

            foreach ($expenses as $item) {
                WelfareBenefitExpense::create([
                    'welfare_benefit_id' => $benefit->id,
                    'description'        => $item['description'],
                    'amount'             => $item['amount'],
                ]);
            }
        });

        return redirect()->route('admin.welfare.benefits.index')
            ->with('success', 'Welfare benefit recorded successfully.');
    }

    public function show(WelfareBenefit $welfareBenefit)
    {
        $welfareBenefit->load(['member', 'approvedBy', 'recordedBy', 'welfareBenefitExpenses']);
        return view('admin.finance.welfare.benefits.show', compact('welfareBenefit'));
    }

    public function edit(WelfareBenefit $welfareBenefit)
    {
        $welfareBenefit->load('welfareBenefitExpenses');
        $members  = Member::active()->orderBy('first_name')->get();
        $purposes = WelfareBenefit::PURPOSES;
        return view('admin.finance.welfare.benefits.create', compact('welfareBenefit', 'members', 'purposes'));
    }

    public function update(Request $request, WelfareBenefit $welfareBenefit)
    {
        $validated = $request->validate([
            'member_id'       => 'nullable|exists:members,id',
            'benefactor_name' => 'required|string|max:255',
            'purpose'         => 'required|in:marriage,funeral,childbirth,other',
            'benefit_date'    => 'required|date',
            'amount'          => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'approved_by'     => 'nullable|exists:users,id',
        ]);

        $validated['benefit_year'] = date('Y', strtotime($validated['benefit_date']));

        // Validate expense lines
        $expenses = [];
        foreach ($request->input('expenses', []) as $item) {
            if (empty($item['description']) && empty($item['amount'])) {
                continue;
            }
            $expenses[] = $item;
        }

        $request->validate([
            'expenses.*.description' => 'required|string|max:255',
            'expenses.*.amount'      => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($welfareBenefit, $validated, $expenses) {
            $welfareBenefit->update($validated);

            // Sync expenses: delete all existing, create new
            $welfareBenefit->welfareBenefitExpenses()->delete();
            foreach ($expenses as $item) {
                WelfareBenefitExpense::create([
                    'welfare_benefit_id' => $welfareBenefit->id,
                    'description'        => $item['description'],
                    'amount'             => $item['amount'],
                ]);
            }
        });

        return redirect()->route('admin.welfare.benefits.show', $welfareBenefit)
            ->with('success', 'Welfare benefit updated successfully.');
    }

    public function destroy(WelfareBenefit $welfareBenefit)
    {
        $welfareBenefit->delete();

        return redirect()->route('admin.welfare.benefits.index')
            ->with('success', 'Welfare benefit deleted successfully.');
    }
}
