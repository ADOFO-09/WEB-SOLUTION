@extends('layouts.admin')

@section('title', 'Welfare Due Rates')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Welfare Due Rates</h1>
    </div>
@endsection

@section('content')
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT: Existing Rates -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Rate History</h2>
                <p class="text-sm text-gray-500 mt-1">Historical rates are preserved for accurate balance calculations.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effective From</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount / Month</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Added By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rates as $rate)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $rate->effective_from->format('M d, Y') }}
                                @if($loop->first)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Current</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-600">
                                {{ $currencySymbol }} {{ number_format($rate->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $rate->notes ?? '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $rate->createdBy?->name ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No rates configured yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT: Add New Rate -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Add New Rate</h2>
            </div>
            <form action="{{ route('admin.welfare.rates.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount per Month <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">{{ $currencySymbol }}</span>
                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01"
                               class="pl-12 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="0.00">
                    </div>
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Effective From <span class="text-red-500">*</span></label>
                    <input type="date" name="effective_from" value="{{ old('effective_from') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('effective_from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="e.g. Rate revised by congregation vote">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Add Rate
                    </button>
                </div>
            </form>
            <div class="px-6 pb-5">
                <p class="text-xs text-gray-400">Note: Adding a new rate creates a new row. Old rates are preserved to calculate historical balances correctly.</p>
            </div>
        </div>
    </div>
@endsection
