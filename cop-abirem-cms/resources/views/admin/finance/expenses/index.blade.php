@extends('layouts.admin')

@section('title', 'Expenses')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Expense Records</h1>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.expenses.budget-report') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Budget Report
            </a>
            @can('finance.create')
            <a href="{{ route('admin.expenses.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Expense
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Year Total (Paid)</div>
            <div class="text-2xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($stats['total_paid'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Month</div>
            <div class="text-2xl font-bold text-red-600">{{ $currencySymbol }} {{ number_format($stats['this_month'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_count'] }}</div>
            <div class="text-xs text-gray-500">{{ $currencySymbol }} {{ number_format($stats['pending_amount'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Awaiting Payment</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['approved_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 bg-yellow-50 border border-yellow-200">
            <div class="text-sm font-medium text-yellow-700">Needs Attention</div>
            <div class="text-2xl font-bold text-yellow-800">{{ $stats['pending_count'] + $stats['approved_count'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.expenses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <tr class="{{ $expense->isVoided() ? 'bg-red-50 opacity-75' : ($expense->isAdjusted() ? 'bg-yellow-50' : 'hover:bg-gray-50') }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $expense->reference_number }}
                            @if($expense->isVoided())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">VOID</span>
                            @elseif($expense->isAdjusted())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">ADJ</span>
                            @elseif($expense->isAdjustment())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">+ADJ</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($expense->description, 40) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $expense->expenseCategory->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $expense->payee_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $expense->isVoided() ? 'text-red-300 line-through' : 'text-red-600' }}">{{ $currencySymbol }} {{ number_format($expense->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->expense_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $expense->status_badge }}">
                                {{ ucfirst($expense->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.expenses.show', $expense) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                @can('corrections.void')
                                @if($expense->isActive() && !$expense->isAdjustment())
                                <x-adjust-entry-modal
                                    entryType="expense"
                                    :entryId="$expense->id"
                                    :reference="$expense->reference_number"
                                    :currentAmount="$expense->amount"
                                    :route="route('admin.finance.corrections.adjust', ['expense', $expense->id])" />
                                @endif
                                @if($expense->isActive())
                                <x-void-entry-modal
                                    entryType="expense"
                                    :entryId="$expense->id"
                                    :reference="$expense->reference_number"
                                    :route="route('admin.finance.corrections.void.expense', $expense)" />
                                @endif
                                @if($expense->isVoided() || $expense->isAdjusted() || $expense->isAdjustment())
                                <a href="{{ route('admin.finance.corrections.history', ['expense', $expense->id]) }}" class="text-xs text-gray-400 hover:underline">History</a>
                                @endif
                                @if($expense->isAdjusted() && $expense->adjusted_by_id)
                                <a href="{{ route('admin.expenses.show', $expense->adjusted_by_id) }}" class="text-xs text-blue-500 hover:underline">View Adj</a>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">No expense records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-4 py-3 border-t">{{ $expenses->links() }}</div>
        @endif
    </div>
@endsection
