@extends('layouts.admin')

@section('title', 'Edit Member - ' . $member->full_name)

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.members.show', $member) }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Member</h1>
        </div>
        @can('members.delete')
        <form action="{{ route('admin.members.destroy', $member) }}" method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this member?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Member
            </button>
        </form>
        @endcan
    </div>
@endsection

@section('content')
<form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Personal Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Personal Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Member ID (readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Member ID</label>
                    <input type="text" value="{{ $member->member_id }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50" readonly>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <select name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Title</option>
                        @foreach(['Mr', 'Mrs', 'Miss', 'Elder', 'Deacon', 'Deaconess', 'Pastor', 'Evangelist', 'Prophet', 'Apostle'] as $title)
                            <option value="{{ $title }}" {{ old('title', $member->title) == $title ? 'selected' : '' }}>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $member->first_name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('first_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Middle Name -->
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $member->middle_name) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $member->last_name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('last_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('date_of_birth')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                    <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <!-- Marital Status -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Marital Status *</label>
                    <select name="marital_status" id="marital_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="single" {{ old('marital_status', $member->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status', $member->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('marital_status', $member->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('marital_status', $member->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Contact Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="phone_primary" class="block text-sm font-medium text-gray-700">Primary Phone *</label>
                    <input type="tel" name="phone_primary" id="phone_primary" value="{{ old('phone_primary', $member->phone_primary) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone_primary')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone_secondary" class="block text-sm font-medium text-gray-700">Secondary Phone</label>
                    <input type="tel" name="phone_secondary" id="phone_secondary" value="{{ old('phone_secondary', $member->phone_secondary) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="lg:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $member->address) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City/Town</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $member->city) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="region" class="block text-sm font-medium text-gray-700">Region</label>
                    <input type="text" name="region" id="region" value="{{ old('region', $member->region) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Work & Emergency Contact -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Work & Emergency Contact</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $member->occupation) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="employer" class="block text-sm font-medium text-gray-700">Employer</label>
                    <input type="text" name="employer" id="employer" value="{{ old('employer', $member->employer) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Church Information -->
    <div class="bg-white shadow rounded-lg" id="ministries">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Church Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="date_joined" class="block text-sm font-medium text-gray-700">Date Joined *</label>
                    <input type="date" name="date_joined" id="date_joined" value="{{ old('date_joined', $member->date_joined?->format('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="baptism_date" class="block text-sm font-medium text-gray-700">Baptism Date</label>
                    <input type="date" name="baptism_date" id="baptism_date" value="{{ old('baptism_date', $member->baptism_date?->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="baptism_type" class="block text-sm font-medium text-gray-700">Baptism Type *</label>
                    <select name="baptism_type" id="baptism_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="none" {{ old('baptism_type', $member->baptism_type) == 'none' ? 'selected' : '' }}>None</option>
                        <option value="water" {{ old('baptism_type', $member->baptism_type) == 'water' ? 'selected' : '' }}>Water</option>
                        <option value="holy_spirit" {{ old('baptism_type', $member->baptism_type) == 'holy_spirit' ? 'selected' : '' }}>Holy Spirit</option>
                        <option value="both" {{ old('baptism_type', $member->baptism_type) == 'both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>

                <div>
                    <label for="membership_status" class="block text-sm font-medium text-gray-700">Membership Status *</label>
                    <select name="membership_status" id="membership_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" {{ old('membership_status', $member->membership_status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('membership_status', $member->membership_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="transferred_out" {{ old('membership_status', $member->membership_status) == 'transferred_out' ? 'selected' : '' }}>Transferred Out</option>
                        <option value="transferred_in" {{ old('membership_status', $member->membership_status) == 'transferred_in' ? 'selected' : '' }}>Transferred In</option>
                        <option value="deceased" {{ old('membership_status', $member->membership_status) == 'deceased' ? 'selected' : '' }}>Deceased</option>
                    </select>
                </div>

                <div>
                    <label for="previous_church" class="block text-sm font-medium text-gray-700">Previous Church</label>
                    <input type="text" name="previous_church" id="previous_church" value="{{ old('previous_church', $member->previous_church) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Ministries -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Ministries</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                    @php
                        $memberMinistryIds = old('ministries', $member->activeMinistries->pluck('id')->toArray());
                    @endphp
                    @foreach($ministries as $ministry)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="ministries[]" value="{{ $ministry->id }}"
                               {{ in_array($ministry->id, $memberMinistryIds) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $ministry->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Photo & Notes -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Photo & Notes</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div id="preview-container" class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                            @if($member->photo_path)
                                <img src="{{ $member->photo_url }}" class="h-full w-full object-cover">
                            @else
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(this)">
                            <button type="button" onclick="document.getElementById('photo').click()" 
                                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Change Photo
                            </button>
                            <p class="mt-1 text-xs text-gray-500">JPEG, PNG up to 2MB</p>
                        </div>
                    </div>
                    @error('photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $member->notes) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Biometric Enrollment -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
            </svg>
            Biometric Enrollment
        </h3>

        @if($member->biometric_enrolled)
        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">Biometric Enrolled</p>
                    <p class="text-xs text-green-600">
                        {{ $member->biometric_enrolled_at ? 'Enrolled ' . $member->biometric_enrolled_at->format('M d, Y g:i A') : '' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.members.biometric', $member) }}"
                   class="px-3 py-1.5 text-xs font-medium bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Re-enroll
                </a>
                <button type="submit"
                        form="biometric-remove-form"
                        onclick="return confirm('Remove all biometric data for {{ addslashes($member->full_name) }}?')"
                        class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Remove
                </button>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm">
                <span class="text-gray-500">Primary Finger</span>
                <span class="{{ $member->fingerprint_template_1 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                    {{ $member->fingerprint_template_1 ? '✓ Enrolled' : 'Not enrolled' }}
                </span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm">
                <span class="text-gray-500">Backup Finger</span>
                <span class="{{ $member->fingerprint_template_2 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                    {{ $member->fingerprint_template_2 ? '✓ Enrolled' : 'Not enrolled' }}
                </span>
            </div>
        </div>
        @else
        <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-800">Not Enrolled</p>
                    <p class="text-xs text-gray-500">Enroll fingerprint for biometric attendance</p>
                </div>
            </div>
            <a href="{{ route('admin.members.biometric', $member) }}"
               class="px-4 py-2 text-sm font-medium bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Enroll Now
            </a>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('admin.members.show', $member) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Update Member
        </button>
    </div>
</form>

{{-- Standalone biometric-remove form (outside the main form to avoid nested-form HTML bug) --}}
<form id="biometric-remove-form"
      action="{{ route('admin.members.biometric.remove', $member) }}"
      method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('preview-container');
            container.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
