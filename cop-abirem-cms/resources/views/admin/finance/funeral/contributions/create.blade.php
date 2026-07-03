@extends('layouts.admin')

@section('title', isset($funeralContribution) ? 'Edit Funeral Due Contribution' : 'Record Funeral Due Contribution')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.funeral.contributions.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ isset($funeralContribution) ? 'Edit Funeral Due Contribution' : 'Record Funeral Due Contribution' }}
        </h1>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                @if(isset($funeralContribution))
                <form action="{{ route('admin.funeral.contributions.update', $funeralContribution) }}" method="POST">
                    @csrf @method('PUT')
                @else
                <form action="{{ route('admin.funeral.contributions.store') }}" method="POST">
                    @csrf
                @endif

                    @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="space-y-5">
                        <!-- Member -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Member <span class="text-red-500">*</span></label>
                            <select name="member_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Member --</option>
                                @foreach($members as $m)
                                    <option value="{{ $m->id }}"
                                        {{ old('member_id', $funeralContribution->member_id ?? '') == $m->id ? 'selected' : '' }}>
                                        {{ $m->full_name }} ({{ $m->member_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Period Month <span class="text-red-500">*</span></label>
                                <select name="period_month" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach(range(1, 12) as $month)
                                        <option value="{{ $month }}"
                                            {{ old('period_month', $funeralContribution->period_month ?? date('n')) == $month ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('period_month') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Period Year <span class="text-red-500">*</span></label>
                                <input type="number" name="period_year"
                                       value="{{ old('period_year', $funeralContribution->period_year ?? date('Y')) }}"
                                       min="2000" max="2099"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('period_year') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">{{ $currencySymbol }}</span>
                                <input type="number" name="amount" step="0.01" min="0.01"
                                       value="{{ old('amount', $funeralContribution->amount ?? '') }}"
                                       class="pl-12 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="0.00">
                            </div>
                            @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date"
                                   value="{{ old('payment_date', isset($funeralContribution) ? $funeralContribution->payment_date->format('Y-m-d') : date('Y-m-d')) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('payment_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select name="payment_method"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- None / Unspecified --</option>
                                <option value="cash" {{ old('payment_method', $funeralContribution->payment_method ?? '') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="mobile_money" {{ old('payment_method', $funeralContribution->payment_method ?? '') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="bank_transfer" {{ old('payment_method', $funeralContribution->payment_method ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method', $funeralContribution->payment_method ?? '') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('payment_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="3"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $funeralContribution->notes ?? '') }}</textarea>
                            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <a href="{{ route('admin.funeral.contributions.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                {{ isset($funeralContribution) ? 'Update Contribution' : 'Record Contribution' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
