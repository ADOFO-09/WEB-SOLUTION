@if(session('success'))
<div class="alert alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.75rem; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger" x-data="{ show: true }" x-show="show">
    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.75rem; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
    </svg>
    <span>{{ session('error') }}</span>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning" x-data="{ show: true }" x-show="show">
    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.75rem; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
    </svg>
    <span>{{ session('warning') }}</span>
</div>
@endif

@if(session('info'))
<div class="alert alert-info" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.75rem; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
    </svg>
    <span>{{ session('info') }}</span>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.75rem; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
    </svg>
    <div>
        <strong>Please fix the following errors:</strong>
        <ul style="margin-top: 0.5rem; margin-left: 1rem; list-style: disc;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
