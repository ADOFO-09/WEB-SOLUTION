@extends('layouts.admin')

@section('title', $visitor->full_name . ' - Visitor')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.visitors.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $visitor->full_name }}</h1>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            @if($visitor->canBeConverted())
            <a href="{{ route('admin.visitors.convert.form', $visitor) }}" 
               class="inline-flex items-center px-4 py-2 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-600 bg-white hover:bg-green-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Convert to Member
            </a>
            @endif
            @can('visitors.edit')
            <a href="{{ route('admin.visitors.edit', $visitor) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Visitor Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <div class="mx-auto h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-indigo-600 font-bold text-2xl">
                        {{ substr($visitor->first_name, 0, 1) }}{{ substr($visitor->last_name, 0, 1) }}
                    </span>
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $visitor->full_name }}</h2>
                
                @php
                    $statusColors = [
                        'pending'        => 'bg-yellow-100 text-yellow-800',
                        'contacted'      => 'bg-blue-100 text-blue-800',
                        'interested'     => 'bg-indigo-100 text-indigo-800',
                        'not_interested' => 'bg-gray-100 text-gray-800',
                        'converted'      => 'bg-purple-100 text-purple-800',
                    ];
                @endphp
                <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$visitor->follow_up_status] ?? 'bg-gray-100' }}">
                    {{ ucfirst(str_replace('_', ' ', $visitor->follow_up_status)) }}
                </span>
            </div>

            <dl class="mt-6 space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Phone</dt>
                    <dd class="text-sm font-medium text-gray-900">
                        <a href="tel:{{ $visitor->phone }}" class="text-indigo-600">{{ $visitor->phone }}</a>
                    </dd>
                </div>
                @if($visitor->email)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Email</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $visitor->email }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">First Visit</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $visitor->first_visit_date->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Total Visits</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $visitor->visits->count() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Source</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $visitor->referral_source)) }}</dd>
                </div>
                @if($visitor->referredBy)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Referred By</dt>
                    <dd class="text-sm font-medium text-gray-900">
                        <a href="{{ route('admin.members.show', $visitor->referredBy) }}" class="text-indigo-600">
                            {{ $visitor->referredBy->full_name }}
                        </a>
                    </dd>
                </div>
                @endif
            </dl>

            @if($visitor->address)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <dt class="text-sm text-gray-500">Address</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $visitor->address }}</dd>
            </div>
            @endif
        </div>

        @if($visitor->prayer_request)
        <!-- Prayer Request -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Prayer Request</h3>
            <p class="text-sm text-gray-600">{{ $visitor->prayer_request }}</p>
        </div>
        @endif

        @if($visitor->isConverted())
        <!-- Converted Member -->
        <div class="bg-green-50 rounded-lg border border-green-200 p-6">
            <h3 class="text-lg font-medium text-green-800 mb-3">Converted to Member</h3>
            <p class="text-sm text-green-700 mb-3">This visitor has been converted to a member.</p>
            <a href="{{ route('admin.members.show', $visitor->convertedToMember) }}" 
               class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800">
                View Member Profile →
            </a>
        </div>
        @endif
    </div>

    <!-- Right Column -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Add Follow-up Log -->
        @if(!$visitor->isConverted())
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Add Follow-up Log</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.visitors.follow-up', $visitor) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="contact_method" class="block text-sm font-medium text-gray-700">Contact Method *</label>
                            <select name="contact_method" id="contact_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="phone">Phone Call</option>
                                <option value="sms">SMS</option>
                                <option value="email">Email</option>
                                <option value="visit">Home Visit</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                        <div>
                            <label for="outcome" class="block text-sm font-medium text-gray-700">Outcome *</label>
                            <select name="outcome" id="outcome" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="reached">Reached</option>
                                <option value="no_answer">No Answer</option>
                                <option value="callback">Callback Requested</option>
                                <option value="interested">Interested</option>
                                <option value="not_interested">Not Interested</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="submit"
                                    class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Add Log
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div>
                            <label for="next_follow_up_date" class="block text-sm font-medium text-gray-700">Next Follow-up Date</label>
                            <input type="date" name="next_follow_up_date" id="next_follow_up_date"
                                   min="{{ now()->toDateString() }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Follow-up History -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Follow-up History</h3>
            </div>
            @if($visitor->followUpLogs->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($visitor->followUpLogs as $log)
                <li class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $log->contact_method)) }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $log->outcome == 'interested' ? 'bg-green-100 text-green-800' : 
                                       ($log->outcome == 'not_interested' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->outcome)) }}
                                </span>
                            </div>
                            @if($log->notes)
                            <p class="mt-1 text-sm text-gray-600">{{ $log->notes }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ $log->contact_date->format('M d, Y g:i A') }}</p>
                            <p class="text-xs text-gray-400">by {{ $log->contactedBy->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No follow-up logs yet.</p>
            </div>
            @endif
        </div>

        <!-- Visit History -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Visit History</h3>
            </div>

            @if(!$visitor->isConverted())
            <!-- Record Visit Form -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <p class="text-sm font-medium text-gray-700 mb-3">Record a New Visit</p>
                <form action="{{ route('admin.visitors.record-visit', $visitor) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label for="visit_date" class="block text-xs font-medium text-gray-600">Visit Date</label>
                            <input type="date" name="visit_date" id="visit_date"
                                   value="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="service_type_id" class="block text-xs font-medium text-gray-600">Service Type</label>
                            <select name="service_type_id" id="service_type_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">— Select (optional) —</option>
                                @foreach($serviceTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Record Visit
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="visit_notes" class="block text-xs font-medium text-gray-600">Notes (optional)</label>
                        <input type="text" name="notes" id="visit_notes" placeholder="Any notes about this visit..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </form>
            </div>
            @endif

            @if($visitor->visits->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($visitor->visits as $visit)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $visit->visit_date->format('M d, Y') }}
                                <span class="block text-xs text-gray-400">{{ $visit->visit_date->format('l') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($visit->serviceType)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $visit->serviceType->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $visit->notes ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No visit records yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
