@extends('layouts.admin')

@section('title', 'Projects')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
        @can('finance.manage')
        <a href="{{ route('admin.finance.projects.create') }}"
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white"
           style="background-color: #1e3a5f;">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Project
        </a>
        @endcan
    </div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raised</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width:140px;">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start / End</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $project)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $project->name }}</div>
                        @if($project->description)
                        <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($project->description, 60) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'planned'   => 'bg-gray-100 text-gray-700',
                                'active'    => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $currencySymbol }} {{ number_format($project->target_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-700">
                        {{ $currencySymbol }} {{ number_format($project->amount_raised, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2" style="min-width:80px;">
                                <div class="h-2 rounded-full" style="width: {{ $project->progress_percentage }}%; background-color: #d4af37;"></div>
                            </div>
                            <span class="text-xs text-gray-600 whitespace-nowrap">{{ $project->progress_percentage }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                        <div>{{ $project->start_date->format('M d, Y') }}</div>
                        @if($project->end_date)
                        <div>{{ $project->end_date->format('M d, Y') }}</div>
                        @else
                        <div class="text-gray-400">No end date</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.finance.projects.show', $project) }}"
                               class="text-indigo-600 hover:text-indigo-900">View</a>
                            @can('finance.manage')
                            <a href="{{ route('admin.finance.projects.edit', $project) }}"
                               class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            <form action="{{ route('admin.finance.projects.destroy', $project) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Delete project \'{{ $project->name }}\'?')"
                                        class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No projects found.
                        @can('finance.manage')
                        <a href="{{ route('admin.finance.projects.create') }}" class="ml-1 text-indigo-600 hover:underline">Create one now.</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($projects->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection
