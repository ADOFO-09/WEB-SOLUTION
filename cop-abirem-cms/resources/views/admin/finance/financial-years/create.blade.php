@extends('layouts.admin')

@section('title', 'New Financial Year')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.finance.years.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">New Financial Year</h1>
    </div>
@endsection

@section('content')
@php
    $suggestedName  = $prefillYear ? 'Financial Year ' . $prefillYear : '';
    $suggestedStart = $prefillYear ? $prefillYear . '-01-01' : '';
    $suggestedEnd   = $prefillYear ? $prefillYear . '-12-31' : '';
@endphp

@if(isset($prefillYear) && $prefillYear)
<div style="background:#fef3c7;border:1px solid #fcd34d;border-left:4px solid #f59e0b;color:#92400e;padding:0.75rem 1rem;border-radius:8px;font-size:0.85rem;margin-bottom:1.25rem;">
    <strong>Quick setup for {{ $prefillYear }}:</strong>
    Dates and name have been pre-filled for the {{ $prefillYear }} calendar year. Adjust them if your financial year runs on a different schedule, then save.
</div>
@endif

<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.finance.years.store') }}" method="POST" class="bg-white shadow rounded-lg">
        @csrf

        <div class="p-6 space-y-6">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Year Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $suggestedName) }}" required maxlength="50"
                       placeholder="e.g., Financial Year {{ now()->year }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date"
                           value="{{ old('start_date', $suggestedStart) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date"
                           value="{{ old('end_date', $suggestedEnd) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $prefillYear ? '1' : '') ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Set as active financial year
                    <span class="text-gray-500 text-xs">(deactivates any current active year)</span>
                </label>
            </div>

        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <a href="{{ route('admin.finance.years.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white"
                    style="background-color: #1e3a5f;">
                Create Financial Year
            </button>
        </div>
    </form>
</div>
@endsection
