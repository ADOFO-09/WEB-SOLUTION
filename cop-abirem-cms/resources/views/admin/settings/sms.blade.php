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

    <div class="lg:col-span-3 space-y-6">

        {{-- Test / Balance result banners --}}
        @if(session('sms_test_success'))
        <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('sms_test_success') }}
        </div>
        @endif
        @if(session('sms_test_error'))
        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('sms_test_error') }}
        </div>
        @endif
        @if(session('sms_balance'))
        <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><strong>Account Balance:</strong> {{ session('sms_balance') }} SMS credits</span>
        </div>
        @endif
        @if(session('sms_balance_error'))
        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('sms_balance_error') }}
        </div>
        @endif
        @if(session('birthday_sms_success'))
        <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div><strong>Birthday SMS Run Complete:</strong><br><pre class="mt-1 text-xs whitespace-pre-wrap">{{ session('birthday_sms_success') }}</pre></div>
        </div>
        @endif
        @if(session('birthday_sms_error'))
        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div><strong>Birthday SMS Failed:</strong><br><pre class="mt-1 text-xs whitespace-pre-wrap">{{ session('birthday_sms_error') }}</pre></div>
        </div>
        @endif

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
                                   value="{{ old('sms_api_key') }}"
                                   placeholder="{{ !empty($settings['sms_api_key']) ? 'Leave blank to keep saved value' : 'GiantSMS username' }}"
                                   autocomplete="new-password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @if(!empty($settings['sms_api_key']))
                                <p class="mt-1 text-xs text-green-600">&#10003; Username saved — enter a new value to replace it</p>
                            @endif
                        </div>

                        <div>
                            <label for="sms_api_secret" class="block text-sm font-medium text-gray-700"
                                   x-text="provider === 'giantsms' ? 'Password' : 'API Secret'"></label>
                            <input type="password" name="sms_api_secret" id="sms_api_secret"
                                   value="{{ old('sms_api_secret') }}"
                                   placeholder="{{ !empty($settings['sms_api_secret']) ? 'Leave blank to keep saved value' : 'GiantSMS password' }}"
                                   autocomplete="new-password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @if(!empty($settings['sms_api_secret']))
                                <p class="mt-1 text-xs text-green-600">&#10003; Password saved — enter a new value to replace it</p>
                            @endif
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

                    <div>
                        <label for="sms_topup_url" class="block text-sm font-medium text-gray-700">Top-up URL</label>
                        <input type="url" name="sms_topup_url" id="sms_topup_url"
                               value="{{ old('sms_topup_url', $settings['sms_topup_url'] ?? '') }}"
                               placeholder="https://giantsms.com/..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Paste your provider's billing or top-up page URL. The low-balance alert will link directly to it.</p>
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

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Auto Tithe Confirmation</h4>
                            <p class="text-sm text-gray-500">Send SMS to member when a tithe payment is recorded.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_auto_tithe_confirmation" value="1"
                                   {{ ($settings['sms_auto_tithe_confirmation'] ?? '0') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Auto Donation Confirmation</h4>
                            <p class="text-sm text-gray-500">Send SMS to donor when a donation is recorded.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_auto_donation_confirmation" value="1"
                                   {{ ($settings['sms_auto_donation_confirmation'] ?? '0') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">Auto Pledge Reminder</h4>
                            <p class="text-sm text-gray-500">Remind members of outstanding pledge balances via SMS.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_auto_pledge_reminder" value="1"
                                   {{ ($settings['sms_auto_pledge_reminder'] ?? '0') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div x-data="{ count: {{ strlen(old('sms_birthday_template', $settings['sms_birthday_template'] ?? '')) }} }">
                        <div class="flex items-center justify-between mb-1">
                            <label for="sms_birthday_template" class="block text-sm font-medium text-gray-700">Birthday Message Template</label>
                            <span class="text-xs" :class="count > 320 ? 'text-red-600 font-semibold' : count > 280 ? 'text-amber-500' : 'text-gray-400'"
                                  x-text="count + ' / 320 chars'"></span>
                        </div>
                        <textarea name="sms_birthday_template" id="sms_birthday_template" rows="4" maxlength="320"
                                  x-on:input="count = $el.value.length"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('sms_birthday_template', $settings['sms_birthday_template'] ?? '') }}</textarea>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="text-xs text-gray-500 font-medium">Placeholders:</span>
                            @foreach(['{name}' => 'Full name', '{first_name}' => 'First name', '{last_name}' => 'Last name'] as $ph => $label)
                            <button type="button"
                                    onclick="var t=document.getElementById('sms_birthday_template');var s=t.selectionStart;t.value=t.value.slice(0,s)+'{{ $ph }}'+t.value.slice(t.selectionEnd);t.focus();t.selectionStart=t.selectionEnd=s+{{ strlen($ph) }};t.dispatchEvent(new Event('input'));"
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 cursor-pointer">
                                {{ $ph }}
                                <span class="ml-1 font-sans text-indigo-400">{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-400">
                            Example: <em>Happy Birthday, {first_name}! Wishing you God's abundant blessings. — COP Abirem</em>
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 text-white rounded-lg" style="background:#1e3a5f;">
                    Save Settings
                </button>
            </div>
        </form>

        {{-- ── Test Connection & Balance ── --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Test &amp; Verify</h3>
                <p class="text-sm text-gray-500">Save your settings first, then use these tools to confirm the integration is working.</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Send Test SMS --}}
                <div class="border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#e0f2fe;">
                            <svg class="w-5 h-5" style="color:#0284c7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Send Test SMS</h4>
                            <p class="text-xs text-gray-500">Sends a test message to verify your credentials</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.settings.sms.test') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="test_phone" class="block text-xs font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="test_phone" id="test_phone"
                                   value="{{ old('test_phone') }}"
                                   placeholder="e.g. 0244123456"
                                   class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('test_phone')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-400 mt-1">Ghana format: 024XXXXXXX or +233XXXXXXXXX</p>
                        </div>
                        <button type="submit" class="w-full py-2 text-sm font-medium text-white rounded-md" style="background:#1e3a5f;">
                            Send Test Message
                        </button>
                    </form>
                </div>

                {{-- Check Balance --}}
                <div class="border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#f0fdf4;">
                            <svg class="w-5 h-5" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Check SMS Balance</h4>
                            <p class="text-xs text-gray-500">Fetch your current credit balance from GiantSMS</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Queries the GiantSMS API to show how many SMS credits remain in your account.
                    </p>
                    <form action="{{ route('admin.settings.sms.balance') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 text-sm font-medium rounded-md border-2 border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50 transition-colors">
                            Check Balance
                        </button>
                    </form>
                </div>

                {{-- Run Birthday SMS Now --}}
                <div class="border border-purple-200 rounded-lg p-5 md:col-span-2">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#faf5ff;">
                            <svg class="w-5 h-5" style="color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6l3 3m0 0l3-3m-3 3V2m0 16l-3 3m3-3l3 3"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Run Birthday SMS Now</h4>
                            <p class="text-xs text-gray-500">Manually trigger today's birthday greetings (ignores enable/disable setting)</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                        <p class="text-sm text-gray-600 flex-1">
                            Sends birthday SMS to all active members whose birthday is <strong>today</strong>.
                            The scheduler runs this automatically at <strong>06:00 every day</strong>.
                            Use this button to test or to catch up if the scheduler missed a run.
                        </p>
                        <form action="{{ route('admin.settings.sms.birthday.run-now') }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Send birthday SMS to all members with a birthday today?')"
                                    class="px-5 py-2 text-sm font-medium text-white rounded-md transition-colors"
                                    style="background:#7c3aed;" onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
                                Send Now
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        {{-- Cron / Scheduler Setup --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Automatic Scheduler Setup</h3>
                <p class="text-sm text-gray-500">Configure your server so birthday SMS runs automatically at 06:00 every day.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <strong>Required:</strong> The birthday SMS runs via Laravel's task scheduler. The scheduler only works if a cron job (or Windows Task) calls <code class="font-mono bg-amber-100 px-1 rounded">php artisan schedule:run</code> every minute.
                </div>

                {{-- Linux / cPanel --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-1">Linux / cPanel / shared hosting</p>
                    <p class="text-xs text-gray-500 mb-2">Add this single cron entry via cPanel → Cron Jobs (replace the path with your actual project path):</p>
                    <pre class="bg-gray-900 text-green-400 rounded-lg p-3 text-xs overflow-x-auto">* * * * * cd /home/yourusername/public_html/cop-abirem-cms &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</pre>
                </div>

                {{-- Windows XAMPP --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-1">Windows (XAMPP — development)</p>
                    <p class="text-xs text-gray-500 mb-2">Open <strong>Task Scheduler</strong> → Create Basic Task → repeat every 1 minute → Action: start a program:</p>
                    <pre class="bg-gray-900 text-green-400 rounded-lg p-3 text-xs overflow-x-auto">Program : C:\xampp\php\php.exe
Arguments: C:\xampp\htdocs\WEB-SOLUTION\cop-abirem-cms\artisan schedule:run</pre>
                </div>

                <p class="text-xs text-gray-400">
                    Once the scheduler is running, birthday messages are sent automatically at 06:00.
                    Check <code class="font-mono bg-gray-100 px-1 rounded">storage/logs/birthday-sms.log</code> to see the results of each run.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
