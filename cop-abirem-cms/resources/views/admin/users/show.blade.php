@extends('layouts.admin')

@section('title', 'User Details')

@section('header')
<div class="flex items-center justify-between">
    <div class="flex items-center">
        <a href="{{ route('admin.users.index') }}" class="mr-3 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
    </div>
    @can('users.edit')
    <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Edit User
    </a>
    @endcan
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-6">
                <div class="w-24 h-24 rounded-full bg-indigo-100 mx-auto flex items-center justify-center">
                    <span class="text-3xl font-bold text-indigo-600">{{ substr($user->name, 0, 2) }}</span>
                </div>
                <h3 class="mt-4 text-xl font-medium text-gray-900">{{ $user->name }}</h3>
                <p class="text-gray-500">{{ $user->email }}</p>
                
                <div class="mt-4 flex justify-center space-x-2">
                    @if($user->is_active)
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">Inactive</span>
                    @endif
                    
                    @if($user->role)
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800">{{ $user->role->name }}</span>
                    @endif
                </div>
            </div>
            
            <div class="border-t pt-4 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Last Login</span>
                    <span class="text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Created</span>
                    <span class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-medium text-gray-900 mb-4">Quick Actions</h4>
            
            <div class="space-y-3">
                @can('users.edit')
                @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-left text-sm rounded-lg {{ $user->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                        {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
                @endif
                @endcan
                
                @can('users.edit')
                <button onclick="document.getElementById('resetPasswordModal').classList.remove('hidden')" 
                        class="w-full px-4 py-2 text-left text-sm rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-100">
                    Reset Password
                </button>
                @endcan
                
                @can('users.delete')
                @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-left text-sm rounded-lg bg-red-50 text-red-700 hover:bg-red-100">
                        Delete User
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
    
    <!-- Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Member Link -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Linked Member Profile</h3>
            </div>
            
            @if($user->member)
            <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                @if($user->member->photo_path)
                <img src="{{ asset('storage/' . $user->member->photo_path) }}" class="w-16 h-16 rounded-full object-cover">
                @else
                <div class="w-16 h-16 rounded-full bg-blue-200 flex items-center justify-center">
                    <span class="text-xl font-bold text-blue-600">{{ substr($user->member->first_name, 0, 1) }}{{ substr($user->member->last_name, 0, 1) }}</span>
                </div>
                @endif
                <div class="ml-4 flex-1">
                    <h4 class="font-medium text-gray-900">{{ $user->member->full_name }}</h4>
                    <p class="text-sm text-gray-500">{{ $user->member->member_id }}</p>
                    <p class="text-sm text-gray-500">{{ $user->member->phone_primary }}</p>
                </div>
                <div class="space-x-2">
                    <a href="{{ route('admin.members.show', $user->member) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        View Profile
                    </a>
                    @can('users.edit')
                    <form action="{{ route('admin.users.unlink-member', $user) }}" method="POST" class="inline" onsubmit="return confirm('Unlink this user from the member profile?')">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm ml-2">Unlink</button>
                    </form>
                    @endcan
                </div>
            </div>
            <p class="mt-3 text-sm text-gray-500">
                <svg class="w-4 h-4 inline text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                This user can access the Member Portal at <code class="bg-gray-100 px-1 rounded">/member</code>
            </p>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                <p class="text-gray-500 mb-4">This user is not linked to any member profile.</p>
                @can('users.edit')
                <a href="{{ route('admin.users.link-member.form', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Link to Member
                </a>
                @endcan
            </div>
            @endif
        </div>
        
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            
            @if($user->activityLogs && $user->activityLogs->count() > 0)
            <div class="space-y-3">
                @foreach($user->activityLogs as $log)
                <div class="flex items-start text-sm">
                    <div class="w-2 h-2 mt-2 rounded-full bg-gray-400 mr-3"></div>
                    <div class="flex-1">
                        <p class="text-gray-900">{{ $log->description }}</p>
                        <p class="text-gray-500 text-xs">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No recent activity recorded.</p>
            @endif
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Reset Password</h3>
            <button onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="modal_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password" id="modal_password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="modal_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="modal_password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('resetPasswordModal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Reset Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
