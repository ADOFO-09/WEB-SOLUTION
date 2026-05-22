<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMaintenance;
use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Helpers\SettingHelper;

class AssetController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:assets.view',   only: ['index', 'show']),
            new Middleware('permission:assets.create', only: ['create', 'store']),
            new Middleware('permission:assets.edit',   only: ['edit', 'update', 'storeMaintenance']),
            new Middleware('permission:assets.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Asset::with(['category', 'assignedToMinistry']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('asset_code', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition_status', $request->condition);
        }

        $assets     = $query->orderBy('name')->paginate(SettingHelper::perPage())->withQueryString();
        $categories = AssetCategory::orderBy('name')->get();

        $stats = [
            'total'          => Asset::count(),
            'active'         => Asset::where('status', 'active')->count(),
            'maintenance'    => Asset::where('status', 'maintenance')->count(),
            'total_value'    => Asset::where('status', 'active')->sum('current_value'),
        ];

        return view('admin.assets.index', compact('assets', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();
        $ministries = Ministry::orderBy('name')->get();

        return view('admin.assets.create', compact('categories', 'ministries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'category_id'             => 'required|exists:asset_categories,id',
            'description'             => 'nullable|string',
            'serial_number'           => 'nullable|string|max:100',
            'purchase_date'           => 'required|date',
            'purchase_price'          => 'required|numeric|min:0',
            'current_value'           => 'required|numeric|min:0',
            'supplier'                => 'nullable|string|max:255',
            'warranty_expiry'         => 'nullable|date|after:purchase_date',
            'location'                => 'nullable|string|max:255',
            'assigned_to_ministry_id' => 'nullable|exists:ministries,id',
            'condition_status'        => 'required|in:excellent,good,fair,poor,damaged,unusable',
            'status'                  => 'required|in:active,maintenance,disposed,lost,stolen',
            'notes'                   => 'nullable|string',
        ]);

        $validated['asset_code'] = $this->generateAssetCode();
        $validated['created_by'] = auth()->id();

        $asset = Asset::create($validated);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', "Asset '{$asset->name}' created successfully.");
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'assignedToMinistry', 'maintenanceRecords' => function ($q) {
            $q->orderByDesc('maintenance_date');
        }]);

        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::orderBy('name')->get();
        $ministries = Ministry::orderBy('name')->get();

        return view('admin.assets.edit', compact('asset', 'categories', 'ministries'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'category_id'             => 'required|exists:asset_categories,id',
            'description'             => 'nullable|string',
            'serial_number'           => 'nullable|string|max:100',
            'purchase_date'           => 'required|date',
            'purchase_price'          => 'required|numeric|min:0',
            'current_value'           => 'required|numeric|min:0',
            'supplier'                => 'nullable|string|max:255',
            'warranty_expiry'         => 'nullable|date',
            'location'                => 'nullable|string|max:255',
            'assigned_to_ministry_id' => 'nullable|exists:ministries,id',
            'condition_status'        => 'required|in:excellent,good,fair,poor,damaged,unusable',
            'status'                  => 'required|in:active,maintenance,disposed,lost,stolen',
            'disposal_date'           => 'nullable|date',
            'notes'                   => 'nullable|string',
        ]);

        $asset->update($validated);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', "Asset '{$asset->name}' updated successfully.");
    }

    public function destroy(Asset $asset)
    {
        $name = $asset->name;
        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', "Asset '{$name}' deleted.");
    }

    public function storeMaintenance(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'maintenance_type'      => 'required|in:repair,service,inspection,upgrade,cleaning',
            'description'           => 'required|string',
            'cost'                  => 'required|numeric|min:0',
            'maintenance_date'      => 'required|date',
            'next_maintenance_date' => 'nullable|date|after:maintenance_date',
            'performed_by'          => 'nullable|string|max:255',
            'vendor'                => 'nullable|string|max:255',
            'notes'                 => 'nullable|string',
        ]);

        $validated['asset_id']   = $asset->id;
        $validated['created_by'] = auth()->id();

        AssetMaintenance::create($validated);

        if ($asset->status === 'active' && $validated['maintenance_type'] === 'repair') {
            $asset->update(['status' => 'maintenance']);
        }

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', 'Maintenance record added.');
    }

    private function generateAssetCode(): string
    {
        $prefix = 'AST';
        $year   = date('Y');
        $last   = Asset::withTrashed()
            ->where('asset_code', 'like', "{$prefix}{$year}%")
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last && preg_match('/(\d{4})$/', $last->asset_code, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return $prefix . $year . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
