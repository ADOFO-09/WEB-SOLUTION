<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\FuneralBenefit;
use App\Models\FuneralBenefitExpense;
use App\Helpers\SettingHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class FuneralBenefitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:funeral.benefits.manage', only: ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = FuneralBenefit::with(['member', 'recordedBy', 'funeralBenefitExpenses']);

        if ($request->filled('year')) {
            $query->byYear($request->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('benefactor_name', 'like', "%{$search}%")
                  ->orWhere('deceased_name', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderByDesc('funeral_date');
        $benefits = $query->paginate(SettingHelper::perPage())->withQueryString();

        return view('admin.finance.funeral.benefits.index', compact('benefits'));
    }

    public function create()
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.finance.funeral.benefits.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'       => 'nullable|exists:members,id',
            'benefactor_name' => 'required|string|max:255',
            'deceased_name'   => 'nullable|string|max:255',
            'funeral_date'    => 'required|date',
            'venue'           => 'nullable|string|max:255',
            'amount_donated'  => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'approved_by'     => 'nullable|exists:users,id',
        ]);

        $validated['funeral_year'] = date('Y', strtotime($validated['funeral_date']));
        $validated['recorded_by']  = auth()->user()?->id;

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

        DB::transaction(function () use ($validated, $expenses) {
            $benefit = FuneralBenefit::create($validated);

            foreach ($expenses as $item) {
                FuneralBenefitExpense::create([
                    'funeral_benefit_id' => $benefit->id,
                    'description'        => $item['description'],
                    'amount'             => $item['amount'],
                ]);
            }
        });

        return redirect()->route('admin.funeral.benefits.index')
            ->with('success', 'Funeral benefit recorded successfully.');
    }

    public function show(FuneralBenefit $funeralBenefit)
    {
        $funeralBenefit->load(['member', 'approvedBy', 'recordedBy', 'funeralBenefitExpenses']);
        return view('admin.finance.funeral.benefits.show', compact('funeralBenefit'));
    }

    public function edit(FuneralBenefit $funeralBenefit)
    {
        $funeralBenefit->load('funeralBenefitExpenses');
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.finance.funeral.benefits.create', compact('funeralBenefit', 'members'));
    }

    public function update(Request $request, FuneralBenefit $funeralBenefit)
    {
        $validated = $request->validate([
            'member_id'       => 'nullable|exists:members,id',
            'benefactor_name' => 'required|string|max:255',
            'deceased_name'   => 'nullable|string|max:255',
            'funeral_date'    => 'required|date',
            'venue'           => 'nullable|string|max:255',
            'amount_donated'  => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'approved_by'     => 'nullable|exists:users,id',
        ]);

        $validated['funeral_year'] = date('Y', strtotime($validated['funeral_date']));

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

        DB::transaction(function () use ($funeralBenefit, $validated, $expenses) {
            $funeralBenefit->update($validated);

            // Sync expenses: delete all existing, create new
            $funeralBenefit->funeralBenefitExpenses()->delete();
            foreach ($expenses as $item) {
                FuneralBenefitExpense::create([
                    'funeral_benefit_id' => $funeralBenefit->id,
                    'description'        => $item['description'],
                    'amount'             => $item['amount'],
                ]);
            }
        });

        return redirect()->route('admin.funeral.benefits.show', $funeralBenefit)
            ->with('success', 'Funeral benefit updated successfully.');
    }

    public function destroy(FuneralBenefit $funeralBenefit)
    {
        $funeralBenefit->delete();

        return redirect()->route('admin.funeral.benefits.index')
            ->with('success', 'Funeral benefit deleted successfully.');
    }
}
