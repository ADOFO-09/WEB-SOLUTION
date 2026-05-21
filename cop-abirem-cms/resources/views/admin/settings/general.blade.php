@extends('layouts.admin')

@section('title', 'General Settings')

@section('header')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Settings Navigation -->
    @include('admin.settings.partials.nav')

    <!-- Settings Form -->
    <div class="lg:col-span-3">
        <form action="{{ route('admin.settings.general.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Church Information -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Church Information</h3>
                    <p class="text-sm text-gray-500">Basic information about your church.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="church_name" class="block text-sm font-medium text-gray-700">Church Name *</label>
                            <input type="text" name="church_name" id="church_name" 
                                   value="{{ old('church_name', $settings['church_name'] ?? '') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('church_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="church_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="church_email" id="church_email" 
                                   value="{{ old('church_email', $settings['church_email'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="church_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="church_phone" id="church_phone" 
                                   value="{{ old('church_phone', $settings['church_phone'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="church_website" class="block text-sm font-medium text-gray-700">Website</label>
                            <input type="url" name="church_website" id="church_website" 
                                   value="{{ old('church_website', $settings['church_website'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label for="church_address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="church_address" id="church_address" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('church_address', $settings['church_address'] ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="church_city" class="block text-sm font-medium text-gray-700">City/Town</label>
                            <input type="text" name="church_city" id="church_city" 
                                   value="{{ old('church_city', $settings['church_city'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="church_region" class="block text-sm font-medium text-gray-700">Region</label>
                            <input type="text" name="church_region" id="church_region" 
                                   value="{{ old('church_region', $settings['church_region'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member ID -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Member Numbering</h3>
                    <p class="text-sm text-gray-500">Prefix used when auto-generating member IDs. Changes apply to new members only.</p>
                </div>
                <div class="p-6">
                    <div class="max-w-xs">
                        <label for="member_id_prefix" class="block text-sm font-medium text-gray-700">Member ID Prefix</label>
                        <input type="text" name="member_id_prefix" id="member_id_prefix"
                               value="{{ old('member_id_prefix', $settings['member_id_prefix'] ?? 'COP') }}"
                               maxlength="10"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-400">e.g. COP → COP-2025-0001</p>
                    </div>
                </div>
            </div>

            <!-- Leadership -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Leadership</h3>
                    <p class="text-sm text-gray-500">Pastor and church hierarchy information.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pastor_name" class="block text-sm font-medium text-gray-700">Pastor's Name</label>
                            <input type="text" name="pastor_name" id="pastor_name" 
                                   value="{{ old('pastor_name', $settings['pastor_name'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="pastor_phone" class="block text-sm font-medium text-gray-700">Pastor's Phone</label>
                            <input type="text" name="pastor_phone" id="pastor_phone" 
                                   value="{{ old('pastor_phone', $settings['pastor_phone'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700">District</label>
                            <input type="text" name="district" id="district" 
                                   value="{{ old('district', $settings['district'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="area" class="block text-sm font-medium text-gray-700">Area</label>
                            <input type="text" name="area" id="area" 
                                   value="{{ old('area', $settings['area'] ?? '') }}"
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
