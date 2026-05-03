@extends('layouts.admin')

@section('title', 'SMS Messages')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">SMS Messages</h1>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.sms.templates') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Templates
            </a>
            @can('sms.send')
            <a href="{{ route('admin.sms.compose') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Compose Message
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    {{-- Low Balance Alert --}}
    @if($balanceAlert)
    <div class="mb-6 rounded-lg border border-amber-300 bg-amber-50 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-amber-800">Low SMS Balance</p>
            <p class="text-sm text-amber-700 mt-0.5">
                Your current SMS credit is <strong>{{ number_format($balanceAlert['balance'], 0) }}</strong>,
                which is at or below your alert threshold of <strong>{{ number_format($balanceAlert['threshold']) }}</strong>.
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

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Sent</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sent']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Total Recipients</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_recipients']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Delivered</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_delivered']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">This Month</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['this_month']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">Drafts</div>
            <div class="text-2xl font-bold text-gray-600">{{ number_format($stats['drafts']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.sms.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search messages..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div>
                    <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="financial" {{ request('category') == 'financial' ? 'selected' : '' }}>Financial</option>
                        <option value="event" {{ request('category') == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="reminder" {{ request('category') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('admin.sms.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($messages as $message)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $message->subject ?? Str::limit($message->message_content, 40) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($message->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $message->recipient_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($message->recipient_count > 0)
                            <span class="text-green-600">{{ $message->successful_count }}</span>
                            @if($message->failed_count > 0)
                            / <span class="text-red-600">{{ $message->failed_count }} failed</span>
                            @endif
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $message->status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $message->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $message->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('admin.sms.show', $message) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">No messages found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($messages->hasPages())
        <div class="px-4 py-3 border-t">{{ $messages->links() }}</div>
        @endif
    </div>
@endsection
