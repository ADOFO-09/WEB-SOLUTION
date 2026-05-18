@extends('layouts.admin')

@section('title', 'Compose SMS')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.sms.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Compose SMS Message</h1>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Low Balance Alert --}}
    @if($balanceAlert)
    <div class="mb-6 rounded-lg border border-amber-300 bg-amber-50 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-amber-800">Low SMS Balance</p>
            <p class="text-sm text-amber-700 mt-0.5">
                Your SMS credit is <strong>{{ number_format($balanceAlert['balance'], 0) }}</strong>
                — at or below your alert threshold of <strong>{{ number_format($balanceAlert['threshold']) }}</strong>.
                Sending this message may exhaust your remaining credits.
                @if($balanceAlert['checked_at'])
                    <span class="text-amber-600 text-xs">(checked {{ \Carbon\Carbon::parse($balanceAlert['checked_at'])->diffForHumans() }})</span>
                @endif
            </p>
        </div>
        <a href="{{ $balanceAlert['topup_url'] ?? route('admin.settings.sms') }}"
           @if($balanceAlert['topup_url'] ?? false) target="_blank" rel="noopener noreferrer" @endif
           class="shrink-0 text-xs font-medium text-amber-700 underline hover:text-amber-900">
            {{ ($balanceAlert['topup_url'] ?? false) ? 'Top up now' : 'SMS Settings' }}
        </a>
    </div>
    @endif

    <form action="{{ route('admin.sms.store') }}" method="POST" class="space-y-6" id="smsForm">
        @csrf

        <!-- Template Selection -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template (Optional)</h3>
            </div>
            <div class="p-6">
                <select id="template_select" onchange="loadTemplate(this.value)" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Write custom message</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" 
                                data-content="{{ $template->content }}"
                                data-category="{{ $template->category }}"
                                {{ ($selectedTemplate && $selectedTemplate->id == $template->id) ? 'selected' : '' }}>
                            [{{ ucfirst($template->category) }}] {{ $template->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Message Content -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Message</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject (for reference)</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" placeholder="e.g., Sunday Reminder"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                    <select name="category" id="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="general">General</option>
                        <option value="financial">Financial</option>
                        <option value="attendance">Attendance</option>
                        <option value="event">Event</option>
                        <option value="reminder">Reminder</option>
                        <option value="birthday">Birthday</option>
                    </select>
                </div>

                <div>
                    <label for="message_content" class="block text-sm font-medium text-gray-700">Message Content * <span class="text-gray-400">(Max 320 characters)</span></label>
                    <textarea name="message_content" id="message_content" rows="5" required maxlength="320"
                              oninput="updateCharCount()"
                              placeholder="Type your message here... Use {name} for member name, {church} for church name."
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message_content', $selectedTemplate->content ?? '') }}</textarea>
                    <div class="mt-1 flex justify-between text-xs">
                        <span class="text-gray-500">Variables: {name}, {church}, {date}</span>
                        <span id="charCount" class="text-gray-500">0 / 320</span>
                    </div>
                    @error('message_content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Recipients -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recipients</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="recipient_type" value="all" checked onchange="toggleRecipientFields()"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">All Members</span>
                            <span class="block text-xs text-gray-500">{{ $memberCount }} members with phone numbers</span>
                        </span>
                    </label>

                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="recipient_type" value="ministry" onchange="toggleRecipientFields()"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">By Ministry</span>
                            <span class="block text-xs text-gray-500">Send to members of a specific ministry</span>
                        </span>
                    </label>

                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="recipient_type" value="custom" onchange="toggleRecipientFields()"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Custom Numbers</span>
                            <span class="block text-xs text-gray-500">Enter phone numbers manually</span>
                        </span>
                    </label>
                </div>

                <!-- Ministry Selection -->
                <div id="ministry_field" style="display: none;">
                    <label for="ministry_id" class="block text-sm font-medium text-gray-700">Select Ministry *</label>
                    <select name="ministry_id" id="ministry_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose a ministry</option>
                        @foreach($ministries as $ministry)
                            <option value="{{ $ministry->id }}">{{ $ministry->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Custom Numbers -->
                <div id="custom_field" style="display: none;">
                    <label for="custom_numbers" class="block text-sm font-medium text-gray-700">Phone Numbers *</label>
                    <textarea name="custom_numbers" id="custom_numbers" rows="4"
                              placeholder="Enter phone numbers, one per line or separated by commas&#10;e.g., 0241234567&#10;0551234567"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Separate numbers with commas, semicolons, or new lines</p>
                </div>
            </div>
        </div>

        <!-- Preview & Actions -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Message Preview</h4>
                <div id="preview" class="text-sm text-gray-900 whitespace-pre-wrap bg-white p-3 rounded border">
                    Your message will appear here...
                </div>
                <p id="smsCount" class="mt-1 text-xs text-gray-500">Estimated: 1 SMS per recipient</p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.sms.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit" name="action" value="draft" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="send" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                        onclick="return confirm('Send this message now?')">
                    Send Now
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function loadTemplate(templateId) {
    if (!templateId) {
        document.getElementById('message_content').value = '';
        updateCharCount();
        return;
    }
    const option = document.querySelector(`#template_select option[value="${templateId}"]`);
    if (option) {
        document.getElementById('message_content').value = option.dataset.content;
        document.getElementById('category').value = option.dataset.category;
        updateCharCount();
    }
}

function updateCharCount() {
    const content = document.getElementById('message_content').value;
    const count = content.length;
    const smsCount = Math.ceil(count / 160) || 1;
    document.getElementById('charCount').textContent = count + ' / 320';
    document.getElementById('charCount').className = count > 320 ? 'text-red-600' : count > 280 ? 'text-amber-500' : 'text-gray-500';
    document.getElementById('preview').textContent = content || 'Your message will appear here...';
    document.getElementById('smsCount').textContent = `Estimated: ${smsCount} SMS per recipient (${count} chars)`;
}

function toggleRecipientFields() {
    const type = document.querySelector('input[name="recipient_type"]:checked').value;
    document.getElementById('ministry_field').style.display = type === 'ministry' ? 'block' : 'none';
    document.getElementById('custom_field').style.display = type === 'custom' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    updateCharCount();
    toggleRecipientFields();
    @if($selectedTemplate)
    loadTemplate('{{ $selectedTemplate->id }}');
    @endif
});
</script>
@endpush
@endsection
