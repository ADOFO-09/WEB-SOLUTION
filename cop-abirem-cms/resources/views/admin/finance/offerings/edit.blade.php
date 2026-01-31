@extends('layouts.admin')

@section('title', isset($offering) ? 'Edit Offering' : 'Record Offering')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.offerings.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($offering) ? 'Edit Offering' : 'Record Offering' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($offering) ? route('admin.offerings.update', $offering) : route('admin.offerings.store') }}" 
          method="POST" class="space-y-6">
        @csrf
        @if(isset($offering))
            @method('PUT')
        @endif

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Offering Details</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Anonymous Toggle -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1"
                           {{ old('is_anonymous', $offering->is_anonymous ?? false) ? 'checked' : '' }}
                           onchange="toggleMemberField()"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_anonymous" class="ml-2 block text-sm text-gray-900">Anonymous Offering</label>
                </div>

                <!-- Member Selection -->
                <div id="member_field">
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Member</label>
                    <select name="member_id" id="member_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a member (optional)</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" 
                                {{ old('member_id', $offering->member_id ?? '') == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }} ({{ $member->member_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label for="income_category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                    <select name="income_category_id" id="income_category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('income_category_id', $offering->income_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('income_category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Session -->
                <div>
                    <label for="session_id" class="block text-sm font-medium text-gray-700">Service Session (Optional)</label>
                    <select name="session_id" id="session_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">No linked session</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" 
                                {{ old('session_id', $offering->session_id ?? $selectedSession?->id) == $session->id ? 'selected' : '' }}>
                                {{ $session->serviceType->name ?? 'Service' }} - {{ $session->service_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (GH₵) *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">GH₵</span>
                        </div>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                               value="{{ old('amount', $offering->amount ?? '') }}" required
                               class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date *</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           value="{{ old('payment_date', isset($offering) ? $offering->payment_date->format('Y-m-d') : date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="cash" {{ old('payment_method', $offering->payment_method ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ old('payment_method', $offering->payment_method ?? '') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank_transfer" {{ old('payment_method', $offering->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cheque" {{ old('payment_method', $offering->payment_method ?? '') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $offering->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.offerings.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($offering) ? 'Update' : 'Record' }} Offering
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleMemberField() {
    const isAnonymous = document.getElementById('is_anonymous').checked;
    const memberField = document.getElementById('member_field');
    memberField.style.display = isAnonymous ? 'none' : 'block';
    if (isAnonymous) {
        document.getElementById('member_id').value = '';
    }
}
document.addEventListener('DOMContentLoaded', toggleMemberField);
</script>
@endpush
@endsection
