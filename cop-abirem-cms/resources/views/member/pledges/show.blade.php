@extends('layouts.member')

@section('title', 'Pledge Details')

@section('header')
<div class="flex items-center">
    <a href="{{ route('member.pledges.index') }}" class="mr-3 text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <h1 class="text-xl font-bold text-gray-900">Pledge Details</h1>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Pledge Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $pledge->project?->name ?? $pledge->incomeCategory?->name ?? 'General Pledge' }}</h2>
                    <p class="text-gray-500">{{ $pledge->pledge_number }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($pledge->status == 'active') bg-blue-100 text-blue-800
                    @elseif($pledge->status == 'completed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($pledge->status) }}
                </span>
            </div>
            
            <!-- Progress -->
            <div class="mb-6">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Payment Progress</span>
                    <span class="font-bold text-lg">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full {{ $pledge->status == 'completed' ? 'bg-green-500' : 'bg-blue-600' }}" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
            
            <!-- Amount Details -->
            <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="text-center">
                    <p class="text-sm text-gray-500">Total Pledged</p>
                    <p class="text-xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($pledge->total_amount, 2) }}</p>
                </div>
                <div class="text-center border-x border-gray-200">
                    <p class="text-sm text-gray-500">Amount Paid</p>
                    <p class="text-xl font-bold text-green-600">{{ $currencySymbol }} {{ number_format($pledge->amount_paid, 2) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Remaining</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $currencySymbol }} {{ number_format($pledge->total_amount - $pledge->amount_paid, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Payment History -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
            </div>
            @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_date->format($dateFormat) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $payment->reference_number ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600">{{ $currencySymbol }} {{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900">Total Paid</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-green-600">{{ $currencySymbol }} {{ number_format($payments->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                <p>No payments recorded yet.</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Pledge Details -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Details</h3>
            <div class="space-y-4 text-sm">
                <div>
                    <span class="text-gray-500">Pledge Date</span>
                    <p class="font-medium text-gray-900">{{ $pledge->pledge_date?->format($dateFormat) ?? $pledge->created_at->format($dateFormat) }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Due Date</span>
                    <p class="font-medium text-gray-900">{{ $pledge->due_date?->format($dateFormat) ?? 'Not set' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Payment Frequency</span>
                    <p class="font-medium text-gray-900">{{ ucfirst($pledge->payment_frequency ?? 'One-time') }}</p>
                </div>
                @if($pledge->project)
                <div>
                    <span class="text-gray-500">Project</span>
                    <p class="font-medium text-gray-900">{{ $pledge->project->name }}</p>
                </div>
                @endif
                @if($pledge->notes)
                <div>
                    <span class="text-gray-500">Notes</span>
                    <p class="font-medium text-gray-900">{{ $pledge->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Stats -->
        @if($pledge->status == 'active')
        <div class="bg-blue-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Payment Info</h3>
            @if($pledge->due_date && $pledge->due_date->isFuture())
            <p class="text-sm text-blue-700 mb-2">
                <strong>{{ $pledge->due_date->diffInDays(now()) }}</strong> days until due date
            </p>
            @elseif($pledge->due_date && $pledge->due_date->isPast())
            <p class="text-sm text-red-700 mb-2">
                <strong>Overdue</strong> by {{ now()->diffInDays($pledge->due_date) }} days
            </p>
            @endif
            
            @if($pledge->payment_frequency && $pledge->total_amount > $pledge->amount_paid)
            @php
                $remaining = $pledge->total_amount - $pledge->amount_paid;
                $monthsLeft = $pledge->due_date ? max(1, now()->diffInMonths($pledge->due_date)) : 1;
                $suggestedPayment = $remaining / $monthsLeft;
            @endphp
            <p class="text-sm text-blue-700">
                Suggested {{ strtolower($pledge->payment_frequency) }} payment: <strong>{{ $currencySymbol }} {{ number_format($suggestedPayment, 2) }}</strong>
            </p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
