@extends('layouts.admin')

@section('title', 'Register New Member')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Register New Member</h1>
        <a href="{{ route('admin.members.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <!-- Personal Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Personal Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Member ID -->
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Member ID *</label>
                    <input type="text" name="member_id" id="member_id" value="{{ old('member_id', $memberId) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50" readonly>
                    @error('member_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <select name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Title</option>
                        @foreach(['Mr', 'Mrs', 'Miss', 'Elder', 'Deacon', 'Deaconess', 'Pastor', 'Evangelist', 'Prophet', 'Apostle'] as $title)
                            <option value="{{ $title }}" {{ old('title') == $title ? 'selected' : '' }}>{{ $title }}</option>
                        @endforeach
                    </select>
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('first_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Middle Name -->
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('middle_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('last_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('date_of_birth')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                    <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Marital Status -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Marital Status *</label>
                    <select name="marital_status" id="marital_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Status</option>
                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                    @error('marital_status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Contact Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Primary Phone -->
                <div>
                    <label for="phone_primary" class="block text-sm font-medium text-gray-700">Primary Phone *</label>
                    <input type="tel" name="phone_primary" id="phone_primary" value="{{ old('phone_primary') }}" required
                           placeholder="0244123456"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone_primary')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Secondary Phone -->
                <div>
                    <label for="phone_secondary" class="block text-sm font-medium text-gray-700">Secondary Phone</label>
                    <input type="tel" name="phone_secondary" id="phone_secondary" value="{{ old('phone_secondary') }}"
                           placeholder="0201234567"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone_secondary')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           placeholder="member@email.com"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Address -->
                <div class="lg:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                           placeholder="House No. / Street Name"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City/Town</label>
                    <input type="text" name="city" id="city" value="{{ old('city', 'Abirem') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Region -->
                <div>
                    <label for="region" class="block text-sm font-medium text-gray-700">Region</label>
                    <input type="text" name="region" id="region" value="{{ old('region', 'Eastern Region') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('region')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Work & Emergency Contact -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Work & Emergency Contact</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Occupation -->
                <div>
                    <label for="occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('occupation')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Employer -->
                <div>
                    <label for="employer" class="block text-sm font-medium text-gray-700">Employer</label>
                    <input type="text" name="employer" id="employer" value="{{ old('employer') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('employer')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Emergency Contact Name -->
                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('emergency_contact_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Emergency Contact Phone -->
                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('emergency_contact_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Church Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Church Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Date Joined -->
                <div>
                    <label for="date_joined" class="block text-sm font-medium text-gray-700">Date Joined *</label>
                    <input type="date" name="date_joined" id="date_joined" value="{{ old('date_joined', date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('date_joined')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Baptism Date -->
                <div>
                    <label for="baptism_date" class="block text-sm font-medium text-gray-700">Baptism Date</label>
                    <input type="date" name="baptism_date" id="baptism_date" value="{{ old('baptism_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('baptism_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Baptism Type -->
                <div>
                    <label for="baptism_type" class="block text-sm font-medium text-gray-700">Baptism Type *</label>
                    <select name="baptism_type" id="baptism_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="none" {{ old('baptism_type') == 'none' ? 'selected' : '' }}>None</option>
                        <option value="water" {{ old('baptism_type') == 'water' ? 'selected' : '' }}>Water</option>
                        <option value="holy_spirit" {{ old('baptism_type') == 'holy_spirit' ? 'selected' : '' }}>Holy Spirit</option>
                        <option value="both" {{ old('baptism_type') == 'both' ? 'selected' : '' }}>Both</option>
                    </select>
                    @error('baptism_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Membership Status -->
                <div>
                    <label for="membership_status" class="block text-sm font-medium text-gray-700">Membership Status *</label>
                    <select name="membership_status" id="membership_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" {{ old('membership_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('membership_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="transferred_in" {{ old('membership_status') == 'transferred_in' ? 'selected' : '' }}>Transferred In</option>
                    </select>
                    @error('membership_status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Previous Church -->
                <div>
                    <label for="previous_church" class="block text-sm font-medium text-gray-700">Previous Church</label>
                    <input type="text" name="previous_church" id="previous_church" value="{{ old('previous_church') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('previous_church')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Ministries -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Ministries</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach($ministries as $ministry)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="ministries[]" value="{{ $ministry->id }}"
                               {{ in_array($ministry->id, old('ministries', [])) ? 'checked' : '' }}
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
                <!-- Photo Upload -->
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div id="preview-container" class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <button type="button" onclick="document.getElementById('photo').click()" 
                                class="px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Upload Photo
                        </button>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">JPEG, PNG up to 2MB</p>
                    @error('photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Biometric Enrollment (Optional) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
            </svg>
            Biometric Enrollment <span class="ml-1 text-sm font-normal text-gray-400">(Optional)</span>
        </h3>
        <p class="text-sm text-gray-500 mb-4">Enroll the member's fingerprint for biometric attendance. You can also do this later from their profile.</p>

        <div class="flex items-center mb-4">
            <input type="checkbox" id="enroll_biometric" name="enroll_biometric" value="1"
                   class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                   onchange="toggleBiometricSection(this.checked)">
            <label for="enroll_biometric" class="ml-2 text-sm font-medium text-gray-700">Enroll fingerprint now</label>
        </div>

        <div id="biometric_section" class="hidden space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Primary Finger -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Primary Finger</h4>
                    <div id="fp_status_1" class="mb-3 text-sm text-gray-400">Not captured</div>
                    <input type="hidden" name="fingerprint_template_1" id="fingerprint_template_1">
                    <button type="button" onclick="captureFingerprint(1)"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                        Capture Primary Finger
                    </button>
                </div>

                <!-- Backup Finger -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Backup Finger <span class="font-normal text-gray-400">(optional)</span></h4>
                    <div id="fp_status_2" class="mb-3 text-sm text-gray-400">Not captured</div>
                    <input type="hidden" name="fingerprint_template_2" id="fingerprint_template_2">
                    <button type="button" onclick="captureFingerprint(2)"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                        Capture Backup Finger
                    </button>
                </div>
            </div>

            <!-- Scanning indicator (shown while waiting) -->
            <div id="fp_scanning" class="hidden flex items-center gap-2 p-3 bg-purple-50 rounded-lg text-sm text-purple-700">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Waiting for fingerprint scan…
            </div>

            <!-- No scanner warning -->
            <div id="fp_warning" class="hidden p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
                <strong>Scanner not detected.</strong>
                Connect a fingerprint scanner (DigitalPersona, SecuGen, ZKTeco) and try again.
                You can also enroll biometrics later from the member's profile.
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('admin.members.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Register Member
        </button>
    </div>
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

// ── Biometric capture (WebSocket bridge to local scanner server) ────────────
const ALL_TEMPLATES_URL = "{{ route('admin.members.biometric.all-templates') }}";

let fpWs             = null;
let fpCapturingIndex = null;
let fpOtherMembers   = [];   // all currently enrolled members — for duplicate check

function toggleBiometricSection(show) {
    document.getElementById('biometric_section').classList.toggle('hidden', !show);
    if (show && !fpWs) initFpScanner();
}

async function initFpScanner() {
    // Load all enrolled templates first, then open the WebSocket
    try {
        const res  = await fetch(ALL_TEMPLATES_URL, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        fpOtherMembers = data.members || [];
    } catch (_) {
        fpOtherMembers = [];
    }
    connectFpScanner();
}

function connectFpScanner() {
    try {
        fpWs = new WebSocket('ws://localhost:15896/fingerprint');
        fpWs.onopen  = () => { document.getElementById('fp_warning').classList.add('hidden'); };
        fpWs.onclose = () => { fpWs = null; document.getElementById('fp_warning').classList.remove('hidden'); };
        fpWs.onerror = () => { fpWs = null; document.getElementById('fp_warning').classList.remove('hidden'); };
        fpWs.onmessage = (e) => {
            let d; try { d = JSON.parse(e.data); } catch (_) { return; }

            if (d.type === 'identify_result') {
                document.getElementById('fp_scanning').classList.add('hidden');
                if (d.matched) {
                    const match = fpOtherMembers.find(m => m.id === d.member_id);
                    const name  = match ? match.name : 'another member';
                    onFpError(fpCapturingIndex,
                        'Duplicate fingerprint — already enrolled for ' + name +
                        '. Each member must use their own unique finger.');
                    fpCapturingIndex = null;
                } else if (d.template) {
                    onFpCaptured(fpCapturingIndex, d.template);
                    fpCapturingIndex = null;
                }

            } else if (d.type === 'capture_result') {
                // Fallback: no enrolled members exist yet — plain capture is fine
                document.getElementById('fp_scanning').classList.add('hidden');
                if (d.success) {
                    onFpCaptured(fpCapturingIndex, d.template);
                } else {
                    onFpError(fpCapturingIndex, d.message || 'Capture failed. Try again.');
                }
                fpCapturingIndex = null;
            }
        };
    } catch (_) {
        document.getElementById('fp_warning').classList.remove('hidden');
    }
}

function captureFingerprint(index) {
    if (!fpWs || fpWs.readyState !== WebSocket.OPEN) {
        document.getElementById('fp_warning').classList.remove('hidden');
        return;
    }
    // Clear any previous capture for this slot
    document.getElementById('fingerprint_template_' + index).value = '';
    fpCapturingIndex = index;
    document.getElementById('fp_scanning').classList.remove('hidden');
    document.getElementById('fp_status_' + index).textContent = 'Scanning…';

    if (fpOtherMembers.length > 0) {
        // Use identify mode so the bridge checks for duplicates automatically
        fpWs.send(JSON.stringify({
            action: 'start_identify',
            members: fpOtherMembers.map(m => ({ id: m.id, t1: m.t1, t2: m.t2 || null }))
        }));
    } else {
        // No one enrolled yet — plain capture is safe
        fpWs.send(JSON.stringify({ action: 'capture' }));
    }
}

function onFpCaptured(index, template) {
    document.getElementById('fingerprint_template_' + index).value = template;
    document.getElementById('fp_status_' + index).innerHTML =
        '<span class="inline-flex items-center gap-1 text-green-600 font-medium">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
        'Captured</span>';
}

function onFpError(index, msg) {
    document.getElementById('fp_status_' + index).innerHTML =
        '<span class="text-red-600">' + msg + '</span>';
}
</script>
@endpush
@endsection
