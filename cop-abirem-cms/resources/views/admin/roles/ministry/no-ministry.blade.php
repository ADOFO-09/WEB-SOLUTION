@extends('layouts.admin')

@section('title', 'Ministry Leader Dashboard')

@section('content')
<div style="display: flex; align-items: center; justify-content: center; min-height: 60vh;">
    <div style="text-align: center; max-width: 400px;">
        <div style="width: 80px; height: 80px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <svg style="width: 40px; height: 40px; color: #d97706;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f; margin-bottom: 0.5rem;">No Ministry Assigned</h2>
        <p style="color: #64748b; margin-bottom: 2rem;">
            You have not been assigned to lead any ministry yet. Please contact the church administrator to be assigned to a ministry.
        </p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
            Go to Main Dashboard
        </a>
    </div>
</div>
@endsection
