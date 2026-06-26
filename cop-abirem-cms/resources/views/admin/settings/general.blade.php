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
        <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="church_short_name" class="block text-sm font-medium text-gray-700">Short Name</label>
                            <input type="text" name="church_short_name" id="church_short_name"
                                   value="{{ old('church_short_name', $settings['church_short_name'] ?? '') }}"
                                   placeholder="e.g. COP Abirem"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-400">Used in SMS sign-offs. Falls back to the full name if blank.</p>
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
                        <label for="church_slogan" class="block text-sm font-medium text-gray-700">Slogan / Tagline</label>
                        <input type="text" name="church_slogan" id="church_slogan"
                               value="{{ old('church_slogan', $settings['church_slogan'] ?? '') }}"
                               placeholder="e.g. Abirem Assembly"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-400">Shown below the church name on the login page.</p>
                    </div>

                    <div>
                        <label for="church_address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="church_address" id="church_address" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('church_address', $settings['church_address'] ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                        <div>
                            <label for="church_country" class="block text-sm font-medium text-gray-700">Country</label>
                            <input type="text" name="church_country" id="church_country"
                                   value="{{ old('church_country', $settings['church_country'] ?? '') }}"
                                   placeholder="e.g. Ghana"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Church Logo -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Church Logo</h3>
                    <p class="text-sm text-gray-500">Shown in the sidebar, login page, and reports. JPEG, PNG or WebP, max 2 MB.</p>
                </div>
                <div class="p-6" x-data="{ preview: null }">
                    @php $existingLogo = \App\Helpers\SettingHelper::churchLogo(); @endphp
                    @if($existingLogo)
                    <div class="mb-4 flex items-center gap-4">
                        <img src="{{ $existingLogo }}" alt="Current logo"
                             class="h-16 w-auto rounded border border-gray-200 object-contain bg-white p-1">
                        <div>
                            <p class="text-sm text-gray-600">Current logo</p>
                            <label class="mt-1 flex items-center gap-1.5 text-sm text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_church_logo" value="1"
                                       class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Remove current logo
                            </label>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center gap-4 flex-wrap">
                        <label class="cursor-pointer">
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $existingLogo ? 'Replace Logo' : 'Upload Logo' }}
                            </span>
                            <input type="file" name="church_logo" accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="sr-only"
                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        </label>
                        <template x-if="preview">
                            <img :src="preview" alt="Preview"
                                 class="h-16 w-auto rounded border border-gray-200 object-contain bg-white p-1">
                        </template>
                    </div>
                    @error('church_logo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Report Header -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Report Header</h3>
                    <p class="text-sm text-gray-500">Text printed at the top of reports and PDFs.</p>
                </div>
                <div class="p-6">
                    <textarea name="report_header" id="report_header" rows="3" maxlength="1000"
                              placeholder="e.g. Church of Pentecost — Abirem Assembly"
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('report_header', $settings['report_header'] ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Allowed tags: &lt;b&gt; &lt;i&gt; &lt;em&gt; &lt;strong&gt; &lt;br&gt;. All other HTML is stripped on save.</p>
                    @error('report_header')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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
