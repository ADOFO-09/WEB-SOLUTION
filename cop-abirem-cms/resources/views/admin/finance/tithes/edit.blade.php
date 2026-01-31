@extends('layouts.admin')

@section('title', isset($tithe) ? 'Edit Tithe' : 'Record Tithe')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.tithes.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($tithe) ? 'Edit Tithe' : 'Record Tithe' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($tithe) ? route('admin.tithes.update', $tithe) : route('admin.tithes.store') }}" 
          method="POST" class="space-y-6">
        @csrf
        @if(isset($tithe))
            @method('PUT')
        @endif

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Tithe Details</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Member Selection -->
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Member *</label>
                    <select name="member_id" id="member_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" 
                                {{ old('member_id', $tithe->member_id ?? $selectedMember?->id) == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }} ({{ $member->member_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (GH₵) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">GH₵</span>
                        </div>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                               value="{{ old('amount', $tithe->amount ?? '') }}" required
                               class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Month For -->
                <div>
                    <label for="month_for" class="block text-sm font-medium text-gray-700">Month For *</label>
                    <input type="month" name="month_for" id="month_for" 
                           value="{{ old('month_for', isset($tithe) ? $tithe->month_for->format('Y-m') : date('Y-m')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">The month this tithe is for</p>
                    @error('month_for')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date *</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           value="{{ old('payment_date', isset($tithe) ? $tithe->payment_date->format('Y-m-d') : date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('payment_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="cash" {{ old('payment_method', $tithe->payment_method ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ old('payment_method', $tithe->payment_method ?? '') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank_transfer" {{ old('payment_method', $tithe->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ old('payment_method', $tithe->payment_method ?? '') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                    @error('payment_method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Payment Reference -->
                <div>
                    <label for="payment_reference" class="block text-sm font-medium text-gray-700">Payment Reference</label>
                    <input type="text" name="payment_reference" id="payment_reference" 
                           value="{{ old('payment_reference', $tithe->payment_reference ?? '') }}"
                           placeholder="e.g., MoMo transaction ID, cheque number"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('payment_reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $tithe->notes ?? '') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.tithes.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($tithe) ? 'Update Tithe' : 'Record Tithe' }}
            </button>
        </div>
    </form>
</div>
@endsection
