@extends('layouts.admin')

@section('title', 'Offerings')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Offering Records</h1>
        @can('finance.create')
        <a href="{{ route('admin.offerings.create') }}" 
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Record Offering
        </a>
        @endcan
    </div>
@endsection

@section('content')
    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Year Total</div>
            <div class="text-2xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($stats['total_amount'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Month</div>
            <div class="text-2xl font-bold text-green-600">{{ $currencySymbol }} {{ number_format($stats['this_month'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Records</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_count']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Anonymous</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['anonymous_count']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.offerings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="anonymous" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Types</option>
                        <option value="no" {{ request('anonymous') == 'no' ? 'selected' : '' }}>Named</option>
                        <option value="yes" {{ request('anonymous') == 'yes' ? 'selected' : '' }}>Anonymous</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('admin.offerings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Offerings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contributor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($offerings as $offering)
                    <tr class="{{ $offering->isVoided() ? 'bg-red-50 opacity-75' : ($offering->isAdjusted() ? 'bg-yellow-50' : 'hover:bg-gray-50') }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $offering->reference_number }}
                            @if($offering->isVoided())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">VOID</span>
                            @elseif($offering->isAdjusted())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">ADJ</span>
                            @elseif($offering->isAdjustment())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">+ADJ</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($offering->is_anonymous)
                                <span class="text-gray-400 italic">Anonymous</span>
                            @else
                                {{ $offering->member->full_name ?? 'Unknown' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $offering->incomeCategory->name ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $offering->isVoided() ? 'text-red-300 line-through' : 'text-green-600' }}">
                            {{ $currencySymbol }} {{ number_format($offering->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $offering->payment_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.offerings.show', $offering) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                @can('corrections.void')
                                @if($offering->isActive() && !$offering->isAdjustment())
                                <x-adjust-entry-modal
                                    entryType="offering"
                                    :entryId="$offering->id"
                                    :reference="$offering->reference_number"
                                    :currentAmount="$offering->amount"
                                    :route="route('admin.finance.corrections.adjust', ['offering', $offering->id])" />
                                @endif
                                @if($offering->isActive())
                                <x-void-entry-modal
                                    entryType="offering"
                                    :entryId="$offering->id"
                                    :reference="$offering->reference_number"
                                    :route="route('admin.finance.corrections.void.offering', $offering)" />
                                @endif
                                @if($offering->isVoided() || $offering->isAdjusted() || $offering->isAdjustment())
                                <a href="{{ route('admin.finance.corrections.history', ['offering', $offering->id]) }}" class="text-xs text-gray-400 hover:underline">History</a>
                                @endif
                                @if($offering->isAdjusted() && $offering->adjusted_by_id)
                                <a href="{{ route('admin.offerings.show', $offering->adjusted_by_id) }}" class="text-xs text-blue-500 hover:underline">View Adj</a>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No offering records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($offerings->hasPages())
        <div class="px-4 py-3 border-t">{{ $offerings->links() }}</div>
        @endif
    </div>
@endsection
