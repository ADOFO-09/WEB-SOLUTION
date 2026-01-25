@extends('layouts.admin')

@section('title', 'Change Password')

@section('header')
<h1>Change Password</h1>
@endsection

@section('content')
<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.profile.password.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-input" required>
                @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-input" required>
                <p class="form-hint">Minimum 8 characters with uppercase, lowercase, number, and symbol</p>
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-input" required>
            </div>
            
            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
