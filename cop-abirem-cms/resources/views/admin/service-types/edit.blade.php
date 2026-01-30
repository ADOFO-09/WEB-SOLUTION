@extends('layouts.admin')

@section('title', isset($serviceType) ? 'Edit Service Type' : 'Create Service Type')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.service-types.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($serviceType) ? 'Edit Service Type' : 'Create Service Type' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($serviceType) ? route('admin.service-types.update', $serviceType) : route('admin.service-types.store') }}" 
          method="POST" class="bg-white shadow rounded-lg">
        @csrf
        @if(isset($serviceType))
            @method('PUT')
        @endif

        <div class="p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Service Name *</label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $serviceType->name ?? '') }}" required
                       placeholder="e.g., Sunday Worship Service"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="day_of_week" class="block text-sm font-medium text-gray-700">Day of Week</label>
                    <select name="day_of_week" id="day_of_week"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Any Day</option>
                        @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                            <option value="{{ $day }}" {{ old('day_of_week', $serviceType->day_of_week ?? '') == $day ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="default_start_time" class="block text-sm font-medium text-gray-700">Default Start Time</label>
                    <input type="time" name="default_start_time" id="default_start_time"
                           value="{{ old('default_start_time', isset($serviceType) && $serviceType->default_start_time ? $serviceType->default_start_time->format('H:i') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $serviceType->description ?? '') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $serviceType->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <a href="{{ route('admin.service-types.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($serviceType) ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</div>
@endsection
