@extends('layouts.admin')

@section('title', isset($donation) ? 'Edit Donation' : 'Record Donation')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.donations.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($donation) ? 'Edit Donation' : 'Record Donation' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($donation) ? route('admin.donations.update', $donation) : route('admin.donations.store') }}" 
          method="POST" class="space-y-6">
        @csrf
        @if(isset($donation))
            @method('PUT')
        @endif

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Donation Details</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Donation Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Donation Type *</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="donation_type" value="cash" 
                                   {{ old('donation_type', $donation->donation_type ?? 'cash') == 'cash' ? 'checked' : '' }}
                                   onchange="toggleDonationType()"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Cash/Monetary</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="donation_type" value="in_kind" 
                                   {{ old('donation_type', $donation->donation_type ?? '') == 'in_kind' ? 'checked' : '' }}
                                   onchange="toggleDonationType()"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">In-Kind</span>
                        </label>
                    </div>
                </div>

                <!-- Anonymous Toggle -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1"
                           {{ old('is_anonymous', $donation->is_anonymous ?? false) ? 'checked' : '' }}
                           onchange="toggleDonorFields()"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_anonymous" class="ml-2 block text-sm text-gray-900">Anonymous Donation</label>
                </div>

                <!-- Donor Fields -->
                <div id="donor_fields">
                    <!-- Member Selection -->
                    <div class="mb-4">
                        <label for="member_id" class="block text-sm font-medium text-gray-700">Member (if applicable)</label>
                        <select name="member_id" id="member_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select a member or enter external donor</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" 
                                    {{ old('member_id', $donation->member_id ?? '') == $member->id ? 'selected' : '' }}>
                                    {{ $member->full_name }} ({{ $member->member_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- External Donor -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="donor_name" class="block text-sm font-medium text-gray-700">Donor Name</label>
                            <input type="text" name="donor_name" id="donor_name" 
                                   value="{{ old('donor_name', $donation->donor_name ?? '') }}"
                                   placeholder="For non-members"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="donor_phone" class="block text-sm font-medium text-gray-700">Donor Phone</label>
                            <input type="text" name="donor_phone" id="donor_phone" 
                                   value="{{ old('donor_phone', $donation->donor_phone ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Project (Optional)</label>
                    <select name="project_id" id="project_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">General Donation</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" 
                                {{ old('project_id', $donation->project_id ?? $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cash Fields -->
                <div id="cash_fields">
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount (GH₵) *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">GH₵</span>
                            </div>
                            <input type="number" name="amount" id="amount" step="0.01" min="0"
                                   value="{{ old('amount', $donation->amount ?? '') }}"
                                   class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select name="payment_method" id="payment_method"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="cash" {{ old('payment_method', $donation->payment_method ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="mobile_money" {{ old('payment_method', $donation->payment_method ?? '') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="bank_transfer" {{ old('payment_method', $donation->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ old('payment_method', $donation->payment_method ?? '') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                </div>

                <!-- In-Kind Fields -->
                <div id="in_kind_fields" style="display: none;">
                    <div class="mb-4">
                        <label for="in_kind_description" class="block text-sm font-medium text-gray-700">Description of Items *</label>
                        <textarea name="in_kind_description" id="in_kind_description" rows="3"
                                  placeholder="Describe the donated items..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('in_kind_description', $donation->in_kind_description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="estimated_value" class="block text-sm font-medium text-gray-700">Estimated Value (GH₵)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">GH₵</span>
                            </div>
                            <input type="number" name="estimated_value" id="estimated_value" step="0.01" min="0"
                                   value="{{ old('estimated_value', $donation->estimated_value ?? '') }}"
                                   class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           value="{{ old('payment_date', isset($donation) ? $donation->payment_date->format('Y-m-d') : date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $donation->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.donations.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($donation) ? 'Update' : 'Record' }} Donation
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleDonationType() {
    const isCash = document.querySelector('input[name="donation_type"]:checked').value === 'cash';
    document.getElementById('cash_fields').style.display = isCash ? 'block' : 'none';
    document.getElementById('in_kind_fields').style.display = isCash ? 'none' : 'block';
}

function toggleDonorFields() {
    const isAnonymous = document.getElementById('is_anonymous').checked;
    document.getElementById('donor_fields').style.display = isAnonymous ? 'none' : 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    toggleDonationType();
    toggleDonorFields();
});
</script>
@endpush
@endsection
