@extends('layouts.admin')

@section('title', 'Tithes')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Tithe Records</h1>
        @can('finance.create')
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('admin.tithes.session.create') }}"
               class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Record Session Tithes
            </a>
            <a href="{{ route('admin.tithes.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Record Tithe
            </a>
        </div>
        @endcan
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Year Total</div>
            <div class="text-2xl font-bold text-gray-900">GH₵ {{ number_format($stats['total_amount'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Month</div>
            <div class="text-2xl font-bold text-green-600">GH₵ {{ number_format($stats['this_month'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Records</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_count']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Unique Members</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['unique_members']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.tithes.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <select name="member_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Members</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Filter
                    </button>
                    <a href="{{ route('admin.tithes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tithes Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month For</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tithes as $tithe)
                    <tr class="{{ $tithe->isVoided() ? 'bg-red-50 opacity-75' : ($tithe->isAdjusted() ? 'bg-yellow-50' : 'hover:bg-gray-50') }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $tithe->receipt_number }}
                            @if($tithe->isVoided())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">VOID</span>
                            @elseif($tithe->isAdjusted())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">ADJ</span>
                            @elseif($tithe->isAdjustment())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">+ADJ</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($tithe->member)
                                <a href="{{ route('admin.tithes.member-history', $tithe->member) }}"
                                   class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ $tithe->member->full_name }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $tithe->member->member_id }}</p>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Session Tithe
                                </span>
                                @if($tithe->attendanceSession)
                                    <p class="text-xs text-gray-500">{{ $tithe->attendanceSession->serviceType->name ?? 'Service' }}</p>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $tithe->isVoided() ? 'text-red-400 line-through' : 'text-green-600' }}">
                            GH₵ {{ number_format($tithe->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tithe->month_for_formatted }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tithe->payment_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($tithe->attendanceSession)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                                    {{ $tithe->attendanceSession->service_date->format('M d') }}
                                </span>
                                @if($tithe->attendanceSession->serviceType)
                                    <p class="text-xs text-gray-400">{{ $tithe->attendanceSession->serviceType->name }}</p>
                                @endif
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.tithes.show', $tithe) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('admin.tithes.receipt', $tithe) }}" class="text-green-600 hover:text-green-900">Receipt</a>
                                @can('corrections.void')
                                @if($tithe->isActive() && !$tithe->isAdjustment())
                                <x-adjust-entry-modal
                                    entryType="tithe"
                                    :entryId="$tithe->id"
                                    :reference="$tithe->receipt_number ?? $tithe->reference_number"
                                    :currentAmount="$tithe->amount"
                                    :route="route('admin.finance.corrections.adjust', ['tithe', $tithe->id])" />
                                @endif
                                @if($tithe->isActive())
                                <x-void-entry-modal
                                    entryType="tithe"
                                    :entryId="$tithe->id"
                                    :reference="$tithe->receipt_number ?? $tithe->reference_number"
                                    :route="route('admin.finance.corrections.void.tithe', $tithe)" />
                                @endif
                                @if($tithe->isVoided() || $tithe->isAdjusted() || $tithe->isAdjustment())
                                <a href="{{ route('admin.finance.corrections.history', ['tithe', $tithe->id]) }}" class="text-xs text-gray-500 hover:underline">History</a>
                                @endif
                                @if($tithe->isAdjusted() && $tithe->adjusted_by_id)
                                <a href="{{ route('admin.tithes.show', $tithe->adjusted_by_id) }}" class="text-xs text-blue-500 hover:underline">View Adj</a>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            No tithe records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tithes->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $tithes->links() }}
        </div>
        @endif
    </div>
@endsection
