@extends('layouts.admin')

@section('title', 'Welfare Benefits')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Welfare Benefits</h1>
        @if(auth()->user()->hasPermission('welfare.benefits.manage'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.welfare.benefits.create') }}"
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
            <form action="{{ route('admin.welfare.benefits.index') }}" method="GET"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="number" name="year" value="{{ request('year') }}"
                           placeholder="Year e.g. {{ date('Y') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <select name="purpose" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Purposes</option>
                        @foreach($purposes as $key => $label)
                            <option value="{{ $key }}" {{ request('purpose') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search benefactor / member..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 text-sm">Filter</button>
                    <a href="{{ route('admin.welfare.benefits.index') }}" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">Clear</a>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Benefactor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Main Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($benefits as $benefit)
                    @php
                        $purposeColors = [
                            'marriage'   => 'bg-pink-100 text-pink-800',
                            'funeral'    => 'bg-gray-100 text-gray-800',
                            'childbirth' => 'bg-blue-100 text-blue-800',
                            'other'      => 'bg-yellow-100 text-yellow-800',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $benefit->benefit_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $benefit->benefactor_name }}</div>
                            @if($benefit->member)
                                <div class="text-xs text-gray-500">{{ $benefit->member->full_name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $purposeColors[$benefit->purpose] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ \App\Models\WelfareBenefit::PURPOSES[$benefit->purpose] ?? $benefit->purpose }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $currencySymbol }} {{ number_format($benefit->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $currencySymbol }} {{ number_format($benefit->total_cost, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $benefit->recordedBy?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.welfare.benefits.show', $benefit) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('admin.welfare.benefits.edit', $benefit) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                <form method="POST" action="{{ route('admin.welfare.benefits.destroy', $benefit) }}"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            No welfare benefits found.
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
