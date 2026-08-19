@extends('setup.layout')
@section('title', 'Step 1 — Administrator Account')

@section('content')
<div class="card">
    <div class="card-head">
        <h2>Step 1 — Administrator Account</h2>
        <p>Set your name, email address, and a strong password. These will be your permanent login credentials.</p>
    </div>
    <div class="card-body">

        <div class="info-box">
            <strong>Why this step?</strong>
            Your account was created with a placeholder email and a temporary password. Replace them now before configuring anything else.
        </div>

        @if($errors->any())
        <div class="alert alert-error">
            <ul style="list-style: inside; padding-left: 0.5rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('setup.account.save') }}" novalidate>
            @csrf

            <div class="form-group">
                <label>Your Full Name</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       placeholder="e.g. Rev. Emmanuel Asante" required
                       class="{{ $errors->has('name') ? 'field-error' : '' }}">
                @error('name')<p class="input-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                       placeholder="you@youremail.com" required
                       class="{{ $errors->has('email') ? 'field-error' : '' }}">
                <p class="input-hint">This becomes your login username. Use a real email you own.</p>
                @error('email')<p class="input-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" required autocomplete="new-password"
                           class="{{ $errors->has('password') ? 'field-error' : '' }}">
                    <p class="input-hint">Min 8 chars · uppercase · number · symbol</p>
                    @error('password')<p class="input-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="{{ $errors->has('password_confirmation') ? 'field-error' : '' }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Save &amp; Continue
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
