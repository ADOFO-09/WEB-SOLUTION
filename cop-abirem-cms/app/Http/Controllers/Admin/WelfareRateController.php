<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelfareRate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WelfareRateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:welfare.rates.manage', only: ['index', 'store']),
        ];
    }

    public function index()
    {
        $rates = WelfareRate::with('createdBy')
            ->orderByDesc('effective_from')
            ->get();

        return view('admin.finance.welfare.rates.index', compact('rates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'effective_from' => 'required|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->user()?->id;
        $validated['is_current'] = $request->boolean('is_current');

        if ($validated['is_current']) {
            WelfareRate::query()->update(['is_current' => false]);
        }

        WelfareRate::create($validated);

        return redirect()->route('admin.welfare.rates.index')
            ->with('success', 'Welfare due rate added successfully.');
    }

    public function setCurrent(WelfareRate $rate)
    {
        WelfareRate::query()->update(['is_current' => false]);
        $rate->update(['is_current' => true]);

        return redirect()->route('admin.welfare.rates.index')
            ->with('success', 'Rate marked as current.');
    }
}
