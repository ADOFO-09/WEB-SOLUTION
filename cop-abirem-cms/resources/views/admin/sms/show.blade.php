@extends('layouts.admin')

@section('title', 'SMS Message Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.sms.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Message Details</h1>
        </div>
        <div class="flex space-x-3">
            @if(in_array($smsMessage->status, ['draft', 'failed']))
            <form action="{{ route('admin.sms.send', $smsMessage) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                        onclick="return confirm('Send this message to {{ $smsMessage->recipient_count }} recipients?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Send Now
                </button>
            </form>
            @endif
            @if($smsMessage->status === 'draft')
            <form action="{{ route('admin.sms.destroy', $smsMessage) }}" method="POST" class="inline"
                  onsubmit="return confirm('Delete this draft?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">Delete</button>
            </form>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Message Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Message Content -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">{{ $smsMessage->subject ?? 'Message' }}</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $smsMessage->status_badge }}">
                    {{ ucfirst(str_replace('_', ' ', $smsMessage->status)) }}
                </span>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-900 whitespace-pre-wrap font-mono">{{ $smsMessage->message_content }}</div>
                @if(!empty($smsMessage->manual_placeholder_values))
                <div class="mt-3 rounded-md border border-amber-100 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                    <p class="font-semibold mb-1">Message fields used:</p>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-0.5">
                        @foreach($smsMessage->manual_placeholder_values as $key => $val)
                        <dt class="font-mono text-amber-700">{{'{'.$key.'}'}}</dt>
                        <dd class="text-amber-900">{{ $val ?: '—' }}</dd>
                        @endforeach
                    </dl>
                </div>
                @endif
                
                <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Category</dt>
                        <dd class="font-medium text-gray-900">{{ ucfirst($smsMessage->category) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Type</dt>
                        <dd class="font-medium text-gray-900">{{ ucfirst($smsMessage->message_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Sent By</dt>
                        <dd class="font-medium text-gray-900">{{ $smsMessage->sentBy->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Created</dt>
                        <dd class="font-medium text-gray-900">{{ $smsMessage->created_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @if($smsMessage->sent_at)
                    <div>
                        <dt class="text-gray-500">Sent At</dt>
                        <dd class="font-medium text-gray-900">{{ $smsMessage->sent_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Recipients -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recipients ({{ $smsMessage->recipient_count }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message Sent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($smsMessage->recipients as $recipient)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $recipient->recipient_name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $recipient->phone_number }}</td>
                            <td class="px-6 py-4 text-xs text-gray-700 max-w-xs">
                                @if($recipient->resolved_message)
                                    <span class="line-clamp-2" title="{{ $recipient->resolved_message }}">{{ $recipient->resolved_message }}</span>
                                @else
                                    <span class="text-gray-400 italic">{{ Str::limit($smsMessage->message_content, 80) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $recipient->status_badge }}">
                                    {{ ucfirst($recipient->status) }}
                                </span>
                                @if($recipient->error_message)
                                <p class="text-xs text-red-500 mt-1">{{ $recipient->error_message }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $recipient->sent_at?->format('g:i A') ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stats Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Delivery Stats -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Total Recipients</span>
                    <span class="text-lg font-bold text-gray-900">{{ $smsMessage->recipient_count }}</span>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-green-600">Sent/Delivered</span>
                        <span class="font-medium">{{ $recipientStats['sent'] + $recipientStats['delivered'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" 
                             style="width: {{ $smsMessage->recipient_count > 0 ? (($recipientStats['sent'] + $recipientStats['delivered']) / $smsMessage->recipient_count * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-red-600">Failed</span>
                        <span class="font-medium">{{ $recipientStats['failed'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" 
                             style="width: {{ $smsMessage->recipient_count > 0 ? ($recipientStats['failed'] / $smsMessage->recipient_count * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Pending</span>
                        <span class="font-medium">{{ $recipientStats['pending'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gray-400 h-2 rounded-full" 
                             style="width: {{ $smsMessage->recipient_count > 0 ? ($recipientStats['pending'] / $smsMessage->recipient_count * 100) : 0 }}%"></div>
                    </div>
                </div>

                @if($smsMessage->delivery_rate > 0)
                <div class="pt-3 border-t text-center">
                    <span class="text-3xl font-bold text-green-600">{{ $smsMessage->delivery_rate }}%</span>
                    <p class="text-sm text-gray-500">Delivery Rate</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Message Info -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Message Info</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Characters</dt>
                    <dd class="font-medium">{{ strlen($smsMessage->message_content) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">SMS Count</dt>
                    <dd class="font-medium">{{ ceil(strlen($smsMessage->message_content) / 160) }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
