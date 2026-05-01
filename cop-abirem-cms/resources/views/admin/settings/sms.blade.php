@extends('layouts.admin')

@section('title', 'SMS Settings')

@section('header')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">SMS Settings</h1>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    @include('admin.settings.partials.nav')

    <div class="lg:col-span-3">
        <form action="{{ route('admin.settings.sms.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- SMS Provider -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">SMS Provider Configuration</h3>
                    <p class="text-sm text-gray-500">Configure your SMS gateway settings.</p>
                </div>
                <div class="p-6 space-y-6" x-data="{ provider: '{{ old('sms_provider', $settings['sms_provider'] ?? '') }}' }">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="sms_provider" class="block text-sm font-medium text-gray-700">SMS Provider *</label>
                            <select name="sms_provider" id="sms_provider" required x-model="provider"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="arkesel" {{ ($settings['sms_provider'] ?? '') == 'arkesel' ? 'selected' : '' }}>Arkesel</option>
                                <option value="giantsms" {{ ($settings['sms_provider'] ?? '') == 'giantsms' ? 'selected' : '' }}>GiantSMS</option>
                                <option value="hubtel" {{ ($settings['sms_provider'] ?? '') == 'hubtel' ? 'selected' : '' }}>Hubtel</option>
                                <option value="mnotify" {{ ($settings['sms_provider'] ?? '') == 'mnotify' ? 'selected' : '' }}>mNotify</option>
                                <option value="twilio" {{ ($settings['sms_provider'] ?? '') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                            </select>
                        </div>

                        <div>
                            <label for="sms_sender_id" class="block text-sm font-medium text-gray-700">Sender ID *</label>
                            <input type="text" name="sms_sender_id" id="sms_sender_id"
                                   value="{{ old('sms_sender_id', $settings['sms_sender_id'] ?? '') }}" required maxlength="11"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Maximum 11 characters</p>
                        </div>

                        <div>
                            <label for="sms_api_key" class="block text-sm font-medium text-gray-700"
                                   x-text="provider === 'giantsms' ? 'Username' : 'API Key'"></label>
                            <input type="password" name="sms_api_key" id="sms_api_key"
                                   value="{{ old('sms_api_key', $settings['sms_api_key'] ?? '') }}"
                                   :placeholder="provider === 'giantsms' ? 'GiantSMS username' : ''"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="sms_api_secret" class="block text-sm font-medium text-gray-700"
                                   x-text="provider === 'giantsms' ? 'Password' : 'API Secret'"></label>
                            <input type="password" name="sms_api_secret" id="sms_api_secret"
                                   value="{{ old('sms_api_secret', $settings['sms_api_secret'] ?? '') }}"
                                   :placeholder="provider === 'giantsms' ? 'GiantSMS password' : ''"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div x-show="provider === 'giantsms'" x-cloak
                         style="background:#eff6ff;border:1px solid #93c5fd;border-radius:.5rem;padding:.75rem 1rem;">
                        <p style="font-size:.8rem;color:#1d4ed8;margin:0;">
                            <strong>GiantSMS credentials:</strong> enter your GiantSMS account username above and your account password below. Your Sender ID must be registered with GiantSMS.
                        </p>
                    </div>

                    <div>
                        <label for="sms_balance_threshold" class="block text-sm font-medium text-gray-700">Low Balance Alert Threshold</label>
                        <div class="mt-1 flex items-center">
                            <input type="number" name="sms_balance_threshold" id="sms_balance_threshold" 
                                   value="{{ old('sms_balance_threshold', $settings['sms_balance_threshold'] ?? '100') }}"
                                   min="0"
                                   class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2 text-gray-500">SMS credits</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMS Notifications -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Notification Settings</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Enable SMS Notifications</h4>
                            <p class="text-sm text-gray-500">Allow sending SMS messages from the system.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_sms_notifications" value="1" 
                                   {{ ($settings['enable_sms_notifications'] ?? '0') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Birthday Messages</h4>
                            <p class="text-sm text-gray-500">Automatically send birthday greetings to members.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_birthday_enabled" value="1" 
                                   {{ ($settings['sms_birthday_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div>
                        <label for="sms_birthday_template" class="block text-sm font-medium text-gray-700">Birthday Message Template</label>
                        <textarea name="sms_birthday_template" id="sms_birthday_template" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('sms_birthday_template', $settings['sms_birthday_template'] ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Available placeholders: {name}, {first_name}, {last_name}</p>
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
