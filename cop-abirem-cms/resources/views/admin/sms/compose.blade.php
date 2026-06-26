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

    <form action="{{ route('admin.sms.store') }}" method="POST" id="smsForm"
          x-data="smsCompose(
              @js(old('message_content', $selectedTemplate->content ?? '')),
              @js($uiRegistry),
              @js($systemPreview),
              @js(old('placeholders', []))
          )"
          @submit.prevent="submitForm($event)"
          class="space-y-6">
        @csrf

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-sm font-semibold text-red-800 mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Template Selection --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template <span class="text-gray-400 text-sm font-normal">(optional)</span></h3>
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

        {{-- Message Card --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Message</h3>
            </div>
            <div class="p-6 space-y-4">

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject <span class="text-gray-400">(for reference only)</span></label>
                    <input type="text" name="subject" id="subject"
                           value="{{ old('subject') }}"
                           placeholder="e.g., Sunday Reminder"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                    <select name="category" id="category" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(['general'=>'General','financial'=>'Financial','attendance'=>'Attendance','event'=>'Event','reminder'=>'Reminder','birthday'=>'Birthday'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Message Textarea --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="message_content" class="block text-sm font-medium text-gray-700">
                            Message Content * <span class="text-gray-400">(max 320 chars)</span>
                        </label>
                        {{-- Insert Placeholder button --}}
                        <div class="relative">
                            <button type="button"
                                    @click="showPlaceholderMenu = !showPlaceholderMenu"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border border-indigo-300 bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Insert Placeholder
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Dropdown panel --}}
                            <div x-show="showPlaceholderMenu"
                                 @click.outside="showPlaceholderMenu = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 z-20 mt-1 w-72 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
                                 style="display:none;">

                                {{-- Recipient group --}}
                                <div class="px-3 pt-3 pb-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Recipient Fields <span class="text-indigo-500">(auto per person)</span></p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="[key, def] in Object.entries(registry.recipient)" :key="key">
                                            <button type="button"
                                                    @click="insertPlaceholder(key)"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                                <span x-text="'{' + key + '}'"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 mx-3 my-2"></div>

                                {{-- Manual group --}}
                                <div class="px-3 pb-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Message Fields <span class="text-amber-500">(you fill in)</span></p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="[key, def] in Object.entries(registry.manual)" :key="key">
                                            <button type="button"
                                                    @click="insertPlaceholder(key)"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100">
                                                <span x-text="'{' + key + '}'"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 mx-3 my-2"></div>

                                {{-- System group --}}
                                <div class="px-3 pb-3">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Church / System Fields <span class="text-green-500">(auto from settings)</span></p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="[key, def] in Object.entries(registry.system)" :key="key">
                                            <button type="button"
                                                    @click="insertPlaceholder(key)"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono border border-green-200 bg-green-50 text-green-700 hover:bg-green-100">
                                                <span x-text="'{' + key + '}'"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <textarea name="message_content" id="message_content" rows="5" required maxlength="320"
                              x-model="message"
                              @input="showPlaceholderMenu = false"
                              placeholder="Type your message here... e.g. Dear {member_name}, you are invited to {service_name} on {date}. — {church_name}"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"></textarea>

                    <div class="mt-1 flex justify-between text-xs">
                        <span class="text-gray-400">Use the button above to insert placeholders at the cursor.</span>
                        <span :class="message.length > 320 ? 'text-red-600 font-semibold' : message.length > 280 ? 'text-amber-500' : 'text-gray-500'"
                              x-text="message.length + ' / 320 · ~' + (Math.ceil(message.length / 160) || 1) + ' SMS'"></span>
                    </div>
                    @error('message_content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ── Dynamic manual-placeholder inputs ── --}}
                <template x-if="detectedManual.length > 0">
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 space-y-3">
                        <p class="text-sm font-semibold text-amber-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Fill in message details
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="ph in detectedManual" :key="ph.key">
                                <div>
                                    <label class="block text-xs font-medium text-amber-900 mb-0.5">
                                        <span x-text="ph.label"></span>
                                        <span class="font-mono text-amber-600 ml-1" x-text="'(' + '{' + ph.key + '}' + ')'"></span>
                                    </label>
                                    <input type="text"
                                           :name="'placeholders[' + ph.key + ']'"
                                           x-model="manualValues[ph.key]"
                                           :placeholder="'Enter ' + ph.label.toLowerCase()"
                                           class="block w-full rounded-md border-amber-300 shadow-sm text-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- ── Warnings ── --}}
                <template x-if="warnings.length > 0">
                    <div class="space-y-1.5">
                        <template x-for="w in warnings" :key="w.message">
                            <div class="flex items-start gap-2 rounded-md px-3 py-2 text-sm"
                                 :class="w.level === 'error' ? 'bg-red-50 border border-red-200 text-red-700' : 'bg-amber-50 border border-amber-200 text-amber-700'">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span x-text="w.message"></span>
                            </div>
                        </template>
                    </div>
                </template>

            </div>
        </div>

        {{-- Recipients --}}
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

                <div id="ministry_field" style="display:none;">
                    <label for="ministry_id" class="block text-sm font-medium text-gray-700">Select Ministry *</label>
                    <select name="ministry_id" id="ministry_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose a ministry</option>
                        @foreach($ministries as $ministry)
                            <option value="{{ $ministry->id }}">{{ $ministry->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="custom_field" style="display:none;">
                    <label for="custom_numbers" class="block text-sm font-medium text-gray-700">Phone Numbers *</label>
                    <textarea name="custom_numbers" id="custom_numbers" rows="4"
                              placeholder="Enter phone numbers, one per line or separated by commas&#10;e.g., 0241234567&#10;0551234567"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Separate numbers with commas, semicolons, or new lines</p>
                </div>
            </div>
        </div>

        {{-- Preview & Actions --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Live Preview
                <span class="text-xs text-gray-400 font-normal">— <span class="text-indigo-600">[Name]</span> = recipient's own value · <span class="text-amber-600">⚠ missing</span> = fill field above · <span class="text-green-600">church values</span> = from Settings</span>
            </h4>
            <div class="bg-gray-50 rounded-lg p-4 text-sm whitespace-pre-wrap font-mono min-h-[3rem] leading-relaxed"
                 x-html="previewHtml || '<span class=\'text-gray-400\'>Your message will appear here…</span>'"></div>

            <div class="mt-5 flex justify-end space-x-3">
                <a href="{{ route('admin.sms.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="send"
                        :disabled="hasBlockingErrors"
                        :title="hasBlockingErrors ? 'Fix unknown placeholders before sending' : ''"
                        :class="hasBlockingErrors ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 disabled:opacity-50">
                    Send Now
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
function loadTemplate(templateId) {
    const ta = document.getElementById('message_content');
    if (!templateId) {
        ta.value = '';
        ta.dispatchEvent(new Event('input'));
        return;
    }
    const option = document.querySelector(`#template_select option[value="${templateId}"]`);
    if (option) {
        ta.value = option.dataset.content;
        ta.dispatchEvent(new Event('input')); // syncs Alpine x-model
        document.getElementById('category').value = option.dataset.category;
    }
}

function toggleRecipientFields() {
    const type = document.querySelector('input[name="recipient_type"]:checked').value;
    document.getElementById('ministry_field').style.display = type === 'ministry' ? 'block' : 'none';
    document.getElementById('custom_field').style.display   = type === 'custom'   ? 'block' : 'none';
}

function smsCompose(initialMessage, registry, systemPreview, oldManualValues) {
    return {
        message:             initialMessage || '',
        manualValues:        oldManualValues || {},
        registry:            registry,
        systemPreview:       systemPreview,
        showPlaceholderMenu: false,

        // ── Computed: list of Type-B (manual) keys found in the current message ──
        get detectedManual() {
            const regex = /\{\s*([a-zA-Z0-9_]+)\s*\}/g;
            const seen  = new Set();
            const result = [];
            let m;
            while ((m = regex.exec(this.message)) !== null) {
                const key = m[1].toLowerCase();
                if (this.registry.manual[key] && !seen.has(key)) {
                    seen.add(key);
                    result.push({ key, label: this.registry.manual[key].label });
                }
            }
            return result;
        },

        // ── Computed: validation warnings ──
        get warnings() {
            const allKeys = new Set([
                ...Object.keys(this.registry.recipient),
                ...Object.keys(this.registry.manual),
                ...Object.keys(this.registry.system),
            ]);
            const result       = [];
            const seenUnknown  = new Set();
            const seenMissing  = new Set();
            const regex        = /\{\s*([a-zA-Z0-9_]+)\s*\}/g;
            let m;
            while ((m = regex.exec(this.message)) !== null) {
                const key = m[1].toLowerCase();
                if (!allKeys.has(key) && !seenUnknown.has(key)) {
                    seenUnknown.add(key);
                    result.push({ level: 'error', message: 'Unknown placeholder: {' + key + '} — fix the typo or it will be sent literally.' });
                } else if (this.registry.manual[key] && !(this.manualValues[key] || '').trim() && !seenMissing.has(key)) {
                    seenMissing.add(key);
                    const label = this.registry.manual[key].label || key;
                    result.push({ level: 'warning', message: 'Missing value for: ' + label + ' — will be sent blank.' });
                }
            }
            return result;
        },

        // ── Computed: true when unknown placeholders block sending ──
        get hasBlockingErrors() {
            return this.warnings.some(w => w.level === 'error');
        },

        // ── Computed: HTML preview string ──
        get previewHtml() {
            if (!this.message.trim()) return '';
            const escaped = this.message
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
            const regex = /\{\s*([a-zA-Z0-9_]+)\s*\}/g;
            return escaped.replace(regex, (_, rawKey) => {
                const key = rawKey.toLowerCase();
                if (this.registry.recipient[key]) {
                    return '<span style="color:#4f46e5;font-weight:600">[' + this.registry.recipient[key].label + ']</span>';
                }
                if (this.registry.manual[key]) {
                    const val = (this.manualValues[key] || '').trim();
                    return val
                        ? '<span style="color:#d97706;font-weight:600">' + val.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span>'
                        : '<span style="color:#dc2626;font-weight:600">⚠ ' + (this.registry.manual[key].label || key) + ' missing</span>';
                }
                if (this.registry.system[key]) {
                    const val = this.systemPreview[key] || '';
                    return val
                        ? '<span style="color:#16a34a;font-weight:600">' + val.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span>'
                        : '<span style="color:#16a34a">(' + key + ')</span>';
                }
                return '<span style="color:#dc2626;text-decoration:underline dotted">⚠ Unknown: {' + key + '}</span>';
            });
        },

        // ── Insert placeholder at textarea cursor position ──
        insertPlaceholder(key) {
            const ta    = document.getElementById('message_content');
            const start = ta.selectionStart;
            const end   = ta.selectionEnd;
            const ph    = '{' + key + '}';
            ta.value    = ta.value.slice(0, start) + ph + ta.value.slice(end);
            ta.dispatchEvent(new Event('input')); // syncs x-model
            ta.focus();
            ta.selectionStart = ta.selectionEnd = start + ph.length;
            this.showPlaceholderMenu = false;
        },

        // ── Form submit: validate, confirm, then post ──
        submitForm(event) {
            const action = event.submitter?.value;

            if (action === 'send') {
                // Hard block: unknown placeholders
                if (this.hasBlockingErrors) {
                    const errs = this.warnings
                        .filter(w => w.level === 'error')
                        .map(w => '• ' + w.message)
                        .join('\n');
                    alert('Cannot send — please fix these first:\n\n' + errs);
                    return;
                }

                // Soft warn: missing manual values
                const missing = this.warnings.filter(w => w.level === 'warning');
                if (missing.length > 0) {
                    const list = missing.map(w => '• ' + w.message).join('\n');
                    if (!confirm('Some placeholders have no value:\n\n' + list + '\n\nThey will be sent blank. Continue?')) {
                        return;
                    }
                }

                if (!confirm('Send this message now to all recipients?')) return;
            }

            // Re-attach the action value and submit natively
            const form   = document.getElementById('smsForm');
            let   hidden = form.querySelector('input[name="action"]');
            if (!hidden) {
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'action';
                form.appendChild(hidden);
            }
            hidden.value = action || 'draft';
            form.submit();
        },
    };
}

document.addEventListener('DOMContentLoaded', function () {
    toggleRecipientFields();
    @if($selectedTemplate)
    loadTemplate('{{ $selectedTemplate->id }}');
    @endif
});
</script>
@endpush
@endsection
