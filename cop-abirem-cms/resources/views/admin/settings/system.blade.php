@extends('layouts.admin')

@section('title', 'System Settings')

@section('header')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    @include('admin.settings.partials.nav')

    <div class="lg:col-span-3">
        <!-- System Info -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">System Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">PHP Version</p>
                        <p class="font-semibold text-gray-900">{{ $systemInfo['php_version'] }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Laravel Version</p>
                        <p class="font-semibold text-gray-900">{{ $systemInfo['laravel_version'] }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Database</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst($systemInfo['database']) }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Timezone</p>
                        <p class="font-semibold text-gray-900">{{ $systemInfo['timezone'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-4">
                    <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Clear Cache
                        </button>
                    </form>
                    <form action="{{ route('admin.settings.optimize') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Optimize
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.system.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Display Settings -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Display Settings</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="items_per_page" class="block text-sm font-medium text-gray-700">Items Per Page</label>
                            <select name="items_per_page" id="items_per_page"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach([10, 15, 25, 50, 100] as $value)
                                <option value="{{ $value }}" {{ ($settings['items_per_page'] ?? '15') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700">Date Format</label>
                            <select name="date_format" id="date_format"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="d M, Y" {{ ($settings['date_format'] ?? '') == 'd M, Y' ? 'selected' : '' }}>25 Dec, 2024</option>
                                <option value="M d, Y" {{ ($settings['date_format'] ?? '') == 'M d, Y' ? 'selected' : '' }}>Dec 25, 2024</option>
                                <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>25/12/2024</option>
                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>12/25/2024</option>
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>2024-12-25</option>
                            </select>
                        </div>

                        <div>
                            <label for="time_format" class="block text-sm font-medium text-gray-700">Time Format</label>
                            <select name="time_format" id="time_format"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="h:i A" {{ ($settings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12-hour (02:30 PM)</option>
                                <option value="H:i" {{ ($settings['time_format'] ?? '') == 'H:i' ? 'selected' : '' }}>24-hour (14:30)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Security Settings</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="session_timeout" class="block text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout" id="session_timeout" 
                               value="{{ old('session_timeout', $settings['session_timeout'] ?? '120') }}"
                               min="5" max="480"
                               class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Activity Logging</h4>
                            <p class="text-sm text-gray-500">Log user activities for audit purposes.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_activity_logging" value="1" 
                                   {{ ($settings['enable_activity_logging'] ?? '1') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Two-Factor Authentication</h4>
                            <p class="text-sm text-gray-500">Require 2FA for admin users.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_two_factor" value="1" 
                                   {{ ($settings['enable_two_factor'] ?? '0') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-red-900">Maintenance Mode</h4>
                            <p class="text-sm text-red-700">Take the application offline for maintenance.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" 
                                   {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Attendance Settings -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Attendance Settings</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="max-w-xs">
                        <label for="attendance_grace_minutes" class="block text-sm font-medium text-gray-700">Grace Period (minutes)</label>
                        <input type="number" name="attendance_grace_minutes" id="attendance_grace_minutes"
                               value="{{ old('attendance_grace_minutes', $settings['attendance_grace_minutes'] ?? '15') }}"
                               min="0" max="120"
                               class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-400">Minutes after session start before marking late</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">Allowed Check-in Methods</p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Biometric (Fingerprint)</h4>
                                    <p class="text-sm text-gray-500">Allow fingerprint check-in on attendance sessions.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="attendance_biometric_enabled" value="1"
                                           {{ ($settings['attendance_biometric_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">Manual (Name List)</h4>
                                    <p class="text-sm text-gray-500">Allow manual name-based check-in.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="attendance_manual_enabled" value="1"
                                           {{ ($settings['attendance_manual_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">QR Code</h4>
                                    <p class="text-sm text-gray-500">Allow QR code scan check-in.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="attendance_qr_enabled" value="1"
                                           {{ ($settings['attendance_qr_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
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
