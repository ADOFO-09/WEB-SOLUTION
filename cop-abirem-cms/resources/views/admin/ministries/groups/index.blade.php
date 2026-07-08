@extends('layouts.admin')

@section('title', $ministry->name . ' — Sub-Groups')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.ministries.show', $ministry) }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $ministry->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Sub-Groups</p>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            @can('ministry.groups.assign')
            <a href="{{ route('admin.ministries.groups.assign', $ministry) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Assign Members
            </a>
            @endcan
            @can('ministry.groups.create')
            <a href="{{ route('admin.ministries.groups.create', $ministry) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Group
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-green-800">{{ session('success') }}</p>
</div>
@endif

{{-- Summary bar --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-indigo-600">{{ $groups->count() }}</div>
        <div class="text-xs text-gray-500 mt-1">Total Groups</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-gray-900">{{ $totalMembers }}</div>
        <div class="text-xs text-gray-500 mt-1">Total Members</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $totalGrouped }}</div>
        <div class="text-xs text-gray-500 mt-1">Grouped</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-amber-600">{{ $totalUngrouped }}</div>
        <div class="text-xs text-gray-500 mt-1">Unassigned</div>
    </div>
</div>

@if($groups->count() > 0)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($groups as $group)
    <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ $group->name }}</h3>
                @if($group->description)
                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $group->description }}</p>
                @endif
            </div>
            <span class="ml-2 flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $group->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                {{ $group->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div class="px-5 py-4 flex-1">
            <div class="flex items-center justify-between mb-3">
                <span class="text-3xl font-bold text-indigo-600">{{ $group->members_count }}</span>
                <span class="text-xs text-gray-400">members</span>
            </div>

            @if($group->leader)
            <div class="flex items-center gap-2 mt-2">
                <div class="w-7 h-7 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-yellow-700 text-xs font-semibold">
                        {{ substr($group->leader->first_name, 0, 1) }}{{ substr($group->leader->last_name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-700">{{ $group->leader->full_name }}</p>
                    <p class="text-xs text-gray-400">Group Leader</p>
                </div>
            </div>
            @endif
        </div>

        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2">
            <div class="flex gap-2">
                @can('ministry.groups.view')
                <a href="{{ route('admin.ministries.groups.report', [$ministry, $group]) }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Report</a>
                @endcan
                @can('ministry.groups.view')
                <a href="{{ route('admin.ministries.groups.export', [$ministry, $group]) }}"
                   class="text-xs text-gray-500 hover:text-gray-700 font-medium">Export</a>
                @endcan
            </div>
            <div class="flex gap-2">
                @can('ministry.groups.edit')
                <a href="{{ route('admin.ministries.groups.edit', [$ministry, $group]) }}"
                   class="inline-flex items-center px-2.5 py-1 text-xs font-medium border border-gray-300 rounded text-gray-700 bg-white hover:bg-gray-50">
                    Edit
                </a>
                @endcan
                @can('ministry.groups.delete')
                <form method="POST" action="{{ route('admin.ministries.groups.destroy', [$ministry, $group]) }}"
                      onsubmit="return confirm('Delete group \'{{ addslashes($group->name) }}\'? {{ $group->members_count }} member(s) will be unassigned from this group but remain in the ministry.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-2.5 py-1 text-xs font-medium border border-red-200 rounded text-red-700 bg-red-50 hover:bg-red-100">
                        Delete
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-white rounded-lg shadow px-6 py-16 text-center">
    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <h3 class="mt-3 text-sm font-medium text-gray-900">No sub-groups yet</h3>
    <p class="mt-1 text-sm text-gray-500">Divide {{ $ministry->name }} into named sub-groups for organised management.</p>
    @can('ministry.groups.create')
    <div class="mt-6">
        <a href="{{ route('admin.ministries.groups.create', $ministry) }}"
           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Create First Group
        </a>
    </div>
    @endcan
</div>
@endif

@endsection
