@extends('layouts.admin')

@section('title', 'Edit Role')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Edit Role: {{ $role->name }}</h1>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Back to Roles</a>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.roles.update', $role) }}">
    @csrf
    @method('PUT')
    
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header"><h3 style="font-weight: 600;">Role Information</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Role Name <span style="color: #ef4444;">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $role->name) }}" required style="max-width: 400px;">
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="3" style="max-width: 600px;">{{ old('description', $role->description) }}</textarea>
            </div>
            
            @if($role->is_system)
            <div class="alert alert-warning" style="margin-top: 1rem;">
                This is a system role. The slug cannot be changed.
            </div>
            @endif
        </div>
    </div>
    
    <div class="card">
        <div class="card-header"><h3 style="font-weight: 600;">Permissions</h3></div>
        <div class="card-body">
            @foreach($permissions as $module => $modulePermissions)
            <div style="margin-bottom: 1.5rem;">
                <h4 style="font-weight: 600; text-transform: capitalize; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e2e8f0;">
                    {{ str_replace('_', ' ', $module) }}
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($modulePermissions as $permission)
                    <label class="checkbox-item {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}" style="cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                        <span>{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
        <button type="submit" class="btn btn-primary">Update Role</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
