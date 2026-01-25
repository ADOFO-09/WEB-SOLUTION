@extends('layouts.admin')

@section('title', 'View Role')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>{{ $role->name }}</h1>
    <div style="display: flex; gap: 0.5rem;">
        @if(auth()->user()->hasPermission('roles.edit'))
        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">Edit Role</a>
        @endif
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Back to Roles</a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-3" style="gap: 1.5rem;">
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header"><h3 style="font-weight: 600;">Permissions</h3></div>
            <div class="card-body">
                @if($permissionsByModule->isEmpty())
                <p style="color: #64748b;">No permissions assigned to this role.</p>
                @else
                @foreach($permissionsByModule as $module => $permissions)
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="font-weight: 600; text-transform: capitalize; margin-bottom: 0.75rem;">{{ str_replace('_', ' ', $module) }}</h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($permissions as $permission)
                        <span class="badge badge-success">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
    
    <div>
        <div class="card">
            <div class="card-header"><h3 style="font-weight: 600;">Role Details</h3></div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <span style="color: #64748b; font-size: 0.875rem;">Slug</span>
                    <div style="font-family: monospace; background: #f1f5f9; padding: 0.5rem; border-radius: 0.25rem;">{{ $role->slug }}</div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <span style="color: #64748b; font-size: 0.875rem;">Type</span>
                    <div>@if($role->is_system)<span class="badge badge-warning">System Role</span>@else<span class="badge badge-secondary">Custom Role</span>@endif</div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <span style="color: #64748b; font-size: 0.875rem;">Users</span>
                    <div style="font-weight: 600;">{{ $role->users->count() }}</div>
                </div>
                @if($role->description)
                <div>
                    <span style="color: #64748b; font-size: 0.875rem;">Description</span>
                    <div>{{ $role->description }}</div>
                </div>
                @endif
            </div>
        </div>
        
        @if($role->users->count() > 0)
        <div class="card" style="margin-top: 1rem;">
            <div class="card-header"><h3 style="font-weight: 600;">Users with this Role</h3></div>
            <div class="card-body" style="padding: 0;">
                @foreach($role->users->take(10) as $user)
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2rem; height: 2rem; border-radius: 9999px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">{{ substr($user->name, 0, 1) }}</div>
                    <div>
                        <div style="font-weight: 500;">{{ $user->name }}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">{{ $user->email }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
