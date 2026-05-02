@extends('layouts.admin')

@section('title', 'Add Asset')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.assets.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Add Asset</h1>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('admin.assets.store') }}" method="POST" class="bg-white shadow rounded-lg">
        @csrf

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Asset Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                    <select name="category_id" id="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" maxlength="100"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('serial_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date *</label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('purchase_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">Purchase Price (GH₵) *</label>
                    <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}"
                           min="0" step="0.01" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('purchase_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="current_value" class="block text-sm font-medium text-gray-700">Current Value (GH₵) *</label>
                    <input type="number" name="current_value" id="current_value" value="{{ old('current_value') }}"
                           min="0" step="0.01" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('current_value')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                    <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}" maxlength="255"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="warranty_expiry" class="block text-sm font-medium text-gray-700">Warranty Expiry</label>
                    <input type="date" name="warranty_expiry" id="warranty_expiry" value="{{ old('warranty_expiry') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" maxlength="255"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="assigned_to_ministry_id" class="block text-sm font-medium text-gray-700">Assigned Ministry</label>
                    <select name="assigned_to_ministry_id" id="assigned_to_ministry_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">None</option>
                        @foreach($ministries as $ministry)
                        <option value="{{ $ministry->id }}" {{ old('assigned_to_ministry_id') == $ministry->id ? 'selected' : '' }}>
                            {{ $ministry->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="condition_status" class="block text-sm font-medium text-gray-700">Condition *</label>
                    <select name="condition_status" id="condition_status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="excellent" {{ old('condition_status','good') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good"      {{ old('condition_status','good') === 'good'      ? 'selected' : '' }}>Good</option>
                        <option value="fair"      {{ old('condition_status') === 'fair'      ? 'selected' : '' }}>Fair</option>
                        <option value="poor"      {{ old('condition_status') === 'poor'      ? 'selected' : '' }}>Poor</option>
                        <option value="damaged"   {{ old('condition_status') === 'damaged'   ? 'selected' : '' }}>Damaged</option>
                        <option value="unusable"  {{ old('condition_status') === 'unusable'  ? 'selected' : '' }}>Unusable</option>
                    </select>
                    @error('condition_status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active"      {{ old('status','active') === 'active'      ? 'selected' : '' }}>Active</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="disposed"    {{ old('status') === 'disposed'    ? 'selected' : '' }}>Disposed</option>
                        <option value="lost"        {{ old('status') === 'lost'        ? 'selected' : '' }}>Lost</option>
                        <option value="stolen"      {{ old('status') === 'stolen'      ? 'selected' : '' }}>Stolen</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <a href="{{ route('admin.assets.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white"
                    style="background-color:#1e3a5f;">
                Add Asset
            </button>
        </div>
    </form>
</div>
@endsection
