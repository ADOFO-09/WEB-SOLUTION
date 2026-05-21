@extends('layouts.admin')

@section('title', 'User Management')

@section('header')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
    <div class="flex items-center space-x-2">
        @if(auth()->user()->hasPermission('roles.view'))
        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Manage Roles
        </a>
        @endif
        @can('users.create')
        <a href="{{ route('admin.users.create') }}"
           class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition flex items-center text-sm"
           style="background-color: #1e3a5f;">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add User
        </a>
        @endcan
    </div>
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

@if(session('info'))
<div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm">
    {{ session('info') }}
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Total Users</div>
        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Active</div>
        <div class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Linked to Members</div>
        <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['linked'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">Not Linked</div>
        <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['unlinked'] }}</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…"
               class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        <select name="role_id" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">All Roles</option>
            @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <select name="member_status" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">All Member Status</option>
            <option value="linked"   {{ request('member_status') == 'linked'   ? 'selected' : '' }}>Linked to Member</option>
            <option value="unlinked" {{ request('member_status') == 'unlinked' ? 'selected' : '' }}>Not Linked</option>
        </select>
        <div class="flex space-x-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 text-center">Clear</a>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Linked Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <!-- User -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-sm font-semibold"
                                 style="background-color: #1e3a5f;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    <!-- Role -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $user->role->name }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">No role</span>
                        @endif
                    </td>

                    <!-- Linked Member -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->member)
                            <a href="{{ route('admin.members.show', $user->member) }}"
                               class="group">
                                <div class="text-sm font-medium text-indigo-600 group-hover:text-indigo-800">{{ $user->member->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->member->member_id }}</div>
                            </a>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Not linked
                            </span>
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>

                    <!-- Last Login -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-1">

                            {{-- View --}}
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="p-1.5 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            @can('users.edit')
                            {{-- Edit --}}
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="p-1.5 text-indigo-500 hover:text-indigo-800 hover:bg-indigo-50 rounded" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Toggle Status (not self) --}}
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                        onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} user \'{{ addslashes($user->name) }}\'?')"
                                        class="p-1.5 rounded {{ $user->is_active ? 'text-yellow-500 hover:text-yellow-700 hover:bg-yellow-50' : 'text-green-500 hover:text-green-700 hover:bg-green-50' }}">
                                    @if($user->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @endif
                                </button>
                            </form>
                            @endif

                            {{-- Link to Member --}}
                            @if(!$user->member_id)
                            <a href="{{ route('admin.users.link-member.form', $user) }}"
                               class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded" title="Link to Member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </a>
                            @endif
                            @endcan

                            @can('users.delete')
                            {{-- Delete (not self) --}}
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Delete"
                                        onclick="return confirm('Permanently delete user \'{{ addslashes($user->name) }}\'? This cannot be undone.')"
                                        class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            @endcan

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        No users found.
                        @can('users.create')
                        <a href="{{ route('admin.users.create') }}" class="ml-1 text-indigo-600 hover:underline">Add one now.</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
