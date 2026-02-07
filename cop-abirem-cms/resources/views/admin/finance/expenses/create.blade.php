@extends('layouts.admin')

@section('title', isset($expense) ? 'Edit Expense' : 'New Expense Request')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.expenses.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($expense) ? 'Edit Expense' : 'New Expense Request' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($expense) ? route('admin.expenses.update', $expense) : route('admin.expenses.store') }}" method="POST" class="space-y-6">
        @csrf
        @if(isset($expense)) @method('PUT') @endif

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Expense Details</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="expense_category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                    <select name="expense_category_id" id="expense_category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                                @if($category->budget_amount > 0)
                                (Budget: GH₵ {{ number_format($category->budget_amount, 2) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('expense_category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea name="description" id="description" rows="3" required placeholder="Describe what this expense is for..."
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $expense->description ?? '') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (GH₵) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">GH₵</span>
                        </div>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount', $expense->amount ?? '') }}" required
                               class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700">Expense Date *</label>
                    <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="cash" {{ old('payment_method', $expense->payment_method ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ old('payment_method', $expense->payment_method ?? '') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank_transfer" {{ old('payment_method', $expense->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ old('payment_method', $expense->payment_method ?? '') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="payee_name" class="block text-sm font-medium text-gray-700">Payee Name *</label>
                        <input type="text" name="payee_name" id="payee_name" value="{{ old('payee_name', $expense->payee_name ?? '') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="payee_phone" class="block text-sm font-medium text-gray-700">Payee Phone</label>
                        <input type="text" name="payee_phone" id="payee_phone" value="{{ old('payee_phone', $expense->payee_phone ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $expense->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($expense) ? 'Update' : 'Submit' }} Expense
            </button>
        </div>
    </form>
</div>
@endsection
