@extends('layouts.admin')

@section('title', isset($smsTemplate) ? 'Edit Template' : 'New Template')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.sms.templates') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ isset($smsTemplate) ? 'Edit Template' : 'New SMS Template' }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ isset($smsTemplate) ? route('admin.sms.templates.update', $smsTemplate) : route('admin.sms.templates.store') }}" method="POST" class="space-y-6">
        @csrf
        @if(isset($smsTemplate)) @method('PUT') @endif

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template Details</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Template Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $smsTemplate->name ?? '') }}" required
                           placeholder="e.g., Sunday Service Reminder"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                    <select name="category" id="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="general" {{ old('category', $smsTemplate->category ?? '') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="financial" {{ old('category', $smsTemplate->category ?? '') == 'financial' ? 'selected' : '' }}>Financial</option>
                        <option value="attendance" {{ old('category', $smsTemplate->category ?? '') == 'attendance' ? 'selected' : '' }}>Attendance</option>
                        <option value="event" {{ old('category', $smsTemplate->category ?? '') == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="reminder" {{ old('category', $smsTemplate->category ?? '') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                        <option value="birthday" {{ old('category', $smsTemplate->category ?? '') == 'birthday' ? 'selected' : '' }}>Birthday</option>
                    </select>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content * <span class="text-gray-400">(Max 480 characters)</span></label>
                    <textarea name="content" id="content" rows="5" required maxlength="480"
                              oninput="document.getElementById('tplCharCount').textContent = this.value.length + ' / 480'"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content', $smsTemplate->content ?? '') }}</textarea>
                    <div class="mt-1 flex justify-between text-xs">
                        <span class="text-gray-500">Available variables: {name}, {church}, {date}, {amount}, {event}</span>
                        <span id="tplCharCount" class="text-gray-500">{{ strlen(old('content', $smsTemplate->content ?? '')) }} / 480</span>
                    </div>
                    @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $smsTemplate->is_active ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.sms.templates') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                {{ isset($smsTemplate) ? 'Update' : 'Create' }} Template
            </button>
        </div>
    </form>
</div>
@endsection
