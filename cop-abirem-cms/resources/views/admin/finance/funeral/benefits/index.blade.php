@extends('layouts.admin')

@section('title', 'Funeral Benefits')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Funeral Benefits</h1>
        @if(auth()->user()->hasPermission('funeral.benefits.manage'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.funeral.benefits.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Record Benefit
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.funeral.benefits.index') }}" method="GET"
                  class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="number" name="year" value="{{ request('year') }}"
                           placeholder="Year e.g. {{ date('Y') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search benefactor / deceased / member..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 text-sm">Filter</button>
                    <a href="{{ route('admin.funeral.benefits.index') }}" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">Clear</a>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funeral Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Benefactor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deceased</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount Donated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($benefits as $benefit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $benefit->funeral_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $benefit->benefactor_name }}</div>
                            @if($benefit->member)
                                <div class="text-xs text-gray-500">{{ $benefit->member->full_name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $benefit->deceased_name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $benefit->venue ?? '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $currencySymbol }} {{ number_format($benefit->amount_donated, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $currencySymbol }} {{ number_format($benefit->total_cost, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.funeral.benefits.show', $benefit) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('admin.funeral.benefits.edit', $benefit) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                <form method="POST" action="{{ route('admin.funeral.benefits.destroy', $benefit) }}"
                                      onsubmit="return confirm('Delete this benefit?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            No funeral benefits found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($benefits->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $benefits->links() }}
        </div>
        @endif
    </div>
@endsection
