@extends('layouts.admin')

@section('title', 'Users')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Users</h1>
    @if(auth()->user()->hasPermission('users.create'))
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Add User
    </a>
    @endif
</div>
@endsection

@section('content')
<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-input" placeholder="Name or email..." value="{{ request('search') }}">
            </div>
            <div style="min-width: 150px;">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 150px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">{{ $user->name }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $user->role->name }}</span>
                    </td>
                    <td>
                        @if($user->trashed())
                        <span class="badge badge-danger">Deleted</span>
                        @elseif($user->is_active)
                        <span class="badge badge-success">Active</span>
                        @else
                        <span class="badge badge-warning">Inactive</span>
                        @endif
                        
                        @if($user->isLocked())
                        <span class="badge badge-danger" style="margin-left: 0.25rem;">Locked</span>
                        @endif
                    </td>
                    <td style="color: #64748b; font-size: 0.875rem;">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </td>
                    <td style="color: #64748b; font-size: 0.875rem;">
                        {{ $user->created_at->format('M j, Y') }}
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary btn-sm" title="View">
                                <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            
                            @if(auth()->user()->hasPermission('users.edit') && !$user->trashed())
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm" title="Edit">
                                <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            @endif
                            
                            @if($user->trashed() && auth()->user()->hasPermission('users.delete'))
                            <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                    <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                    </svg>
                                </button>
                            </form>
                            @elseif(auth()->user()->hasPermission('users.delete') && $user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                    <svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="card-body" style="border-top: 1px solid #e2e8f0;">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
