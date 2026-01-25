@extends('layouts.admin')

@section('title', 'Edit User')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Edit User</h1>
        <p style="color: #64748b; margin-top: 0.25rem;">{{ $user->name }}</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
</div>
@endsection

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Full Name <span style="color: #ef4444;">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address <span style="color: #ef4444;">*</span></label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Role <span style="color: #ef4444;">*</span></label>
                <select name="role_id" class="form-select" required {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @if($user->id === auth()->id())
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                <p class="form-hint">You cannot change your own role</p>
                @endif
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <span>Active</span>
                </label>
            </div>
            
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
