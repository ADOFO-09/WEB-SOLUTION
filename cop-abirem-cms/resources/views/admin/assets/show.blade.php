@extends('layouts.admin')

@section('title', $asset->name)

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.assets.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $asset->name }}</h1>
                <p class="text-sm text-gray-500 font-mono">{{ $asset->asset_code }}</p>
            </div>
        </div>
        @can('assets.edit')
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.assets.edit', $asset) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white"
               style="background-color:#1e3a5f;">
                Edit Asset
            </a>
        </div>
        @endcan
    </div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">{{ session('success') }}</div>
@endif

@php
    $statusColors = [
        'active'      => 'bg-green-100 text-green-800',
        'maintenance' => 'bg-yellow-100 text-yellow-800',
        'disposed'    => 'bg-gray-100 text-gray-700',
        'lost'        => 'bg-red-100 text-red-800',
        'stolen'      => 'bg-red-100 text-red-800',
    ];
    $conditionColors = [
        'excellent' => 'bg-green-100 text-green-800',
        'good'      => 'bg-blue-100 text-blue-800',
        'fair'      => 'bg-yellow-100 text-yellow-800',
        'poor'      => 'bg-orange-100 text-orange-800',
        'damaged'   => 'bg-red-100 text-red-800',
        'unusable'  => 'bg-gray-100 text-gray-700',
    ];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Asset Details sidebar --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Asset Details</h3>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$asset->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Condition</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conditionColors[$asset->condition_status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($asset->condition_status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Category</dt>
                    <dd class="font-medium text-gray-900">{{ $asset->category->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Serial No.</dt>
                    <dd class="font-mono text-xs text-gray-900">{{ $asset->serial_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Purchase Date</dt>
                    <dd class="font-medium text-gray-900">{{ $asset->purchase_date->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Purchase Price</dt>
                    <dd class="font-medium text-gray-900">{{ $currencySymbol }}{{ number_format($asset->purchase_price, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Current Value</dt>
                    <dd class="font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($asset->current_value, 2) }}</dd>
                </div>
                @if($asset->supplier)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Supplier</dt>
                    <dd class="font-medium text-gray-900">{{ $asset->supplier }}</dd>
                </div>
                @endif
                @if($asset->warranty_expiry)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Warranty Expiry</dt>
                    <dd class="font-medium {{ $asset->warranty_expiry->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $asset->warranty_expiry->format('M d, Y') }}
                    </dd>
                </div>
                @endif
                @if($asset->location)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Location</dt>
                    <dd class="font-medium text-gray-900">{{ $asset->location }}</dd>
                </div>
                @endif
                @if($asset->assignedToMinistry)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Assigned To</dt>
                    <dd class="font-medium text-gray-900">{{ $asset->assignedToMinistry->name }}</dd>
                </div>
                @endif
            </dl>

            @if($asset->description || $asset->notes)
            <div class="pt-3 border-t border-gray-100 mt-3 space-y-2">
                @if($asset->description)
                <p class="text-sm text-gray-600">{{ $asset->description }}</p>
                @endif
                @if($asset->notes)
                <p class="text-xs text-gray-500 italic">{{ $asset->notes }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Right column: Maintenance --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Log maintenance form --}}
        @can('assets.edit')
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Log Maintenance</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.assets.maintenance.store', $asset) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type *</label>
                            <select name="maintenance_type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Select type</option>
                                <option value="repair">Repair</option>
                                <option value="service">Service</option>
                                <option value="inspection">Inspection</option>
                                <option value="upgrade">Upgrade</option>
                                <option value="cleaning">Cleaning</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date *</label>
                            <input type="date" name="maintenance_date" value="{{ date('Y-m-d') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cost ({{ $currencySymbol }}) *</label>
                            <input type="number" name="cost" value="0" min="0" step="0.01" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Next Due Date</label>
                            <input type="date" name="next_maintenance_date"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Performed By</label>
                            <input type="text" name="performed_by" maxlength="255"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vendor</label>
                            <input type="text" name="vendor" maxlength="255"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Description *</label>
                            <textarea name="description" rows="2" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white"
                                style="background-color:#1e3a5f;">
                            Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endcan

        {{-- Maintenance history --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Maintenance History</h3>
            </div>
            @if($asset->maintenanceRecords->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Next Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($asset->maintenanceRecords as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $record->maintenance_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($record->maintenance_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900">{{ Str::limit($record->description, 60) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">{{ $currencySymbol }}{{ number_format($record->cost, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $record->next_maintenance_date?->format('M d, Y') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="px-6 py-8 text-center text-sm text-gray-400">No maintenance records yet.</p>
            @endif
        </div>

    </div>
</div>
@endsection
