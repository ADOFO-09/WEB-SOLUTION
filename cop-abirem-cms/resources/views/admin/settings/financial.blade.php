@extends('layouts.admin')

@section('title', 'Financial Settings')

@section('header')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">Financial Settings</h1>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    @include('admin.settings.partials.nav')

    <div class="lg:col-span-3">
        <form action="{{ route('admin.settings.financial.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Currency Settings -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Currency Settings</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700">Currency Code *</label>
                            <input type="text" name="currency" id="currency" 
                                   value="{{ old('currency', $settings['currency'] ?? 'GHS') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">e.g., GHS, USD, EUR</p>
                        </div>

                        <div>
                            <label for="currency_symbol" class="block text-sm font-medium text-gray-700">Currency Symbol *</label>
                            <input type="text" name="currency_symbol" id="currency_symbol" 
                                   value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'GH₵') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">e.g., GH₵, $, €</p>
                        </div>

                        <div>
                            <label for="fiscal_year_start" class="block text-sm font-medium text-gray-700">Fiscal Year Start</label>
                            <input type="text" name="fiscal_year_start" id="fiscal_year_start" 
                                   value="{{ old('fiscal_year_start', $settings['fiscal_year_start'] ?? '01-01') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Format: MM-DD</p>
                        </div>
                    </div>

                    <div>
                        <label for="tithe_percentage" class="block text-sm font-medium text-gray-700">Tithe Percentage</label>
                        <div class="mt-1 flex items-center">
                            <input type="number" name="tithe_percentage" id="tithe_percentage" 
                                   value="{{ old('tithe_percentage', $settings['tithe_percentage'] ?? '10') }}"
                                   min="0" max="100" step="0.1"
                                   class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2 text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Payment Methods</h3>
                    <p class="text-sm text-gray-500">Select accepted payment methods.</p>
                </div>
                <div class="p-6">
                    @php
                        $selectedMethods = explode(',', $settings['payment_methods'] ?? 'cash,mobile_money,bank_transfer');
                    @endphp
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="payment_methods[]" value="cash" 
                                   {{ in_array('cash', $selectedMethods) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Cash</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="payment_methods[]" value="mobile_money" 
                                   {{ in_array('mobile_money', $selectedMethods) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Mobile Money</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="payment_methods[]" value="bank_transfer" 
                                   {{ in_array('bank_transfer', $selectedMethods) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Bank Transfer</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="payment_methods[]" value="cheque" 
                                   {{ in_array('cheque', $selectedMethods) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Cheque</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Bank Account Details</h3>
                    <p class="text-sm text-gray-500">For bank transfer payments and receipts.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" 
                                   value="{{ old('bank_name', $settings['bank_name'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="bank_account_name" class="block text-sm font-medium text-gray-700">Account Name</label>
                            <input type="text" name="bank_account_name" id="bank_account_name" 
                                   value="{{ old('bank_account_name', $settings['bank_account_name'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="bank_account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" 
                                   value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Money Details -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Mobile Money Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mobile_money_name" class="block text-sm font-medium text-gray-700">Registered Name</label>
                            <input type="text" name="mobile_money_name" id="mobile_money_name" 
                                   value="{{ old('mobile_money_name', $settings['mobile_money_name'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="mobile_money_number" class="block text-sm font-medium text-gray-700">Mobile Money Number</label>
                            <input type="text" name="mobile_money_number" id="mobile_money_number" 
                                   value="{{ old('mobile_money_number', $settings['mobile_money_number'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
