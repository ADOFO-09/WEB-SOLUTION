@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('header')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
    @if(auth()->user()->hasPermission('roles.manage'))
    <a href="{{ route('admin.roles.create') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg hover:opacity-90"
       style="background-color: #1e3a5f;">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Role
    </a>
    @endif
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-gray-900">{{ $role->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                        {{ Str::limit($role->description, 60) ?: '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $role->users_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $role->permissions_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($role->is_system)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            System
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            Custom
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">

                            {{-- View --}}
                            <a href="{{ route('admin.roles.show', $role) }}"
                               class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">
                                View
                            </a>

                            {{-- Permissions --}}
                            @if(auth()->user()->hasPermission('permissions.assign'))
                            <a href="{{ route('admin.roles.permissions', $role) }}"
                               class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded hover:bg-indigo-100">
                                Permissions
                            </a>
                            @endif

                            {{-- Edit (system roles: name/description only; custom: full edit) --}}
                            @if(auth()->user()->hasPermission('roles.manage'))
                            <a href="{{ route('admin.roles.edit', $role) }}"
                               class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100">
                                Edit
                            </a>
                            @endif

                            {{-- Delete — custom roles only --}}
                            @if(auth()->user()->hasPermission('roles.manage') && !$role->is_system)
                                @if($role->users_count > 0)
                                <span title="Cannot delete: {{ $role->users_count }} user(s) assigned. Reassign them first."
                                      class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-50 rounded cursor-not-allowed border border-dashed border-gray-300">
                                    Delete
                                </span>
                                @else
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline"
                                      onsubmit="return confirm('Permanently delete the role \'{{ addslashes($role->name) }}\'?\n\nThis cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
