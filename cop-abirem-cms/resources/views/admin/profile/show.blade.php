@extends('layouts.admin')

@section('title', 'My Profile')

@section('header')
<h1>My Profile</h1>
@endsection

@section('content')
<div class="grid grid-cols-3" style="gap: 1.5rem;">
    <div style="grid-column: span 2;">
        <div class="card">
            <div class="card-header"><h3 style="font-weight: 600;">Profile Information</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
    
    <div>
        <div class="card">
            <div class="card-header"><h3 style="font-weight: 600;">Account Info</h3></div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <span style="color: #64748b; font-size: 0.875rem;">Role</span>
                    <div><span class="badge badge-info">{{ $user->role->name }}</span></div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <span style="color: #64748b; font-size: 0.875rem;">Member Since</span>
                    <div>{{ $user->created_at->format('M j, Y') }}</div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.875rem;">Last Login</span>
                    <div>{{ $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never' }}</div>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-top: 1rem;">
            <div class="card-header"><h3 style="font-weight: 600;">Security</h3></div>
            <div class="card-body">
                <a href="{{ route('admin.profile.password') }}" class="btn btn-secondary" style="width: 100%;">Change Password</a>
            </div>
        </div>
    </div>
</div>
@endsection
