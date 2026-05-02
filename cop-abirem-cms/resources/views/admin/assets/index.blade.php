@extends('layouts.admin')

@section('title', 'Assets')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Assets</h1>
        @can('assets.create')
        <a href="{{ route('admin.assets.create') }}"
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white"
           style="background-color:#1e3a5f;">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Asset
        </a>
        @endcan
    </div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Assets</div>
        <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Active</div>
        <div class="mt-1 text-2xl font-bold text-green-700">{{ number_format($stats['active']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">In Maintenance</div>
        <div class="mt-1 text-2xl font-bold text-yellow-600">{{ number_format($stats['maintenance']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Value</div>
        <div class="mt-1 text-2xl font-bold text-gray-900">GH₵{{ number_format($stats['total_value'], 2) }}</div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code..."
               class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        <select name="category_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">All Statuses</option>
            <option value="active"      {{ request('status') === 'active'      ? 'selected' : '' }}>Active</option>
            <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="disposed"    {{ request('status') === 'disposed'    ? 'selected' : '' }}>Disposed</option>
            <option value="lost"        {{ request('status') === 'lost'        ? 'selected' : '' }}>Lost</option>
            <option value="stolen"      {{ request('status') === 'stolen'      ? 'selected' : '' }}>Stolen</option>
        </select>
        <div class="flex space-x-2">
            <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-md"
                    style="background-color:#1e3a5f;">Filter</button>
            <a href="{{ route('admin.assets.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Clear</a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condition</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Value</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assets as $asset)
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
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ $asset->asset_code }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $asset->category->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $conditionColors[$asset->condition_status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($asset->condition_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$asset->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900">GH₵{{ number_format($asset->current_value, 2) }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $asset->location ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            @can('assets.edit')
                            <a href="{{ route('admin.assets.edit', $asset) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            @endcan
                            @can('assets.delete')
                            <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete asset \'{{ $asset->name }}\'?')"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No assets found.
                        @can('assets.create')
                        <a href="{{ route('admin.assets.create') }}" class="ml-1 text-indigo-600 hover:underline">Add one now.</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assets->hasPages())
    <div class="px-4 py-3 border-t">{{ $assets->links() }}</div>
    @endif
</div>
@endsection
