@extends('layouts.admin')

@section('title', 'Pledge Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.pledges.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Pledge {{ $pledge->pledge_number }}</h1>
        </div>
        @if($pledge->status === 'active')
        @can('finance.edit')
        <a href="{{ route('admin.pledges.edit', $pledge) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Edit Pledge
        </a>
        @endcan
        @endif
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Pledge Details -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Progress Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-6">
                @php
                    $statusColors = [
                        'active' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-gray-100 text-gray-800',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$pledge->status] ?? 'bg-gray-100' }}">
                    {{ ucfirst($pledge->status) }}
                </span>
            </div>

            <!-- Progress Circle -->
            <div class="flex justify-center mb-6">
                <div class="relative w-32 h-32">
                    <svg class="w-32 h-32 transform -rotate-90">
                        <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="12" fill="none"/>
                        <circle cx="64" cy="64" r="56" stroke="#10b981" stroke-width="12" fill="none"
                                stroke-dasharray="{{ 352 * ($pledge->progress_percentage / 100) }} 352"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900">{{ $pledge->progress_percentage }}%</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3 text-center">
                <div>
                    <p class="text-sm text-gray-500">Total Pledged</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $currencySymbol }} {{ number_format($pledge->total_amount, 2) }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Paid</p>
                        <p class="text-lg font-bold text-green-600">{{ $currencySymbol }} {{ number_format($pledge->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Balance</p>
                        <p class="text-lg font-bold text-red-600">{{ $currencySymbol }} {{ number_format($pledge->balance, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Details</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-500">Member</dt>
                    <dd class="text-sm font-medium text-gray-900">
                        <a href="{{ route('admin.members.show', $pledge->member) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $pledge->member->full_name }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Purpose</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $pledge->purpose }}</dd>
                </div>
                @if($pledge->project)
                <div>
                    <dt class="text-sm text-gray-500">Project</dt>
                    <dd class="text-sm font-medium text-indigo-600">{{ $pledge->project->name }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm text-gray-500">Pledge Date</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $pledge->pledge_date->format('M d, Y') }}</dd>
                </div>
                @if($pledge->due_date)
                <div>
                    <dt class="text-sm text-gray-500">Due Date</dt>
                    <dd class="text-sm font-medium {{ $pledge->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $pledge->due_date->format('M d, Y') }}
                        @if($pledge->is_overdue)
                        <span class="text-xs">(Overdue)</span>
                        @endif
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm text-gray-500">Payment Frequency</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $pledge->payment_frequency)) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">SMS Notification</dt>
                    <dd class="mt-1">
                        @if($pledge->sms_sent)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Sent
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Not sent</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Cancel Pledge -->
        @if($pledge->status === 'active')
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Pledge</h3>
            <form action="{{ route('admin.pledges.cancel', $pledge) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to cancel this pledge?');">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason *</label>
                    <textarea name="reason" id="reason" rows="2" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <button type="submit" 
                        class="w-full px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                    Cancel Pledge
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Payments -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Record Payment Form -->
        @if($pledge->status === 'active' && $pledge->balance > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Record Payment</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.pledges.record-payment', $pledge) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ $currencySymbol }}</span>
                            </div>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                                   max="{{ $pledge->balance }}" required
                                   class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Date *</label>
                        <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Method *</label>
                        <select name="payment_method" id="payment_method" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @include('admin.partials.payment-method-options')
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Payment History -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Payment History</h3>
            </div>
            @if($pledge->payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pledge->payments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $payment->receipt_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->payment_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                {{ $currencySymbol }} {{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->recordedBy->name ?? 'System' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm font-medium text-gray-900">Total Paid</td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600">
                                {{ $currencySymbol }} {{ number_format($pledge->payments->sum('amount'), 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No payments recorded yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
