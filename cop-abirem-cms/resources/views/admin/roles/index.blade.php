@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Roles & Permissions</h1>
    @if(auth()->user()->hasPermission('roles.create'))
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Add Role</a>
    @endif
</div>
@endsection

@section('content')
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Description</th>
                    <th>Users</th>
                    <th>Permissions</th>
                    <th>Type</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td style="font-weight: 600;">{{ $role->name }}</td>
                    <td style="color: #64748b; max-width: 300px;">{{ Str::limit($role->description, 50) }}</td>
                    <td><span class="badge badge-info">{{ $role->users_count }}</span></td>
                    <td><span class="badge badge-secondary">{{ $role->permissions_count }}</span></td>
                    <td>
                        @if($role->is_system)
                        <span class="badge badge-warning">System</span>
                        @else
                        <span class="badge badge-secondary">Custom</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary btn-sm">View</a>
                            @if(auth()->user()->hasPermission('permissions.assign'))
                            <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-secondary btn-sm">Permissions</a>
                            @endif
                            @if(auth()->user()->hasPermission('roles.edit'))
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-secondary btn-sm">Edit</a>
                            @endif
                            @if(auth()->user()->hasPermission('roles.delete') && !$role->is_system && $role->users_count === 0)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="display: inline;" onsubmit="return confirm('Delete this role?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
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
