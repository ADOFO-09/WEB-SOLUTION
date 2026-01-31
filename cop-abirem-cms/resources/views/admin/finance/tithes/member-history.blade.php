@extends('layouts.admin')

@section('title', $member->full_name . ' - Tithe History')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.tithes.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $member->full_name }}</h1>
                <p class="text-sm text-gray-500">Tithe History</p>
            </div>
        </div>
        <a href="{{ route('admin.tithes.create', ['member_id' => $member->id]) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Record Tithe
        </a>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Member Info & Summary -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Member Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                @if($member->photo_path)
                    <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                @else
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-bold text-xl">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $member->full_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $member->member_id }}</p>
                </div>
            </div>
        </div>

        <!-- Year Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">{{ $year }} Summary</h3>
                <form action="" method="GET" class="flex items-center">
                    <select name="year" onchange="this.form.submit()" 
                            class="text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="text-center py-4 bg-green-50 rounded-lg mb-4">
                <p class="text-sm text-green-600">Total Paid</p>
                <p class="text-3xl font-bold text-green-700">GH₵ {{ number_format($yearlyTotal, 2) }}</p>
            </div>

            <div class="space-y-2">
                <p class="text-sm font-medium text-gray-700">Monthly Breakdown</p>
                @forelse($monthlyBreakdown as $month => $amount)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $month }}</span>
                    <span class="font-medium">GH₵ {{ number_format($amount, 2) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No tithes recorded for this year.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tithe Records -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ $year }} Tithe Records</h3>
            </div>

            @if($tithes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month For</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Paid</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tithes as $tithe)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $tithe->receipt_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tithe->month_for_formatted }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                GH₵ {{ number_format($tithe->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tithe->payment_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.tithes.receipt', $tithe) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Receipt
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm font-medium text-gray-900">Total</td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600">GH₵ {{ number_format($yearlyTotal, 2) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No tithe records for {{ $year }}.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
