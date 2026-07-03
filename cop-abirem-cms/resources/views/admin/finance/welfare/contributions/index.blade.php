@extends('layouts.admin')

@section('title', 'Welfare Contributions')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Welfare Contributions</h1>
        @if(auth()->user()->hasPermission('welfare.create'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.welfare.contributions.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Record Contribution
            </a>
        </div>
        @endif
    </div>
@endsection

@section('content')
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Year Total</div>
            <div class="text-2xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($stats['total_this_year'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Month Records</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['this_month_count']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Unique Payers (Year)</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['unique_members']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.welfare.contributions.index') }}" method="GET"
                  class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search ref / member..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <select name="member_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Members</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="period_month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('period_month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="number" name="period_year" value="{{ request('period_year') }}"
                           placeholder="Year e.g. {{ date('Y') }}" min="2000"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <select name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ request('payment_method') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 text-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.welfare.contributions.index') }}" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">
                        Clear
                    </a>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Received By</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contributions as $contribution)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $contribution->reference_number }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $contribution->member?->full_name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $contribution->member?->member_id }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ date('F', mktime(0,0,0,$contribution->period_month,1)) }} {{ $contribution->period_year }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-600">
                            {{ $currencySymbol }} {{ number_format($contribution->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $contribution->payment_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($contribution->payment_method)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $contribution->payment_method)) }}
                            </span>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $contribution->receivedBy?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.welfare.contributions.show', $contribution) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                @if(auth()->user()->hasPermission('welfare.edit'))
                                <a href="{{ route('admin.welfare.contributions.edit', $contribution) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                @endif
                                @if(auth()->user()->hasPermission('welfare.delete'))
                                <form method="POST" action="{{ route('admin.welfare.contributions.destroy', $contribution) }}"
                                      onsubmit="return confirm('Delete this contribution?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            No welfare contributions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contributions->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $contributions->links() }}
        </div>
        @endif
    </div>
@endsection
