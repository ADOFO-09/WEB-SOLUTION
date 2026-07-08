@extends('layouts.admin')

@section('title', $group->name . ' — ' . $ministry->name)

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.ministries.groups.index', $ministry) }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $ministry->name }}</p>
            </div>
            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $group->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                {{ $group->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            @can('ministry.groups.view')
            <a href="{{ route('admin.ministries.groups.export', [$ministry, $group]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
            @endcan
            @can('ministry.groups.assign')
            <a href="{{ route('admin.ministries.groups.assign', $ministry) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Manage Assignments
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: group details --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Group Details</h3>
            <dl class="space-y-3">
                @if($group->description)
                <div>
                    <dt class="text-xs font-medium text-gray-400">Description</dt>
                    <dd class="mt-0.5 text-sm text-gray-900">{{ $group->description }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-400">Total Members</dt>
                    <dd class="mt-0.5 text-3xl font-bold text-indigo-600">{{ $members->count() }}</dd>
                </div>
            </dl>
        </div>

        @if($group->leader)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Group Leader</h3>
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 h-10 w-10">
                    @if($group->leader->photo_path)
                    <img class="h-10 w-10 rounded-full object-cover"
                         src="{{ asset('storage/' . $group->leader->photo_path) }}" alt="">
                    @else
                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <span class="text-yellow-700 font-semibold text-sm">
                            {{ substr($group->leader->first_name, 0, 1) }}{{ substr($group->leader->last_name, 0, 1) }}
                        </span>
                    </div>
                    @endif
                </div>
                <div>
                    <a href="{{ route('admin.members.show', $group->leader) }}"
                       class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                        {{ $group->leader->full_name }}
                    </a>
                    @if($group->leader->phone_primary)
                    <p class="text-xs text-gray-400">{{ $group->leader->phone_primary }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Other groups in this ministry --}}
        @if($allGroups->count() > 1)
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">All Groups</h3>
            <ul class="space-y-1">
                @foreach($allGroups as $g)
                <li>
                    <a href="{{ route('admin.ministries.groups.report', [$ministry, $g]) }}"
                       class="flex items-center justify-between px-2 py-1.5 rounded text-sm hover:bg-gray-50 {{ $g->id === $group->id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        <span>{{ $g->name }}</span>
                        <span class="text-xs text-gray-400">{{ $g->members_count ?? '' }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- Right: member list --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-medium text-gray-900">Members</h3>
                <span class="text-sm text-gray-400">{{ $members->count() }} total</span>
            </div>

            @if($members->count() > 0)
            <ul class="divide-y divide-gray-100">
                @foreach($members as $member)
                <li class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($member->photo_path)
                            <img class="h-10 w-10 rounded-full object-cover"
                                 src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                            @else
                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-gray-500 font-medium text-sm">
                                    {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                </span>
                            </div>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('admin.members.show', $member) }}"
                               class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                {{ $member->full_name }}
                            </a>
                            <p class="text-xs text-gray-400">{{ $member->phone_primary }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $member->pivot->role == 'leader' ? 'bg-yellow-100 text-yellow-800' :
                               ($member->pivot->role == 'assistant_leader' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}
                        </span>
                        @if($member->pivot->joined_date)
                        <span class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($member->pivot->joined_date)->format('M Y') }}
                        </span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="mt-3 text-sm font-medium text-gray-900">No members assigned</h3>
                <p class="mt-1 text-sm text-gray-400">Assign members to this group from the assignment page.</p>
                @can('ministry.groups.assign')
                <a href="{{ route('admin.ministries.groups.assign', $ministry) }}"
                   class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                    Assign Members
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
