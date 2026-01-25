@extends('layouts.admin')

@section('title', 'QR Code - ' . $member->full_name)

@section('header')
    <div class="flex items-center">
        <a href="{{ route('admin.members.show', $member) }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Member QR Code</h1>
    </div>
@endsection

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white text-center">{{ $member->full_name }}</h2>
            <p class="text-indigo-100 text-center text-sm">{{ $member->member_id }}</p>
        </div>

        <!-- QR Code -->
        <div class="p-8 flex flex-col items-center" id="qr-section">
            <div class="bg-white p-4 rounded-lg shadow-inner border-2 border-gray-200">
                @php
                    $qrData = json_encode($member->getQrCodeData());
                @endphp
                {!! QrCode::size(200)->margin(1)->generate($qrData) !!}
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">Scan this code for attendance</p>
            </div>
        </div>

        <!-- Member Info -->
        <div class="px-6 pb-6">
            <div class="border-t border-gray-200 pt-4">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="font-medium text-gray-900">{{ $member->phone_primary }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="font-medium text-gray-900">{{ ucfirst($member->membership_status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Date Joined</dt>
                        <dd class="font-medium text-gray-900">{{ $member->date_joined?->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Gender</dt>
                        <dd class="font-medium text-gray-900">{{ ucfirst($member->gender) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-6 py-4 flex justify-center space-x-4">
            <button onclick="printQR()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print QR Code
            </button>
            <a href="{{ route('admin.members.card', $member) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                </svg>
                Print Member Card
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printQR() {
    const printContent = document.getElementById('qr-section').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>QR Code - {{ $member->full_name }}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    display: flex; 
                    flex-direction: column;
                    align-items: center; 
                    justify-content: center; 
                    min-height: 100vh;
                    margin: 0;
                }
                h2 { margin: 0 0 5px 0; }
                p { margin: 0; color: #666; }
            </style>
        </head>
        <body>
            <h2>{{ $member->full_name }}</h2>
            <p>{{ $member->member_id }}</p>
            ${printContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush
@endsection
