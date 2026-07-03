<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuneralRate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FuneralRateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:funeral.rates.manage', only: ['index', 'store']),
        ];
    }

    public function index()
    {
        $rates = FuneralRate::with('createdBy')
            ->orderByDesc('effective_from')
            ->get();

        return view('admin.finance.funeral.rates.index', compact('rates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'effective_from' => 'required|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->user()?->id;

        FuneralRate::create($validated);

        return redirect()->route('admin.funeral.rates.index')
            ->with('success', 'Funeral due rate added successfully.');
    }
}
