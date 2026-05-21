@extends('layouts.admin')

@section('title', 'Financial Years')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Financial Years</h1>
        @can('settings.manage')
        <a href="{{ route('admin.finance.years.create') }}"
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white"
           style="background-color: #1e3a5f;">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Financial Year
        </a>
        @endcan
    </div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Income</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Records</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($yearsPaginated as $year)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $year->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $year->start_date->format('M d, Y') }}
                        <span class="text-gray-400 mx-1">&ndash;</span>
                        {{ $year->end_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($year->is_closed)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                Closed
                            </span>
                        @elseif($year->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Open
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $currencySymbol }} {{ number_format($year->getTotalIncome(), 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-0.5">
                            <div>Tithes: {{ $year->tithes()->count() }}</div>
                            <div>Offerings: {{ $year->offerings()->count() }}</div>
                            <div>Donations: {{ $year->donations()->count() }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            @can('settings.manage')
                            @if(!$year->is_closed)
                            <a href="{{ route('admin.finance.years.edit', $year) }}"
                               class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            @endif

                            @if(!$year->is_active && !$year->is_closed)
                            <form action="{{ route('admin.finance.years.activate', $year) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Set \'{{ $year->name }}\' as the active financial year?')"
                                        class="text-green-600 hover:text-green-900">
                                    Activate
                                </button>
                            </form>
                            @endif

                            @if($year->is_active && !$year->is_closed)
                            <form action="{{ route('admin.finance.years.close', $year) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Close \'{{ $year->name }}\'? This cannot be undone.')"
                                        class="text-yellow-600 hover:text-yellow-900">
                                    Close
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('admin.finance.years.destroy', $year) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Permanently delete \'{{ $year->name }}\'?')"
                                        class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        No financial years found.
                        @can('settings.manage')
                        <a href="{{ route('admin.finance.years.create') }}" class="ml-1 text-indigo-600 hover:underline">Create one now.</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($yearsPaginated->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $yearsPaginated->links() }}
    </div>
    @endif
</div>
@endsection
