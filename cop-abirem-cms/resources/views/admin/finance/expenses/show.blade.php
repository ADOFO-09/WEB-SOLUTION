@extends('layouts.admin')

@section('title', 'Expense Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.expenses.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Expense {{ $expense->reference_number }}</h1>
        </div>
        <div class="flex space-x-3">
            @if($expense->status === 'paid')
            <a href="{{ route('admin.expenses.voucher', $expense) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Print Voucher
            </a>
            @endif
            @if($expense->status === 'pending')
            @can('finance.edit')
            <a href="{{ route('admin.expenses.edit', $expense) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">Edit</a>
            @endcan
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Status Banner -->
            @php
                $bannerColors = [
                    'pending' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                    'approved' => 'bg-blue-50 border-blue-200 text-blue-800',
                    'rejected' => 'bg-red-50 border-red-200 text-red-800',
                    'paid' => 'bg-green-50 border-green-200 text-green-800',
                ];
            @endphp
            <div class="px-6 py-4 border-b-2 {{ $bannerColors[$expense->status] ?? '' }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $expense->status_badge }}">
                            {{ ucfirst($expense->status) }}
                        </span>
                        <span class="ml-3 text-sm">{{ $expense->reference_number }}</span>
                    </div>
                    <span class="text-2xl font-bold">GH₵ {{ number_format($expense->amount, 2) }}</span>
                </div>
            </div>

            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $expense->expenseCategory->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">Expense Date</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $expense->expense_date->format('F d, Y') }}</dd>
                    </div>
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">Payee</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            {{ $expense->payee_name }}
                            @if($expense->payee_phone)
                            <span class="text-xs text-gray-500 block">{{ $expense->payee_phone }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</dd>
                    </div>
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">Requested By</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $expense->requestedBy->name ?? 'System' }}</dd>
                    </div>
                    @if($expense->approved_by)
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">{{ $expense->status === 'rejected' ? 'Rejected' : 'Approved' }} By</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            {{ $expense->approvedBy->name ?? 'N/A' }}
                            <span class="text-xs text-gray-500 block">{{ $expense->approved_at?->format('M d, Y g:i A') }}</span>
                        </dd>
                    </div>
                    @endif
                    @if($expense->voucher_number)
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">Voucher Number</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $expense->voucher_number }}</dd>
                    </div>
                    @endif
                    @if($expense->payment_reference)
                    <div class="border-b pb-3">
                        <dt class="text-sm text-gray-500">Payment Reference</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $expense->payment_reference }}</dd>
                    </div>
                    @endif
                </dl>

                <div class="mt-4 pt-4 border-t">
                    <dt class="text-sm text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $expense->description }}</dd>
                </div>

                @if($expense->rejection_reason)
                <div class="mt-4 pt-4 border-t">
                    <dt class="text-sm text-red-500 font-medium">Rejection Reason</dt>
                    <dd class="mt-1 text-sm text-red-700 bg-red-50 p-3 rounded">{{ $expense->rejection_reason }}</dd>
                </div>
                @endif

                @if($expense->notes)
                <div class="mt-4 pt-4 border-t">
                    <dt class="text-sm text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $expense->notes }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Panel -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Workflow Status -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow</h3>
            <div class="space-y-4">
                @php
                    $steps = [
                        ['status' => 'pending', 'label' => 'Submitted', 'done' => true],
                        ['status' => 'approved', 'label' => 'Approved', 'done' => in_array($expense->status, ['approved', 'paid'])],
                        ['status' => 'paid', 'label' => 'Paid', 'done' => $expense->status === 'paid'],
                    ];
                    if ($expense->status === 'rejected') {
                        $steps[1] = ['status' => 'rejected', 'label' => 'Rejected', 'done' => true];
                    }
                @endphp
                @foreach($steps as $step)
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center {{ $step['done'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                        @if($step['done'])
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                        <span class="text-xs font-medium">{{ $loop->iteration }}</span>
                        @endif
                    </div>
                    <span class="ml-3 text-sm {{ $step['done'] ? 'font-medium text-gray-900' : 'text-gray-500' }}">{{ $step['label'] }}</span>
                </div>
                @if(!$loop->last)
                <div class="ml-4 border-l-2 h-4 {{ $step['done'] ? 'border-green-200' : 'border-gray-200' }}"></div>
                @endif
                @endforeach
            </div>
        </div>

        <!-- Approve/Reject Actions -->
        @if($expense->can_approve)
        @can('finance.edit')
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Review</h3>
            
            <form action="{{ route('admin.expenses.approve', $expense) }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                        onclick="return confirm('Approve this expense of GH₵ {{ number_format($expense->amount, 2) }}?')">
                    Approve Expense
                </button>
            </form>

            <form action="{{ route('admin.expenses.reject', $expense) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="2" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                    Reject Expense
                </button>
            </form>
        </div>
        @endcan
        @endif

        <!-- Mark as Paid -->
        @if($expense->can_pay)
        @can('finance.edit')
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Record Payment</h3>
            <form action="{{ route('admin.expenses.mark-paid', $expense) }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label for="voucher_number" class="block text-sm font-medium text-gray-700">Voucher Number</label>
                        <input type="text" name="voucher_number" id="voucher_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700">Payment Reference</label>
                        <input type="text" name="payment_reference" id="payment_reference" value="{{ $expense->payment_reference }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Mark as Paid
                    </button>
                </div>
            </form>
        </div>
        @endcan
        @endif

        <!-- Delete -->
        @if(in_array($expense->status, ['pending', 'rejected']))
        @can('finance.delete')
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?');">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                    Delete Expense
                </button>
            </form>
        </div>
        @endcan
        @endif
    </div>
</div>
@endsection
