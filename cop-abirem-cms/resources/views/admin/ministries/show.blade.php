@extends('layouts.admin')

@section('title', $ministry->name)

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.ministries.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $ministry->name }}</h1>
            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ministry->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $ministry->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            @can('ministry.groups.view')
            <a href="{{ route('admin.ministries.groups.index', $ministry) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Sub-Groups
            </a>
            @endcan
            <a href="{{ route('admin.ministries.members', $ministry) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Manage Members
            </a>
            @can('ministries.edit')
            <a href="{{ route('admin.ministries.edit', $ministry) }}" 
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
    <!-- Ministry Details -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ministry Details</h3>
            
            <dl class="space-y-4">
                @if($ministry->description)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $ministry->description }}</dd>
                </div>
                @endif

                @if($ministry->meeting_schedule)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Meeting Schedule</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $ministry->meeting_schedule }}</dd>
                </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Members</dt>
                    <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ $ministry->activeMembers->count() }}</dd>
                </div>
            </dl>
        </div>

        @if($ministry->leader)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ministry Leader</h3>
            
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12">
                    @if($ministry->leader->photo_path)
                        <img class="h-12 w-12 rounded-full object-cover" 
                             src="{{ asset('storage/' . $ministry->leader->photo_path) }}" alt="">
                    @else
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-600 font-medium">
                                {{ substr($ministry->leader->first_name, 0, 1) }}{{ substr($ministry->leader->last_name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="ml-4">
                    <a href="{{ route('admin.members.show', $ministry->leader) }}" 
                       class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                        {{ $ministry->leader->full_name }}
                    </a>
                    <p class="text-sm text-gray-500">{{ $ministry->leader->phone_primary }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Sub-Groups summary card --}}
        @can('ministry.groups.view')
        @php $subGroups = $ministry->activeGroups()->withCount('members')->orderBy('name')->get(); @endphp
        @if($subGroups->count() > 0)
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Sub-Groups</h3>
                <a href="{{ route('admin.ministries.groups.index', $ministry) }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Manage</a>
            </div>
            <div class="space-y-2">
                @foreach($subGroups as $sg)
                <a href="{{ route('admin.ministries.groups.report', [$ministry, $sg]) }}"
                   class="flex items-center justify-between rounded px-2 py-1.5 hover:bg-indigo-50 transition-colors">
                    <span class="text-sm text-gray-700">{{ $sg->name }}</span>
                    <span class="text-sm font-bold text-indigo-600">{{ $sg->members_count }}</span>
                </a>
                @endforeach
                @php $ungrouped = $ministry->totalUngroupedMembers(); @endphp
                @if($ungrouped > 0)
                <div class="flex items-center justify-between px-2 py-1.5">
                    <span class="text-sm text-gray-400 italic">Unassigned</span>
                    <span class="text-sm font-bold text-amber-600">{{ $ungrouped }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endcan
    </div>

    <!-- Members List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Members</h3>
                <span class="text-sm text-gray-500">{{ $ministry->activeMembers->count() }} total</span>
            </div>

            @if($ministry->activeMembers->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($ministry->activeMembers as $member)
                <li class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($member->photo_path)
                                <img class="h-10 w-10 rounded-full object-cover" 
                                     src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">
                                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('admin.members.show', $member) }}" 
                               class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                {{ $member->full_name }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $member->phone_primary }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $member->pivot->role == 'leader' ? 'bg-yellow-100 text-yellow-800' : 
                               ($member->pivot->role == 'assistant_leader' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}
                        </span>
                        @if($member->pivot->joined_date)
                        <span class="text-xs text-gray-500">
                            Joined {{ \Carbon\Carbon::parse($member->pivot->joined_date)->format('M Y') }}
                        </span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No members yet</h3>
                <p class="mt-1 text-sm text-gray-500">Start by adding members to this ministry.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.ministries.members', $ministry) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Members
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
