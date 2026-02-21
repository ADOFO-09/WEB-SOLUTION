@extends('layouts.member')

@section('title', 'My Profile')

@section('header')
<h1 class="text-xl font-bold text-gray-900">My Profile</h1>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-24"></div>
            <div class="px-6 pb-6">
                <div class="-mt-12 mb-4">
                    @if($member->photo_path)
                    <img src="{{ asset('storage/' . $member->photo_path) }}" 
                         class="w-24 h-24 rounded-full border-4 border-white object-cover shadow-lg">
                    @else
                    <div class="w-24 h-24 rounded-full border-4 border-white bg-blue-500 flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-bold text-white">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                    </div>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $member->title }} {{ $member->full_name }}</h2>
                <p class="text-gray-500">{{ $member->member_id }}</p>
                
                <div class="mt-4 space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $member->membership_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($member->membership_status) }} Member
                    </span>
                    @if($member->date_joined)
                    <p class="text-sm text-gray-500">Member since {{ $member->date_joined->format('F Y') }}</p>
                    @endif
                </div>

                <div class="mt-6 space-y-2">
                    <a href="{{ route('member.profile.edit') }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Edit Profile
                    </a>
                    <a href="{{ route('member.profile.password') }}" class="block w-full text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Change Password
                    </a>
                </div>

                <!-- Photo Upload -->
                <form action="{{ route('member.profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <label class="block text-sm text-gray-600 mb-2">Update Photo</label>
                    <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <button type="submit" class="mt-2 w-full px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                        Upload Photo
                    </button>
                </form>
            </div>
        </div>

        <!-- Ministries -->
        @if($member->ministries->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <h3 class="font-semibold text-gray-900 mb-4">My Ministries</h3>
            <div class="space-y-2">
                @foreach($member->ministries as $ministry)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="ml-3 text-gray-900">{{ $ministry->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Profile Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Full Name</label>
                    <p class="text-gray-900 font-medium">{{ $member->title }} {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Date of Birth</label>
                    <p class="text-gray-900 font-medium">{{ $member->date_of_birth ? $member->date_of_birth->format('F d, Y') : '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Gender</label>
                    <p class="text-gray-900 font-medium">{{ ucfirst($member->gender) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Marital Status</label>
                    <p class="text-gray-900 font-medium">{{ ucfirst($member->marital_status) }}</p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <p class="text-gray-900 font-medium">{{ $member->email ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Primary Phone</label>
                    <p class="text-gray-900 font-medium">{{ $member->phone_primary ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Secondary Phone</label>
                    <p class="text-gray-900 font-medium">{{ $member->phone_secondary ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Address</label>
                    <p class="text-gray-900 font-medium">{{ $member->address ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">City</label>
                    <p class="text-gray-900 font-medium">{{ $member->city ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Region</label>
                    <p class="text-gray-900 font-medium">{{ $member->region ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employment Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Occupation</label>
                    <p class="text-gray-900 font-medium">{{ $member->occupation ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Employer</label>
                    <p class="text-gray-900 font-medium">{{ $member->employer ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Contact Name</label>
                    <p class="text-gray-900 font-medium">{{ $member->emergency_contact_name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Contact Phone</label>
                    <p class="text-gray-900 font-medium">{{ $member->emergency_contact_phone ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Church Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Church Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Date Joined</label>
                    <p class="text-gray-900 font-medium">{{ $member->date_joined ? $member->date_joined->format('F d, Y') : '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Baptism Date</label>
                    <p class="text-gray-900 font-medium">{{ $member->baptism_date ? $member->baptism_date->format('F d, Y') : '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Baptism Type</label>
                    <p class="text-gray-900 font-medium">{{ $member->baptism_type ? ucfirst($member->baptism_type) : '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Previous Church</label>
                    <p class="text-gray-900 font-medium">{{ $member->previous_church ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
