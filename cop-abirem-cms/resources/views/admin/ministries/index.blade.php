@extends('layouts.admin')

@section('title', 'Ministries')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Ministries</h1>
        @can('ministries.create')
        <a href="{{ route('admin.ministries.create') }}" 
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Ministry
        </a>
        @endcan
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.ministries.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search ministries..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Filter
                    </button>
                    <a href="{{ route('admin.ministries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Ministries Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ministries as $ministry)
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            <a href="{{ route('admin.ministries.show', $ministry) }}" class="hover:text-indigo-600">
                                {{ $ministry->name }}
                            </a>
                        </h3>
                        @if($ministry->meeting_schedule)
                        <p class="text-sm text-gray-500 mt-1">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $ministry->meeting_schedule }}
                        </p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ministry->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $ministry->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($ministry->type && $ministry->type !== 'general')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">
                            {{ \App\Models\Ministry::TYPES[$ministry->type] ?? $ministry->type }}
                        </span>
                        @endif
                    </div>
                </div>

                @if($ministry->description)
                <p class="mt-3 text-sm text-gray-600 line-clamp-2">{{ $ministry->description }}</p>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ $ministry->active_members_count }} members</span>
                    </div>

                    @if($ministry->leader)
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-6 w-6">
                            @if($ministry->leader->photo_path)
                                <img class="h-6 w-6 rounded-full object-cover" 
                                     src="{{ asset('storage/' . $ministry->leader->photo_path) }}" alt="">
                            @else
                                <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium text-xs">
                                        {{ substr($ministry->leader->first_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <span class="ml-2 text-xs text-gray-500">{{ $ministry->leader->full_name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                <a href="{{ route('admin.ministries.show', $ministry) }}" 
                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</a>
                @can('ministries.edit')
                <a href="{{ route('admin.ministries.edit', $ministry) }}" 
                   class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">Edit</a>
                @endcan
                <a href="{{ route('admin.ministries.members', $ministry) }}" 
                   class="text-gray-600 hover:text-gray-900 text-sm font-medium">Members</a>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No ministries found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new ministry.</p>
                @can('ministries.create')
                <div class="mt-6">
                    <a href="{{ route('admin.ministries.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Ministry
                    </a>
                </div>
                @endcan
            </div>
        </div>
        @endforelse
    </div>

    @if($ministries->hasPages())
    <div class="mt-6">
        {{ $ministries->links() }}
    </div>
    @endif
@endsection
