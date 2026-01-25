@extends('layouts.admin')

@section('title', 'Create User')

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Create User</h1>
        <p style="color: #64748b; margin-top: 0.25rem;">Add a new user to the system</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Back to Users
    </a>
</div>
@endsection

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Full Name <span style="color: #ef4444;">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required autofocus>
                @error('name')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address <span style="color: #ef4444;">*</span></label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
                @error('email')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Password <span style="color: #ef4444;">*</span></label>
                <input type="password" name="password" class="form-input" required>
                <p class="form-hint">Minimum 8 characters with uppercase, lowercase, number, and symbol</p>
                @error('password')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm Password <span style="color: #ef4444;">*</span></label>
                <input type="password" name="password_confirmation" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Role <span style="color: #ef4444;">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="">Select a role...</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
                @error('role_id')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                    <span>Active</span>
                </label>
                <p class="form-hint">Inactive users cannot log in</p>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="send_welcome_email" value="1" {{ old('send_welcome_email', true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                    <span>Send welcome email with login credentials</span>
                </label>
            </div>
            
            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
