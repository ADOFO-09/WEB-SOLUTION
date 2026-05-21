@extends('layouts.admin')

@section('title', 'Record Donation')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.donations.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Record Donation</h1>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.donations.store') }}" method="POST" class="max-w-3xl">
    @csrf
    
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Donation Details</h3>
        </div>
        <div class="p-6 space-y-6">
            <!-- Anonymous Toggle -->
            <div class="flex items-center">
                <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" 
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                       {{ old('is_anonymous') ? 'checked' : '' }}>
                <label for="is_anonymous" class="ml-2 text-sm text-gray-700">Anonymous Donation</label>
            </div>

            <!-- Donor Info -->
            <div id="donor-info" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Member (Optional)</label>
                    <select name="member_id" id="member_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Member or Enter Donor Name</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }} ({{ $member->member_id }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="donor_name" class="block text-sm font-medium text-gray-700">Donor Name</label>
                    <input type="text" name="donor_name" id="donor_name" value="{{ old('donor_name') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="donor_phone" class="block text-sm font-medium text-gray-700">Donor Phone</label>
                    <input type="text" name="donor_phone" id="donor_phone" value="{{ old('donor_phone') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Donation Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Donation Type</label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="donation_type" value="cash" class="text-indigo-600" {{ old('donation_type', 'cash') == 'cash' ? 'checked' : '' }}>
                        <span class="ml-2">Cash</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="donation_type" value="in_kind" class="text-indigo-600" {{ old('donation_type') == 'in_kind' ? 'checked' : '' }}>
                        <span class="ml-2">In-Kind</span>
                    </label>
                </div>
            </div>

            <!-- Cash Fields -->
            <div id="cash-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount ({{ $currencySymbol }}) *</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @include('admin.partials.payment-method-options', ['selected' => old('payment_method')])
                    </select>
                </div>
                <div>
                    <label for="payment_reference" class="block text-sm font-medium text-gray-700">Payment Reference</label>
                    <input type="text" name="payment_reference" id="payment_reference" value="{{ old('payment_reference') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- In-Kind Fields -->
            <div id="in-kind-fields" class="hidden space-y-4">
                <div>
                    <label for="in_kind_description" class="block text-sm font-medium text-gray-700">Description of Items *</label>
                    <textarea name="in_kind_description" id="in_kind_description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('in_kind_description') }}</textarea>
                </div>
                <div class="w-1/2">
                    <label for="estimated_value" class="block text-sm font-medium text-gray-700">Estimated Value ({{ $currencySymbol }})</label>
                    <input type="number" name="estimated_value" id="estimated_value" value="{{ old('estimated_value') }}" step="0.01" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Common Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('payment_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Project (Optional)</label>
                    <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">General Fund</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="income_category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="income_category_id" id="income_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('income_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end space-x-3">
        <a href="{{ route('admin.donations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">Record Donation</button>
    </div>
</form>

@push('scripts')
<script>
    const anonymousCheckbox = document.getElementById('is_anonymous');
    const donorInfo = document.getElementById('donor-info');
    const donationTypeRadios = document.querySelectorAll('input[name="donation_type"]');
    const cashFields = document.getElementById('cash-fields');
    const inKindFields = document.getElementById('in-kind-fields');

    function toggleDonorInfo() {
        donorInfo.style.display = anonymousCheckbox.checked ? 'none' : 'grid';
    }

    function toggleDonationType() {
        const type = document.querySelector('input[name="donation_type"]:checked').value;
        if (type === 'cash') {
            cashFields.classList.remove('hidden');
            inKindFields.classList.add('hidden');
        } else {
            cashFields.classList.add('hidden');
            inKindFields.classList.remove('hidden');
        }
    }

    anonymousCheckbox.addEventListener('change', toggleDonorInfo);
    donationTypeRadios.forEach(radio => radio.addEventListener('change', toggleDonationType));
    
    toggleDonorInfo();
    toggleDonationType();
</script>
@endpush
@endsection
