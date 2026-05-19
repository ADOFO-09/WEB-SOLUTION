@extends('layouts.admin')

@section('title', isset($ministry) ? 'Edit Ministry' : 'Create Ministry')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.ministries.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($ministry) ? 'Edit Ministry' : 'Create Ministry' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($ministry) ? route('admin.ministries.update', $ministry) : route('admin.ministries.store') }}" 
          method="POST" class="bg-white shadow rounded-lg">
        @csrf
        @if(isset($ministry))
            @method('PUT')
        @endif

        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Ministry Details</h3>
        </div>

        <div class="p-6 space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Ministry Name *</label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $ministry->name ?? '') }}" required
                       placeholder="e.g., Youth Ministry"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Brief description of the ministry's purpose and activities..."
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $ministry->description ?? '') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Ministry Type *</label>
                <select name="type" id="type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select type</option>
                    @foreach(\App\Models\Ministry::TYPES as $value => $label)
                        <option value="{{ $value }}"
                            {{ old('type', $ministry->type ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Set <strong>Home Cell</strong> or <strong>Bible Study Group</strong> so this ministry appears in the monthly statistical report.
                </p>
                @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Leader -->
            <div>
                <label for="leader_id" class="block text-sm font-medium text-gray-700">Ministry Leader</label>
                <select name="leader_id" id="leader_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select a leader (optional)</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" 
                            {{ old('leader_id', $ministry->leader_id ?? '') == $member->id ? 'selected' : '' }}>
                            {{ $member->full_name }} ({{ $member->member_id }})
                        </option>
                    @endforeach
                </select>
                @error('leader_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Meeting Schedule -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="meeting_day" class="block text-sm font-medium text-gray-700">Meeting Day</label>
                    <select name="meeting_day" id="meeting_day" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select day</option>
                        @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                            <option value="{{ $day }}" 
                                {{ old('meeting_day', $ministry->meeting_day ?? '') == $day ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </select>
                    @error('meeting_day')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="meeting_time" class="block text-sm font-medium text-gray-700">Meeting Time</label>
                    <input type="time" name="meeting_time" id="meeting_time" 
                           value="{{ old('meeting_time', isset($ministry) && $ministry->meeting_time ? $ministry->meeting_time->format('H:i') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('meeting_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $ministry->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active Ministry
                </label>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <a href="{{ route('admin.ministries.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($ministry) ? 'Update Ministry' : 'Create Ministry' }}
            </button>
        </div>
    </form>
</div>
@endsection
