@extends('layouts.admin')

@section('title', 'Change Password Required')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div class="card" style="max-width: 500px; width: 100%;">
        <div class="card-header" style="background: #fef3c7; border-bottom-color: #fcd34d;">
            <h3 style="font-weight: 600; color: #92400e;">Password Change Required</h3>
        </div>
        <div class="card-body">
            <p style="margin-bottom: 1.5rem; color: #64748b;">
                For security reasons, you must change your password before continuing.
            </p>
            
            <form method="POST" action="{{ route('admin.password.change.update') }}">
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
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Change Password & Continue</button>
            </form>
        </div>
    </div>
</div>
@endsection
